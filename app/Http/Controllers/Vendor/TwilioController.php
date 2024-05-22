<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client as RestClient;

class TwilioController extends Controller
{
    public function get_send_sms()
    {
        return view('vendor.sms.index');
    }

    public function send_sms(Request $request)
    {
        $rules = [
            'phone' => 'required|min:13',
            'message' => 'required|max:256',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->getMessageBag()->toArray(),
                'data' => [],
            ]);
        }

        $reciever_number = $request->phone;
        $message = $request->message;
        // $reciever_number = "+917838385874";
        // $message = "Hi from Green Spark";

        try {
            // $account_sid = getenv('TWILIO_SID');
            // $auth_token = getenv('TWILIO_TOKEN');
            // $twilio_number = getenv('TWILIO_FROM');

            // $client = new RestClient($account_sid, $auth_token);
            // $sent = $client->messages->create($reciever_number, [
            //     'from' => $twilio_number,
            //     'body' => $message,
            // ]);

           $sent   =    sms($reciever_number, $message);
            if ($sent) {
                return response()->json([
                    'status' => true,
                    'msg' => 'sms sent successfully',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'type' => 'exception',
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
