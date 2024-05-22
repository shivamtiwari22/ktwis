<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOrder
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
       $orderVendorId = Order::find($orderId)->seller_id;
        // Check if the authenticated vendor matches the order's vendor
        if ($authenticatedVendorId != $orderVendorId) {
            return back();
        }

        return $next($request);
    }
}
