<?php

namespace App\Http\Controllers\Vendor\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\ShippingCountry;
use App\Models\ShippingRate;
use App\Models\ShippingState;
use App\Models\ShippingZone;
use App\Models\State;
use App\Models\Tax;
use App\Models\User;
use App\Models\Zone;
use App\Models\ZoneCountryState;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function add_new()
    {
        $country = Country::all();
        $tax = Tax::all();
        return view('vendor.shipping.zone.create', compact('country', 'tax'));
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->id)->get();
        if ($states) {
            return response()->json([
                'status' => true,
                'data' => $states,
                'msg' => 'States Successfully Fetched',
            ]);
        }
        return response()->json([
            'status' => false,
            'data' => [],
            'msg' => 'Something Went Wrong',
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
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
            foreach($request->country as $item){
                $existsCountry = ShippingCountry::where('created_by',auth()->user()->id)->where('country_id', $item)->first();
              if($existsCountry){
                return response()->json([
                    'status' => false,
                    'msg' => "Shipping zone already exists with ". $existsCountry->country_name . " country",
                ]);
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

            if ($zone) {
                return response()->json([
                    'status' => true,
                    'location' => route('vendor.zones.index'),
                    'msg' => 'Zone created successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Something Went Wrong',
                ]);
            }
        }
    }

    public function index()
    {
        $zone = ShippingZone::where(['created_by'=>Auth::user()->id])->get();
        $state = State::pluck('state_name', 'id');
        $country = Country::pluck('country_name', 'id');
        $carrier_name = Carrier::pluck('name', 'id');


        foreach ($zone as $item) {
            $item->rates = ShippingRate::where('zone_id', $item->id)->get();
            $item->shipping_countries = ShippingCountry::where(
                'zone_id',
                $item->id
            )->get();

            $item->shippingCountryId = ShippingCountry::where('zone_id', $item->id)->pluck('country_id')->toArray(); 
            
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
        $Carriers = Carrier::where('status',1)->where('created_by', Auth::user()->id)->get();
        $countries = Country::all();
        
        return view(
            'vendor.shipping.rate.new_index',
            compact(
                'zone',
                'state',
                'country',
                'Carriers',
                'carrier_name',
                'countries',
            )
        );

        // return view('vendor.shipping.zone.index');
    }

    public function list_zones(Request $request)
    {
        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }
        if (isset($request->length)) {
            $limit = $request->length;
        } else {
            $limit = 10;
        }
        if (isset($request->start)) {
            $ofset = $request->start;
        } else {
            $ofset = 0;
        }
        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = ShippingZone::Where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%');
        });
        $total = $total->count();

        $zone = ShippingZone::Where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%');
        });

        $zones = $zone
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($ofset)
            ->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($zones as $key => $zone) {
            $action =
                '<a href="' .
                route('vendor.zones.view', $zone->id) .
                '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a> ' .
                '<a href="' .
                route('vendor.zones.edit', $zone->id) .
                '"class="px-2 btn btn-warning text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a> ' .
                '<a class=" px-2 btn btn-danger  deleteTypes"  data-bs-toggle="modal" data-bs-target="#exampleModal" id="DeleteClient" data-id="' .
                $zone->id .
                '">
                <i class="dripicons-trash"></i>
                </a>';

            $name = $zone->name;
            $state = State::pluck('state_name', 'id');
            $country = Country::pluck('country_name', 'id');
            $created_by = user::pluck('name', 'id');
            $tax = Tax::pluck('tax_name', 'id');

            $country_name = @$country[$zone->country_id];
            $state_name = @$state[$zone->state_id];
            $tax_name = @$tax[$zone->tax_id];

            if ($zone->status == 0) {
                $status =
                    '
                        <input type="checkbox" id="switch2_' .
                    $zone->id .
                    '" data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
                        <label for="switch2_' .
                    $zone->id .
                    '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' .
                    $zone->id .
                    '" my-value="1"></label>
                        ';
            } else {
                $status =
                    '
                        <input type="checkbox" id="switch2_' .
                    $zone->id .
                    '" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
                        <label for="switch2_' .
                    $zone->id .
                    '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' .
                    $zone->id .
                    '" my-value="0"></label>
                        ';
            }

            $data[] = [
                $i + $key,
                $name,
                $tax_name,
                $country_name,
                $state_name,
                $status,
                $action,
            ];
        }

        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function zone_status_update(Request $request)
    {
        $rules = [];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
            exit();
        } else {
            $zone = ShippingZone::find($request->id);
            $zone->status = $zone->status == '0' ? '1' : '0';
            $zone->save();
            if ($zone) {
                return response()->json([
                    'status' => true,
                    'location' => route('vendor.zones.index'),
                    'msg' => 'Updated successfully!!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Something Went Wrong!',
                ]);
            }
        }
    }

    public function edit($id)
    {
        $zones = ShippingZone::find($id);
        $selectedTaxId = $zones->tax_id ?? null;
        $taxes = Tax::all();

        $selectcountry = $zones->country_id ?? null;
        $country = Country::all();
        $selectstate = $zones->state_id ?? null;
        $state = state::all();

        return view(
            'vendor.shipping.zone.edit',
            compact(
                'zones',
                'taxes',
                'selectedTaxId',
                'country',
                'selectcountry',
                'selectstate',
                'state'
            )
        );
    }
    public function update_zone(Request $request)
    {

        $rules = [
            'name' => 'required',
            'status' => 'required',
            'country' => 'required'
        ];
        
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
        }

        $id = $request->id;
        $zone = ShippingZone::find($id);
        $zone->name = $request->name;
        $zone->status = $request->status;
        $zone->save();
        $country = ShippingCountry::where('zone_id', $id)->pluck('id')->toArray();

        foreach($request->country as $item){
            $existsCountry = ShippingCountry::where('created_by',auth()->user()->id)->where('country_id', $item)->whereNotIn('id',$country)->first();
          if($existsCountry){
            return response()->json([
                'status' => false,
                'msg' => "Shipping zone already exists with ". $existsCountry->country_name . " country",
            ]);
          }
      
        }

        $stateExists = ShippingState::whereIn('s_country_id', $country)->delete();
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
            return response()->json([
                'status' => true,
                'location' => route('vendor.zones.index'),
                'msg' => 'Data Update Successfully',
            ]);

        } else {
            return response()->json([
                'status' => false,
                'msg' =>
                    'Error Occurred while deleting zone, Please try again',
            ]);
        }
    }

    public function shipping_zone_delete(Request $request)
    {
        $id = $request->id;
        $delete_shipping = ShippingZone::where('id', $id)->delete();
        if ($delete_shipping) {
            $delete_shipping_rates = ShippingRate::where(
                'zone_id',
                $id
            )->delete();

            if ($delete_shipping_rates !== false) {
                // Check for false explicitly
                return response()->json([
                    'status' => true,
                    'location' => route('vendor.zones.index'),
                    'msg' => 'Data delete successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'location' => route('vendor.zones.index'),
                    'msg' =>
                        'Error occurred while deleting shipping rate, please try again',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error occurred, please try again',
            ]);
        }
    }

    public function update(Request $request)
    {
        //  return $request->all();
        $id = $request->id;
        $countries = Country::all();
        $zone = ShippingZone::find($id);
        $zoneCountries = ShippingCountry::where('zone_id',$id)->get();
       
        return response()->json([
            'status' => true,
            'msg' => 'Data Update Successfully',
            'data' => [ 'countries' => $countries , 'zone' => $zone , 'zoneCountries' => $zoneCountries] 
        ]);

      
    }

    public function view($id)
    {
        $zone = ShippingZone::findOrFail($id);
        $state = State::pluck('state_name', 'id');
        $country = Country::pluck('country_name', 'id');
        $tax = Tax::pluck('tax_name', 'id');
        $carrier_name = Carrier::pluck('name', 'id');
        $rates = ShippingRate::where('zone_id', $id)->get();
        $Carriers = Carrier::all();
        return view(
            'vendor.shipping.rate.new_index',
            compact(
                'zone',
                'state',
                'country',
                'tax',
                'Carriers',
                'rates',
                'carrier_name'
            )
        );
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $delete_country_state = ShippingCountry::where('zone_id', $id)->get();

        if ($delete_country_state->count() > 0) {
            foreach ($delete_country_state as $item) {
                ShippingState::where('s_country_id', $item->id)->delete();
            }

            ShippingCountry::where('zone_id', $id)->delete();
        }

        ShippingRate::where('zone_id',$id)->forceDelete();
        $delete_zone = ShippingZone::where('id',$id)->delete();

        if ($delete_zone) {
            return response()->json([
                'status' => true,
                'location' => route('vendor.zones.index'),
                'msg' => 'Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }
    public function delete_shipping(Request $request)
    {
        $id = $request->id;

        $delete_zone = ShippingRate::where('id', $id)->delete();

        if ($delete_zone) {
            return response()->json([
                'status' => true,
                'location' => route('vendor.zones.index'),
                'msg' => 'Deleted Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' =>
                    'Error Occurred while deleting country, Please try again',
            ]);
        }
    }


    public function delete_zone_country(Request $request){
        $id = $request->id;
        $delete_country_state = ShippingCountry::where('id', $id)->first();
        if ($delete_country_state) {
                ShippingState::where('s_country_id', $id)->delete();
              $delete =  ShippingCountry::where('id', $id)->delete();
        }

        if ($delete) {
            return response()->json([
                'status' => true,
                'location' => route('vendor.zones.index'),
                'msg' => 'Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }


    public function states_edit(Request $request){
      
     
        $states = ShippingState::where('s_country_id',$request->id)->pluck('state_id')->toArray();

         $states =   array_map('intval', $states);
        $country = ShippingCountry::find($request->id);
        $CurrentStates = State::where('country_id',$country->country_id)->get();
        return response()->json([
            'status' => true,
            'data' =>  $states,
            'country_id' => $request->id,
            'current_states' => $CurrentStates,
            'msg' => 'data fetch successfully',
        ]);
    }


    public function states_update(Request $request){
        $country = ShippingCountry::find($request->country);
          $states = ShippingState::where('s_country_id',$country->id)->delete();

          foreach($request->states as $state){
               $newState = new ShippingState();
               $newState->s_country_id = $country->id;
               $newState->state_id = $state;
               $newState->state_name = State::find($state)->state_name;
               $newState->save();
          }

          return response()->json([
               'status' =>  true,
               'msg' => 'Data Updated Successfully',
          ]);

    }

    public function states_search(Request $request){
         $results = ShippingState::where(function ($queryBuilder) use ($request) {
             $queryBuilder
                 ->where('s_country_id', $request->country_id )
                 ->where(function ($subQueryBuilder) {
                     $query = '%' . request()->input('query') . '%';
                     $subQueryBuilder
                         ->where('state_name', 'like', $query);
                 });
         })->get();
 
         return response()->json([
            'status' => true,
            'data' =>  $results,
            'msg' => 'data fetch successfully',
        ]);
    
    }
}
