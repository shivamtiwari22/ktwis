<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCheck
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

        if(!Auth::guard('api')->check()){
            return response()->json(
                [
                    'http_status_code' => 401,
                    'status' => false,
                    'context' => ['error' => 'Unauthenticated'],
                    'timestamp' => Carbon::now(),
                    'message' => 'User authentication failed',
                ],
                401
            );
        }
        return $next($request);
    }
}
