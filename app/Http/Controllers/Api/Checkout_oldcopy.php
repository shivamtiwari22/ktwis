<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OfflineOrder;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\cartSummary;
use App\Models\CommissionCharge;
use App\Models\Currency;
use App\Models\currencyBalance;
use App\Models\GlobalSetting;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Variant;
use App\Models\UserAddress;
use App\Models\userWallet;
use App\Models\VendorCoupon;
use Carbon\Carbon;
use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class CheckoutApiController extends Controller
{
    public function store_order(Request $request)
    {
        $rules = [
            'shipping_address_id' => 'required|integer',
            'billing_address_id' => 'required|integer',
            'payment_method' => 'required',
            'amount' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $validator->errors()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        }

        // check same transaction id 
        $same =  Payment::where('transaction_id',$request->transaction_id)->exists();
        if($same){
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'Invalid Transaction Id'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Invalid Transaction Id',
                ],
                404
            );
        }
        

        $FcmToken =  $FcmToken = User::whereNotNull('fcm_token')
        ->where('id', Auth::user()->id)
        ->pluck('fcm_token')
        ->all();

        // Amount Pay to admin wallet
        $app_name = client::where('password_client', true)
        ->where('id', $request->header('client-id'))
        ->where('secret', $request->header('client-key'))
        ->first();
        $headers = [
            'Accept' => 'application/json',
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU',
        ];
        $data = ['amount' => $request->amount , 'app_name' => $app_name->name ];
        $url = app('api_url');
        $response = Http::withHeaders($headers)->post(
            $url . 'admin-pay',
            $data
        );

        $summary = cartSummary::where(
            'user_id',
            auth('api')->user()->id
        )->orderBy('id', 'desc')->first();

        if ($summary) {
            $summary->shipping_address_id = $request->shipping_address_id;
            $summary->billing_address_id = $request->billing_address_id;
            $summary->payment_method = $request->payment_method;
            $summary->save();

            $order_summary = OrderSummary::create([
                'user_id' => $summary->user_id,
                'shipping_address_id' => $summary->shipping_address_id,
                'billing_address_id' => $summary->billing_address_id,
                'payment_method' => $summary->payment_method,
                'total_amount' => $summary->total_amount,
                'discount_amount' => $summary->discount_amount,
                'coupon_discount' => $summary->coupon_discount,
                'tax_amount' => $summary->tax_amount,
                'shipping_charges' => $summary->shipping_charges,
                'grand_total' => $summary->grand_total,
                'guarantee_charge' => $summary->guarantee_charge,
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'Something Went Wrong'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Something Went Wrong',
                ],
                500
            );
        }

        $carts = Cart::where(['user_id' => auth('api')->user()->id])->get();
        $orderCount = Order::all();

        if (count($carts) > 0) {
            $cartId = 0;
            foreach ($carts as $cart) {
               
                $coupon = VendorCoupon::where([
                    'code' => $cart->coupon_code,
                    'status' => 'published',
                ])->first();

                if($coupon){
                      $used_coupons = $coupon->used_coupons + 1;
                     $coupon->used_coupons = $used_coupons;
                      $coupon->save(); 
                }

                $cartId++;
                $order = new Order();
                $order->user_id = auth('api')->user()->id;
                $order->seller_id = $cart->seller_id;
                $order->shipping_address_id = $request->input(
                    'shipping_address_id'
                );
                $order->order_number = rand(1, 999999);
                $order->tax_id = $request->input('tax_id');
                $order->item_count = $cart->item_count;
                $order->invoice_number = sprintf(
                    '%05d',
                    count($orderCount) + $cartId
                );
                $order->coupon_code = $cart->coupon_code;
                $order->sub_total = $cart->sub_total;
                $order->discount_amount = $cart->discount_amount;
                $order->coupon_discount = $cart->coupon_discount;
                $order->tax_amount = $cart->tax_amount;
                $order->shipping_amount = $cart->shipping_amount;
                $order->total_amount = $cart->total_amount;
                $order->order_summary_id = $order_summary->id;
                $order->status = 'pending';
                $order->total_refund_amount = $cart->total_amount;
                $order->save();

                $cartItem = CartItem::where('cart_id', $cart->id)->get();
                foreach ($cartItem as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $item->product_id;
                    $orderItem->variant_id = $item->variant_id;
                    $orderItem->quantity = $item->quantity;
                    $orderItem->name = $item->name;
                    $orderItem->weight = $item->weight;
                    $orderItem->total_weight = $item->total_weight;
                    $orderItem->price = $item->price;
                    $orderItem->offer_price = $item->offer_price;
                    $orderItem->purchase_price = $item->purchase_price;
                    $orderItem->total_amount = $item->base_total;
                    $orderItem->refund_amount = $item->base_total;
                    $orderItem->user_id = $item->user_id;
                    $orderItem->save();

                    // deduction of stock quantity 
                    if($item->variant_id){
                           $inventory = InventoryWithVariant::where('p_id',$item->product_id)->first();
                        $variant =   Variant::where('inventory_with_variant_id', $inventory->id)->where('id',$item->variant_id)->first();
                        $variant->stock_quantity = $variant->stock_quantity - $item->quantity;
                        $variant->save();
                    }
                    else {
                        $inventory = InventoryWithoutVariant::where('p_id',$item->product_id)->first();
                        $inventory->stock_qty = $inventory->stock_qty - $item->quantity;
                        $inventory->save();
                    }

                }
            }

            $payment = new Payment();
            $payment->tx_ref = $request->tx_ref;
            $payment->payment_method = $request->payment_method;
            $payment->charged_amount = $request->charged_amount;
            $payment->currency = $request->currency;
            $payment->amount = $request->amount;
            $payment->transaction_id = $request->transaction_id;
            $payment->remarks = $request->remarks;
            $payment->status = $request->status;
            $payment->order_summary_id = $order_summary->id;
            $payment->save();

          // send push notification to user for confirmation of payment
            $title = "Payment Confirmation";
            $body = "Your payment is confirmed on  ". date('D, j M');
            notification($title,$body,$FcmToken);
          
        } else {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'Cart item not found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Cart item not found',
                ],
                404
            );
        }

        
        // remove cart items
         Cart::where(['user_id' => auth('api')->user()->id])->delete();
         cartSummary::where('user_id',auth('api')->user()->id)->delete();
         CartItem::where('user_id', auth('api')->user()->id)->delete();

        $customer = [
            'name' => auth('api')->user()->name,
            'email' => auth('api')->user()->email,
            'phone' => auth('api')->user()->mobile_number,
        ];

        // send confirmation mail to customer 

        $shipping_address = UserAddress::find($request->shipping_address_id);
        $order_summary = OrderSummary::find($payment->order_summary_id);
        $orders = Order::where('order_summary_id', $payment->order_summary_id)->get();

        foreach($orders as $item){
            
        // send push notification to vendor for placement of order
            $FcmToken =  $FcmToken = User::whereNotNull('fcm_token')
            ->where('id', $item->seller_id)
            ->pluck('fcm_token')
            ->all();
            $title = "New Order Placed";
            $body = "We're excited to inform you that a new order has been placed by a customer , Please check your dashboard";
            notification($title,$body,$FcmToken);
            $item->vendor = User::where('id',$item->seller_id)->first();
        }

             //  store payout data in credit pass 

             $charge = CommissionCharge::first()->amount ??  3;
             $data = ['orders' => $orders , 'guarantee_charge' => $order_summary->guarantee_charge > 0 ? "Yes" : "No" , 'charge' => $charge ];
             $response = Http::withHeaders([
                'Accept' => 'application/json',
                'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU',
            ])->post(
                 $url . 'store-payout-data',
                 $data
             );

        $ordersId = Order::where('order_summary_id', $payment->order_summary_id)->pluck('id')->toArray();

        $orderItem = OrderItem::whereIn('order_id',$ordersId)->get();

        $global = GlobalSetting::first();
        if($global){
            $logo = asset('public/admin/global/'. $global->logo);
        }
        else {
            $logo = null;
        }
        $data = [
            'shipping_address' => $shipping_address,
             'order' => $orders,
             'order_items' => $orderItem,
             'order_summary' => $order_summary ,
             'global' => $global,
             'logo' => $logo
        ];

        Mail::to(Auth::user()->email)->send(new OrderConfirmation($data));

        // send push notification to user for confirmation of order
         $title = "Order Confirmation";
         $body = "Sit Back and relax, your order is confirmed on ". date('D, j M');
         notification($title,$body,$FcmToken);

        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => [
                'data' => [
                    'customer_name' => $customer,
                    'Address_detail' => $shipping_address,
                    'transaction_id' => $payment->transaction_id
                ],
            ],
            'timestamp' => Carbon::now(),
            'message' => 'order placed successfully',
        ]);
    }

    public function store_order_item(Request $request)  
    {
        try {
            $request->validate([
                'order_id' => 'required|integer',
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
            ]);

            $order = new OrderItem();
            $order->order_id = $request->input('order_id');
            $order->product_id = $request->input('product_id');
            $order->quantity = $request->input('quantity');
            $order->price_with_tax = $request->input('price_with_tax');
            $order->price_without_tax = $request->input('price_without_tax');
            $order->price_with_discount = $request->input(
                'price_with_discount'
            );
            $order->price_without_discount = $request->input(
                'price_without_discount'
            );
            $order->refund_amount = $request->input('refund_amount');
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'order item store successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function payment(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|integer',
                'amount' => 'required',
                'transaction_number' => 'required|integer',
                'transaction_method' => 'required',
            ]);

            $payment = new Payment();
            $payment->order_id = $request->input('order_id');
            $payment->amount = $request->input('amount');
            $payment->transaction_number = $request->input(
                'transaction_number'
            );
            $payment->transaction_method = $request->input(
                'transaction_method'
            );
            $payment->remarks = $request->input('remarks');
            $payment->status = $request->input('status');
            $payment->save();

            $orders = Order::where('id', $payment->order_id)->get();
            $user = Auth::user();
            Mail::to($user->email)->send(new OrderConfirmation($orders));

            return response()->json([
                'status' => true,
                'message' => 'payment done successfully',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function flutterPayment($amount)
    {
        // $user = User::find(Auth::user()->id);
        // $reference = Flutterwave::generateReference();

        // // Enter the details of the payment
        // $data = [
        //     'payment_options' => 'card,banktransfer',
        //     'amount' => $amount,
        //     'email' => $user->email,
        //     'tx_ref' => uniqid(),
        //     'currency' => 'NGN',
        //     'redirect_url' => route('checkout.callback'),
        //     'customer' => [
        //         'email' => $user->email,
        //         'phone_number' => $user->mobile_number,
        //         'name' => $user->name,
        //     ],

        //     'customizations' => [
        //         'title' => 'Complete Order',
        //         'description' => date('d M Y'),
        //     ],
        // ];

        // $payment = Flutterwave::initializePayment($data);

        // if ($payment['status'] !== 'success') {
        //     // notify something went wrong
        //     return response()->json(
        //         [
        //             'http_status_code' => 500,
        //             'status' => true,
        //             'context' => ['error' => 'Something Went Wrong'],
        //             'timestamp' => Carbon::now(),
        //             'message' =>
        //                 'Something went wrong while generating payment link',
        //         ],
        //         500
        //     );
        // }
        // return response()->json([
        //     'http_status_code' => 200,
        //     'status' => true,
        //     'context' => ['data' => $payment['data']['link']],
        //     'timestamp' => Carbon::now(),
        //     'message' => 'flutter wave payment link',
        // ]);



        // stipe payment gateway 

        $user = User::find(Auth::user()->id);
        $stripe = new \Stripe\StripeClient('sk_test_51PIlKHA134iHPR0AlFoqppMzAmbOzoSWrB9mv30VBO61TOzsePLymmRLt5rHUn9Xqd3V2pYUdWzANUb0DEeb3qlx00rqMJ5gIS');
        $redirectUrl =  route('checkout.callback').'?session_id={CHECKOUT_SESSION_ID}';
        $response = $stripe->checkout->sessions->create([
                   'success_url' => $redirectUrl,
                   'customer_email' => auth()->user()->email,
                   'payment_method_types' => ['link','card'],
                   'line_items' => [
                    [
                             'price_data' => [
                                'product_data' => [
                                    'name' => 'Checkout'
                                ],
                                'unit_amount' => 100 * $amount,
                                'currency' =>  'USD',
                            ],
                            'quantity' => 1
                        ],
                    ],
                    'mode' => 'payment',
                    'allow_promotion_codes' => true,

                ]);

                // return  redirect($response['url']);

                return response()->json([
                    'success' => true,
                    'url' => $response['url'],
                    'message' => '',
                ]);




    }

    // flutter call back response
    public function callBack(Request $request)
    {
        // $status = request()->status;
        // if ($status == 'successful') {
        //     $transactionID = Flutterwave::getTransactionIDFromCallback();
        //     $data = Flutterwave::verifyTransaction($transactionID);

        //     return response()->json([
        //         'http_status_code' => 200,
        //         'status' => true,
        //         'context' => ['data' => $data],
        //         'timestamp' => Carbon::now(),
        //         'message' => 'Payment Done Successfully',
        //     ]);
        // } else {
        //     return response()->json(
        //         [
        //             'http_status_code' => 500,
        //             'status' => true,
        //             'context' => ['error' => $status],
        //             'timestamp' => Carbon::now(),
        //             'message' => 'Something Went Wrong',
        //         ],
        //         500
        //     );
        // }


        // stipe call back 

        $stripe = new \Stripe\StripeClient('sk_test_51PIlKHA134iHPR0AlFoqppMzAmbOzoSWrB9mv30VBO61TOzsePLymmRLt5rHUn9Xqd3V2pYUdWzANUb0DEeb3qlx00rqMJ5gIS');
        $response = $stripe->checkout->sessions->retrieve($request->session_id);
         $transaction_id = $response->id; 
             
         return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $response],
            'timestamp' => Carbon::now(),
            'message' => 'Payment Done Successfully',
        ]);

        


    }


    // stripe payment gateway 
    public function stipe_checkout($amount){
        $user = User::find(Auth::user()->id);
        $stripe = new \Stripe\StripeClient('sk_test_51PIlKHA134iHPR0AlFoqppMzAmbOzoSWrB9mv30VBO61TOzsePLymmRLt5rHUn9Xqd3V2pYUdWzANUb0DEeb3qlx00rqMJ5gIS');
        $redirectUrl =  route('stripe_success').'?session_id={CHECKOUT_SESSION_ID}';
        $response = $stripe->checkout->sessions->create([
                   'success_url' => $redirectUrl,
                   'customer_email' => auth()->user()->email,
                   'payment_method_types' => ['link','card'],
                   'line_items' => [
                    [
                             'price_data' => [
                                'product_data' => [
                                    'name' => 'Checkout'
                                ],
                                'unit_amount' => 100 * $amount,
                                'currency' =>  'USD',
                            ],
                            'quantity' => 1
                        ],
                    ],
                    'mode' => 'payment',
                    'allow_promotion_codes' => true,

                ]);

                // return  redirect($response['url']);

                return response()->json([
                    'success' => true,
                    'url' => $response['url'],
                    'message' => '',
                ]);
    }


    public function stripe_success(Request $request){
        $stripe = new \Stripe\StripeClient('sk_test_51PIlKHA134iHPR0AlFoqppMzAmbOzoSWrB9mv30VBO61TOzsePLymmRLt5rHUn9Xqd3V2pYUdWzANUb0DEeb3qlx00rqMJ5gIS');
        $response = $stripe->checkout->sessions->retrieve($request->session_id);
         $transaction_id = $response->id; 


         $url = 'https://green-spark-backup.vercel.app/cart/payment?session_id='.$request->session_id;
         return  redirect($url);
    
         return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $response],
            'timestamp' => Carbon::now(),
            'message' => 'Payment Done Successfully',
        ]);
    }


    // create offline order from vendor side to customer 

    public function createOrder(Request $request)
    {
        try {
            $order_summary = OrderSummary::create([
                'user_id' => $request->customer_id,
                'shipping_address_id' => $request->shipping_address,
                'billing_address_id' => $request->billing_address,
                'total_amount' => $request->total,
                'discount_amount' => $request->discount,
                'coupon_discount' => 0,
                'tax_amount' => 0,
                'shipping_charges' => $request->shipping,
                'grand_total' => $request->grand_total,
            ]);

            $orderCount = Order::all();
            if ($order_summary) {
                $order = new Order();
                $order->user_id = $request->customer_id;
                $order->seller_id = Auth::user()->id;
                $order->shipping_address_id = $request->shipping_address;
                $order->order_number = rand(1, 999999);
                $order->tax_id = $request->tax_id;
                $order->item_count = count($request->product);
                $order->invoice_number = count($orderCount) + 1;
                $order->sub_total = $request->total;
                $order->discount_amount = $request->discount;
                $order->tax_amount = 0;
                $order->order_notes = $request->admin_note;
                $order->shipping_amount = $request->shipping;
                $order->total_amount = $request->grand_total;
                $order->order_summary_id = $order_summary->id;
                $order->status = 'pending';
                $order->total_refund_amount = $request->grand_total;
                $order->save();

                for ($a = 0; $a < count($request->product); $a++) {
                    $product = Product::where(
                        'id',
                        $request->product[$a]
                    )->first();
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->quantity = $request->quantity[$a];
                    $orderItem->name = $product->name;
                    $orderItem->weight = $product->weight;
                    $orderItem->total_weight =
                        $product->weight * $request->quantity[$a];
                    $orderItem->price = $request->actual_price[$a];
                    $orderItem->offer_price = $request->actual_price[$a];
                    $orderItem->purchase_price = $request->offer_price[$a];
                    $orderItem->total_amount = $request->offer_price[$a];
                    $orderItem->refund_amount = $request->offer_price[$a];
                    $orderItem->user_id = $request->customer_id;
                    $orderItem->save();
                }

                $currency =
                    Currency::where('currency_code', 'USD')->first() ?? null;
                $payment = new Payment();
                $payment->currency = $currency->id;
                $payment->amount = $order_summary->grand_total;
                $payment->status = 'Unpaid';
                $payment->order_summary_id = $order_summary->id;
                $payment->save();

                $user = User::find($request->customer_id);
                $code = Str::random(20);
                $order_items = OrderItem::where('order_id', $order->id)->get();
                $data = [
                    'order' => $order,
                    'order_items' => $order_items,
                    'code' => $code,
                ];

                order::where('id', $order->id)->update([
                    'payment_url' =>
                        url('/') .
                        '/payment-invoice/' .
                        $order->id .
                        '/' .
                        Str::random(20),
                ]);

                // mail payment link to customer 
                Mail::to($user->email)->send(new OfflineOrder($data));

                if ($payment) {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => false,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Order placed successfully',
                    ]);
                } else {
                    return response()->json(
                        [
                            'http_status_code' => 500,
                            'status' => false,
                            'context' => ['error' => "An unexpected error occurred"],
                            'timestamp' => Carbon::now(),
                            'message' => 'Something Went Wrong While Creating Order',
                        ],
                        500
                    );
                }
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
