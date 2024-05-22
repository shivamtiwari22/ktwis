<?php

namespace App\Http\Controllers\Vendor\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Country;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\Zone;
use App\Models\ZoneCountryState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarrierController extends Controller
{
    public function add_new()
    {
        return view('vendor.shipping.carrier.create');
    }

    public function save(Request $request)
    {
        $rules = [
            'name'    => 'required',
            'email' => 'required|email'
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
        } else {
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                $extension = $logoFile->getClientOriginalExtension();
                $logoName = 'logo_' . time() . '.' . $extension;
                $logoFile->move('public/admin/shipping/carriers/logo/', $logoName);
            } else {
                $logoName = null;
            }
            $created_by = auth()->user()->id;
            $carrier = new Carrier();
            $data = [
                'name'          => $request->name,
                'tracking_url'  => $request->url,
                'phone'         => $request->phone,
                'email'         => $request->email,
                'logo'          => $logoName,
                'status'        => $request->status,
                'created_by'    => $created_by,
            ];
            $carrier = $carrier->insert($data);

            if ($carrier) {
                return response()->json(array('status' => true,  'location' => route('vendor.carrier.list'),   'msg' => 'Carrier created successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);

        if ($val->fails()) {
        } else {
            $carrier = Carrier::find($request->id);
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                $extension = $logoFile->getClientOriginalExtension();
                $logoName = 'logo_' . time() . '.' . $extension;
                $logoFile->move('public/admin/shipping/carriers/logo', $logoName);
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
                    'status' => true,
                    'location' => route('vendor.carrier.list'),
                    'message' => 'Carriers Updated Successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong!'
                ]);
            }
        }
    }


    public function list()
    {
        $zones = ShippingZone::all();
        
        return view('vendor.shipping.carrier.list');
    }
    public function list_render(Request $request)
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

        $total =  Carrier::where('created_by',auth()->user()->id)->select('carriers.*')
            ->Where(function ($query) use ($search) {
                $query->orWhere('carriers.name',           'like', '%' . $search . '%');
                $query->orWhere('carriers.tracking_url',           'like', '%' . $search . '%');
                $query->orWhere('carriers.phone',           'like', '%' . $search . '%');
                $query->orWhere('carriers.email',           'like', '%' . $search . '%');
            });
        $total = $total->count();

        $carrier =  Carrier::where('created_by',auth()->user()->id)->select('carriers.*')
            ->Where(function ($query) use ($search) {
                $query->orWhere('carriers.name',           'like', '%' . $search . '%');
                $query->orWhere('carriers.tracking_url',           'like', '%' . $search . '%');
                $query->orWhere('carriers.phone',           'like', '%' . $search . '%');
                $query->orWhere('carriers.email',           'like', '%' . $search . '%');
            });

        $carrier = $carrier->orderBy('id', $orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($carrier as $key => $carrier) {
            $action =
                '<a href="' . route('vendor.carrier.view', $carrier->id) . '"class="px-1 btn btn-primary text-white" id="showClient" data-toggle="tooltip" title="View"><i class="dripicons-preview"></i></a> ' .
                '<a href="' . route('vendor.carrier.edit', $carrier->id) . '"class="px-1 btn btn-warning text-white" id="editClient" data-toggle="tooltip" title="Edit"><i class="dripicons-document-edit"></i></i></a> ' .
                '<button class="  px-1 btn btn-danger deleteType " id="DeleteClient" data-id="' . $carrier->id . '" data-name="' . $carrier->code . '"  data-toggle="tooltip" title="Delete"><i class="dripicons-trash"></i></button>';

            if ($carrier->logo) {
                $logo = '<img src="' . asset('public/admin/shipping/carriers/logo/' . $carrier->logo) . '" alt="' . $carrier->name . '" width="40px">';
            } else {
                $logo = null;
            }



            if ($carrier->status == 0) {
        
            $status = '
            <input type="checkbox" id="switch2_' . $carrier->id . '" data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' . $carrier->id . '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' . $carrier->id . '" my-value="1"></label>
            ';
            
} else {
    $status = '
            <input type="checkbox" id="switch2_' . $carrier->id . '" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' . $carrier->id . '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' . $carrier->id . '" my-value="0"></label>
            ';
}


            $data[] = array(
                $i + $key,
                $carrier->name,
                $carrier->tracking_url,
                $carrier->phone,
                $carrier->email,
                $logo,
                $status,
                $action,
            );
        }
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        echo json_encode($records);
    }
    public function list_status_update(Request $request)
    {

        $carrier = Carrier::find($request->id);

        $carrier->status = ($carrier->status == '0') ? '1' : '0';
        $save = $carrier->save();
        if ($save) {
            return response()->json(['status' => true, 'msg' => "Status changed successfully"]);
        } else {
            return response()->json(['status' => false, 'msg' => "Error occurred. Please try again"]);
        }
    }

    public function delete(Request $request)
    {
        $category = Carrier::find($request->id);
        ShippingRate::where('carrier_id',$request->id)->delete();
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('vendor.carrier.list'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }

    public function edit($id)
    {
        $carrier = Carrier::find($id);
        return view('vendor.shipping.carrier.edit', compact('carrier'));
    }

    public function view($id)
    {
        $carrier = Carrier::find($id);
        return view('vendor.shipping.carrier.view', compact('carrier'));
    }

    public function index_rate()
    {
        return view('vendor.shipping.rate.index');
    }

    public function create_rate()
    {
        $carriers = Carrier::where('status', '1')->get();
        return view('vendor.shipping.rate.create', ['carriers' => $carriers]);
    }

    public function store_rate(Request $request)
    {
      
        $validatedData = $request->validate([
            'name' => 'required|string',
            'carrier' => 'required',
            'delivery_time' => 'required|numeric',
            'max_order' => 'required|numeric',
            'mini_order' => 'required|numeric',
            'rate' => 'required|numeric',
        ]);


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
            return response()->json(['status' => false, 'msg' => 'Rate Exists For Weight '.$overlap->minimum_order_weight.' to ' .$overlap->max_order_weight]);
        }
         
        $user = Auth::user();
        $carrier = new ShippingRate();
        $carrier->name = $validatedData['name'];
        $carrier->carrier_id = $validatedData['carrier'];
        $carrier->zone_id = $request->zone_id;
        $carrier->delivery_time = $validatedData['delivery_time'];
        $carrier->minimum_order_weight = $validatedData['mini_order'];
        $carrier->max_order_weight = $validatedData['max_order'];
        $carrier->rate = $validatedData['rate'];
        $carrier->is_free = $request['free_shipping'];
        $carrier->created_by = $user->id;
        $carrier->updated_by = $user->id;
        $carrier->save();

        if ($carrier) {
            return response()->json(['status' => true, 'location' => route('vendor.shipping.rates'), 'msg' => 'Rate added successfully!']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Something went wrong!']);
        }
    }

    public function list_rate(Request $request)
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

        $total =  ShippingRate::Where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%');
            $query->orWhere('delivery_time', 'like', '%' . $search . '%');
            $query->orWhere('rate', 'like', '%' . $search . '%');
            $query->orWhere('minimum_order_weight', 'like', '%' . $search . '%');
            $query->orWhere('max_order_weight', 'like', '%' . $search . '%');
        });
        $total = $total->count();

        $rates =  ShippingRate::Where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%');
            $query->orWhere('delivery_time', 'like', '%' . $search . '%');
            $query->orWhere('rate', 'like', '%' . $search . '%');
            $query->orWhere('minimum_order_weight', 'like', '%' . $search . '%');
            $query->orWhere('max_order_weight', 'like', '%' . $search . '%');
        });

        $carrier = $rates->orderBy('id', $orderRecord)->limit($limit)->offset($ofset)->get();


        $i = 1 + $ofset;
        $data = [];
        foreach ($carrier as $key => $rate) {
            $action = '<a href="' . route('vendor.shipping.rates.view', $rate->id) . '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a> ' .
                '<a href="' . route('vendor.shipping.rates.edit', $rate->id) . '"class="px-2 btn btn-info text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a> ' .
                '<button class="  px-2 btn btn-danger delete_rate " id="delete_rate" data-id="' . $rate->id . '" data-name="' . $rate->name . '"><i class="dripicons-trash"></i></button>';


            $name = $rate->name;
            $carrier_id = $rate->carrier_id;
            $carrier = Carrier::where('id', $carrier_id)->first();
            $carrier_name = $carrier->name;

            $time = $rate->delivery_time;

            $min = $rate->minimum_order_weight;
            $max = $rate->max_order_weight;
            $order_value = '<span>' . $min . ' kg to  ' . $max . ' kg</span>';

            $rate = $rate->rate;

            $data[] = array(
                $i + $key,
                $name,
                $carrier_name,
                $time,
                $order_value,
                $rate,
                $action,
            );
        }
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        echo json_encode($records);
    }

    public function edit_rate($id)
    {
        $rates = ShippingRate::where('id', $id)->where('status', 'active')->first();
        $carriers = Carrier::where('status', '1')->get();
        return view('vendor.shipping.rate.edit', ['carriers' => $carriers, 'rates' => $rates]);
    }

    public function update_rate(Request $request)
    {

      
        $validatedData = $request->validate([
            'name' => 'required|string',
            'carrier' => 'required|exists:carriers,id',
            'delivery_time' => 'required|numeric',
            'max_order' => 'required|numeric',
            'mini_order' => 'required|numeric',
            'rate' => 'required|numeric',
        ]);


         $zone_id = ShippingRate::find($request->id);
        $overlap = ShippingRate::where('zone_id', $zone_id->zone_id)
     
        ->where(function ($query) use ($request) {
            $query->whereBetween('minimum_order_weight', [$request->mini_order, $request->max_order])
                ->orWhereBetween('max_order_weight', [$request->mini_order, $request->max_order])
                ->orWhere(function ($query) use ($request) {
                    $query->where('minimum_order_weight', '<=', $request->mini_order)
                        ->where('max_order_weight', '>=', $request->max_order);
                });
        })   ->where('id', '!=', $request->id) 
        ->first();
    
            if($overlap){
                return response()->json(['status' => false, 'msg' => 'Rate Exists For Weight '.$overlap->minimum_order_weight.' to ' .$overlap->max_order_weight]);
            }

        $id = $request->id;
        $user = Auth::user();
        $carrier = ShippingRate::findOrFail($id);
        $carrier->name = $validatedData['name'];
        $carrier->carrier_id = $validatedData['carrier'];
        $carrier->delivery_time = $validatedData['delivery_time'];
        $carrier->minimum_order_weight = $validatedData['mini_order'];
        $carrier->max_order_weight = $validatedData['max_order'];
        $carrier->rate = $validatedData['rate'];
        $carrier->is_free = $request['free_shipping'];
        $carrier->updated_by = $user->id;
        $carrier->save();

        if ($carrier) {
            return response()->json(['status' => true, 'location' => route('vendor.shipping.rates'), 'msg' => 'Rate Updated successfully!']);
        } else {
            return response()->json(['status' => false, 'msg' => 'Something went wrong!']);
        }
    }

    public function view_rate($id)
    {
        $rates = ShippingRate::where('id', $id)->where('status', 'active')->first();
        $carriers = Carrier::where('status', '1')->get();
        return view('vendor.shipping.rate.view', ['carriers' => $carriers, 'rates' => $rates]);
    }

    public function delete_rate(Request $request)
    {
        $category = ShippingRate::find($request->id);
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('vendor.shipping.rates'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }

    //////////////////////////////////////////////////////////////

    public function add_zone(Request $request)
    {
        $rules = [
            'name'    => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $created_by = auth()->user()->id;
            $zone = new Zone();
            $data = [
                'zone_name'       => $request->name,
                'created_by'      => $created_by,
                'status'          => $request->status,
            ];
            $zone_id = DB::table('zones')->insertGetId($data);
            if ($zone_id) {
                $countryIds = $request->input('country');

                foreach ($countryIds as $countryId) {
                    $country = Country::with('states')->find($countryId);
                    foreach ($country->states as $state) {
                        $countryState = new ZoneCountryState();
                        $countryState->zone_id = $zone_id;
                        $countryState->country_id = $country->id;
                        $countryState->state_id = $state->id;
                        $countryState->status = 1;
                        $countryState->save();
                    }
                }
                if ($zone) {
                    return response()->json(array('status' => true,  'location' => route('vendor.carrier.list'),   'msg' => 'Currency created successfully!!'));
                } else {
                    return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
                }
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }
}
