<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SocialApiController extends Controller
{
    public function social_media_email(Request $request) {
      
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
            $data = User::where('email', $request->email)->first();
            if ($data) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $data],
                    'timestamp' => Carbon::now(),
                    'message' =>  'User data matches with the database!',
                ]);
            
            } else {
                    return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'An unexpected error occurred'],
                        'timestamp' => Carbon::now(),
                        'message' => 'user does not match.',
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
            );        }   
    }

}
