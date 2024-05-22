<?php

namespace App\Http\Controllers\Api\Vendor;


use App\Http\Controllers\Controller;
use App\Models\ReturnPolicy;
use App\Models\Specification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SpecificationApiController extends Controller
{
    public function specification_add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required',
                'product_id' => 'required|integer'
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

         
            $exists = Specification::where('product_id', $request->product_id)->exists();
            if($exists){
                return response()->json(
                    [
                        'http_status_code' => 409,
                        'status' => false,
                        'context' => ['error' => "Specification for this product already exists"],
                        'timestamp' => Carbon::now(),
                        'message' => 'Specification Exists',
                    ],
                    409
                );
            }
            $user = Auth::user();
            $product = new Specification();
            $product->product_id = $request->product_id;
            $product->message = $request->input('message');
            $product->created_by = $user->id;
            $product->save();
         
            $product->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Specification Added successfully',
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
    public function specification_edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required',
                'id' => 'required',
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
            $product = Specification::find($request->input('id'));
            $product->message = $request->input('message');
            $product->save();
         
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Data update successfully',
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

    public function return_policy_add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required',
                'subject' => 'required',
                'category_id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()->first()],
                        'timestamp' => Carbon::now(),
                        'message' =>  $validator->errors()->first(),
                    ],
                    422
                );
            }

            
            $exists = ReturnPolicy::where('category_id', $request->category_id)->where('created_by', Auth::user()->id)->exists();
            if($exists){
                return response()->json(
                    [
                        'http_status_code' => 409,
                        'status' => false,
                        'context' => ['error' => "Policy for this category already exists"],
                        'timestamp' => Carbon::now(),
                        'message' => 'Return & Policy Exists',
                    ],
                    409
                );
            }

            $user = Auth::user();
            $product = new ReturnPolicy();
            $product->message = $request->input('message');
            $product->subject = $request->input('subject');
            $product->category_id = $request->category_id;
            $product->created_by = $user->id;
            $product->save();
         
            $product->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Data add successfully',
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
    public function return_policy_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required',
                'subject' => 'required',
                'id' => 'required',
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
            $product = ReturnPolicy::find($request->input('id'));
            $product->message = $request->input('message');
            $product->subject = $request->input('subject');
            $product->save();
         
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Data update successfully',
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
    public function specification_show()
    {
        try {
            $product =Specification::with('product')->where('created_by',Auth::user()->id)->get();  
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Specification fetched successfully',
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



    public function specification_delete($id){
        try {
            $product =Specification::destroy($id);  

            if($product){
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Specification deleted successfully',
                ]);
            }
            else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => "Something Went Wrong Please try again"],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
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

    public function return_policy_data()
    {
        try {
            $product =ReturnPolicy::with('category')->where('created_by',Auth::user()->id)->get();  
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Return & Policy Fetched successfully',
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


    public function return_policy_delete($id){
        try {
            $product =ReturnPolicy::destroy($id);  

            if($product){
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Return & Policy deleted successfully',
                ]);
            }
            else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => "Something Went Wrong Please try again"],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
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
