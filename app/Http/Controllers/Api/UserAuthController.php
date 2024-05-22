<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\currencyBalance;
use App\Models\Role;
use App\Models\User;
use App\Models\userWallet;
use App\Models\VendorCoupon;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client as RestClient;

class UserAuthController extends Controller
{
    public function index()
    {
        $response = ['success' => false, 'message' => 'Succesfully Fetch'];
        return response()->json($response);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'mobile_number' => 'required|unique:users',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $validator->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $role = Role::where('role', 'user')->first();
        if ($user) {
            $user->roles()->attach($role->id, ['user_id' => $user->id]);

            $otp = strval(random_int(1000, 9999));
            $user->otp = $otp;
            $user->otp_created_at = Carbon::now();
            $user->save();

            // For Notification
            $FcmToken = User::whereNotNull('fcm_token')
                ->where('id', $user->id)
                ->pluck('fcm_token')
                ->all();

            $title = 'One-Time Passcode Verification (OTP)';
            $body = 'Your One Time Password is: ' . $otp;
            notification($title, $body, $FcmToken);

            if ($request->has('email')) {
                $data['otp'] = $otp;
                $data['email'] = $request->email;
                $data['title'] = 'OTP Verification';
                $data['body'] = 'Your OTP is: ' . $otp;

                Mail::send(
                    'email.forgotPasswordMail',
                    ['data' => $data],
                    function ($message) use ($data) {
                        $message
                            ->from('mail@dilamsys.com', 'Ktwis')
                            ->to($data['email'])
                            ->subject($data['body']);
                    }
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Email not exists'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Email not exists',
                    ],
                    404
                );
            }
        }

        // $success['token'] = $user->createToken(
        //     'user_application_token'
        // )->accessToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;

        if ($user) {
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $success],
                'timestamp' => Carbon::now(),
                'message' => 'User register successfully',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'An unexpected error occurred'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Something Went Wrong',
                ],
                500
            );
        }
    }

    public function verify_user_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $validator->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        }

        $user = User::where('email', $request->user_id)
            ->orWhere('mobile_number', $request->user_id)
            ->first();
        if ($user && $user->otp == $request->otp) {
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            $token = null;
            if ($request->type) {
                $token = $user->createToken('user_application_token')
                    ->accessToken;
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $token],
                'timestamp' => Carbon::now(),
                'message' => 'Code Verified',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 403,
                    'status' => false,
                    'context' => ['error' => 'Code Does Not Match'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Code Does Not Match',
                ],
                403
            );
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|max:16|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $validator->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        }

        $user = User::where('email', $request->user_id)
            ->orWhere('mobile_number', $request->user_id)
            ->first();

        if ($user) {
            if (
                Auth::attempt([
                    'email' => $user->email,
                    'password' => $request->password,
                ])
            ) {
                if ($user->customer_status == 0) {
                    return response()->json(
                        [
                            'http_status_code' => 400,
                            'status' => false,
                            'context' => [
                                'error' => 'Your Account is Inactive',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'Your Account is Inactive',
                        ],
                        400
                    );
                }

                if ($user->otp_created_at) {
                    $otp = strval(random_int(1000, 9999));
                    $user->otp = $otp;
                    $user->save();

                    $data['otp'] = $otp;
                    $data['email'] = $user->email;
                    $data['title'] = 'OTP Verification';
                    $data['body'] = 'Your OTP is: ' . $otp;

                    Mail::send(
                        'email.forgotPasswordMail',
                        ['data' => $data],
                        function ($message) use ($data) {
                            $message
                                ->from('mail@dilamsys.com', 'Ktwis')
                                ->to($data['email'])
                                ->subject($data['body']);
                        }
                    );

                    return response()->json(
                        [
                            'http_status_code' => 400,
                            'status' => false,
                            'context' => ['error' => 'Account not verified'],
                            'timestamp' => Carbon::now(),
                            'message' =>
                                'First verified your otp & try again .. ',
                        ],
                        400
                    );
                }



                $userCart = Cart::where([
                    'user_id' => $user->id,
                ])->get();
                foreach ($userCart as $cart) {
                    $guestCart = Cart::where('guest_user', $request->device_id)
                        ->where('seller_id', $cart->seller_id)
                        ->first();

                    if ($guestCart) {
                        $addCart = Cart::where('seller_id', $cart->seller_id)
                            ->where('user_id', $user->id)
                            ->first();
                        if ($addCart) {
                            $addCart->sub_total =
                                $guestCart->sub_total + $addCart->sub_total;
                            $addCart->discount_amount =
                                $guestCart->discount_amount +
                                $addCart->discount_amount;
                            $addCart->coupon_discount =
                                $guestCart->coupon_discount +
                                $addCart->coupon_discount;

                            $addCart->coupon_discount =
                                $guestCart->coupon_discount +
                                $addCart->coupon_discount;

                            $addCart->total_amount =
                                $guestCart->total_amount +
                                $addCart->total_amount;
                            $addCart->save();

                            $cartItem = CartItem::where(
                                'cart_id',
                                $guestCart->id
                            )->get();

                            foreach ($cartItem as $item) {
                                $cart_items = CartItem::where(
                                    'user_id',
                                    $user->id
                                )
                                    ->where('product_id', $item->product_id)
                                    ->first();

                                if ($cart_items) {
                                    $cart_items->quantity =
                                        $item->quantity + $cart_items->quantity;
                                    $cart_items->total_weight =
                                        $item->total_weight +
                                        $cart_items->total_weight;
                                    $cart_items->offer_price =
                                        $item->offer_price +
                                        $cart_items->offer_price;
                                    $cart_items->purchase_price =
                                        $item->purchase_price +
                                        $cart_items->purchase_price;
                                    $cart_items->base_total =
                                        $item->base_total +
                                        $cart_items->base_total;
                                    $cart_items->save();
                                }

                                CartItem::where(
                                    'guest_user',
                                    $request->device_id
                                )
                                    ->where('product_id', $item->product_id)
                                    ->delete();
                            }
                        }

                        //   destroy guest Cart
                        Cart::where('guest_user', $request->device_id)
                            ->where('seller_id', $cart->seller_id)
                            ->forceDelete();
                    }
                }

                // assign guest cart
                Cart::where('guest_user', $request->device_id)->update([
                    'user_id' => $user->id,
                ]);

                $cart = Cart::where('guest_user', $request->device_id)
                    ->pluck('id')
                    ->toArray();

                $cartItem = CartItem::whereIn('cart_id', $cart)->update([
                    'user_id' => $user->id,
                ]);

                $existWishlist = Wishlist::where(
                    'created_by',
                    $user->id
                )
                    ->pluck('product_id')
                    ->toArray();

                //   update wishlist cart
                Wishlist::where('guest_user', $request->device_id)
                    ->whereNotIn('product_id', $existWishlist)
                    ->update(['created_by' => $user->id]);


                    // update cart item count 
                    foreach(Cart::where('user_id', $user->id)->get() as $item){
                        $cart_count = CartItem :: where('cart_id', $item->id)->count();
                        Cart::where('id',$item->id)->update([
                              'item_count' => $cart_count
                        ]);
               }

                $user = Auth::guard('web')->user();
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['user'] = $user;
                $user->profile_pic = asset(
                    'public/customer/profile/' . $user->profile_pic
                );

                // update customer fcm token
                if ($request->fcm_token) {
                    User::where('id', Auth::user()->id)->update([
                        'fcm_token' => $request->fcm_token,
                    ]);
                }
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $success],
                    'timestamp' => Carbon::now(),
                    'message' => 'Login Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => 'Invalid Password'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Invalid Password',
                    ],
                    422
                );
            }
        } else {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'User Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'User Not Found',
                ],
                404
            );
        }
    }

    public function logout()
    {
        $user = Auth::user();
        $user->token()->revoke();

        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => []],
            'timestamp' => Carbon::now(),
            'message' => 'Successfully logged out',
        ]);
    }

    public function email_token(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['user'] = $user;
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$success]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Token created Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'User Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'User Not Found',
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }

    public function socialLogin(Request $request)
    {
        try {
            $existingUser = User::where('email', $request->email)
                ->when(
                    $request->has('user_identifier') &&
                        $request->user_identifier != null,
                    function ($query) use ($request) {
                        $query->where(
                            'user_identifier',
                            $request->user_identifier
                        );
                    }
                )
                ->first([
                    'name',
                    'email',
                    'id',
                    'user_identifier',
                    'customer_status',
                ]);

            if ($existingUser) {
                
                if ($existingUser->customer_status == 0) {
                    return response()->json(
                        [
                            'http_status_code' => 400,
                            'status' => false,
                            'context' => [
                                'error' => 'Your Account is Inactive',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'Your Account is Inactive',
                        ],
                        400
                    );
                }

                $userCart = Cart::where([
                    'user_id' => $existingUser->id,
                ])->get();
                foreach ($userCart as $cart) {
                    $guestCart = Cart::where('guest_user', $request->device_id)
                        ->where('seller_id', $cart->seller_id)
                        ->first();

                    if ($guestCart) {
                        $addCart = Cart::where('seller_id', $cart->seller_id)
                            ->where('user_id', $existingUser->id)
                            ->first();
                        if ($addCart) {
                        
                            $addCart->sub_total =
                                $guestCart->sub_total + $addCart->sub_total;
                            $addCart->discount_amount =
                                $guestCart->discount_amount +
                                $addCart->discount_amount;
                            $addCart->coupon_discount =
                                $guestCart->coupon_discount +
                                $addCart->coupon_discount;

                            $addCart->coupon_discount =
                                $guestCart->coupon_discount +
                                $addCart->coupon_discount;

                            $addCart->total_amount =
                                $guestCart->total_amount +
                                $addCart->total_amount;
                            $addCart->save();

                            $cartItem = CartItem::where(
                                'cart_id',
                                $guestCart->id
                            )->get();

                            foreach ($cartItem as $item) {
                                $cart_items = CartItem::where(
                                    'user_id',
                                    $existingUser->id
                                )
                                    ->where('product_id', $item->product_id)
                                    ->first();

                                if ($cart_items) {
                                    $cart_items->quantity =
                                        $item->quantity + $cart_items->quantity;
                                    $cart_items->total_weight =
                                        $item->total_weight +
                                        $cart_items->total_weight;
                                    $cart_items->offer_price =
                                        $item->offer_price +
                                        $cart_items->offer_price;
                                    $cart_items->purchase_price =
                                        $item->purchase_price +
                                        $cart_items->purchase_price;
                                    $cart_items->base_total =
                                        $item->base_total +
                                        $cart_items->base_total;
                                    $cart_items->save();
                                }

                                CartItem::where(
                                    'guest_user',
                                    $request->device_id
                                )
                                    ->where('product_id', $item->product_id)
                                    ->delete();
                            }
                        }

                        //   destroy guest Cart
                        Cart::where('guest_user', $request->device_id)
                            ->where('seller_id', $cart->seller_id)
                            ->forceDelete();
                    }
                }

                // assign guest cart
                Cart::where('guest_user', $request->device_id)->update([
                    'user_id' => $existingUser->id,
                ]);

                $cart = Cart::where('guest_user', $request->device_id)
                    ->pluck('id')
                    ->toArray();

                $cartItem = CartItem::whereIn('cart_id', $cart)->update([
                    'user_id' => $existingUser->id,
                ]);

                $existWishlist = Wishlist::where(
                    'created_by',
                    $existingUser->id
                )
                    ->pluck('product_id')
                    ->toArray();

                //   update wishlist cart
                Wishlist::where('guest_user', $request->device_id)
                    ->whereNotIn('product_id', $existWishlist)
                    ->update(['created_by' => $existingUser->id]);

                  
                     // update cart item count 
                     foreach(Cart::where('user_id', $existingUser->id)->get() as $item){
                        $cart_count = CartItem :: where('cart_id', $item->id)->count();
                        Cart::where('id',$item->id)->update([
                              'item_count' => $cart_count
                        ]);
               }  

                $user = Auth::guard('web')->user();
                $success['token'] = $existingUser->createToken(
                    'MyApp'
                )->accessToken;
                $success['user'] = $existingUser;

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $success],
                    'timestamp' => Carbon::now(),
                    'message' => 'Login Successfully',
                ]);
            } else {
                $input = $request->all();
                $input['password'] = Hash::make(Str::random(10));
                $user = User::create($input);
                $role = Role::where('role', 'user')->first();
                $user->roles()->attach($role->id, ['user_id' => $user->id]);

                // assign guest cart
                Cart::where('guest_user', $request->device_id)->update([
                    'user_id' => $user->id,
                ]);
                $cart = Cart::where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
                $cartItem = CartItem::whereIn('cart_id', $cart)->update([
                    'user_id' => $user->id,
                ]);

                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['user'] = $user;

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $success],
                    'timestamp' => Carbon::now(),
                    'message' => 'Login Successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
}
