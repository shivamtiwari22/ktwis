<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Kutia\Larafirebase\Facades\Larafirebase as FacadesLarafirebase;
use Kutia\Larafirebase\Services\Larafirebase;

class NotificationApiController extends Controller
{
   
        
        public function notification(Request $request)
        {
            try {
             
                $title = "Hello";
                $body = "Description";

            
                $url = 'https://fcm.googleapis.com/fcm/send';

                  $FcmToken = User::whereNotNull('fcm_token')
                  ->where('id',Auth::user()->id)
                ->pluck('fcm_token')
                ->all();
            $serverKey =  env('FIREBASE_SERVER_KEY');
            $data = [
                'registration_ids' => $FcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ];
            $encodedData = json_encode($data);

            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // Execute post
            $result = curl_exec($ch);
            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }
            // Close connection
            curl_close($ch);
         
        
                $response = json_decode($result, true);

                if (isset($response['failure']) && $response['failure'] > 0) {
                    $failedTokens = [];
                    foreach ($response['results'] as $index => $result) {
                        if (isset($result['error'])) {
                            $error = $result['error'];
                            if ($error === 'InvalidRegistration' || $error === 'NotRegistered') {

                            }
                            $failedTokens[] = $FcmToken[$index];
                        }
                    }
                }
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $response],
                    'timestamp' => Carbon::now(),
                    'message' => 'Notification created successfully',
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



    public function allNotification(){

        try {
            $notification = auth()->user()->notifications;
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $notification],
                'timestamp' => Carbon::now(),
                'message' => 'Notification fetched successfully',
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

    public function markAsRead($id){
        try {
        if($id){
          auth()->user()->unreadNotifications->where('id',$id)->markAsRead();
        }
        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => []],
            'timestamp' => Carbon::now(),
            'message' => 'Notification Mark successfully',
        ]);

    }catch (\Exception $e) {
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
  
     public function allMarkAsRead(){
        try {
            // auth()->user()->unreadNotifications->markAsRead();
            auth()->user()->notifications->each(function ($notification) {
                $notification->delete();
            });
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'All Notification Cleared',
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
