<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\Tax;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AddressApiController extends Controller
{
    public function add_address(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'address_type' => 'required|in:primary,billing,shipping',
                'full_name' => 'required',
                'address' => 'required',
                'country' => 'required',
                'state' => 'required',
                'zip_code' => 'required',
                'city' => 'required',
                'phone_number' => 'required',
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

             $user_id = $request->header('user_id') ?? auth('api')->user()->id;
              $count = UserAddress::where('user_id', $user_id)->count();
              $current  =  $count > 0 ? 0: 1; 
                   

              if($request->is_default == 0){
                $default = $count > 0 ? 0: 1;
              }
              else {
                  $default = $request->is_default;
              }
           
              
             $address = UserAddress::create(['user_id' => $user_id, 'contact_person'=> $request->full_name, 'contact_no' => $request->phone_number , 'is_current' => $current , 'is_default' => $default ,
                    'address_type' => $request->address_type,
                    'address' => $request->address,
                    'floor_apartment' => $request->floor_apartment,
                    'country' => $request->country,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'city' => $request->city,
                    'country_code' => $request->country_code
            ]);

             if($request->is_default == 1){
                UserAddress::where('user_id', $user_id)->where('id', '!=',$address->id )->update([
                    'is_default' => 0
             ]);
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $address],
                'timestamp' => Carbon::now(),
                'message' => 'Address created successfully',
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

    public function updateAddress(Request $request, $address_id)
    {
        try {
           
            $validator = Validator::make($request->all(), [
                'address_type' => 'required|in:primary,billing,shipping',
                'full_name' => 'required',
                'address' => 'required',
                'country' => 'required',
                'state' => 'required',
                'zip_code' => 'required',
                'city' => 'required',
                'phone_number' => 'required',
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


            $user_id = $request->header('user_id') ?? auth('api')->user()->id ;

            $address = UserAddress::findOrFail($address_id);

            $address->update($request->all() + ['contact_person'=> $request->full_name, 'contact_no' => $request->phone_number]);

            if($request->is_default == 1){
                UserAddress::where('user_id',$user_id)->where('id', '!=',$address_id )->update([
                    'is_default' => 0
             ]);
            }

            if($request->is_current == 1){
                UserAddress::where('user_id',$user_id)->where('id', '!=',$address_id )->update([
                    'is_current' => 0
             ]);
            }
        

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $address],
                'timestamp' => Carbon::now(),
                'message' => 'Address Updated successfully',
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

    public function get_address($address_id)
    {
        try {
            $get_address = UserAddress::findOrFail($address_id);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $get_address],
                'timestamp' => Carbon::now(),
                'message' => 'Get Address successfully',
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


    public function get_all_address(request $request)
    {
        try {

             if($request->header('user_id')){
                  $user_id = $request->header('user_id');
             }
             else {
                 $user_id = auth('api')->user()->id;
             }

            $get_address = UserAddress::where('user_id', $user_id)->get();
            foreach($get_address as $address){
                $address->state =  State::find($address->state)->state_name  ?? $address->state ;
                $address->country =  Country::find($address->country)->country_name  ?? $address->country ;
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $get_address],
                'timestamp' => Carbon::now(),
                'message' => 'Get Address successfully',
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


    public function get_all_customer_address($id)
    {
        try {
            $get_address = UserAddress::where('user_id', $id)->get();
            foreach($get_address as $address){
                $address->state =  State::find($address->state)->state_name  ?? $address->state ;
                $address->country =  Country::find($address->country)->country_name  ?? $address->country ;
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $get_address],
                'timestamp' => Carbon::now(),
                'message' => 'Get Address successfully',
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



    public function get_address_by_user_id(Request $request)
    {
            try {
            $user_id = $request->header('user_id') ??  auth('api')->user()->id ;
          
            $get_address = UserAddress::where('address_type', 'billing')->where('user_id', $user_id)
                ->join('states', 'user_addresses.state', '=', 'states.id')
                ->join('countries', 'user_addresses.country', '=', 'countries.id')
                ->select('user_addresses.*', 'states.state_name', 'countries.country_name')
                ->get();

            if ($get_address->toarray()) {
                foreach ($get_address as $address) {
                    $tax = Tax::where('taxes.state_id', $address->state)->where('taxes.country_id', $address->country)
                        ->select('taxes.*')
                        ->get();
                    $address->tax = $tax;
                }
                if ($tax->toarray()) {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $get_address],
                        'timestamp' => Carbon::now(),
                        'message' => 'Dispute created successfully',
                    ]);
                } else {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $get_address],
                        'timestamp' => Carbon::now(),
                        'message' => 'Dispute created successfully',
                    ]);
                }
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Address not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Address not found',
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

    public function remove_address($id)
    {
        try {
            $delete = UserAddress::where('id', $id)->delete();
            //  return $delete;
            if ($delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => ''],
                    'timestamp' => Carbon::now(),
                    'message' => 'Address delete successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'User address not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Address not found',
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


    public function get_address_by_type($type){
        try {

            $user_id = Auth::user()->id;
            $get_address = UserAddress::where('address_type', $type)->where('user_id', $user_id )
            ->join('states', 'user_addresses.state', '=', 'states.id')
            ->join('countries', 'user_addresses.country', '=', 'countries.id')
            ->select('user_addresses.*', 'states.state_name', 'countries.country_name')
            ->get();


            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $get_address],
                'timestamp' => Carbon::now(),
                'message' => 'Get Address successfully',
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


    // update current user current address 
    public function updateCurrentAddress(Request $request, $address_id){
        try {

              $user_id = $request->header('user_id') ?? auth('api')->user()->id ;
            $address = UserAddress::findOrFail($address_id);

            $address->update(['is_current'=> $request->is_current]);

            if($request->is_current == 1){
                UserAddress::where('user_id', $user_id)->where('id', '!=', $address_id )->update([
                    'is_current' => 0
             ]);
            }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $address],
                'timestamp' => Carbon::now(),
                'message' => 'Current Address Successfully',
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


    public function updateCustomerAddress(Request $request){
        try {
        $validator = Validator::make($request->all(), [
            'address_type' => 'required',
            'address_line1' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'state' => 'required',
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


        $address = UserAddress::find($request->address_id);
        if ($address) {
            $address->address_type = $request->address_type;
            $address->contact_no = $request->phone;
            $address->floor_apartment = $request->address_line1;
            $address->address = $request->address_line2;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->country_code = '+'.$request->country_code;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->save();
        } else {
            $address = new UserAddress();
            $address->user_id = $request->user_id;
            $address->contact_person = $request->contact_person;
            $address->address_type = $request->address_type;
            $address->contact_no = $request->phone;
            $address->floor_apartment = $request->address_line1;
            $address->address = $request->address_line2;
            $address->city = $request->city;
            $address->country_code = '+'.$request->country_code;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->zip_code = $request->zip_code;
            $address->save();
        }
        if ($address) {
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Address Updated Successfully.',
            ]);
        }
        return response()->json([
            'http_status_code' => 500,
            'status' => false,
            'context' => ['error' => "something Went Wrong"],
            'timestamp' => Carbon::now(),
            'message' => 'Something Went Wrong.',
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
