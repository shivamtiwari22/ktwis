<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use App\Models\Order;
use App\Models\OrderSummary;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PDO;

class CustomerController extends Controller
{
    
    public function creditPassLogin(Request $request)
    {
        $headers = [
            'Accept' => 'application/json',
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU',
        ];
        $data = [
            'user_id' => $request->email,
            'password' => $request->password,
        ];
        $url = app('api_url');

        // Make the API request to get the wallet balance
        $response = Http::withHeaders($headers)->post($url .'login', $data);

        if ($response->successful()) {
            // Handle successful response, e.g., store data in the wallet app's database or display to the user.
            return $response->json();
            // $phpArray = json_decode($balance, true);
        } else {
            // Handle errors, e.g., log the error, show a message to the user, or take appropriate action.
            $errorMessage = $response->json();
            return $errorMessage;
        }
    }

    public function creditVerifyCustomer(Request $request)
    {
        $apiToken = $request->token;
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken,
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU'
        ];
        $data = [
            'one_time_password' =>  $request->one_time_password,
        ];
        $url = app('api_url');

        $response = Http::withHeaders($headers)->post($url .'verify-otp' , $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            $errorMessage = $response->json();
            return $errorMessage;
        }
    }


    public function customerWallet(Request $request){

        $apiToken = $request->token;
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken,
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU'
        ];
      
        $url = app('api_url');
        $response = Http::withHeaders($headers)->get($url .'wallet');
        if ($response->successful()) {
              return response()->json(['status' => true , 'data' => $response->json(), 'token' => $request->token]);
            return $response->json();
        } else {
            $errorMessage = $response->json();
            return $errorMessage;
        }
    }

    public function customerPayment(Request $request){

        // return $request->all();
        $apiToken = $request->token;
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken,
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU'
        ];
        $data = [
            'amount' =>  $request->amount,
        ];
        $url = app('api_url');

        $response = Http::withHeaders($headers)->post($url .'invoice-payment' , $data);
      
        if ($response->successful()) {
            $order = Order::find($request->order_id);
            $order_summary = OrderSummary::find($order->order_summary_id);

            $payment = Payment::where('order_summary_id',$order_summary->id)->first();
            $payment->status = "success";
            $payment->transaction_id = Str::random(10);
            $payment->payment_method = "Wallet";
            $payment->updated_at = date('Y-m-d H:i:s');
            $payment->save();

            $link =  Str::random(20);
            return  response()->json(['status' => true , 'data' => $response->json(), 'location' => route('payment-invoice',['id'=>$request->order_id , 'link'=> $link]) , 'message' => 'Payment Done Successfully' ]);
        } else {
            $errorMessage = $response->json();
            return $errorMessage;
        }
    }


    public function customCustomerPayment(Request $request){

        $apiToken = $request->token;
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiToken,
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU'
        ];
        $data = [
            'amount' =>  $request->amount,
        ];
        $url = app('api_url');

        $response = Http::withHeaders($headers)->post($url .'invoice-payment' , $data);
      
        if ($response->successful()) {
            $payment =  CustomOrder::find($request->order_id);
            $payment->payment_status = "paid";
            $payment->transaction_id = Str::random(10);
            $payment->payment_method = "Wallet";
            $payment->updated_at = date('Y-m-d H:i:s');
            $payment->save();

            $link =  Str::random(20);
            return  response()->json(['status' => true , 'data' => $response->json(), 'location' => route('custom-payment-invoice',['id'=>$request->order_id , 'link'=> $link]) , 'message' => 'Payment Done Successfully']);
        } else {
            $errorMessage = $response->json();
            return $errorMessage;
        }
    }
}
