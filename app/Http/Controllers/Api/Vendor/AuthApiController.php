<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\verificationMail;
use App\Models\currencyBalance;
use App\Models\Shop;
use App\Models\User;
use App\Models\userWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class AuthApiController extends Controller
{
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'mobile_number' => 'required|unique:users',
            'shop_name' => 'required|unique:shops',
            'password' => 'required|confirmed|min:6|max:16',
            'country_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $validator->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => $validator->errors()->first(),
                ],
                422
            );
        }

        $user = new User();
        $user->name = $request['shop_name'];
        $user->email = $request['email'];
        $user->mobile_number = $request['mobile_number'];
        $user->password = Hash::make($request['password']);
        $user->country_code = $request->country_code;
        $user->is_verified = 0;
        $user->save();

        $shop = new Shop();
        $shop->shop_name = $request->shop_name;
        $shop->email = $request->email;
        $shop->vendor_id = $user->id;
        $shop->status = 'inactive';
        $shop->save();

        $otp = strval(random_int(1000, 9999));
        $update = User::where('id', $user->id)->update([
            'otp' => $otp,
            'otp_created_at' => Carbon::now(),
        ]);

        $data = [
            'otp' => $otp,
            'email' => $request->email,
            'title' => 'OTP Verification',
            'body' => 'Your OTP is: ' . $otp,
        ];

        Mail::send('email.forgotPasswordMail', ['data' => $data], function (
            $message
        ) use ($data) {
            $message
                ->from('mail@dilamsys.com', 'Ktwis')
                ->to($data['email'])
                ->subject($data['body']);
        });

        Mail::to($user->email)->send(new verificationMail($user->id));
        $role_id = 2;
        $user->roles()->attach($role_id, ['user_id' => $user->id]);
        // assign user as customer role 
        $user->roles()->attach(3, ['user_id' => $user->id]);

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
                'message' => 'Vendor register successfully',
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

    public function account_verify(Request $request)
    {
        try {
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
            if ($user) {
                if ($user && $user->otp == $request->otp) {
                    $user->otp = null;
                    $user->otp_created_at = null;
                    $user->is_verified = 1;
                    $user->save();
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => []],
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
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'User Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Invalid User',
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
                    'message' => $validator->errors()->first(),
                ],
                422
            );
        }

        // check  valid vendor
        $user = User::where(function ($query) use ($request) {
            $query
                ->where('email', $request->user_id)
                ->orWhere('mobile_number', $request->user_id);
        })
            ->whereHas('roles', function ($query) {
                $query->where('role', 'vendor');
            })
            ->first();

        if ($user) {
            if (
                Auth::attempt([
                    'email' => $user->email,
                    'password' => $request->password,
                ])
            ) {

                if ($user->is_verified == '0') {
                    $otp = strval(random_int(1000, 9999));
                    $user->otp = $otp;
                    $user->save();

                    $message = "Your One Time Password for verification is: ". $otp .". Please enter this code to verify your identity. Do not share this code with anyone";
                    $number = $user->country_code.$user->mobile_number ;
                    sms($number, $message);

                    return response()->json(
                        [
                            'http_status_code' => 400,
                            'status' => false,
                            'context' => ['error' => 'Account not verified'],
                            'timestamp' => Carbon::now(),
                            'message' => 'First verified your otp & try again .. ',
                        ],
                        400
                    );
                }

                $user = Auth::guard('web')->user();
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['user'] = $user;
                $user->profile_pic = asset(
                    'public/vendor/profile_pic' . $user->profile_pic
                );
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
                        'context' => ['error' => 'Invalid Credentials'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Invalid Credentials',
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

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_number' =>  [
                'required',
                Rule::unique('users')->ignore(Auth::user()->id),
            ],
            'dob' => 'required',
            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000',
        ]);

        $customMessages = [
            'profile_pic.max' =>
                'The profile picture must not be larger than 2 MB.',
        ];
        $validator->setCustomMessages($customMessages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => 'Validation failed'],
                    'timestamp' => Carbon::now(),
                    'message' => $validator->errors()->first(),
                ],
                422
            );
        }

        $userId = $request->user_id;
        $user = User::with('vendor')->findOrFail(Auth::user()->id);

        // Update the user and vendor data
        $user->name = $request->name;
        $user->mobile_number = $request->mobile_number;
        $user->dob = $request->dob;

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/profile_pic');
            $image->move($destinationPath, $image_name);

            $user->profile_pic = $image_name;
        }
        $user->save();

        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $user],
            'timestamp' => Carbon::now(),
            'message' => 'Profile Updated Successfully',
        ]);
    }

    public function getProfile()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first([
                'id',
                'name',
                'email',
                'dob',
                'profile_pic',
                'mobile_number',
            ]);
            if($user->profile_pic){
                $user->profile_pic = asset(
                    'public/vendor/profile_pic/' . $user->profile_pic
                );
            }
            else {
                $user->profile_pic = null ;
                
            }
           

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $user],
                'timestamp' => Carbon::now(),
                'message' => 'Profile Fetch Successfully',
            ]);
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


    // resend otp to vendor 
    public function sendCode(Request $request){
        $user = User::where('email',$request->email)->first();
        if ($user) {

            $otp = strval(random_int(1000, 9999));
            $user->otp = $otp;
            // $user->otp_created_at = Carbon::now();
            $user->save();
            
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

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'OTP send successfully',
            ]);
        }
        else{

            return response()->json([
                'http_status_code' => 404,
                'status' => false,
                'context' => ['error' => 'User Not Found'],
                'timestamp' => Carbon::now(),
                'message' => 'User Not Found',
            ]);

        }
    }


    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['http_status_code' => '422' ,'status' => false, 'context' =>  ['error' => $validator->errors()->first()] ,  'timestamp'=> Carbon::now() , 'message' => 'Validation failed'], 422);
        }
        try {
         
            $user = User::where(function ($query) use ($request) {
                $query
                    ->where('email', $request->user_id)
                    ->orWhere('mobile_number', $request->user_id);
            })
                ->whereHas('roles', function ($query) {
                    $query->where('role', 'vendor');
                })
                ->first();


            if ($user) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/reset-password?token=' . $token;

                // $data['url'] = $url;
                // $data['title'] = "Password Reset";
                // $data['body'] = "Please click on below link to reset your password.";
                $otp = strval(random_int(1000, 9999));
                $data['otp'] = $otp;
                $data['email'] = $user->email;
                $data['title'] = "OTP Verification";
                $data['body'] = "Your OTP is: " . $otp;

                Mail::send('email.forgotPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->from('mail@dilamsys.com', "Ktwis")->to($data['email'])->subject($data['body']);
                });
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                $user->otp = $otp;
                $user->save();
               
                return response()->json([ 'http_status_code' => 200 ,'status' => true, 'context' =>  ['data' => []] ,  'timestamp'=> Carbon::now() , 'message' => "Email Sent Successfully"]);
            } else {
            return response()->json([ 'http_status_code' => 404 ,'status' => false, 'context' =>  ['error' => 'User Not Found'] ,  'timestamp'=> Carbon::now() , 'message' => "User Not Found" ],404);
            }
        } catch (\Exception $e) {
            return response()->json([ 'http_status_code' => 500 ,'status' => false, 'context' =>  ['error' => $e->getMessage()] ,  'timestamp'=> Carbon::now() , 'message' => "An unexpected error occurred" ],500);
        }
    }
    


    
}
