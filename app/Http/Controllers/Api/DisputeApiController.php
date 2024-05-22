<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\disputeText;
use App\Models\InventoryWithoutVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Variant;
use Carbon\Carbon;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;
use Termwind\Components\Raw;

class DisputeApiController extends Controller
{
    public function openDispute(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer',
                'reason' => 'required',
                'good_received' => 'required|boolean',
                'refund_amount' => 'required',
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

            $order = Order::find($request->order_id);
            $orderItem = OrderItem::where('order_id',$request->order_id)->where('product_id',$request->product_id)->first();
    
                $dispute = new Dispute();
                $dispute->customer_id = auth('api')->user()->id;
                $dispute->vendor_id = $order->seller_id;
                $dispute->order_id = $request->order_id;
                $dispute->order_item_id =$orderItem->id ?? null;
                $dispute->type = $request->reason;
                if ($request->refund_amount) {
                    $dispute->refund_requested = 1;
                }
                $dispute->status = 'open';
                $dispute->refund_amount = $request->refund_amount;
                $dispute->good_received = $request->good_received;
                $dispute->p_id = $request->product_id;
                $dispute->variant_id =  $orderItem->variant_id ?? null;
                $dispute->description = $request->description;
                $dispute->save();

                // send notification to vendor for open dispute 
                $FcmToken =  $FcmToken = User::whereNotNull('fcm_token')
                ->where('id', $order->seller_id)
                ->pluck('fcm_token')
                ->all();
                $title = "Dispute Notification";
                $body = "Dispute Notification: A customer has opened a dispute. Please review the details on your dashboard and provide any necessary information promptly";
                notification($title,$body,$FcmToken);
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $dispute],
                    'timestamp' => Carbon::now(),
                    'message' => 'Dispute created successfully',
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

    public function customerDisputeDetails($id)
    {
        $dispute =
            Dispute::where([
                'id' => $id,
            ])->first([
                'id',
                'refund_amount',
                'good_received',
                'type',
                'order_id',
                'description',
                'vendor_id',
                'status',
            ]) ?? null;

        if ($dispute) {
            $dispute->order_id = Order::where('id', $dispute->order_id)->first()->order_number ?? null;  
            $dispute->shop_name =
                Shop::where('vendor_id', $dispute->vendor_id)->first()
                    ->shop_name ?? null;


           if($dispute->good_received == 1  && $dispute->refund_amount > 0){
                   $dispute->return_goods = "Yes";
           }
           else{
            $dispute->return_goods = "No";
           }

           $dispute_messages = DisputeMessage::where('dispute_id',$id)->get(['message','attachment','response_by_id'])->map(function($item){

                   $item->response_by = Auth::user()->id == $item->response_by_id ? 'Me' :  "Vendor's Reply";
                   $item->file = asset('public/customer/dispute/' . $item->attachment);

                   return $item;
           });

           $dispute->dispute_messages = $dispute_messages ;
        }

        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $dispute],
            'timestamp' => Carbon::now(),
            'message' => 'Dispute get successfully',
        ]);
    }


    public function disputeResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dispute_id' => 'required|integer',
            'reply' => 'required',
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
  
        if($request->mark_as_resolved == "Yes"){
            $dispute =Dispute::find($request->dispute_id);
            if($dispute){
                $dispute->status = 'solved';
                $dispute->save();
            }
        }
        
        $dispute_response = new DisputeMessage();
        $dispute_response->dispute_id = $request->dispute_id;
        $dispute_response->message = $request->reply;
        if ($request->attachment) {
            $image = $request->file('attachment');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('customer/dispute');
            $image->move($destinationPath, $image_name);
            $dispute_response->attachment = $image_name;
           
        }
        $dispute_response->response_by_id = Auth::user()->id;
        $dispute_response->save();
          
        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $dispute_response],
            'timestamp' => Carbon::now(),
            'message' => 'Response sent successfully',
        ]);
    }


    public function disputeResolved($id){
        // $validator = Validator::make($request->all(), [
        //     'dispute_id' => 'required|integer',
          
        // ]);
        // if ($validator->fails()) {
        //     return response()->json(
        //         [
        //             'http_status_code' => 422,
        //             'status' => false,
        //             'context' => ['error' => $validator->errors()->first()],
        //             'timestamp' => Carbon::now(),
        //             'message' => 'Validation failed',
        //         ],
        //         422
        //     );
        // }

            $dispute =Dispute::find($id);
            if($dispute){
                $dispute->status = 'solved';
                $dispute->save();
            }
            else{
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => "Something went wrong"],
                        'timestamp' => Carbon::now(),
                        'message' => 'Something went wrong',
                    ],
                    500
                );
            }
        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $dispute],
            'timestamp' => Carbon::now(),
            'message' => 'Response sent successfully',
        ]);
    }


    public function get_my_disputes(){
        
        try {
            $user = auth('api')->user()->id;
            $dispute = Dispute::where('customer_id', $user)->orderBy('id','DESC')->get(['id','order_id','type','status','refund_amount','good_received','description','order_item_id','p_id']);

            foreach($dispute as $item){
                $item->order_detail = Order::where('id',$item->order_id)->first(['id',
            'user_id',
            'order_number',
            'status',
            'created_at',
            'order_summary_id',
            'seller_id',
            'total_amount'
            ]);

            $shop_name = Shop::where(
                'vendor_id',
                $item->order_detail->seller_id
            )->first();
            if ($shop_name) {
                $item->order_detail->shop_name = $shop_name->shop_name;
            }
            $item->order_detail->order_date = date("D,d.m.y,g:i a",strtotime($item->order_detail->created_at)); 
            $order_summary = OrderSummary::where('id',$item->order_detail->order_summary_id)->first() ?? null;
            $payment = Payment::where('order_summary_id', $order_summary->id)->first();
            if ($payment) {
                $item->order_detail->order_status = $payment->status;
            } else {
                $item->order_detail->order_status = 'Failed';
            }
     
           if($item->order_item_id){
            $item->order_detail->orderItem = OrderItem::where(
                'order_id',
                $item->order_detail->id
            )->where('id',$item->order_item_id)->get();
           }
           else{
            $item->order_detail->orderItem = OrderItem::where(
                'order_id',
                $item->order_detail->id
            )->get(); }

            foreach( $item->order_detail->orderItem as $order){
                $product = Product::where(
                    'id',
                    $order->product_id
                )->first();
                $order->product_description = $product->description;
                $order->featured_image_url = asset(
                    'public/vendor/featured_image/' .
                        $product->featured_image
                );
            }  

            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $dispute,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Dispute Fetched Successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([ 'http_status_code' => 500 ,'status' => false, 'context' =>  ['error' => $e->getMessage()] ,  'timestamp'=> Carbon::now() , 'message' => "An unexpected error occurred" ],500);
        }
    }


    public function get_dispute_products($id){
        try{
            $orderItem = OrderItem::where('order_id',$id)->get(['id','product_id','price','quantity']);
            foreach($orderItem as $item){
                   $product = Product::where('id',$item->product_id)->first();
                   $item->product_name = $product->name;
                   $item->product_description = $product->description;
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $orderItem,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetched Successfully!',
            ]);

        }
        catch (\Exception $e) {
            return response()->json([ 'http_status_code' => 500 ,'status' => false, 'context' =>  ['error' => $e->getMessage()] ,  'timestamp'=> Carbon::now() , 'message' => "An unexpected error occurred" ],500);
        }
    }

    public function get_dispute_text(){
        try{
            $dispute = disputeText::first();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $dispute,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetched Successfully!',
            ]);

        }
        catch(\Exception $e){
            return response()->json([ 'http_status_code' => 500 ,'status' => false, 'context' =>  ['error' => $e->getMessage()] ,  'timestamp'=> Carbon::now() , 'message' => "An unexpected error occurred" ],500);
        }
    }
}
