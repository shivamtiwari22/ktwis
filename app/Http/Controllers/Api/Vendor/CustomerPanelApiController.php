<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CancelOrderRequest;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Message;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\UserAddress;
use App\Models\Variant;
use App\Models\Vendor;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class CustomerPanelApiController extends Controller
{
    public function cart_data_show()
    {
        try {
            $users = Cart::with([
                'cartItem' => function ($query) {
                    $query->select(
                        'id',
                        'cart_id',
                        'product_id',
                        'quantity',
                        'name',
                        'price',
                        'offer_price'
                    );
                },
                'cartItem.product' => function ($query) {
                    $query->select(
                        'id',
                        'name',
                        'featured_image',
                        'weight',
                        'description',
                        'created_by'
                    );
                },
            ])
                ->join('users', 'carts.user_id', '=', 'users.id')
                ->where('carts.seller_id', Auth::user()->id)
                ->select(
                    'carts.id',
                    'carts.user_id',
                    'carts.item_count',
                    'carts.seller_id',
                    'carts.discount_amount',
                    'carts.coupon_discount',
                    'carts.total_amount',
                    'carts.created_at',
                    'users.name',
                    'users.email',
                    'users.created_at'
                )
                ->get();

            foreach ($users as $key => $user) {
                $user->created_at =
                    Carbon::parse($user->created_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';


                    $user->member_since =
                    Carbon::parse($user->created_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
                $user->total_quantity = CartItem::where('cart_id', $user->id)
                    ->get()
                    ->sum('quantity');
                foreach ($user->cartItem as $item) {
                    $item->product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $item->product->featured_image
                    );
                }
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $users],
                'timestamp' => Carbon::now(),
                'message' => 'Cart data fetch successfully',
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
    public function wishlist()
    {
        try {
            $loginVendorProducts = Product::where('created_by',Auth::user()->id)->pluck('id')->toArray();
            $users = User::with([
                'wishlists' => function ($query) use ($loginVendorProducts) {
                    $query->select(
                        'id',
                        'product_id',
                        'variant_id',
                        'created_at',
                        'deleted_at',
                        'created_by'
                    )
                    ->whereIn('product_id',$loginVendorProducts);
                },
                'wishlists.product' => function ($query) {
                    $query
               
                    ->select(
                        'id',
                        'name',
                        'description',
                        'featured_image',
                        'key_features',
                        'created_by'
                        )
                        ->where('created_by', Auth::user()->id)
                    ;
                },
                'wishlists.product.inventory' => function ($query) {
                    $query->select('id', 'p_id', 'price', 'offer_price');
                },
            ])
                ->whereHas('wishlists', function ($query) {
                    $query->whereNull('deleted_at');
                })
             

                ->whereIn('id', function ($query) {
                    $query->select('created_by')->from('wishlists');
                })
                ->get(['id', 'name', 'email','created_at']);

            foreach ($users as $key => $user) {
                $user->wishlistCount = $user->wishlists->count();
                $user->member_since =  Carbon::parse($user->created_at)->diffForHumans(
                    null,
                    true
                ) . ' ago';
                foreach ($user->wishlists as $item) {
                    if($item->product){
                        $item->product->featured_image_url = asset(
                            'public/vendor/featured_image/' .
                                $item->product->featured_image
                        );
                    }
                 
                    if ($item->variant_id) {
                        $item->product->variant = Variant::where(
                            'id',
                            $item->variant_id
                        )->first(['id', 'price', 'offer_price']);
                    }
                }
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $users],
                'timestamp' => Carbon::now(),
                'message' => 'wishlist data fetch successfully',
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

    public function order()
    {
        try {
            $perPage = 20;
            $orders = Order::where('seller_id', Auth::user()->id)->orderBy('id','desc') ->paginate($perPage);
            foreach ($orders as $order) {
                $order_summary = OrderSummary::where(
                    'id',
                    $order->order_summary_id
                )->first();
                $order->guarantee_charge = $order_summary->guarantee_charge;
                
                $order->orderItem = OrderItem::where(
                    'order_id',
                    $order->id
                )->get();

                 foreach( $order->orderItem as $orderItem ) {
                    $product = Product::withTrashed()->where('id',$orderItem->product_id)->first();
                     $orderItem->featured_image =   asset(
                        'public/vendor/featured_image/' .
                            $product->featured_image
                    );
                 }
                $order->customer = User::where('id', $order->user_id)->first([
                    'name',
                    'email',
                ]);

                $order->customer_address = UserAddress::withTrashed()->where(
                    'user_id',
                    $order->user_id
                )->get();
            
                $order->payment = Payment::where(
                    'order_summary_id',
                    $order_summary->id
                )->first([
                    'payment_method',
                    'transaction_id',
                    'amount',
                    'status',
                    'paid_at',
                ]);

            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $orders],
                'timestamp' => Carbon::now(),
                'message' => 'Order data fetch successfully',
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

    public function cancellation_search(Request $request)
    {
        try {
            $search = $request->search;

            $cancelStatus = 'NEW'; // Define the cancel status you want to search for.

            $orders = order::where(function ($query) use ($search) {
                $query
                    ->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('total_amount', 'like', '%' . $search . '%');
            })
                ->with([
                    'cancelReq',
                    'user' => function ($query) use ($search) {
                        $query->select('id', 'name', 'email');
                    },
                ])
                ->where('seller_id', Auth::user()->id);
            if (!empty($cancelStatus)) {
                $orders->whereHas('cancelReq', function ($query) use (
                    $cancelStatus
                ) {
                    $query->where('status', $cancelStatus);
                });
            }
            $orders = $orders->get([
                'id',
                'seller_id',
                'user_id',
                'order_number',
                'item_count',
                'sub_total',
                'discount_amount',
                'coupon_discount',
                'shipping_amount',
                'total_amount',
                'total_refund_amount',
                'created_at',
                'status',
                'order_summary_id',
            ]);

            foreach ($orders as $key => $item) {
                $order_summary = OrderSummary::where(
                    'id',
                    $item->order_summary_id
                )->first();
                $item->payment = Payment::where(
                    'order_summary_id',
                    $order_summary->id
                )->first([
                    'payment_method',
                    'transaction_id',
                    'amount',
                    'status',
                    'paid_at',
                ]);
                $item->requested =
                    count($item->cancelReq) . '/' . $item->item_count;
                $item->request_at =
                    Carbon::parse(
                        $item->cancelReq->first()->created_at
                    )->diffForHumans(null, true) . ' ago';
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $orders],
                'timestamp' => Carbon::now(),
                'message' => 'Cart data fetch successfully',
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

    public function cancellation()
    {
        try {
            $cancel = order::with([
                'cancelReq',
                'user' => function ($query) {
                    $query->select('id', 'name', 'email');
                },
            ])
                ->where('seller_id', Auth::user()->id)
                ->whereHas('cancelReq', function ($query) {
                    $query->where('status', 'NEW');
                })
                ->get([
                    'id',
                    'seller_id',
                    'user_id',
                    'order_number',
                    'item_count',
                    'sub_total',
                    'discount_amount',
                    'coupon_discount',
                    'shipping_amount',
                    'total_amount',
                    'total_refund_amount',
                    'created_at',
                    'status',
                    'order_summary_id',
                ]);

            foreach ($cancel as $key => $item) {
                $order_summary = OrderSummary::where(
                    'id',
                    $item->order_summary_id
                )->first();
                $item->payment = Payment::where(
                    'order_summary_id',
                    $order_summary->id
                )->first([
                    'payment_method',
                    'transaction_id',
                    'amount',
                    'status',
                    'paid_at',
                ]);
                $item->requested =
                    count($item->cancelReq) . '/' . $item->item_count;
                $item->request_at =
                    Carbon::parse(
                        $item->cancelReq->first()->created_at
                    )->diffForHumans(null, true) . ' ago';
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $cancel],
                'timestamp' => Carbon::now(),
                'message' => 'Cart data fetch successfully',
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

    public function cancellation_approval(Request $request)
    {
        try {
            if (strtolower($request->status) == 'approve') {
                $cancel = CancelOrderRequest::where(
                    'order_id',
                    $request->order_id
                )->update(['status' => 'Approved']);
                $order = Order::where('id', $request->order_id)->update([
                    'status' => 'canceled',
                ]);
            } else {
                $cancel = CancelOrderRequest::where(
                    'order_id',
                    $request->order_id
                )->update(['status' => 'Declined']);
            }

            if ($cancel) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Request ' . $request->status . ' successfully.',
                    ],
                    200
                );
            }
            return response()->json(
                ['status' => false, 'msg' => 'Something Went Wrong'],
                200
            );
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

    public function get_all_customer(Request $request)
    {
        try {
            $search = $request->input('query');
      // get those customers id's who buys your product 
       $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
            $customers = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->with([
                'roles' => function ($query) {
                    $query->where('role', 'user');
                },
                'address',
            ])
                ->whereHas('roles', function ($query) {
                    $query->where('role', 'user');
                })
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->get([
                    'id',
                    'name',
                    'email',
                    'profile_pic',
                    'nickname',
                    'created_at',
                    'mobile_number',
                    'dob',
                    'details',
                ]);

            foreach ($customers as $customer) {
                $customer->user_address =
                    UserAddress::where('user_id', $customer->id)->first() ??
                    null;

                if ($customer->profile_pic) {
                    $customer->profile_pic = asset(
                        'public/customer/profile/' . $customer->profile_pic
                    );
                } else {
                    $customer->profile_pic =
                        'https://www.gravatar.com/avatar/f82262222694aaf364eae2a611272f7b?s=30&d=mm';
                }

                $customer->member_since =
                    Carbon::parse($customer->created_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $customers],
                'timestamp' => Carbon::now(),
                'message' => 'data fetch successfully',
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

    public function view_customer($id)
    {
        try {
            $customer = User::where('id', $id)->first([
                'id',
                'name',
                'email',
                'profile_pic',
                'nickname',
                'created_at',
                'mobile_number',
                'dob',
                'details',
            ]);
            $customer->user_address =
                UserAddress::where('user_id', $customer->id)->first() ?? null;

            if ($customer->profile_pic) {
                $customer->profile_pic = asset(
                    'public/customer/profile/' . $customer->profile_pic
                );
            } else {
                $customer->profile_pic =
                    'https://www.gravatar.com/avatar/f82262222694aaf364eae2a611272f7b?s=30&d=mm';
            }

            $customer->member_since =
                Carbon::parse($customer->created_at)->diffForHumans(
                    null,
                    true
                ) . ' ago';

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $customer],
                'timestamp' => Carbon::now(),
                'message' => 'data fetch successfully',
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

    public function delete_customer($id)
    {
        try {
            $user = User::destroy($id);
            if ($user) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Customer Deleted Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => 'Something Went wrong'],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
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

    public function change_password(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|Confirmed',
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => 'Validation failed '],
                        'timestamp' => Carbon::now(),
                        'message' => $validator->errors()->first(),
                    ],
                    422
                );
            }

            $user = User::find($request->user_id);
            $user->password = Hash::make($request->password);
            $user->save();
            if ($user) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Password Changed Successfully',
                ]);
            } else {
                return response()->json([
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'Something Went Wrong'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Something Went Wrong',
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

    public function add_customer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'profile_pic' => 'mimes:jpeg,jpg,png|max:2000|nullable',
                'password' => 'required|same:password',
                'address_line1' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'city' => 'required',
                'zip_code' => 'required',
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

            $customer = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
                'nickname' => $request->nick_name,
                'dob' => $request->dob,
                'details' => $request->description,
                'mobile_number' => $request->phone,
                'created_by' => Auth::user()->id
            ]);

            $role = Role::where('role', 'user')->first();
            $customer->roles()->attach($role->id, ['user_id' => $customer->id]);
            if ($request->hasFile('profile_pic')) {
                $image = $request->file('profile_pic');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $destinationPath = public_path('customer/profile');
                $image->move($destinationPath, $image_name);

                $customer->profile_pic = $image_name;
            }

            $customer->save();
            $address = new UserAddress();
            $address->user_id = $customer->id;
            $address->address_type = 'shipping';
            $address->contact_person = $request->name;
            $address->contact_no = $request->phone;
            $address->floor_apartment = $request->address_line1;
            $address->address = $request->address_line2;
            $address->city = $request->city;
            $address->state = $request->state_id;
            $address->country = $request->country_id;
            $address->zip_code = $request->zip_code;
            $address->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $customer],
                'timestamp' => Carbon::now(),
                'message' => 'Customer Created Successfully',
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

    public function edit_customer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'address_line1' => 'required',
                'user_id' => 'required',
                'profile_pic' => 'mimes:jpeg,jpg,png|max:2000|nullable',
                'country_id' => 'required|numeric',
                'state_id' => 'required|numeric',
                'city' => 'required',
                'zip_code' => 'required',
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

            if ($request->hasFile('profile_pic')) {
                $image = $request->file('profile_pic');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $destinationPath = public_path('customer/profile');
                $image->move($destinationPath, $image_name);
            }

            $id = $request->user_id;
            $customer = User::where('id', $id)->update([
                'name' => $request->name,
                'nickname' => $request->nick_name,
                'dob' => $request->dob,
                'details' => $request->description,
                'mobile_number' => $request->phone,
                'profile_pic' => $request->profile_pic ? $image_name : '',
            ]);

            $address = UserAddress::where('user_id', $id)
                ->where('address_type', 'shipping')
                ->first();
            $address->floor_apartment = $request->address_line1;
            $address->address = $request->address_line2;
            $address->city = $request->city;
            $address->state = $request->state_id;
            $address->country = $request->country_id;
            $address->zip_code = $request->zip_code;
            $address->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Customer Updated Successfully',
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

    public function getWithoutVariantProduct()
    {
        try {
            $products = Product::
            with([
                'inventory',            
            ])->where('created_by', auth()->user()->id )
                ->where('products.has_variant', 0 )
                ->get()->map(function($item){
                    $item->featured_image = asset(
                        'public/vendor/featured_image/' .
                            $item->featured_image
                    );
                    return $item;
                });

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $products],
                'timestamp' => Carbon::now(),
                'message' => 'Product Fetched Successfully',
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

    public function sendMessage(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'message' => 'required',
                "subject" => "required",

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
            $user = Auth::user();
            $product = new Message();
            $product->received_by = $request->input('customer_id');
            $product->subject = $request->input('subject');
            $product->message = $request->input('message');
            $product->created_by = $user->id;
            if ($request->hasFile('file_data')) {
                $image = $request->file('file_data');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/file');
                $image->move($destinationPath, $image_name);
                $product->file = $image_name;
            }

            $product->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Message send successfully',
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
}
