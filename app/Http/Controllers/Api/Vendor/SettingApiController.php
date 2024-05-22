<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\verificationMail;
use App\Models\Shop;
use App\Models\Tax;
use App\Models\VendorAddress;
use App\Models\VendorBankDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingApiController extends Controller
{
    public function createTax(Request $request)
    {
       
            $rules = [
                'tax_name' => 'required|min:2|max:30',
                'tax_rate' => 'required|numeric|max:30',
                'status' => 'required',
                'country_id' => 'required|integer',
                'state_id' => 'required|integer',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            $user = auth('api')->user();
            $tax = new Tax();
            $tax->tax_name = $request->input('tax_name');
            $tax->tax_rate = $request->input('tax_rate');
            $tax->status = $request->input('status');
            $tax->country_id = $request->input('country_id');
            $tax->state_id = $request->input('state_id');
            $tax->created_by = $user->id;
            $tax->updated_by = $user->id;
            $tax->save();

            if ($tax) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $tax,
                        'location' => route('vendor.settings.tax.index'),
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Tax stored successfully',
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

    public function allTax(Request $request)
    {
        try {
            $taxes = Tax::join(
                'countries',
                'taxes.country_id',
                '=',
                'countries.id'
            )
                ->join('states', 'taxes.state_id', '=', 'states.id')
                ->join('users', 'taxes.created_by', '=', 'users.id')
                ->select(
                    'taxes.*',
                    'users.name as username',
                    'countries.country_name as country_name',
                    'states.state_name  as state_name'
                )
                ->get();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $taxes,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Taxes Fetched Successfully',
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

    public function updateTax(Request $request)
    {
        try {
            $rules = [
                'tax_name' => 'required|min:2|max:30',
                'tax_rate' => 'required|numeric|max:30',
                'status' => 'required',
                'country_id' => 'required|integer',
                'state_id' => 'required|integer',
                'id' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

        
            $user =  auth('api')->user();
            $tax = Tax::find($request->id);
            $tax->tax_name = $request->input('tax_name');
            $tax->tax_rate = $request->input('tax_rate');
            $tax->status = $request->input('status');
            $tax->country_id = $request->input('country_id');
            $tax->state_id = $request->input('state_id');
            $tax->updated_by = $user->id;
            $tax->save();

            if ($tax) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'location' => route('vendor.settings.tax.index'),
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Tax updated successfully',
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

    public function viewTax(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            $taxes = Tax::join(
                'countries',
                'taxes.country_id',
                '=',
                'countries.id'
            )
                ->join('states', 'taxes.state_id', '=', 'states.id')
                ->join('users', 'taxes.created_by', '=', 'users.id')
                ->select(
                    'taxes.*',
                    'users.name as username',
                    'countries.country_name as country_name',
                    'states.state_name  as state_name'
                )
                ->where('taxes.id', $request->id)
                ->first();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $taxes,
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Tax Fetched Successfully',
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

    public function deleteTax(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }
            $tax_delete = Tax::where('id', $request->id)->delete();
            if ($tax_delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'location' => route('vendor.settings.tax.index'),
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Deleted Successfully',
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

    public function updateShop(Request $request)
    {
        try {
            $user = auth('api')->user();
            $shop = Shop::where('vendor_id',$user->id)->first();
               
            if(!$shop){
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => "Not Found"],
                        'timestamp' => Carbon::now(),
                        'message' => 'Shop not Found',
                    ],
                    404
                );
            }
            $rules = [
                'shop_name' =>  [
                    'required',
                    'min:2',
                    'max:46',
                    Rule::unique('shops')->ignore($shop->id),
                ],
                'legal_name' => 'required|min:2|max:46',
                'email' => 'required|email|max:256',
                'timezone' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

        
            $shop->shop_name = $request->input('shop_name');
            $shop->shop_url = $request->input('shop_url');
            $shop->legal_name = $request->input('legal_name');
            $shop->email = $request->input('email');
            $shop->timezone = $request->input('timezone');
            $shop->description = $request->input('description');
            $shop->maintenance_mode	= $request->input('maintenance_mode');
            $shop->vendor_id = $user->id;
            $shop->created_by = $user->id;
            $shop->updated_by = $user->id;

            if ($request->hasFile('brand_logo')) {
                $image = $request->file('brand_logo');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('vendor/shop/brand');
                $image->move($destinationPath, $image_name);

                $shop->brand_logo = $image_name;
            }

            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('vendor/shop/cover');
                $image->move($destinationPath, $image_name);

                $shop->cover_image = $image_name;
            }


            if ($request->hasFile('banner_img')) {
                $image = $request->file('banner_img');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/shop/banner');
                $image->move($destinationPath, $image_name);
                $shop->banner_img = $image_name;
            }

            $shop->save();
            if ($shop) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $shop,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Shop Updated successfully!',
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

    public function getShopDetails(){

        try{
                 $shop = Shop::where('vendor_id',auth('api')->user()->id)->first();
                 if($shop){
                      $shop->shop_address = VendorAddress::where('shop_id',$shop->id)->first() ?? null ;
                      $shop->bank_details = VendorBankDetail::where('shop_id',$shop->id)->first() ?? null;
                      $shop->brand_logo = asset(
                        'public/vendor/shop/brand/' . $shop->brand_logo
                    );
                    $shop->cover_image = asset(
                        'public/vendor/shop/cover/' . $shop->cover_image
                    );

                    $shop->banner_img = asset(
                        'public/vendor/shop/banner/' . $shop->banner_img
                    );
                 }

                 return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $shop
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Shop Updated successfully!',
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

    public function updateBankDetail(Request $request){

        try{

            $rules = [
            'account_holder_name' => 'required',
            'account_number' => 'required',
            'account_type' => 'required',
            'routing_number' => 'required',
            'bic_code' => 'required',
            'iban_number' => 'required',
            'bank_address' => 'required',
            'shop_id' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            
            $bank = VendorBankDetail::where(['shop_id'=> $request->shop_id , 'vendor_id' => auth('api')->user()->id])->first();
            if($bank){
               $bank->update($request->all());
            }
            else{
               $bank =  VendorBankDetail::create($request->all() + ['vendor_id' => auth('api')->user()->id]);
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $bank
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Bank Information Updated successfully!',
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



    public function updateShopAddress(Request $request){
        try{

            $rules = [
                'address_line1' => 'required',
                'city' => 'required',
                'postal_code' => 'required',
                'phone' => 'required',
                'country' => 'required',
                'state' => 'required',
                'shop_id' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            
            $address = VendorAddress::where(['shop_id'=> $request->shop_id , 'vendor_id' => auth('api')->user()->id])->first();
        
            if($address){
               $address->update($request->all());
            }
            else{
               $address =  VendorAddress::create([
                   'shop_id' => $request->shop_id,
                   'address_line1' => $request->address_line1,
                   'address_line2' => $request->address_line2,
                   'city' => $request->city,
                   'country' => $request->country,
                   'postal_code' => $request->postal_code,
                   'phone' => $request->phone,
                   'state' => $request->state,
                   'vendor_id'=> auth('api')->user()->id
               ]);
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => $address
                ],
                'timestamp' => Carbon::now(),
                'message' => 'shop Address Updated successfully!',
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


    public function verification_email(Request $request){
        try{
            $sent = Mail::to(Auth::user()->email)->send(new verificationMail());

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => []
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Mail Sent Successfully',
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

    public function verification_confirmation($id){
        try{

            $shop = Shop::where('vendor_id', $id)->first();
            $shop->email_is_verified = 1;
            $shop->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => [
                    'data' => []
                ],
                'timestamp' => Carbon::now(),
                'message' => 'Email is Verified Successfully',
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
