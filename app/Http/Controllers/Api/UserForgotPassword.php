<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserResetPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Str;

class UserForgotPassword extends Controller
{
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['http_status_code' => '422' ,'status' => false, 'context' =>  ['error' => $validator->errors()->first()] ,  'timestamp'=> Carbon::now() , 'message' => 'Validation failed'], 422);
        }
        try {
         
                $user = User::where(function ($query) use ($request) {
                    $query
                        ->where('email', $request->user_id)
                        ->orWhere('mobile_number', $request->user_id);
                })
                    ->first();


            if ($user) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/reset-password?token=' . $token;

                // $data['url'] = $url;
                // $data['title'] = "Password Reset";
                // $data['body'] = "Please click on below link to reset your password.";
                $otp = strval(random_int(1000, 9999));
                $data['otp'] = $otp;
                $data['email'] = $user->email;
                $data['title'] = "OTP Verification";
                $data['body'] = "Your OTP is: " . $otp;

                Mail::send('email.forgotPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->from('mail@dilamsys.com', "Ktwis")->to($data['email'])->subject($data['body']);
                });
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                $user->otp = $otp;
                $user->save();
               
                return response()->json([ 'http_status_code' => 200 ,'status' => true, 'context' =>  ['data' => []] ,  'timestamp'=> Carbon::now() , 'message' => "Email Sent Successfully"]);
            } else {
            return response()->json([ 'http_status_code' => 404 ,'status' => false, 'context' =>  ['error' => 'User Not Found'] ,  'timestamp'=> Carbon::now() , 'message' => "User Not Found" ],404);
            }
        } catch (\Exception $e) {
            return response()->json([ 'http_status_code' => 500 ,'status' => false, 'context' =>  ['error' => $e->getMessage()] ,  'timestamp'=> Carbon::now() , 'message' => "An unexpected error occurred" ],500);
        }
    }


    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['http_status_code' => 422 ,'status' => false, 'context' =>  ['error' => $validator->errors()->first()] ,  'timestamp'=> Carbon::now() , 'message' => 'Validation failed'], 422);
        }

        $user = User::where('email', $request->user_id)->orWhere('mobile_number', $request->user_id)->first();
      
        if ($user && $user->otp == $request->otp) {
            
            return response()->json([ 'http_status_code' => 200 ,'status' => true, 'context' =>  ['data' => []] ,  'timestamp'=> Carbon::now() , 'message' => "Code Verified"]);

        } else {
            return response()->json([ 'http_status_code' => 403 ,'status' => false, 'context' =>  ['error' => 'Code Does Not Match'] ,  'timestamp'=> Carbon::now() , 'message' => "Code Does Not Match" ],403);
        }
    }

    public function new_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['http_status_code' => 422 ,'status' => false, 'context' =>  ['error' => $validator->errors()->first()] ,  'timestamp'=> Carbon::now() , 'message' => 'Validation failed'], 422);
        }
     
        $user = User::where('email', $request->user_id)->orWhere('mobile_number',$request->user_id)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->otp = null; 
            $user->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Password reset successfully',
            ]);
        } else {
            return response()->json([ 'http_status_code' => 404 ,'status' => false, 'context' =>  ['error' => 'User Not Found'] ,  'timestamp'=> Carbon::now() , 'message' => "User Not Found" ],404);
        }
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:6|max:16',
            'new_password' => 'required|confirmed|min:6|max:16',
        ]);

        if ($validator->fails()) {
            return response()->json(['http_status_code' => 422 ,'status' => false, 'context' =>  ['error' => 'Validation failed'] ,  'timestamp'=> Carbon::now() , 'message' => $validator->errors()->first()], 422);
        }


        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            $newPassword = Hash::make($request->new_password);
            $user->password = $newPassword;
            $user->save();

            return response()->json([ 'http_status_code' => 200,'status' => true, 'context' =>  ['data' => []] ,  'timestamp'=> Carbon::now() , 'message' => "Password updated successfully"]);
        }

        return response()->json([ 'http_status_code' => 400 ,'status' => false, 'context' =>  ['error' => 'Invalid current password, Please try again'] ,  'timestamp'=> Carbon::now() , 'message' => "Invalid current password" ],400);

    }
}
