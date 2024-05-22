<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\UsedVendorCoupon;
use App\Models\VendorCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function add_coupon(Request $request)
    {
        $id = Auth::user()->id;

        $rules = [
            'code' => 'required',
            'amount' => 'required',
            'coupon_type' => 'required',
            'no_of_coupons' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $val->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        } else {
            $new_Content_type = new VendorCoupon();
            $data = [
                'code' => $request->code,
                'coupon_type' => $request->coupon_type,
                'amount' => $request->amount,
                'expiry_date' => $request->expiry_date,
                'no_of_coupons' => $request->no_of_coupons,
                'created_by' => $id,
            ];
            $new_Content_type = $new_Content_type->insert($data);

            if ($new_Content_type) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$data]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Coupon created successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
                );
            }
        }
    }

    public function view_coupon($id)
    {
        $coupon = VendorCoupon::find($id);
        if (!$coupon) {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'Coupon Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Coupon Not Found',
                ],
                404
            );
        }
        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => [$coupon]],
            'timestamp' => Carbon::now(),
            'message' => 'Coupon Fetched Successfully',
        ]);
    }

    public function delete_coupon($id)
    {
        $record = VendorCoupon::find($id);
        if ($record) {
            $record->delete();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Deleted Successfully',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => [
                        'error' => 'Some error occurred! , Please try again',
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }

    public function edit_coupon(Request $request, $id)
    {
        $rules = [
            'code' => 'required',
            'amount' => 'required',
            'no_of_coupons' => 'required',
        ];

        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(
                [
                    'http_status_code' => 422,
                    'status' => false,
                    'context' => ['error' => $val->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        } else {

            $coupon = VendorCoupon::find($id);
            $coupon->code = $request->code;
            $coupon->coupon_type = $request->coupon_type;
            $coupon->amount = $request->amount;
            $coupon->expiry_date = $request->expiry_date;
            $coupon->no_of_coupons = $request->no_of_coupons;
            $coupon->status = $request->status;
            if ($coupon->save()) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$coupon]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Coupon Updated successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
                );
            }
        }
    }

    public function use_coupon($id)
    {
        $coupon = VendorCoupon::where('id', $id)->where('status','published')->first();
        if ($coupon) {

            if($coupon->expiry_date < date("Y-m-d") ){
                return response()->json(
                    [
                        'http_status_code' => 400,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Coupon Expired',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'The coupon you provided has expired',
                    ],
                    400
                );
            }

            if ($coupon->no_of_coupons > $coupon->used_coupons) {
                // $used_coupons = $coupon->used_coupons + 1;
                // $coupon->used_coupons = $used_coupons;
                if ($coupon->save()) {
                    
                    // $customer_id = auth()->id();
                    // $order_id = 1;
                    // $new_Content_type = new UsedVendorCoupon();
                    // $data = [
                    //     'coupon_id' => $coupon->id,
                    //     'customer_id' => $customer_id,
                    //     'order_id' => $order_id,
                    // ];
                    // $new_Content_type = $new_Content_type->insert($data);

                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => [ ]],
                        'timestamp' => Carbon::now(),
                        'message' => 'valid coupon',
                    ]);
                }
            } else {
                return response()->json(
                    [
                        'http_status_code' => 400,
                        'status' => false,
                        'context' => [
                            'error' => 'Coupon Expired',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'The coupon you provided has reached its maximum usage limit',
                    ],
                    400
                );
            }


        } else {
            return response()->json(
                [
                    'http_status_code' => 400,
                    'status' => false,
                    'context' => [
                        'error' =>
                            'Invalid coupon code. Please enter a valid coupon.',
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Invalid coupon code',
                ],
                400
            );
        }
    }

    public function applyCoupon(Request $request)
    {
        try {
            $couponCode = $request->input('coupon_code');
            // $totalAmount = $request->input('total_amount');

            $coupon = VendorCoupon::where(["code"=> $couponCode,"status" => "published"])->first();
            if (!$coupon) {
                return response()->json(
                    [
                        'http_status_code' => 400,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Invalid coupon code. Please enter a valid coupon.',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Invalid coupon code',
                    ],
                    400
                );
            } else {
                if($coupon->expiry_date < date("Y-m-d") ){
                    return response()->json(
                        [
                            'http_status_code' => 400,
                            'status' => false,
                            'context' => [
                                'error' =>
                                    'Coupon Expired',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'The coupon you provided has expired and is no longer valid',
                        ],
                        400
                    );
                }
                elseif ($coupon->no_of_coupons <= $coupon->used_coupons) {
                    return response()->json(
                        [
                            'http_status_code' => 400,
                            'status' => false,
                            'context' => [
                                'error' =>
                                    'Coupon can no longer be used',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'The coupon you provided has reached its maximum usage limit .',
                        ],
                        400
                    );
                }
                else {
                // if ($coupon->coupon_type == 'Percentage') {
                //     $discountedAmount =
                //         $totalAmount - $totalAmount * ($coupon->amount / 100);

                // } elseif ($coupon->coupon_type == 'fixed') {
                //     $discountedAmount = $totalAmount - $coupon->amount;
                // }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $coupon],
                    'timestamp' => Carbon::now(),
                    'message' => 'Coupon Fetched Successfully',
                ]);


            }
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


    public function allVenderCoupon($id){
        try{
            $coupons = VendorCoupon::where(["created_by" => $id ,"status" => "published"])
            ->where('expiry_date', '>=', date("Y-m-d"))
            ->whereColumn('used_coupons','<',DB::raw('CAST(no_of_coupons AS UNSIGNED)'))
            ->get();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $coupons],
                'timestamp' => Carbon::now(),
                'message' => 'Coupons Fetched Successfully',
            ]);  
        }
        catch (\Exception $e) {
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

    public function allCoupon(){
        try{
            $coupons = VendorCoupon::where(["created_by" => Auth::user()->id])->get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $coupons],
                'timestamp' => Carbon::now(),
                'message' => 'Coupons Fetched Successfully',
            ]);  
        }
        catch (\Exception $e) {
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
