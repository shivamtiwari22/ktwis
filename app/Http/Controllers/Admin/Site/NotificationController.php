<?php

namespace App\Http\Controllers\Admin\Site;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomNotifcation;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notification.index');
    }

    public function notification(Request $request){
         
            if($request->send_to == "vendor"){
                $user = User::with(['roles' => function ($query) {
                    $query->where('role', 'vendor');
                }])
                    ->whereHas('roles', function ($query) {
                        $query->where('role', 'vendor');
                    })
                    ->get();
            }
            elseif ($request->send_to == "customer"){
                $user = User::with(['roles' => function ($query) {
                    $query->where('role', 'user');
                }])
                    ->whereHas('roles', function ($query) {
                        $query->where('role', 'user');
                    })
                    ->get();
            }
            else{
                     $user = User::where('id','!=','1')->get();
            }

            $message = $request->message;
        
        //   auth()->user()->notify(new CustomNotifcation($user,$message));
          Notification::send($user, new CustomNotifcation($message));

          return back()->with('message','Notification send successfully');
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
      return view('admin.notification.view_all');
     }
}
