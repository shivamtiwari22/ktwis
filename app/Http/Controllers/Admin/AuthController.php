<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function login_submit(Request $request)
    {

        $rules = [
            'email'   => 'required',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('result' => false, 'msg' => $validator->errors()->first()));
        }
        $role = 'admin';
        $user = User::where('email', $request->email)->first();

        if ($user  && $user->hasRole($role)) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                if ($request->remember_me  && isset($request->remember_me)) {
                    setcookie("email", $request->email, time() + 3600);
                    setcookie("password", $request->password, time() + 3600);
                } else {
                    setcookie("email", "");
                    setcookie("password", "");
                }
                return response()->json(array('status' => true, 'location' => route('dashboard'), 'msg' => "Logged in Successfully!"));
            } else {
                return response()->json(array('status' => false, 'msg' => "Invalid Credentials!"));
            }
        }
        return response()->json(array('status' => false, 'msg' => "User Not Found!"));
        exit;
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('admin.login');
    }

    public function forgot_password()
    {
        return view('admin.auth.forgot_password');
    }


    public function send_mail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $isAdmin = $user->roles()->where('role', 'admin')->exists();
            if ($isAdmin) {

                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/reset-password?token=' . $token;

                $password = '';
                $password .= chr(rand(65, 90)); // Uppercase letter
                $password .= chr(rand(97, 122)); // Lowercase letter
                $password .= rand(0, 9); // Number
                $password .= Str::random(5); // Random characters

                $password = str_shuffle($password);

                $data['email'] = $request->email;
                $data['title'] = "Ktwis";
                $data['body'] = "Your New Password for " . $request->email . " is :  " . $password;

                Mail::send('email.forgotPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->from('mail@dilamsys.com', "Ktwis")->to($data['email'])->subject($data['body']);
                });
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                $user = User::where('email', $request->email)->first();
                $user->password = Hash::make($password);
                $user->save();
                return response()->json(['status' => true, 'location' => route('admin.login'), 'msg' => 'Email Sent']);
            } else {
                return response()->json(array('status' => false, 'msg' => "Admin not found!"));
            }
        } else {
            return response()->json(array('status' => false, 'msg' => "Invalid Email!"));
        }
    }

    public function change_password()
    {
        $user = Auth::user();
        return view('admin.auth.change_password', compact('user'));
    }


    public function change_password_store(Request $request)
    {
        $request->validate([
            'old_pass' => 'required',
            'new_pass' => 'required|min:8',
            'confirm_pass' => 'required|same:new_pass',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_pass, $user->password)) {
            return response()->json(['message' => 'The old password is incorrect.'], 422);
        }

        $user->password = Hash::make($request->new_pass);
        $user->save();

        return response()->json(['status' => true ,'message' => 'Password updated successfully.','location' => route('dashboard'),]);
    }
}
