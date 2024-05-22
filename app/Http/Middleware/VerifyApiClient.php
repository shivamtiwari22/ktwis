<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Laravel\Passport\Exceptions\MissingScopeException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Route;

class VerifyApiClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    protected $headers = ['client-id', 'client-key'];

    public function handle(Request $request, Closure $next)
    {
        $clientId = $request->header('client-id');
        $clientSecret = $request->header('client-key');
        $client = Client::where('password_client', true)
            ->where('id', $clientId)
            ->where('secret', $clientSecret)
            ->first();


        if (!$client) {
            // return $next($request);
            return response()->json(
                [
                    'http_status_code' => 401,
                    'status' => false,
                    'context' => ['error' => 'invalid client'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Client authentication failed',
                ],
                401
            );
        }

        
        return $next($request);
    }
}
