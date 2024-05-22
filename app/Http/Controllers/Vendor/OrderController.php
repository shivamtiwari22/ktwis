<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\OfflineOrder;
use App\Mail\SampleMail;
use App\Models\CancelOrderRequest;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CustomOrder;
use App\Models\CustomOrderItem;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Order;
use App\Models\OrderHisory;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\Shop;
use App\Models\State;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Variant;
use App\Models\Vendor;
use App\Models\VendorAddress;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function allOrder()
    {
        $orders = Order::where('seller_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($orders as $order) {
            $order->orderItem = OrderItem::where('order_id', $order->id)->get();
            $order->customer = User::where('id', $order->user_id)->first();
            $order_summary = OrderSummary::where(
                'id',
                $order->order_summary_id
            )->first();
            $order->payment = Payment::where(
                'order_summary_id',
                $order_summary->id
            )->first();
        }

        // get those customers id's who buys your product
        $getUserId = Order::where('seller_id', Auth::user()->id)
            ->pluck('user_id')
            ->toArray();

        $customers = User::where('created_by', Auth::user()->id)
            ->orWhereIn('id', $getUserId)
            ->with([
                'roles' => function ($query) {
                    $query->where('role', 'user');
                },
            ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();


            $statuses = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'canceled' => 'Canceled',
                'dispatched' => 'Dispatched',
                'delivered' => 'Delivered',
                'initiate_refund' => 'Initiate Refund',
                'refunded' => 'Refunded',
                'fulfilled' => 'Fulfilled',
            ];

        return view('vendor.orders.index', compact('orders', 'customers' , 'statuses'));
    }

    public function postAllOrder(Request $request)
    {
        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }

        if (isset($request->length)) {
            $limit = $request->length;
        } else {
            $limit = 10;
        }

        if (isset($request->start)) {
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $order_status = $request->input('order_status');
        $payment_status = $request->input('payment_status');

        $total = Order::where('seller_id', Auth::user()->id)
            ->join('users', 'orders.user_id', 'users.id')
            ->join(
                'order_summaries',
                'orders.order_summary_id',
                'order_summaries.id'
            )
            ->join(
                'payments',
                'orders.order_summary_id',
                'payments.order_summary_id'
            )

            ->where(function ($query) use ($search) {
                $query
                    ->where('orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere(
                        'orders.total_amount',
                        'like',
                        '%' . $search . '%'
                    );
            })
            ->where(function ($query) use ($from_date , $to_date , $order_status , $payment_status) {
                if ($from_date != null) {
                    $query->where('orders.created_at', '>=', $from_date);
                }

                if ($to_date != null) {
                    $query->where('orders.created_at', '<=', $to_date);
                }

                if($order_status){
                    $query->where('orders.status', $order_status);
                }

                if($payment_status){
                    $query->where('payments.status', $payment_status);
                }

            })
            ->select('orders.*')
            ->count();

        $orders = Order::where('seller_id', Auth::user()->id)
            ->join('users', 'orders.user_id', 'users.id')
            ->join(
                'order_summaries',
                'orders.order_summary_id',
                'order_summaries.id'
            )
            ->join(
                'payments',
                'orders.order_summary_id',
                'payments.order_summary_id'
            )
            ->where(function ($query) use ($search) {
                $query
                    ->where('orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere(
                        'orders.total_amount',
                        'like',
                        '%' . $search . '%'
                    );
            })
            ->where(function ($query) use ($from_date , $to_date , $order_status , $payment_status) {
                if ($from_date != null) {
                    $query->where('orders.created_at', '>=', $from_date);
                }

                if ($to_date != null) {
                    $query->where('orders.created_at', '<=', $to_date);
                }

                if($order_status){
                    $query->where('orders.status', $order_status);
                }

                if($payment_status){
                    $query->where('payments.status', $payment_status);
                }

            })
        
            ->select('orders.*')
            ->orderBy('orders.id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();

        // return $cancel;
        $data = [];
        foreach ($orders as $key => $order) {
            $order->orderItem = OrderItem::where('order_id', $order->id)->get();
            $order->customer = User::where('id', $order->user_id)->first();
            $order_summary = OrderSummary::where(
                'id',
                $order->order_summary_id
            )->first();
            $order->payment = Payment::where(
                'order_summary_id',
                $order_summary->id
            )->first();

            $action = '
            <a href="' . route('vendor.order.show_product_detail', $order->id) . '"   data-toggle="tooltip" data-placement="top" title="View Order"
            class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i
                class="dripicons-preview"></i> </a> '
            
            ;

            $orderStatus = strtolower($order->status);
            $status = '';

            if ($orderStatus == 'pending') {
                $status =
                    '<span class="label" style="display: inline-block; color:white;padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: orange;">Pending</span>';
            } elseif ($orderStatus == 'processing') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: white;">Processing</span>';
            } elseif ($orderStatus == 'confirmed') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: green; color: white;">Confirmed</span>';
            } elseif ($orderStatus == 'canceled') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: white;">Canceled</span>';
            } elseif ($orderStatus == 'dispatched') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: green; color: white;">Dispatched</span>';
            } elseif ($orderStatus == 'delivered') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: white;">Delivered</span>';
            } elseif ($orderStatus == 'initiate_refund') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: white;">Initiate Refund</span>';
            } elseif ($orderStatus == 'refunded') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: green; color: white;">Refunded</span>';
            } elseif ($orderStatus == 'fulfilled') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color:#5bc0de; color: white;">Fulfilled</span>';
            }


            $url = '<span id="url">'. $order->payment_url .'</span>';

            $data[] = [
                $offset + $key + 1,
                '#' . $order->order_number,
                $order->created_at->format('D, M j, Y g:i A'),
                $order->customer->name,
                $order->total_amount,
                strtolower($order->payment->status) == 'success'
                    ? 'Paid'
                    : $order->payment->status,
                $url,
              html_entity_decode($status),
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function show_product_detail(Request $request, $id)
    {
        $id = $request->id;
        $order = Order::find($id);
        $order_summary = OrderSummary::where(
            'id',
            $order->order_summary_id
        )->first();
        $order->payment = Payment::where(
            'order_summary_id',
            $order_summary->id
        )->first();
        $order->orderItem = OrderItem::where('order_id', $order->id)->get();

        foreach ($order->orderItem as $item) {
            $item->product = Product::withTrashed()
                ->where('id', $item->product_id)
                ->first();
        }

        $order->customer_detail = User::where('id', $order->user_id)->first();
        $user = User::where('id', $order->user_id)->first();
        $orders = Order::where('user_id', $user->id)->first();
        $order->billingAddress = UserAddress::withTrashed()
            ->where('id', $order_summary->billing_address_id)
            ->first();

        if ($order->billingAddress) {
            $order->billingAddress->state = State::where(
                'id',
                $order->billingAddress->state
            )
                ->orWhere('state_name', $order->billingAddress->state)
                ->value('state_name');
            $order->billingAddress->country = Country::where(
                'id',
                $order->billingAddress->country
            )
                ->orWhere('country_name', $order->billingAddress->country)
                ->value('country_name');
        }

        $order->shippingaddress = UserAddress::withTrashed()
            ->where('id', $order_summary->shipping_address_id)
            ->first();

        if ($order->shippingaddress) {
            $order->shippingaddress->state = State::where(
                'id',
                $order->shippingaddress->state
            )
                ->orWhere('state_name', $order->shippingaddress->state)
                ->value('state_name');
            $order->shippingaddress->country = Country::where(
                'id',
                $order->shippingaddress->country
            )
                ->orWhere('country_name', $order->shippingaddress->country)
                ->value('country_name');
        }

        $Carriers = Carrier::where('status', 1)
            ->where('created_by', Auth::user()->id)
            ->get();
        return view('vendor.orders.productdata', compact('order', 'Carriers'));
    }

    public function payment_status(Request $request)
    {
        $data = Order::find($request->id);
        $data->status = $request->status;
        $data->save();

        //   update payout status in credit pass
        $url = app('api_url');
        $value = [
            'order_id' => $data->id,
            'status' => $data->status,
        ];
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU',
        ])->post($url . 'update-payout-status', $value);

        // making order history
        OrderHistory::create([
            'order_id' => $data->id,
            'order_summary_id' => $data->order_summary_id,
            'order_number' => $data->order_number,
            'status' => $data->status,
            'action_date' => date('Y-m-d'),
            'created_by' => auth()->user()->id,
        ]);

        if ($data->status == 'Dispatched') {
            $user = User::where('id', $data->user_id)->first();
            $arr = [];
            $arr['email'] = $user->email;
            $arr['subject'] = 'Order Dispatched';

            $data = ['user' => $user];
            Mail::send('email.orderDispatched', ['data' => $data], function (
                $message
            ) use ($arr) {
                $message
                    ->from('mail@dilamsys.com', 'Ktwis')
                    ->to($arr['email'])
                    ->subject($arr['subject']);
            });

            // send push notification to customer
            $FcmToken = $FcmToken = User::whereNotNull('fcm_token')
                ->where('id', $user->id)
                ->pluck('fcm_token')
                ->all();

            $title = 'Order Dispatched';
            $body =
                "We have dispatched your
        order on " . date('D, j M');
            notification($title, $body, $FcmToken);
        } elseif ($data->status == 'Delivered') {
            $user = User::where('id', $data->user_id)->first();
            $arr = [];
            $arr['email'] = $user->email;
            $arr['subject'] = 'Order Delivered';

            $data = ['user' => $user, 'order' => $data];
            Mail::send('email.orderDelivered', ['data' => $data], function (
                $message
            ) use ($arr) {
                $message
                    ->from('mail@dilamsys.com', 'Ktwis')
                    ->to($arr['email'])
                    ->subject($arr['subject']);
            });

            // send push notification to customer
            $FcmToken = $FcmToken = User::whereNotNull('fcm_token')
                ->where('id', $user->id)
                ->pluck('fcm_token')
                ->all();

            $title = 'Order Delivered';
            $body =
                "we have delivered your
        order " . date('D, j M');
            notification($title, $body, $FcmToken);
        }

        if ($request->checkbox == 'no') {
            $userEmail = $request->input('email');
            $subject = $request->status;
            $mailData = [
                'subject' => $subject,
            ];
            Mail::to($userEmail)->send(new SampleMail($mailData));
            return response()->json([
                'status' => true,
                'msg' => 'Email sent successfully',
            ]);
        }
        if ($data) {
            return response()->json(
                [
                    'status' => true,
                    'msg' => 'Order updated successfully.',
                ],
                200
            );
        } else {
            return response()->json(
                ['status' => false, 'msg' => 'Failed to submit dispute reply.'],
                500
            );
        }
    }

    public function order_status(Request $request)
    {
        $id = $request->input('id');
        $data = Order::find($id);
        $data->status = 'Canceled';
        $data->save();
        $user = User::where('id', $data->user_id)->first();
        $arr = [];
        $arr['email'] = $user->email;
        $arr['subject'] = 'Order Cancel';

        // making order history
        OrderHistory::create([
            'order_id' => $data->id,
            'order_summary_id' => $data->order_summary_id,
            'order_number' => $data->order_number,
            'status' => $data->status,
            'action_date' => date('Y-m-d'),
            'created_by' => auth()->user()->id,
        ]);

        // send mail to customer
        $data = ['user' => $user, 'order' => $data];
        Mail::send('email.orderCancel', ['data' => $data], function (
            $message
        ) use ($arr) {
            $message
                ->from('mail@dilamsys.com', 'Ktwis')
                ->to($arr['email'])
                ->subject($arr['subject']);
        });

        // send push notification to customer
        $FcmToken = $FcmToken = User::whereNotNull('fcm_token')
            ->where('id', $user->id)
            ->pluck('fcm_token')
            ->all();

        $order = Order::find($id);
        $title = 'Order Cancel';
        $body =
            "Your order id #$order->order_number, as per your request is cancelled on " .
            date('D, j M');
        notification($title, $body, $FcmToken);

        if ($data) {
            return response()->json([
                'status' => true,
                'msg' => 'order Status cancel Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function send_message(Request $request)
    {
        //   $user= Auth::user()->id;
        //   return $user;
        // return $request->all();

        $rules = [
            'subject' => 'required|max:36',
            'message' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
            exit();
        }

        $userEmail = $request->input('email');
        $message = $request->input('message');
        $subject = $request->input('subject');
        $file = $request->file('file');

        $mailData = [
            'message' => strip_tags($message),
            'subject' => $subject,
            'file' => $file,
        ];
        Mail::to($userEmail)->send(new SampleMail($mailData));

        return response()->json([
            'status' => true,
            'msg' => 'Email sent successfully',
        ]);
    }
    public function customer_invoice(Request $request, $id)
    {
        $order_id = $request->id;
        $order = Order::find($order_id);
        $order_item = OrderItem::where('order_id', $order->id)->get();

        $user = User::find($order->user_id) ?? null;

        $address =
            UserAddress::where('id', $order->shipping_address_id)->first() ??
            null;

        if ($address) {
            $address->state = State::where('id', $address->state)
                ->orWhere('state_name', $address->state)
                ->value('state_name');
            $address->country = Country::where('id', $address->country)
                ->orWhere('country_name', $address->country)
                ->value('country_name');
        }

        $orderSummary =
            OrderSummary::where('id', $order->order_summary_id)->first() ??
            null;
        $payment =
            Payment::where('order_summary_id', $orderSummary->id)->first() ??
            null;
        // return view(
        //     'pdf.payment_invoice',
        //     compact('order', 'order_item', 'user', 'address', 'payment')
        // );

        $dompdf = new Dompdf();
        $html = view(
            'pdf.payment_invoice',
            compact(
                'order',
                'order_item',
                'user',
                'address',
                'payment',
                'orderSummary'
            )
        )->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->stream('customer_invoice.pdf');
    }

    public function invoice(Request $request, $id)
    {
        $id = $request->id;
        $order = Order::with('payment')->find($id);
        $user = User::where('id', $order->user_id)->first();
        $shippingaddress = UserAddress::where('user_id', $user->id)->first();
        $shipping = UserAddress::where(
            'id',
            $order->shipping_address_id
        )->first();
        $orderitems = OrderItem::with('product')
            ->where('order_id', $order->id)
            ->get();
        $taxdata = Tax::where('id', $order->tax_id)->first();

        return view(
            'vendor.orders.invoicing',
            compact(
                'order',
                'user',
                'shipping',
                'shippingaddress',
                'orderitems',
                'taxdata'
            )
        );
    }

    public function full_fill(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'Fulfilled';
        $order->Save();
        // $id = $request->input('id');
        // $shipping = ShippingRate::where('created_by', $id)->first();
        // $shipping->carrier_id = $request->input('carrier');
        // $shipping->save();

        if ($request->email_send) {
            $user = User::where('id', $order->user_id)->first();
            $arr = [];
            $arr['email'] = $user->email;
            $arr['subject'] = 'Order FullFilled';

            $data = ['order' => $order, 'user' => $user];
            Mail::send('email.orderFullfill', ['data' => $data], function (
                $message
            ) use ($arr) {
                $message
                    ->from('mail@dilamsys.com', 'Ktwis')
                    ->to($arr['email'])
                    ->subject($arr['subject']);
            });
        }

        if ($order) {
            return response()->json(
                ['status' => true, 'msg' => 'Data save successfully.'],
                200
            );
        } else {
            return response()->json(
                ['status' => false, 'msg' => 'Failed to submit dispute reply.'],
                500
            );
        }
    }

    public function index_cancellation()
    {
        return view('vendor.cancellation.index');
    }

    public function cancellation_list(Request $request)
    {
        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }

        if (isset($request->length)) {
            $limit = $request->length;
        } else {
            $limit = 10;
        }

        if (isset($request->start)) {
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = CancelOrderRequest::join(
            'orders',
            'cancel_order_requests.order_id',
            '=',
            'orders.id'
        )
            ->join('users', 'orders.user_id', 'users.id')
            ->where('cancel_order_requests.status', 'NEW')
            ->where('orders.seller_id', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query
                    ->where('orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%');
            })
            ->get()
            ->groupBy('order_id')
            ->count();

        $cancel = CancelOrderRequest::join(
            'orders',
            'cancel_order_requests.order_id',
            '=',
            'orders.id'
        )
            ->join('users', 'orders.user_id', 'users.id')
            ->where('cancel_order_requests.status', 'NEW')
            ->where('orders.seller_id', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query
                    ->where('orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%');
            })
            ->select('cancel_order_requests.*')
            ->orderBy('cancel_order_requests.id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get()
            ->groupBy('order_id');

        // return $cancel;
        $data = [];
        foreach ($cancel as $key => $item) {
            $order = Order::where('id', $item[0]->order_id)->first();
            $customer = user::where('id', $order->user_id)->first();
            $action =
                '<button class="px-2 btn btn-secondary action" id="view_wishlist" data-id="' .
                $item[0]->order_id .
                '" data-name = "approve">APPROVE</button>
                <button class="px-2 btn btn-danger action" id="view_wishlist" data-id="' .
                $item[0]->order_id .
                '" data-name = "decline">DECLINE</button> ';

            $order_summary = OrderSummary::where(
                'id',
                $order->order_summary_id
            )->first();
            $payment = Payment::where(
                'order_summary_id',
                $order_summary->id
            )->first();
            $requested = count($item) . '/' . $order->item_count;
            $request_at =
                Carbon::parse($item[0]->created_at)->diffForHumans(null, true) .
                ' ago';
            $data[] = [
                $offset + $key,
                '#' . $order->order_number,
                $customer->name,
                $order->total_amount,
                $payment->status,
                $requested,
                $request_at,
                $item[0]->status,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function cancellation_approved(Request $request)
    {
        if ($request->status == 'approve') {
            $cancel = CancelOrderRequest::where(
                'order_id',
                $request->order_id
            )->update(['status' => 'Approved']);
            $order = Order::where('id', $request->order_id)->update([
                'status' => 'canceled',
            ]);

            $orders = Order::find($request->order_id);

            $user = User::where('id', $orders->user_id)->first();
            $arr = [];
            $arr['email'] = $user->email;
            $arr['subject'] = 'Order Cancel';

            $data = ['order' => $orders, 'user' => $user];

            Mail::send('email.orderCancel', ['data' => $data], function (
                $message
            ) use ($arr) {
                $message
                    ->from('mail@dilamsys.com', 'Ktwis')
                    ->to($arr['email'])
                    ->subject($arr['subject']);
            });
        } else {
            $cancel = CancelOrderRequest::where(
                'order_id',
                $request->order_id
            )->update(['status' => 'Declined']);
        }

        if ($cancel) {
            return response()->json(
                [
                    'status' => true,
                    'msg' => 'Request ' . $request->status . ' successfully.',
                ],
                200
            );
        }
        return response()->json(
            ['status' => false, 'msg' => 'Something Went Wrong'],
            200
        );
    }

    public function addOrder(Request $request)
    {
        if ($request->order_type == 'regular') {
            $customer = User::find($request->customer_id);
            $shippingAddress =
                UserAddress::where([
                    'user_id' => $request->customer_id,
                    'address_type' => 'shipping',
                ])->first() ?? null;
            $billingAddress =
                UserAddress::where([
                    'user_id' => $request->customer_id,
                    'address_type' => 'billing',
                ])->first() ?? null;

            if ($billingAddress) {
                $billingAddress->obj_state =
                    state::find($billingAddress->state)->state_name ??
                    $billingAddress->state;
                $billingAddress->obj_country =
                    Country::find($billingAddress->country)->country_name ??
                    $billingAddress->country;
            }

            if ($shippingAddress) {
                $shippingAddress->obj_state =
                    state::find($shippingAddress->state)->state_name ??
                    $shippingAddress->state;
                $shippingAddress->obj_country =
                    Country::find($shippingAddress->country)->country_name ??
                    $shippingAddress->country;
            }

            $countries = Country::all();
            $state = State::all();

            $products = Product::where('created_by', Auth::user()->id)
                ->where('has_variant', 0)
                ->whereHas('inventory', function ($query) {
                    $query->WhereNotNull('created_at');
                })
                ->get();

            return view(
                'vendor.orders.add_order',
                compact(
                    'customer',
                    'shippingAddress',
                    'billingAddress',
                    'countries',
                    'state',
                    'products'
                )
            );
        } else {
            $products = Product::where('created_by', Auth::user()->id)
                ->where('has_variant', 0)
                ->whereHas('inventory', function ($query) {
                    $query->WhereNotNull('created_at');
                })
                ->get();

            $customer = User::find($request->customer_id);

            $invoice = mt_rand(1000, 9999);
            return view(
                'vendor.orders.custom_order',
                compact('products', 'invoice', 'customer')
            );
        }
    }

    public function updateCustomerAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_type' => 'required',
            'address_line1' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'state' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first(),
            ]);
        }

        $address = UserAddress::find($request->address_id);
        if ($address) {
            $address->address_type = $request->address_type;
            $address->contact_no = $request->phone;
            $address->floor_apartment = $request->address_line1;
            $address->address = $request->address_line2;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->country_code = '+' . $request->country_code;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->save();
        } else {
            $address = new UserAddress();
            $address->user_id = $request->user_id;
            $address->contact_person = $request->contact_person;
            $address->address_type = $request->address_type;
            $address->contact_no = $request->phone;
            $address->floor_apartment = $request->address_line1;
            $address->address = $request->address_line2;
            $address->city = $request->city;
            $address->country_code = '+' . $request->country_code;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->save();
        }
        if ($address) {
            return response()->json([
                'status' => true,
                'msg' => 'Address Updated Successfully.',
            ]);
        }
        return response()->json([
            'status' => false,
            'msg' => 'Something Went Wrong',
        ]);
    }

    public function getProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first(),
            ]);
        }

        $product = Product::with('inventory')
            ->where('id', $request->id)
            ->first();
        $product->feature_img_url = asset(
            'public/vendor/featured_image/' . $product->featured_image
        );
        return response()->json([
            'status' => true,
            'msg' => 'Product Fetched Successfully.',
            'data' => $product,
        ]);
    }

    public function orderPlace(Request $request)
    {
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
                $product = Product::where('id', $request->product[$a])->first();
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

            Mail::to($user->email)->send(new OfflineOrder($data));

            if ($payment) {
                return response()->json([
                    'status' => true,
                    'msg' => 'Order placed successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Something Went Wrong',
                ]);
            }
        }
    }

    public function invoicePaymentVerify($pay, $user)
    {
        $pay = Payment::find($pay);
        $user = User::find($user);
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $pay->amount,
            'email' => $user->email,
            'tx_ref' => uniqid(),
            'currency' => 'NGN',
            'redirect_url' => route('callback', $pay),
            'customer' => [
                'email' => $user->email,
                'phone_number' => $user->mobile_number,
                'name' => $user->name,
            ],

            'customizations' => [
                'title' => 'Complete Order',
                'description' => date('d M Y'),
            ],
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return 'Something went wrong while generating payment link';
        }

        return redirect($payment['data']['link']);
    }

    public function callBack($id)
    {
        $status = request()->status;
        if ($status == 'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            $payment = Payment::find($id);
            $payment->status = 'success';
            $payment->transaction_id = $transactionID;
            $payment->payment_method = 'FlutterWave';
            $payment->updated_at = date('Y-m-d H:i:s');
            $payment->save();

            if ($payment) {
                $order_summary = OrderSummary::find($payment->order_summary_id);
                $order = order::where(
                    'order_summary_id',
                    $order_summary->id
                )->first();
                $link = Str::random(20);
            }
            return redirect(
                route('payment-invoice', ['id' => $order->id, 'link' => $link])
            );
        } else {
            return 'Something Went Wrong';
        }
    }

    // custom order invoice payment verify
    public function customInvoicePaymentVerify($pay)
    {
        $order = CustomOrder::find($pay);

        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $order->total_amount,
            'email' => $order->email,
            'tx_ref' => uniqid(),
            'currency' => 'NGN',
            'redirect_url' => route('customCallback', $pay),
            'customer' => [
                'email' => $order->email,
                'phone_number' => 987654334,
                'name' => 'dummy',
            ],

            'customizations' => [
                'title' => 'Complete Order',
                'description' => date('d M Y'),
            ],
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return 'Something went wrong while generating payment link';
        }

        return redirect($payment['data']['link']);
    }

    // custom order call back
    public function customCallback($id)
    {
        $status = request()->status;
        if ($status == 'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            $payment = CustomOrder::find($id);
            $payment->payment_status = 'paid';
            $payment->transaction_id = $transactionID;
            $payment->payment_method = 'FlutterWave';
            $payment->updated_at = date('Y-m-d H:i:s');
            $payment->save();

            if ($payment) {
                $link = Str::random(20);
            }
            return redirect(
                route('custom-payment-invoice', ['id' => $id, 'link' => $link])
            );
        } else {
            return 'Something Went Wrong';
        }
    }

    public function invoiceRedirection($order_id, $payment_link)
    {
        $order = Order::where('id', $order_id)->first();
        if ($order) {
            $order_items = OrderItem::where('order_id', $order->id)->get();
            $order_summary = OrderSummary::where(
                'id',
                $order->order_summary_id
            )->first();

            $payment = Payment::where(
                'order_summary_id',
                $order_summary->id
            )->first();
            $address = UserAddress::find($order_summary->billing_address_id);
            $address->obj_state =
                state::find($address->state)->state_name ?? $address->state;
            $address->obj_country =
                Country::find($address->country)->country_name ??
                $address->country;
            $user = User::find($order->user_id);
            return view(
                'pages.payment_invoice',
                compact('order', 'user', 'order_items', 'payment', 'address')
            );
        } else {
            abort('404');
        }
    }

    public function redirectCustomInvoice($order_id, $payment_link)
    {
        $order = CustomOrder::where('id', $order_id)->first();
        if ($order) {
            $order_items = CustomOrderItem::where(
                'custom_order_id',
                $order->id
            )->get();
            $vendor = User::find($order->created_by);
            $vendorAddress =
                VendorAddress::where(
                    'vendor_id',
                    $order->created_by
                )->first() ?? null;

            if ($vendorAddress) {
                $vendorAddress->obj_state = state::find(
                    $vendorAddress->state
                )->state_name;
                $vendorAddress->obj_country = Country::find(
                    $vendorAddress->country
                )->country_name;
            }

            return view(
                'pages.custom_payment_invoice',
                compact('order', 'order_items', 'vendor', 'vendorAddress')
            );
        } else {
            abort('404');
        }
    }

    public function downloadInvoice($id)
    {
        $order_id = $id;
        $order = Order::find($order_id);
        $order_item = OrderItem::where('order_id', $order->id)->get();

        $user = User::find($order->user_id) ?? null;

        $address =
            UserAddress::where('id', $order->shipping_address_id)->first() ??
            null;

        if ($address) {
            $address->state = State::where('id', $address->state)
                ->orWhere('state_name', $address->state)
                ->value('state_name');
            $address->country = Country::where('id', $address->country)
                ->orWhere('country_name', $address->country)
                ->value('country_name');
        }

        $orderSummary =
            OrderSummary::where('id', $order->order_summary_id)->first() ??
            null;
        $payment =
            Payment::where('order_summary_id', $orderSummary->id)->first() ??
            null;
        // return view(
        //     'pdf.payment_invoice',
        //     compact('order', 'order_item', 'user', 'address', 'payment')
        // );

        $dompdf = new Dompdf();
        $html = view(
            'pdf.payment_invoice',
            compact(
                'order',
                'order_item',
                'user',
                'address',
                'payment',
                'orderSummary'
            )
        )->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('payment_invoice.pdf', ['Attachment' => false]);
        dd('d');
    }

    // download Custom Invoice
    public function customDownloadInvoice($id)
    {
        // $order_id = $id;
        // $order = Order::find($order_id);
        // $order_item =
        //     OrderItem::where('order_id', $order->id)
        //         ->get();

        // $address =
        //     UserAddress::where(
        //         'id',
        //         $order->shipping_address_id
        //     )->first() ?? null;

        //     $orderSummary = OrderSummary::where('id',$order->order_summary_id)->first() ?? null;
        // $payment = Payment::where('order_summary_id',$orderSummary->id)->first() ?? null;
        // // return view(
        // //     'pdf.payment_invoice',
        // //     compact('order', 'order_item', 'user', 'address', 'payment')
        // // );

        // $dompdf = new Dompdf();
        // $html = view(
        //     'pdf.payment_invoice',
        //     compact('order', 'order_item', 'user', 'address', 'payment')
        // )->render();
        // $dompdf->loadHtml($html);
        // $dompdf->setPaper('A4', 'portrait');
        // $dompdf->render();

        //  return   $dompdf->stream('payment_invoice.pdf', ['Attachment' => false]);
    }

    // create custom order
    public function addCustomOrder(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'date' => 'required',
            'item_name' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
        }

        // return $request->all();

        $order = CustomOrder::create([
            'email' => $request->email,
            'date' => $request->date,
            'order_number' => rand(1, 999999),
            'seller_to_customer' => $request->seller_to_customer,
            'terms_condition' => $request->terms_condition,
            'reference' => $request->reference,
            'invoice_number' => $request->invoice_number,
            'sub_total' => $request->sub_total,
            'discount' => $request->discount,
            'shipping' => $request->shipping,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'created_by' => auth()->user()->id,
        ]);

        if ($request->hasFile('attachments')) {
            $image = $request->file('attachments');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/orders/attachments');
            $image->move($destinationPath, $image_name);

            CustomOrder::where('id', $order->id)->update([
                'attachments' => $image_name,
            ]);
        }

        $itemCount = count($request->item_name);
        for ($a = 0; $a < $itemCount; $a++) {
            $orderItem = new CustomOrderItem();
            $orderItem->custom_order_id = $order->id;
            $orderItem->item_name = $request->item_name[$a];
            $orderItem->quantity = $request->quantity[$a];
            $orderItem->price = $request->price[$a];
            $orderItem->amount = $request->price[$a];
            $orderItem->description = $request->description[$a];
            $orderItem->save();
        }

        //  Send mail to customer
        $orderItems = CustomOrderItem::where(
            'custom_order_id',
            $order->id
        )->get();
        $data = [
            'order' => $order,
            'order_items' => $orderItems,
            'code' => Str::random(20),
            'vendor_name' => User::find($order->created_by)->name,
        ];

        CustomOrder::where('id', $order->id)->update([
            'payment_url' =>
                url('/') .
                '/payment-custom-invoice/' .
                $order->id .
                '/' .
                Str::random(20),
        ]);

        $arr = [];
        $arr['email'] = $request->email;
        $arr['subject'] = 'Complete Your Order';

        Mail::send('email.customOrder', ['data' => $data], function (
            $message
        ) use ($arr) {
            $message
                ->from('mail@dilamsys.com', 'Ktwis')
                ->to($arr['email'])
                ->subject($arr['subject']);
        });

        return response()->json([
            'status' => true,
            'message' => 'Order Invoice Sent Successfully',
        ]);
    }

    public function customOrders()
    {
        $orders = CustomOrder::where('created_by', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                $item->orderItem = CustomOrderItem::where(
                    'custom_order_id',
                    $item->id
                )->get();
                return $item;
            });

        // get those customers id's who buys your product
        $getUserId = Order::where('seller_id', Auth::user()->id)
            ->pluck('user_id')
            ->toArray();

        $customers = User::where('created_by', Auth::user()->id)
            ->orWhereIn('id', $getUserId)
            ->with([
                'roles' => function ($query) {
                    $query->where('role', 'user');
                },
            ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        return view(
            'vendor.orders.custom_index',
            compact('orders', 'customers')
        );
    }


    public function postAllCustomOrder(Request $request){

        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }

        if (isset($request->length)) {
            $limit = $request->length;
        } else {
            $limit = 10;
        }

        if (isset($request->start)) {
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $payment_status = $request->input('payment_status');

        $total = CustomOrder::where('created_by', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query
                    ->where('email', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%')
                    ->orWhere(
                        'payment_status',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'total_amount',
                        'like',
                        '%' . $search . '%'
                    );
            })
            ->where(function ($query) use ($from_date , $to_date  , $payment_status) {
                if ($from_date != null) {
                    $query->where('date', '>=', $from_date);
                }
    
                if ($to_date != null) {
                    $query->where('date', '<=', $to_date);
                }
    
                if($payment_status){
                    $query->where('payment_status', $payment_status);
                }

            })
    
            ->count();

        $orders = CustomOrder::where('created_by', Auth::user()->id)
        ->where(function ($query) use ($search) {
            $query
                ->where('email', 'like', '%' . $search . '%')
                ->orWhere('order_number', 'like', '%' . $search . '%')
                ->orWhere(
                    'payment_status',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'total_amount',
                    'like',
                    '%' . $search . '%'
                );
        })
        ->where(function ($query) use ($from_date , $to_date  , $payment_status) {
            if ($from_date != null) {
                $query->where('date', '>=', $from_date);
            }

            if ($to_date != null) {
                $query->where('date', '<=', $to_date);
            }

            if($payment_status){
                $query->where('payment_status', $payment_status);
            }

        })
            ->orderBy('id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();

        // return $cancel;
        $data = [];
        foreach ($orders as $key => $order) {

            $url = '<span id="url">'. $order->payment_url .'</span>';
            $data[] = [
                $offset + $key + 1,
                '#' . $order->order_number,
                $order->created_at->format('D, M j, Y g:i A'),
                $order->email,
                $order->total_amount,
              $order->payment_status ,
                $url,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);

    }
}
