<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserResetPassword;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // public function reset_password(Request $request)
    // {
    //     $resetData = DB::table('password_resets')->where('token', $request->token);
    //     $resest_data = $resetData->first();
    //     $resetcount = $resetData->count();

    //     if (isset($request->token)  && $resetcount) {
    //         $user = User::where('email', $resest_data->email)->first();
    //         return view('user.forgot-password.forgot-password' , compact('user'));
    //     } else {
    //         return view('404');
    //     }
    // }

    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        return view('pages.success');
    }
   
   
}
