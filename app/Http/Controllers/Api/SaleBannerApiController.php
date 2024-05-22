<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\SaleBanner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SaleBannerApiController extends Controller
{
    public function get_all_sale_banner()
    {
        try {
            $data = SaleBanner::where('status','0')->latest()->take(3)->get(['id','link','image']);
            foreach ($data as $banner) {
                $banner->image = asset('public/admin/salebanner/' . $banner->image);            
            }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetch Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
    public function sale_banner_add(Request $request)
    {
        try {
          
            $validator = Validator::make($request->all(), [
                'file_data' => 'required',
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
          
            $user = Auth::user()->id;
            $myvariable =  new SaleBanner();
            $myvariable->created_by = $user;
            if ($request->hasFile('file_data')) {
                $image = $request->file('file_data');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('admin/salebanner');
                $image->move($destinationPath, $image_name);
                $myvariable->image = $image_name;
            }
            $myvariable->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $myvariable],
                'timestamp' => Carbon::now(),
                'message' => 'Sale banner data add successfully',
            ]);
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
    public function sale_banner_edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_data' => 'required',
                  'id' =>'required',
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
            $myvariable = SaleBanner::find($request->id);
         
            if ($request->hasFile('file_data')) {
                $image = $request->file('file_data');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('admin/salebanner');
                $image->move($destinationPath, $image_name);
                $myvariable->image = $image_name;
            }
        
            $myvariable->save();          
              return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$myvariable]],
                'timestamp' => Carbon::now(),
                'message' => 'Sale banner update Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
    public function sale_banner_delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                  'id' =>'required',
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
            $category = SaleBanner::find($request->id);
            $category->delete();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$category]],
                'timestamp' => Carbon::now(),
                'message' => 'Data delete Successfully ',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
    public function sale_banner_status(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' =>'required',
                'status_value' =>'required',
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
            
                $coupon = SaleBanner::find($request->id);
                $coupon->status = $request->status_value;
            $coupon->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$coupon]],
                'timestamp' => Carbon::now(),
                'message' => 'Sale banner status update Successfully ',
            ]);
    
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' =>500,
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
