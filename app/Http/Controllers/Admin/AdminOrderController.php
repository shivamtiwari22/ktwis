<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SampleMail;
use App\Models\CancelOrderRequest;
use App\Models\Carrier;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Vendor;
use App\Models\Wishlist;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminOrderController extends Controller
{
    public function index_order()
    {
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

        return view('admin.orderadmin.order.index',compact('statuses'));
    }

    public function list_order(Request $request)
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

        

        $total=Order::join('users','orders.user_id','=','users.id')
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
                ->orWhere('users.name','like', '%' . $search . '%')
                ->orWhere('orders.order_number', 'like', '%' . $search . '%')
                ->orWhere('orders.total_amount', 'like', '%' . $search . '%')
                ->orWhere('orders.status', 'like', '%' . $search . '%');
          
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

        $orders =
          Order::join('users','orders.user_id','=','users.id')
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
                ->orWhere('users.name','like', '%' . $search . '%')
                ->orWhere('orders.order_number', 'like', '%' . $search . '%')
                ->orWhere('orders.total_amount', 'like', '%' . $search . '%')
                ->orWhere('orders.status', 'like', '%' . $search . '%');
          
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
        ->select('orders.*')->
          orderBy('id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();

            $data = [];
        foreach ($orders as $key => $order) {
            // $action =
                // '<button class="px-2 btn btn-primary view_wishlist" id="view_wishlist" data-id="' . $order->id . '" data-name="' . $order->id . '"><i class="dripicons-preview"></i></button>';
                $action = '<a href="' . route('admin.order.show_product_detail_admin', $order->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>';

                    $user=User::where('id',$order->user_id)->first();
                    $date=$order->created_at->format('D, M j, Y g:i A') ;
                    $order_summary = OrderSummary::where('id',$order->order_summary_id)->first();
                      $payment=Payment::where('order_summary_id',$order_summary->id)->first();
               $order_number="#".$order->order_number;
            $data[] = [
                $offset + $key + 1,
                $order_number,
                $date,
                $user->name,
                $order->total_amount,
                $payment->status == "success" ? "Paid" : "Unpaid",
                $order->status == "initiate_refund" ?   "Initiate Refund" : ucwords($order->status),
                $action,
            ];
        }
        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
     
    public function show_product_detail_admin(Request $request, $id)
    {
        $id = $request->id;
        $order = Order::find($id);
        $order_summary = OrderSummary::where('id', $order->order_summary_id)->first();
        $order->payment = Payment::where('order_summary_id', $order_summary->id)->first();
        $order->orderItem = OrderItem::where('order_id', $order->id)->get();

        foreach($order->orderItem as $item){
                $item->product = Product::withTrashed()->where('id', $item->product_id)->first();
        }
       
        $order->customer_detail = User::where('id', $order->user_id)->first();
        $user = User::where('id', $order->user_id)->first();
        $orders = Order::where('user_id', $user->id)->first();

        $order->billingAddress= UserAddress::withTrashed()->where('id', $order_summary->billing_address_id)->first();

        if($order->billingAddress){
            $order->billingAddress->state = State::where('id',$order->billingAddress->state)->orWhere('state_name', $order->billingAddress->state)->value('state_name');
            $order->billingAddress->country = Country::where('id',$order->billingAddress->country)->orWhere('country_name', $order->billingAddress->country)->value('country_name');
        }

        $order->shippingaddress = UserAddress::withTrashed()->where('id', $order_summary->shipping_address_id)->first();

        if($order->shippingaddress){
            $order->shippingaddress->state = State::where('id',$order->shippingaddress->state)->orWhere('state_name', $order->shippingaddress->state)->value('state_name');
            $order->shippingaddress->country = Country::where('id',$order->shippingaddress->country)->orWhere('country_name', $order->shippingaddress->country)->value('country_name');
        }

        $Carriers = Carrier::all();
        return view('admin.orderadmin.order.productdata', compact('order','Carriers'));



    }
   public function payment_status(Request $request)

   {
            $id=$request->id;
            $payment=Payment::find($id);
            if ($payment->status === "unpaid") {
                $payment->status = "paid";
            } else {
                $payment->status = "unpaid";
            }
             $payment->save();
             if ($payment) {
                return response()->json(array('status' => true, 'msg' => 'Payment status update successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
        public function update_status(Request $request)
        {
    
            $data = Payment::find($request->id);
            $data->status = $request->status;
            $data->save();
    
            if($request->checkbox == "no"){
            $userEmail = $request->input('email');
            $subject = $request->status;
            $mailData = [
                'subject' => $subject,
            ];
            Mail::to($userEmail)->send(new SampleMail($mailData));
            return response()->json(['status' => true, 'msg' => "Email sent successfully"]);
            exit;
        }
        if ($data) {
            return response()->json(['status' => true, 'msg' => 'Payment status updated successfully.'], 200);
        } else {
            return response()->json(['status' => false, 'msg' => 'Failed to submit dispute reply.'], 500);
        }
        
     
     
        
        }
    public function view_order($id)
    {
        $user = User::where('id', $id)->with('order.orderItems.product.inventory', 'order.orderItems.variant')->get();
        return response()->json($user);
    }


    ///wishlist
    
    public function index_wishlist()
    {
        return view('admin.orderadmin.wishlist.index');
    }

    public function list_wishlist(Request $request)
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

        $total = User::with(['wishlists' => function ($query) {
            $query->with('product');
        }])
        ->whereHas('wishlists', function ($query) use ($search) {
            $query->whereNull('deleted_at');
        })
        ->whereIn('id', function ($query) {
            $query->select('created_by')
                ->from('wishlists');
        })
        ->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->count();

        $users = User::with(['wishlists' => function ($query) {
            $query->with('product');
            $query->whereNull('deleted_at');
        }])
        ->whereHas('wishlists', function ($query) use ($search) {
            $query->whereNull('deleted_at');
        })
      
            ->whereIn('id', function ($query) {
                $query->select('created_by')
                    ->from('wishlists');
            })
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();


        $data = [];
        foreach ($users as $key => $user) {
            $action =
                '<button class="px-2 btn btn-primary view_wishlist" id="view_wishlist" data-id="' . $user->id . '" data-name="' . $user->id . '"><i class="dripicons-preview"></i></button>';
            $name = $user->name;

            $wishlisted_on = Wishlist::where('created_by', $user->id)->orderByDesc('created_at')->first();
            $last_on = null;
            if ($wishlisted_on) {
                $last_on = $wishlisted_on->created_at;
                $dateTime = new \DateTime($last_on);
                $date_time = $dateTime->format('M d, Y');
            }
            else{
                $date_time = null;
            }

            $vendor = null;
            // if ($wishlisted_on) {
            //     $last_on = $wishlisted_on->product_id;
            //     $product = Product::find($last_on);
            //     $product_vendor_id = $product->created_by;
            //     $vendor = User::find($product_vendor_id);
            //     $vendor_name = $vendor->name;
            // }

            $quantity = Wishlist::where('created_by', $user->id)->count();

            $data[] = [
                $offset + $key + 1,
                $name,
                $date_time,
                $quantity,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function view_wishlist($id)
    {
        // $user = User::where('id', $id)->with('wishlists.product.inventory', 'wishlists.variant')->get();
        $user = User::where('id', $id)->with('wishlists.product.inventory', 'wishlists.variant','wishlists.product.inventoryVariants.variants')->get();
    //    return $user;
        return response()->json($user);
    }


    public function index_cart()
    {
        return view('admin.orderadmin.carts.index');
    }

    public function list_cart(Request $request)
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

        $total = Cart::join('users','carts.user_id','=','users.id')->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%');
            })->count();


        $users = Cart::
            join('users','carts.user_id','=','users.id') ->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%');
            })->select('carts.*')
            ->orderBy('carts.id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();


        $data = [];
        foreach ($users as $key => $user) {

            $customer = user::where('id',$user->user_id)->first();
            $action =
                '<button class="px-2 btn btn-primary view_wishlist" id="view_wishlist" data-id="' . $user->id . '" data-name="' . $user->id . '"><i class="dripicons-preview"></i></button>';
            $name = $customer->name;

            $last_on = null;

            if ($user->created_at) {
                $last_on = $user->created_at;
                $dateTime = new \DateTime($last_on);
                $date_time_now = $dateTime->format('M j, Y');
            } else {
                $date_time_now = "";
            }

          
            $quantity = CartItem::where('cart_id',$user->id)->get()->sum('quantity');

            $data[] = [
                $offset + $key + 1,
                $name,
                $date_time_now,
                $user->item_count,
                $quantity,
                $user->total_amount,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function view_carts($id)
    {
        $cart = Cart::find($id);
        $cart->cart_details = CartItem::where('cart_id',$cart->id)->get();

        foreach( $cart->cart_details as $detail){
             $detail->product = Product::where('id',$detail->product_id)->first();
        } 
        $cart->customer = User::where('id',$cart->user_id)->first();
        $cart->customer->member_since = date("M d,Y", strtotime($cart->customer->created_at));
        return response()->json($cart);
    }

    public function index_cancellation(){
        return view('admin.orderadmin.cancellation.index');
    }

    public function cancellation_list(Request $request){

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

        $total = CancelOrderRequest::join('orders','cancel_order_requests.order_id','=','orders.id')
        ->join('users','orders.user_id','users.id')
        ->join('shops','orders.seller_id','shops.vendor_id')
        ->where('cancel_order_requests.status','NEW')
        ->where(function ($query) use ($search) {
                $query->where('orders.order_number', 'like', '%' . $search . '%')
                        ->orWhere('users.name','like', '%' . $search . '%')
                        ->orWhere('shops.shop_name','like', '%' . $search . '%');

            })->get()->groupBy('order_id')->count();

        $cancel =CancelOrderRequest::join('orders','cancel_order_requests.order_id','=','orders.id')
        ->join('users','orders.user_id','users.id')
        ->join('shops','orders.seller_id','shops.vendor_id')
        ->where('cancel_order_requests.status','NEW')
        ->where(function ($query) use ($search) {
                $query->where('orders.order_number', 'like', '%' . $search . '%')
                        ->orWhere('users.name','like', '%' . $search . '%')
                        ->orWhere('shops.shop_name','like', '%' . $search . '%');

            })->select('cancel_order_requests.*')
            ->orderBy('cancel_order_requests.id', $orderRecord)
            ->get()->groupBy('order_id')  ->skip($offset)  ->take($limit);

            // return $cancel;
        $data = [];
        $count = 0 ;
        foreach ($cancel as $key => $item) {
             $count ++ ;
            $order = Order::where('id',$item[0]->order_id)->first();
            $customer = user::where('id',$order->user_id)->first();
            $shop = Shop::where('vendor_id',$order->seller_id)->first() ?? null;
             $action =
                '<button class="px-2 btn btn-primary approve" id="view_wishlist" data-id="' .$item[0]->order_id. '" >APPROVED</button>';
        
            $order_summary = OrderSummary::where('id',$order->order_summary_id)->first();
            $payment = Payment::where('order_summary_id',$order_summary->id)->first();

           $requested = count($item) .'/'. $order->item_count;

           $request_at  = Carbon::parse($item[0]->created_at)->diffForHumans(null, true) . " ago";

            $data[] = [
                $offset + $count ,
                '#'.$order->order_number,
                $shop->shop_name ?? null,
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

    public function cancellation_approved(Request $request){
         $cancel = CancelOrderRequest::where('order_id',$request->order_id)->update(['status' => 'Approved']);
         $order = Order::where('id',$request->order_id)->update(['status'=> "canceled"]);
         if($order){
            return response()->json(['status' => true, 'msg' => 'Cancel order approved successfully.'], 200);
         }
         return response()->json(['status' => false, 'msg' => 'Something Went Wrong'], 200);
    }


    public function send_message(Request $request)
    {
        //   $user= Auth::user()->id;
        //   return $user;
     
        $userEmail = $request->input('email');
        $message = $request->input('message');
        $subject = $request->input('subject');
        $file = $request->file('file');

        $mailData = [
            'message' => $message,
            'subject' => $subject,
            'file' => $file,
        ];
        Mail::to($userEmail)->send(new SampleMail($mailData));
        return response()->json([
            'status' => true,
            'msg' => 'Email sent successfully',
        ]);
      
    }

    public function customer_invoice($id){
        $order_id = $id;
        $order = Order::find($order_id);
        $order_item =
            OrderItem::where('order_id', $order->id)
                ->get();

        $user = User::find($order->user_id) ?? null;

        $address =
            UserAddress::where(
                'id',
                $order->shipping_address_id
            )->first() ?? null;

            $orderSummary = OrderSummary::where('id',$order->order_summary_id)->first() ?? null;
        $payment = Payment::where('order_summary_id',$orderSummary->id)->first() ?? null;
        // return view(
        //     'pdf.payment_invoice',
        //     compact('order', 'order_item', 'user', 'address', 'payment')
        // );
        
        $dompdf = new Dompdf();
        $html = view(
            'pdf.payment_invoice',
            compact('order', 'order_item', 'user', 'address', 'payment','orderSummary')
        )->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
      return    $dompdf->stream('customer_invoice.pdf');
    }
}
