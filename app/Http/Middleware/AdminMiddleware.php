<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        // if(!Auth::guard('web')->check()){          
        //     return redirect(route('admin.login'));
        //     }
        // return $next($request);
        $user = Auth::user(); 
        if($user && Auth::check() ){
            
            $roles = $user->roles->first();
            if (!($roles &&$roles->role == "admin")) {
                return redirect()->back();
            }
            return $next($request);
        }else{
            // return redirect()->back();
            return redirect('admin/login');
        } 
    }
}
