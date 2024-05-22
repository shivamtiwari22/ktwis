<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\ShippingCountry;
use App\Models\ShippingRate;
use App\Models\ShippingState;
use App\Models\ShippingZone;
use App\Models\State;
use App\Models\Zone;
use App\Models\ZoneCountryState;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShippingApiController extends Controller
{
    public function add_carriers(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'url' => 'required',
                'phone' => 'required',
                'email' => 'required',
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
                if ($request->hasFile('logo')) {
                    $logoFile = $request->file('logo');
                    $extension = $logoFile->getClientOriginalExtension();
                    $logoName = 'logo_' . time() . '.' . $extension;
                    $logoFile->move(
                        'public/admin/shipping/carriers/logo/',
                        $logoName
                    );
                } else {
                    $logoName = null;
                }
                $created_by = Auth::user()->id;
                $carrier = new Carrier();
                $data = [
                    'name' => $request->name,
                    'tracking_url' => $request->url,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'logo' => $logoName,
                    'status' => $request->status,
                    'created_by' => $created_by,
                ];
                $carrier = $carrier->insert($data);
                if ($carrier) {
                    return response()->json(
                        [
                            'http_status_code' => 200,
                            'status' => true,
                            'context' => ['data' => [$carrier]],
                            'timestamp' => Carbon::now(),
                            'message' => 'Carrier created Successfully',
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'http_status_code' => 404,
                            'status' => false,
                            'context' => ['error' => 'Dispute Not Found'],
                            'timestamp' => Carbon::now(),
                            'message' => 'Something Went Wrong!',
                        ],
                        404
                    );
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

    public function update_carriers(Request $request, $carrier_id)
    {
        try {
            $rules = [
                'name' => 'required',
                'url' => 'required',
                'phone' => 'required',
                'email' => 'required',
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
                $carrier = Carrier::find($carrier_id);
                if ($request->hasFile('logo')) {
                    $logoFile = $request->file('logo');
                    $extension = $logoFile->getClientOriginalExtension();
                    $logoName = 'logo_' . time() . '.' . $extension;
                    $logoFile->move(
                        'public/admin/shipping/carriers/logo',
                        $logoName
                    );
                } else {
                    $logoName = $carrier->logo;
                }

                $carrier->name = $request->name;
                $carrier->tracking_url = $request->url;
                $carrier->phone = $request->phone;
                $carrier->email = $request->email;
                $carrier->logo = $logoName;
                $carrier->status = $request->status;

                if ($carrier->save()) {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => [$carrier]],
                        'timestamp' => Carbon::now(),
                        'message' => 'Carrier Updated successfully!',
                    ]);
                } else {
                    return response()->json(
                        [
                            'http_status_code' => 404,
                            'status' => false,
                            'context' => ['error' => 'Something went wrong!'],
                            'timestamp' => Carbon::now(),
                            'message' => 'Something went wrong!',
                        ],
                        404
                    );
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

    public function get_carrier_by_id($carrier_id)
    {
        try {
            $carrier = Carrier::find($carrier_id);
            if ($carrier) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$carrier]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Carrier Updated Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data Not Found',
                    ],
                    404
                );
                return response()->json([
                    'status' => true,
                    'message' => 'Data not exists!',
                ]);
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

    public function get_all_carrier()
    {
        try {
            $carrier = Carrier::where('created_by',auth()->user()->id)->get()->map(function ($item) {
                $item['logo'] = asset('public/admin/shipping/carriers/logo/'. $item->logo);
                return $item;
            });
            return response()->json(
                [
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $carrier],
                    'timestamp' => Carbon::now(),
                    'message' => 'Carrier Updated successfully!',
                ],
                200
            );
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

    public function delete_carrier($carrier_id)
    {
        try {
            $category = Carrier::find($carrier_id);

            if ($category->delete()) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$category]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Carrier Deleted Successfully',
                ]);
                exit();
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Error Occurred, Please try again',
                    ],
                    404
                );
                return response()->json([
                    'status' => false,
                    'msg' => 'Error Occurred, Please try again',
                ]);
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

    // carrier status update 
    public function update_status($id){
        try {

            $carrier = Carrier::find($id);
            $carrier->status = ($carrier->status == '0') ? '1' : '0';
            $save = $carrier->save();
            if ($save) {

                if($carrier->status == 1){
                    $status = 'Active';
                }
                else {
                    $status = "Inactive";
                }
                
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Carrier '.$status. ' Successfully',
                ]);

            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' =>  "Something Went Wrong"],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
                );
            }
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

    ////////////////////////////////////////////////////////////////

    public function add_shipping_rates(Request $request)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string',
                'carrier_id' => 'required|exists:carriers,id',
                'delivery_time' => 'required|numeric',
                'max_order' => 'required|numeric',
                'mini_order' => 'required|numeric',
                'rate' => 'required|numeric',
                'zone_id' => 'required'
            ]);
            if ($validatedData->fails()) {
                return response()->json(
                    [
                        'http_status_code' => '422',
                        'status' => false,
                        'context' => [
                            'error' => $validatedData->errors()->first(),
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }


            $overlap = ShippingRate::where('zone_id', $request->zone_id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('minimum_order_weight', [$request->mini_order, $request->max_order])
                    ->orWhereBetween('max_order_weight', [$request->mini_order, $request->max_order])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('minimum_order_weight', '<=', $request->mini_order)
                            ->where('max_order_weight', '>=', $request->max_order);
                    });
            })
            ->first();
        
                if($overlap){
                    return response()->json(
                        [
                            'http_status_code' => 409,
                            'status' => false,
                            'context' => ['error' => "Rate Already Exists"],
                            'timestamp' => Carbon::now(),
                            'message' => 'Rate Exists For Weight '.$overlap->minimum_order_weight.' to ' .$overlap->max_order_weight,
                        ],
                        409
                    );
                }

            $user = Auth::user();
            $carrier = new ShippingRate();
            $carrier->name = $request->name;
            $carrier->carrier_id = $request['carrier_id'];
            $carrier->zone_id = $request->zone_id;
            $carrier->delivery_time = $request['delivery_time'];
            $carrier->minimum_order_weight = $request['mini_order'];
            $carrier->max_order_weight = $request['max_order'];
            $carrier->rate = $request['rate'];
            $carrier->is_free = $request['free_shipping'];
            $carrier->created_by = $user->id;
            $carrier->updated_by = $user->id;
            $carrier->save();

            if ($carrier) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $carrier],
                    'timestamp' => Carbon::now(),
                    'message' => 'Rate added successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => [
                            'error' => 'An unexpected error occurred',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Something went wrong!',
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

    public function update_shipping_rates(Request $request)
    {
        try {
      
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string',
                'carrier_id' => 'required',
                'delivery_time' => 'required|numeric',
                'max_order' => 'required|numeric',
                'mini_order' => 'required|numeric',
                'rate' => 'required|numeric',
                'rate_id' => 'required'
            ]);
            if ($validatedData->fails()) {
                return response()->json(
                    [
                        'http_status_code' => '422',
                        'status' => false,
                        'context' => [
                            'error' => $validatedData->errors()->first(),
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }


            $zone_id = ShippingRate::find($request->rate_id);
            $overlap = ShippingRate::where('zone_id', $zone_id->zone_id)
         
            ->where(function ($query) use ($request) {
                $query->whereBetween('minimum_order_weight', [$request->mini_order, $request->max_order])
                    ->orWhereBetween('max_order_weight', [$request->mini_order, $request->max_order])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('minimum_order_weight', '<=', $request->mini_order)
                            ->where('max_order_weight', '>=', $request->max_order);
                    });
            })   ->where('id', '!=', $request->rate_id) 
            ->first();
        
                if($overlap){
                    return response()->json(
                        [
                            'http_status_code' => 409,
                            'status' => false,
                            'context' => ['error' => "Rate Already Exists"],
                            'timestamp' => Carbon::now(),
                            'message' => 'Rate Exists For Weight '.$overlap->minimum_order_weight.' to ' .$overlap->max_order_weight,
                        ],
                        409
                    );
                }

            $id = $request->rate_id;
            $user = Auth::user();
            $carrier = ShippingRate::findOrFail($id);
            $carrier->name = $request['name'];
            $carrier->carrier_id = $request['carrier_id'];
            $carrier->delivery_time = $request['delivery_time'];
            $carrier->minimum_order_weight = $request['mini_order'];
            $carrier->max_order_weight = $request['max_order'];
            $carrier->rate = $request['rate'];
            $carrier->is_free = $request['free_shipping'];
            $carrier->updated_by = $user->id;
            $carrier->save();

            if ($carrier) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => [$carrier]],
                        'timestamp' => Carbon::now(),
                        'message' => 'Rate updated  Successfully',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Oops! Something went wrong. Please try again later.',
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

    public function get_rate_by_id($rate_id)
    {
        try {
            $carrier = ShippingRate::find($rate_id);
            if ($carrier) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => [$carrier]],
                        'timestamp' => Carbon::now(),
                        'message' => 'Shipping Rate fetched successfully!',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Oops! Something went wrong. Please try again later.',
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

    public function get_rate_by_vendor_id($vendor_id)
    {
        try {
            $carrier = ShippingRate::where('created_by', $vendor_id)->get();
            $carrier = ShippingRate::where('created_by', $vendor_id)->get();

            if ($carrier->isEmpty()) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not exists!',
                    ],
                    404
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $carrier],
                        'timestamp' => Carbon::now(),
                        'message' => 'Shipping Rate fetched successfully!',
                    ],
                    200
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function get_all_rate()
    {
        try {
            $carrier = ShippingRate::get();
            if ($carrier) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $carrier],
                        'timestamp' => Carbon::now(),
                        'message' => 'Shipping Rate fetched successfully!',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Oops! Something went wrong. Please try again later.',
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

    public function getRates($id){
        try{

            $carrier = ShippingRate::where('zone_id',$id)->get();
            return response()->json(
                [
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $carrier],
                    'timestamp' => Carbon::now(),
                    'message' => 'Shipping Rate fetched successfully!',
                ],
                200
            );
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

    public function delete_rate($rate_id)
    {
        try {
            $carrier = ShippingRate::where('id', $rate_id)->delete();

            if ($carrier) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $carrier],
                        'timestamp' => Carbon::now(),
                        'message' => 'Shipping Rate Deleted successfully!',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Oops! Something went wrong. Please try again later.',
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

    /////////////////////////////////////////

    public function add_zone(Request $request)
    {

       
        try {
            $rules = [
                'name' => 'required',
                'country' => 'required',
                'status' => 'required',
            ];
            $val = Validator::make($request->all(), $rules);
            if ($val->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $val->errors()->first(),
                ]);
            } else {

                foreach($request->country as $item){
                    $existsCountry = ShippingCountry::where('created_by',auth()->user()->id)->where('country_id', $item)->first();
                  if($existsCountry){
                  
                    return response()->json(
                        [
                            'http_status_code' => 409,
                            'status' => false,
                            'context' => ['error' => 'Zone already exists'],
                            'timestamp' => Carbon::now(),
                            'message' =>
                            "Shipping zone already exists with ". $existsCountry->country_name . " country",
                        ],
                        409
                    );
                    
                  }
              
                }

                $created_by = auth()->user()->id;
                $zone = new ShippingZone();
                $zone->name = $request->name;
                $zone->created_by = $created_by;
                $zone->status = $request->status;
                $zone->save();

                foreach ($request->country as $item) {
                    $shippingCountry = new ShippingCountry();
                    $shippingCountry->zone_id = $zone->id;
                    $shippingCountry->country_id = $item;
                    $shippingCountry->country_name = Country::find(
                        $item
                    )->country_name;
                    $shippingCountry->created_by = $created_by;
                    $shippingCountry->save();

                    $states = State::where('country_id', $item)->get();
                    foreach ($states as $state) {
                        $shippingState = new ShippingState();
                        $shippingState->s_country_id = $shippingCountry->id;
                        $shippingState->state_id = $state->id;
                        $shippingState->state_name = $state->state_name;
                        $shippingState->save();
                    }
                }
            }

            if ($zone) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $zone],
                        'timestamp' => Carbon::now(),
                        'message' => 'Shipping Zone Created successfully!',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => 'Something Went Wrong'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Oops! Something went wrong. Please try again later.',
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

    public function update_zone(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'zone_id' => 'required',
                'status' => 'required',
                'country' => 'required',
            ];

            $val = Validator::make($request->all(), $rules);
            if ($val->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $val->errors()->first(),
                ]);
            } else {
                $id = $request->zone_id;
                $zone = ShippingZone::find($id);
                $zone->name = $request->name;
                $zone->status = $request->status;
                $zone->save();
                $country = ShippingCountry::where('zone_id', $id)
                    ->pluck('id')
                    ->toArray();

                    foreach($request->country as $item){
                        $existsCountry = ShippingCountry::where('created_by',auth()->user()->id)->where('country_id', $item)->whereNotIn('id',$country)->first();
                      if($existsCountry){
                        return response()->json(
                            [
                                'http_status_code' => 409,
                                'status' => false,
                                'context' => ['error' => 'Zone already exists'],
                                'timestamp' => Carbon::now(),
                                'message' =>
                                "Shipping zone already exists with ". $existsCountry->country_name . " country",
                            ],
                            409
                        );
                      }
                  
                    }   

                $stateExists = ShippingState::whereIn(
                    's_country_id',
                    $country
                )->delete();
                ShippingCountry::where('zone_id', $id)->delete();

                foreach ($request->country as $item) {
                    $shippingCountry = new ShippingCountry();
                    $shippingCountry->zone_id = $zone->id;
                    $shippingCountry->country_id = $item;
                    $shippingCountry->country_name = Country::find(
                        $item
                    )->country_name;
                    $shippingCountry->created_by = Auth::user()->id;
                    $shippingCountry->save();

                    $states = State::where('country_id', $item)->get();
                    foreach ($states as $state) {
                        $shippingState = new ShippingState();
                        $shippingState->s_country_id = $shippingCountry->id;
                        $shippingState->state_id = $state->id;
                        $shippingState->state_name = $state->state_name;
                        $shippingState->save();
                    }
                }

                if ($shippingCountry) {
                    return response()->json(
                        [
                            'http_status_code' => 200,
                            'status' => true,
                            'context' => ['data' => $zone],
                            'timestamp' => Carbon::now(),
                            'message' => 'Shipping Zone Updated successfully!',
                        ],
                        200
                    );
                } else {
                    return response()->json(
                        [
                            'http_status_code' => 500,
                            'status' => false,
                            'context' => ['error' => 'Something Went Wrong'],
                            'timestamp' => Carbon::now(),
                            'message' =>
                                'Oops! Something went wrong. Please try again later.',
                        ],
                        500
                    );
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

    public function get_all_zone()
    {
        try {
            $zone = ShippingZone::where([
                'created_by' => Auth::user()->id,
                'status' => 1,
            ])->get();
            $state = State::pluck('state_name', 'id');
            $country = Country::pluck('country_name', 'id');
            $carrier_name = Carrier::pluck('name', 'id');

            foreach ($zone as $item) {
                $item->rates = ShippingRate::where('zone_id', $item->id)->get();
                $item->shipping_countries = ShippingCountry::where(
                    'zone_id',
                    $item->id
                )->get(['id', 'country_name', 'country_id']);

                $item->shippingCountryId = ShippingCountry::where(
                    'zone_id',
                    $item->id
                )
                    ->pluck('country_id')
                    ->toArray();

                foreach ($item->shipping_countries as $country) {
                    $country->totalStateCount = State::where(
                        'country_id',
                        $country->country_id
                    )->count();
                    $country->stateCount = ShippingState::where(
                        's_country_id',
                        $country->id
                    )->count();
                }
            }

            return response()->json(
                [
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $zone],
                    'timestamp' => Carbon::now(),
                    'message' => 'Shipping Zone Fetched Successfully',
                ],
                200
            );
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

    public function get_zone_by_id($zone_id)
    {
        try {
            $zone = Zone::with('zone_country.country')
                ->where('id', $zone_id)
                ->first();
            if ($zone) {
                return response()->json([
                    'status' => true,
                    'data' => $zone,
                    'message' => 'Zone fetched successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Date not exists!',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function get_zone_by_vendor_id($vendor_id)
    {
        try {
            $zone = Zone::with('zone_country.country')
                ->where('created_by', $vendor_id)
                ->get();
            if ($zone) {
                return response()->json([
                    'status' => true,
                    'data' => $zone,
                    'message' => 'Zone fetched successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Date not exists!',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function delete_zone($zone_id)
    {
        try {
            $id = $zone_id;
            $delete_country_state = ShippingCountry::where(
                'zone_id',
                $id
            )->get();

            if ($delete_country_state->count() > 0) {
                foreach ($delete_country_state as $item) {
                    ShippingState::where('s_country_id', $item->id)->delete();
                }

                ShippingCountry::where('zone_id', $id)->delete();
            }

            ShippingRate::where('zone_id', $id)->forceDelete();
            $delete_zone = ShippingZone::where('id', $id)->delete();

            if ($delete_zone) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Deleted Successfully',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => 'Something Went Wrong'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Oops! Something went wrong. Please try again later.',
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

    public function shipping_data()
    {
        try {
            $data = ShippingZone::with('shippingRates')->get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Shipping Zone data show successfully',
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
    public function shipping_data_add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'status' => 'required',
                'tax' => 'required',
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
            $created_by = auth()->user()->id;
            $zone = new ShippingZone();
            $zone->name = $request->name;
            $zone->tax_id = $request->tax;
            $zone->country_id = $request->country;
            $zone->state_id = $request->state;
            $zone->created_by = $created_by;
            $zone->status = $request->status;
            $zone->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $zone],
                'timestamp' => Carbon::now(),
                'message' => 'Shipping zone  created successfully',
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
    public function shipping_data_update(Request $request)
    {
        try {
            $created_by = auth()->user()->id;
            $zone = ShippingZone::find($request->input('id'));
            $zone->name = $request->input('name');
            $zone->tax_id = $request->input('tax');
            $zone->country_id = $request->input('country');
            $zone->state_id = $request->input('state');
            $zone->created_by = $created_by;
            $zone->status = $request->input('status');
            $zone->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $zone],
                'timestamp' => Carbon::now(),
                'message' => 'Shippin zone data update successfully',
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
    public function shipping_data_delete(Request $request)
    {
        try {
            $id = $request->id;
            $delete_shipping = ShippingZone::where('id', $id)->delete();
            if ($delete_shipping) {
                $delete_shipping_rates = ShippingRate::where(
                    'zone_id',
                    $id
                )->delete();

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $delete_shipping_rates],
                    'timestamp' => Carbon::now(),
                    'message' => 'Shipping zone data delete successfully',
                ]);
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
    public function shipping_rate_data_add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'zone_id' => 'required',
                'carrier_id' => 'required',
                'status' => 'required',
                'delivery_time' => 'required',
                'minimum_order_weight' => 'required',
                'max_order_weight' => 'required',
                'rate' => 'required',
                'is_free' => 'required',
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
            $created_by = auth()->user()->id;
            $zone = new ShippingRate();
            $zone->name = $request->name;
            $zone->zone_id = $request->zone_id;
            $zone->carrier_id = $request->carrier_id;
            $zone->delivery_time = $request->delivery_time;
            $zone->minimum_order_weight = $request->minimum_order_weight;
            $zone->max_order_weight = $request->max_order_weight;
            $zone->rate = $request->rate;
            $zone->is_free = $request->is_free;
            $zone->created_by = $created_by;
            $zone->updated_by = $created_by;
            $zone->status = $request->status;
            $zone->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $zone],
                'timestamp' => Carbon::now(),
                'message' => 'Shipping rate  created successfully',
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
    public function shipping_rate_update(Request $request)
    {
        try {
            $created_by = auth()->user()->id;
            $zone = ShippingRate::find($request->input('id'));
            $zone->name = $request->input('name');
            $zone->zone_id = $request->input('zone_id');
            $zone->carrier_id = $request->input('carrier_id');
            $zone->delivery_time = $request->input('delivery_time');
            $zone->minimum_order_weight = $request->input(
                'minimum_order_weight'
            );
            $zone->max_order_weight = $request->input('max_order_weight');
            $zone->rate = $request->input('rate');
            $zone->is_free = $request->input('is_free');
            $zone->created_by = $created_by;
            $zone->updated_by = $created_by;
            $zone->status = $request->input('status');
            $zone->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $zone],
                'timestamp' => Carbon::now(),
                'message' => 'Shippin zone data update successfully',
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
    public function shipping_zone_delete(Request $request)
    {
        try {
            $id = $request->id;

            $delete_shipping_rates = ShippingRate::where('id', $id)->delete();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $delete_shipping_rates],
                'timestamp' => Carbon::now(),
                'message' => 'Shipping rate data delete successfully',
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
}
