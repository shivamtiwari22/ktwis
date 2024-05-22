<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SampleMail;
use App\Models\CancelOrderRequest;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\Shop;
use App\Models\State;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Variant;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use stdClass;

class OrderApiController extends Controller
{
    public function getAllCusomerOrder()
    {
        try {
            $user = auth('api')->user()->id;
            $orders = Order::where('user_id', $user)->orderBy('id','desc')->get([
                'id',
                'user_id',
                'order_number',
                'status',
                'created_at',
                'order_summary_id',
                'seller_id',
                'total_amount',
            ]);

            foreach ($orders as $order) {
                $shop_name = Shop::where(
                    'vendor_id',
                    $order->seller_id
                )->first();
                if ($shop_name) {
                    $order->shop_name = $shop_name->shop_name;
                }

                $order->order_date = date(
                    'D,d.m.y',
                    strtotime($order->created_at)
                );

                $order->order_time = date(
                    'g:i a',
                    strtotime($order->created_at)
                );

                $order_summary =
                    OrderSummary::where(
                        'id',
                        $order->order_summary_id
                    )->first() ?? null;
                $payment = Payment::where(
                    'order_summary_id',
                    $order_summary->id
                )->first();
                if ($payment) {
                    $order->order_status = $payment->status;
                } else {
                    $order->order_status = 'Failed';
                }

                $order->orderItem = OrderItem::where(
                    'order_id',
                    $order->id
                )->get();

                foreach ($order->orderItem as $item) {
                    $product = Product::withTrashed()->where('id', $item->product_id)->first();
                    $item->product_description = $product->description;
                    $item->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->featured_image
                    );
                }
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $orders,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Orders Fetched Successfully!',
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

    public function getAllPayment()
    {
        try {
            $user = Auth::user()->id;
            $orders = Order::where('user_id', $user)->get();

            foreach ($orders as $order) {
                $payments = Payment::where('id', $order->payment_id)->first();
                if ($payments) {
                    $order->payment = $payments;
                }
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $orders,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetched Successfully!',
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

    public function vendorAllOrder()
    {
        try {
            $user = auth('api')->user();
            $vendor = Vendor::where('user_id', $user->id)->first();
            $orders = Order::where('seller_id', $vendor->id)->get();
            foreach ($orders as $order) {
                $order->orderItem = OrderItem::where(
                    'order_id',
                    $order->id
                )->first();
                if ($order->orderItem) {
                    $order->product = Product::where(
                        'id',
                        $order->orderItem->product_id
                    )->first();
                }

                $order->product->featured_image_url = asset(
                    'public/vendor/featured_image/' .
                        $order->product->featured_image
                );
                $galleryImages = json_decode(
                    $order->product->gallery_images,
                    true
                );
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $order->product->gallery_image_urls = $galleryImageUrls;
                $inventory_with_variants = InventoryWithVariant::where(
                    'p_id',
                    $order->product->id
                )->first();

                if ($inventory_with_variants) {
                    $variant = Variant::where(
                        'inventory_with_variant_id',
                        $inventory_with_variants->id
                    )->get();
                    if (count($variant) > 0) {
                        $order->product->variant_details = $variant;
                    }
                }
                $inventory_without_variants = InventoryWithoutVariant::where(
                    'p_id',
                    $order->product->id
                )->first();
                if ($inventory_without_variants) {
                    $order->product->price = $inventory_without_variants->price;
                }

                $order->customer_detail = User::where(
                    'id',
                    $order->user_id
                )->first();
            }

            return response()->json(['status' => true, 'data' => $orders]);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'msg' => $e->getMessage()],
                401
            );
        }
    }

    public function get_tax(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'state_id' => 'required',
                'country_id' => 'required',
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

            $product = Product::find($request->product_id);
            $tax = Tax::where('state_id', $request->state_id)
                ->where('country_id', $request->country_id)
                ->where('created_by', $product->created_by)
                ->get();

            if ($tax->toarray()) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $tax],
                    'timestamp' => Carbon::now(),
                    'message' => ' Tax fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Tax Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => ' Tax cannot be fetched!',
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

    public function search_order_by_orderId(Request $request)
    {
        try {
            $query = $request->order_id;
            $user = auth('api')->user()->id;
            $orders = Order::where('user_id', $user)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where(
                        'order_number',
                        'like',
                        '%' . $query . '%'
                    );
                })
                ->get([
                    'id',
                    'user_id',
                    'order_number',
                    'status',
                    'created_at',
                    'order_summary_id',
                    'seller_id',
                    'total_amount',
                ]);

            foreach ($orders as $order) {
                $shop_name = Shop::where(
                    'vendor_id',
                    $order->seller_id
                )->first();
                if ($shop_name) {
                    $order->shop_name = $shop_name->shop_name;
                }

                $order->order_date = date(
                    'D,d.m.y,g:i a',
                    strtotime($order->created_at)
                );
                $order_summary =
                    OrderSummary::where(
                        'id',
                        $order->order_summary_id
                    )->first() ?? null;
                $payment = Payment::where(
                    'order_summary_id',
                    $order_summary->id
                )->first();
                if ($payment) {
                    $order->order_status = $payment->status;
                } else {
                    $order->order_status = 'Failed';
                }

                $order->orderItem = OrderItem::where(
                    'order_id',
                    $order->id
                )->get();

                foreach ($order->orderItem as $item) {
                    $product = Product::where('id', $item->product_id)->first();
                    $item->product_description = $product->description;
                    $item->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->featured_image
                    );
                }
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $orders,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Orders Fetched Successfully!',
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

    public function order_details($id)
    {
        try {
            $order = Order::where('id', $id)->first([
                'id',
                'sub_total',
                'discount_amount',
                'shipping_amount',
                'coupon_discount',
                'tax_amount',
                'total_amount',
                'order_summary_id',
                'shipping_id'
            ]);

          

            $order_summary =
                OrderSummary::where('id', $order->order_summary_id)->first() ??
                null;
            $payment = Payment::where(
                'order_summary_id',
                $order_summary->id
            )->first();
            
            $shipping = ShippingRate::where('id',$order->shipping_id)->first();
              $carrier = null ;
            if($shipping){
                $carrier = Carrier::find($shipping->carrier_id);
            }

            $order->tracking_url = $carrier ? $carrier->tracking_url : null ;
            $order->payment_method = $payment->payment_method ?? null;
            $order->status = $payment->status ?? null;
            $order->amount = $order->total_amount;
            $order->guarantee_charge = $order_summary->guarantee_charge;
            $order->order_details = Order::where('id', $id)->first([
                'id',
                'user_id',
                'order_number',
                'status',
                'created_at',
                'order_summary_id',
                'seller_id',
                'total_amount',
            ]);

            $order->order_details->shipping_address = UserAddress::withTrashed()
            ->where('id', $order_summary->shipping_address_id)
            ->first() ?? null;

            if($order->order_details->shipping_address){
                $order->order_details->shipping_address->country = Country::where('id',$order->order_details->shipping_address->country)->orWhere('country_name', $order->order_details->shipping_address->country)->value('country_name');
                $order->order_details->shipping_address->state = State::where('id',$order->order_details->shipping_address->state) ->orWhere('state_name', $order->order_details->shipping_address->state)  ->value('state_name');
            }

            $order->order_details->billing_address =
                UserAddress::withTrashed()->where(
                    'id',
                    $order_summary->billing_address_id
                )->first() ?? null;

                if($order->order_details->billing_address){
                    $order->order_details->billing_address->country = Country::where('id',$order->order_details->billing_address->country)->orWhere('country_name', $order->order_details->billing_address->country)->value('country_name');
                    $order->order_details->billing_address->state = State::where('id',$order->order_details->billing_address->state) ->orWhere('state_name', $order->order_details->billing_address->state)  ->value('state_name');
                }


            $order->order_details->shop_name =
                Shop::where(
                    'vendor_id',
                    $order->order_details->seller_id
                )->first()->shop_name ?? null;

            $order->order_details->vendor =
                User::where('id', $order->order_details->seller_id)->first([
                    'name',
                    'email',
                    'mobile_number',
                ]) ?? null;

            $order->order_details->order_date = date(
                'd/m/y',
                strtotime($order->order_details->created_at)
            );
            $order->order_details->order_time = date(
                'g:i a',
                strtotime($order->order_details->created_at)
            );

            $order->order_details->order_items = OrderItem::where(
                'order_id',
                $order->id
            )->get();

            foreach ($order->order_details->order_items as $item) {
                $product = Product::withTrashed()->where('id', $item->product_id)->first();
                $item->product_description = $product->description;
                $item->featured_image_url = asset(
                    'public/vendor/featured_image/' . $product->featured_image
                );

                $item->cancellation_request = CancelOrderRequest::where([
                    'order_id' => $item->order_id,
                    'p_id' => $item->product_id,
                ])->first()
                    ? 'Yes'
                    : 'No';
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $order,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Orders Fetched Successfully!',
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

    public function cancel_order_request(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'product_id' => 'required',
                'reason' => 'required', 
                'description' => 'required',
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

            $alreadyExists = CancelOrderRequest::where(
                'order_id',$request->order_id)->whereIn('p_id',$request->product_id)->exists();

                if($alreadyExists){
                    return response()->json(
                        [
                            'http_status_code' => 409,
                            'status' => false,
                            'context' => [
                                'error' => 'Cancel Request Already Exists',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'Cancel Request Already Exists',
                        ],
                        409
                    );
                }
        
            foreach ($request->product_id as $id) {
                $cancel = new CancelOrderRequest();
                $cancel->order_id = $request->order_id;
                $cancel->p_id = $id;
                $cancel->order_status = 'WAITING FOR PAYMENT';
                $cancel->reason = $request->reason;
                $cancel->description = $request->description;
                $cancel->status = 'NEW';
                $cancel->created_by = auth('api')->user()->id;
                $cancel->save();
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => [],
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Cancellation Request Sent Successfully',
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

    public function order_status(Request $request)
    {
        try {
            $data = Order::find($request->order_id);
            $data->status = $request->status;
            $data->save();

            if ($request->checkbox == 'no') {
                $userEmail = $request->input('email');
                $subject = $request->status;
                $mailData = [
                    'subject' => $subject,
                ];
                Mail::to($userEmail)->send(new SampleMail($mailData));

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => [],
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Email sent successfully',
                ]);
            }
            if ($data) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => [],
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Order updated successfully.',
                ]);
            } else {
                return response()->json([
                    'http_status_code' => 500,
                    'status' => true,
                    'context' => [
                        'error' => 'Something Went Wrong',
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
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

    public function full_fill(Request $request)
    {
        try {
            $order = Order::find($request->order_id);
            $order->status = 'Fulfilled';
            $order->Save();
            // $id = $request->input('id');
            // $shipping = ShippingRate::where('created_by', $id)->first();
            // $shipping->carrier_id = $request->input('carrier');
            // $shipping->save();

            if ($order) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => [],
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Order Full Filled Successfully.',
                ]);
            } else {
                return response()->json([
                    'http_status_code' => 500,
                    'status' => true,
                    'context' => [
                        'error' => 'Something Went Wrong',
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
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

    public function cancel_order(Request $request)
    {
        try {
            $id = $request->input('order_id');
            $data = Order::find($id);
            $data->status = 'Canceled';
            $data->save();
            if ($data) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => [],
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Order canceled Successfully.',
                ]);
            } else {
                return response()->json([
                    'http_status_code' => 500,
                    'status' => true,
                    'context' => [
                        'error' => 'Something Went Wrong',
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
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

    public function updatePaymentStatus(Request $request){
        try {

               $order = Order::where('id',$request->order_fk_id)->first();
               if($order){
                     $order->payment_release_status = $request->payout_status;
                     $order->save();
               }

               return response()->json([
                      'status' => true,
                      'message' => 'Status updated successfully'
               ]);

        }
        catch (\Exception $e) {
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
