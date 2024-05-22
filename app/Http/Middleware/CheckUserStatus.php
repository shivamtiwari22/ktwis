<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
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
        if ($request->header('Authorization')) {
            if (auth('api')->user()) {
                if (auth('api')->user()->customer_status == 0) {
                    auth('api')
                        ->user()
                        ->token()
                        ->revoke();

                        return response()->json(
                            [
                                'http_status_code' => 401,
                                'status' => false,
                                'context' => ['error' => 'Unauthenticated'],
                                'timestamp' => Carbon::now(),
                                'message' => 'Account is Inactive',
                            ],
                            401
                        );
                } else {
                    return $next($request);
                }
            }
            return $next($request);
        }
        return $next($request);
    }
}
