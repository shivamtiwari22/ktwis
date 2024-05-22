<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerInvoiceApiController extends Controller
{
    public function customer_invoice(Request $request)
    {
        try {
            $user = Auth::user();
            $order = Order::with(
                'payment',
                'user',
                'user.address',
                'orderItems',
                'orderItems.product',
                'taxes'
            )->find($request->id);
            $address = UserAddress::where(
                'id',
                $order->shipping_address_id
            )->first();
            $data = [
                'customer' => $user->name,
                'customer_address' => $address,
                'orderdata' => $order,
            ];

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$data]],
                'timestamp' => Carbon::now(),
                'message' => 'Customer all data show Successfully',
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

    public function contact_to_seller(Request $request)
    {
        try {
            $rules = [
                'message' => 'required',
                'order_id' => 'required|integer',
                'photo' => 'mimes:jpeg,jpg,png|required|max:20000',
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
            $user = Auth::user();
            $order = Order::find($request->order_id);
            $message = new Message();
            $message->received_by = $order->seller_id;
            $message->message = $request->message;
            $message->created_by = $user->id;
            $message->order_id  = $order->order_number;
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/file');
                $image->move($destinationPath, $image_name);
                $message->file = $image_name;
            }
            $message->save();


        //    send notification to customer 
            $FcmToken =  $FcmToken = User::whereNotNull('fcm_token')
            ->where('id', $order->seller_id)
            ->pluck('fcm_token')
            ->all();

         $title = "Message";
         $body = "You have received a message from customer,please check your dashboard";
         notification($title,$body,$FcmToken);

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Message sent successfully',
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
