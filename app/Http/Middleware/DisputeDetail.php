<?php

namespace App\Http\Middleware;

use App\Models\Dispute;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeDetail
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
        $authenticatedVendorId = Auth::user()->id;

        // Assuming the route parameter is named vendorId
        $orderId = $request->route('id');
       $orderVendorId = Dispute::find($orderId)->customer_id;
        // Check if the authenticated vendor matches the order's vendor
        if ($authenticatedVendorId != $orderVendorId) {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Not Found',
                ],
                404
            );
        }

        return $next($request);
    }
}
