<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();  

        if($user && Auth::check()){
            $roles = $user->roles->first();
            if (!($roles &&$roles->role == "vendor")) {
                return redirect()->back();
            }
            return $next($request);
        }else{
            // return redirect()->back();
            return redirect('vendor/login');
        }      
    }
}
