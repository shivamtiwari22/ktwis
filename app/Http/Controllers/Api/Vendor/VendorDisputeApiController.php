<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispute;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class VendorDisputeApiController extends Controller
{
    public function vendorDispute(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'order_id' => 'required|integer|min:1',
                'reason' => 'required',
                'good_received' => 'required',
                'refund_amount' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()->first()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }
            $product = Product::find($request->product_id);
            if ($product) {
                $vendor = User::where('id', $product->created_by)->first();

                $dispute = new Dispute();
                $dispute->customer_id = auth('api')->user()->id;
                $dispute->vendor_id = $vendor->id;
                $dispute->order_id = $request->order_id;
                $dispute->type = $request->reason;
                if ($request->refund_amount) {
                    $dispute->refund_requested = 1;
                }
                $dispute->status = "open";
                $dispute->refund_amount = $request->refund_amount;
                $dispute->good_received = $request->good_received;
                $dispute->p_id = $request->product_id;
                $dispute->variant_id = $request->variant_id;
                $dispute->description = $request->description;
                $dispute->save(); 
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $dispute],
                    'timestamp' => Carbon::now(),
                    'message' => 'Dispute created successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'An unexpected error occurred'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Product not found',
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
}
