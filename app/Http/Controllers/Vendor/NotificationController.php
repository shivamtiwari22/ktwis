<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kutia\Larafirebase\Facades\Larafirebase;

class NotificationController extends Controller
{
    public function index()
    {
        return view('vendor.notification.index');
    }

    public function updateToken(Request $request)
    {
        Auth::user()->fcm_token = $request->token;
        Auth::user()->save();
        return response()->json(['Token successfully stored.']);
    }


    // test notification
    public function testNotification(Request $request)
    {
     
        $title = "hello";
        $body = "Tested Body";

        $FcmToken = User::whereNotNull('fcm_token')
        ->where('id',Auth::user()->id)
        ->pluck('fcm_token')
        ->all();

        $FcmToken = User::whereNotNull('fcm_token')
        ->where('id', Auth::user()->id)
        ->pluck('fcm_token')
        ->all();

        Larafirebase::withTitle("hello")
        ->withBody("Message")
        ->sendMessage($FcmToken);
        
        // notification($title,$body,$FcmToken);
        return redirect()
            ->back()
            ->with('status', 'Notification Send Successfully');
    }



    public function notification(Request $request)
    {
     
        $title = $request->title;
        $body = $request->body;

        $FcmToken = User::whereNotNull('fcm_token')
        ->pluck('fcm_token')
        ->all();
        
        notification($title,$body,$FcmToken);
        return redirect()
            ->back()
            ->with('status', 'Notification Send Successfully');
    }


   public function markAsRead($id){
      if($id){
        auth()->user()->unreadNotifications->where('id',$id)->markAsRead();
      }
      return back();
   }

   public function allMarkAsRead(){
    auth()->user()->unreadNotifications->markAsRead();
    return back();
   }

   public function viewAll(){

    // dd(auth()->user()->notifications);
    return view('vendor.notification.view_all');
   }

   

}
