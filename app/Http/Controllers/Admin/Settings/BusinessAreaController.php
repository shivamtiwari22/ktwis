<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\BusinessArea;
use App\Models\Commisions;
use App\Models\Country;
use App\Models\Currency;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BusinessAreaController extends Controller
{
    public function add_business_area()
    {
        $currencies = Currency::all();
        $countries = Country::all();
        $state = State::all();
        return view(
            'admin.settings.business_area.create',
            compact('currencies', 'countries')
        );
    }

    public function save_business_area(Request $request)
    {
        $rules = [
            'name' => 'required',
            'full_name' => 'required',
            'iso_code' => 'required',
            'country_id' => 'required',
            'status' => 'required',
            'currency' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
            exit();
        } else {
            if ($request->flag) {
                $flag =
                    'flag' .
                    '_' .
                    $request->name .
                    time() .
                    '.' .
                    request()->flag->getClientOriginalExtension();
                request()->flag->move(
                    public_path('admin/setting/business/flag'),
                    $flag
                );
            } else {
                $flag = null;
            }

            $currency = Currency::where('id', $request->currency)->first();

            $headers = [
                'Accept' => 'application/json',
            ];
            $data = [
                "name" => $currency->currency_name,
                "symbol" => $currency->symbol,
                "code" => $currency->currency_code,
                "country_id" => $request->country_id,
                "is_default" => $request->is_default,
            ];

            $url = app('api_url');
            $response = Http::withHeaders($headers)->post($url.'add_currency',$data);

            $new_business = new BusinessArea();
            $data = [
                'name' => $request->name,
                'full_name' => $request->full_name,
                'iso_code' => $request->iso_code,
                'flag' => $flag,
                'calling_code' => $request->calling_code,
                'Currency_fk_id' => $request->currency,
                'status' => $request->status,
                'country_id' => $request->country_id,
            ];
            $new_business = $new_business->insert($data);

        
            if ($new_business) {
                return response()->json([
                    'status' => true,
                    'location' => route('business.list'),
                    'msg' => 'Currency created successfully!!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Something Went Wrong!',
                ]);
            }
        }
    }

    public function list()
    {
        return view('admin.settings.business_area.list');
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

        $total = BusinessArea::select('business_areas.*')->Where(function (
            $query
        ) use ($search) {
            $query->orWhere(
                'business_areas.full_name',
                'like',
                '%' . $search . '%'
            );
            $query->orWhere(
                'business_areas.iso_code',
                'like',
                '%' . $search . '%'
            );
            $query->orWhere('business_areas.flag', 'like', '%' . $search . '%');
            $query->orWhere(
                'business_areas.calling_code',
                'like',
                '%' . $search . '%'
            );

            $searchLower = strtolower($search);
            if (
                $searchLower === 'off' ||
                $searchLower === 'of' ||
                $searchLower === 'o'
            ) {
                $query->orWhere('business_areas.status', 0);
            }
            $searchLower = strtolower($search);
            if ($searchLower === 'on' || $searchLower === 'o') {
                $query->orWhere('business_areas.status', 1);
            }
        });
        $total = $total->count();

        $business = BusinessArea::select('business_areas.*')->Where(function (
            $query
        ) use ($search) {
            $query->orWhere('business_areas.name', 'like', '%' . $search . '%');
            $query->orWhere(
                'business_areas.full_name',
                'like',
                '%' . $search . '%'
            );
            $query->orWhere(
                'business_areas.iso_code',
                'like',
                '%' . $search . '%'
            );
            $query->orWhere('business_areas.flag', 'like', '%' . $search . '%');
            $query->orWhere(
                'business_areas.calling_code',
                'like',
                '%' . $search . '%'
            );

            $searchLower = strtolower($search);
            if (
                $searchLower === 'off' ||
                $searchLower === 'of' ||
                $searchLower === 'o'
            ) {
                $query->orWhere('business_areas.status', 0);
            }
            $searchLower = strtolower($search);
            if ($searchLower === 'on' || $searchLower === 'o') {
                $query->orWhere('business_areas.status', 1);
            }
        });

        $business = $business
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($ofset)
            ->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($business as $key => $business) {
            $action =
                '<a href="' .
                route('business_area.view', $business->id) .
                '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>
            <a href="' .
                route('business_area.edit', $business->id) .
                '"class="px-2 btn btn-warning text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>
             <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' .
                $business->id .
                '" data-name="' .
                $business->currency_name .
                '"><i class="dripicons-trash"></i></button>';

            if ($business->flag) {
                $currency_flag =
                    '<img src="' .
                    asset(
                        'public/admin/setting/business/flag/' . $business->flag
                    ) .
                    '" alt="' .
                    $business->name .
                    '" width="40px">';
            } else {
                $currency_flag = null;
            }

            if ($business->status == 0) {
                $status =
                    '
            <input type="checkbox" id="switch2_' .
                    $business->id .
                    '" data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' .
                    $business->id .
                    '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' .
                    $business->id .
                    '" my-value="1"></label>
            ';
            } else {
                $status =
                    '
            <input type="checkbox" id="switch2_' .
                    $business->id .
                    '" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' .
                    $business->id .
                    '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' .
                    $business->id .
                    '" my-value="0"></label>
            ';
            }

            $currency = Currency::find($business->Currency_fk_id)
                ->currency_name;

                
            $country =
                Country::find($business->country_id)->country_name ?? null;
            $data[] = [
                $i + $key,
                $business->name,
                $business->full_name,
                $business->iso_code,
                $currency_flag,
                $business->calling_code,
                $country,
                $currency,
                $status,
                $action,
            ];
        }
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        echo json_encode($records);
    }

    public function status_change(Request $request)
    {
        $business = BusinessArea::find($request->id);

        $business->status = $business->status == '0' ? '1' : '0';
        $save = $business->save();
        if ($save) {
            return response()->json([
                'status' => true,
                'msg' => 'Status changed successfully',
                'location' => route('business.list'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error occurred. Please try again',
            ]);
        }
    }

    public function delete(Request $request)
    {
        $category = BusinessArea::find($request->id);
        if ($category->delete()) {
            return response()->json([
                'status' => true,
                'location' => route('business.list'),
                'msg' => 'Deleted Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function view_business_area($id)
    {
        $business = BusinessArea::find($id);
        $currencies = Currency::all();
        return view(
            'admin.settings.business_area.view',
            compact('business', 'currencies')
        );
    }

    public function edit_business_area($id)
    {
        $business = BusinessArea::find($id);
        $currencies = Currency::all();
        $countries = Country::all();
        return view(
            'admin.settings.business_area.edit',
            compact('business', 'currencies', 'countries')
        );
    }

    public function update(Request $request)
    {

        
        $rules = [
            'name' => 'required',
            'full_name' => 'required',
            'iso_code' => 'required',
            'currency' => 'required',
            'status' => 'required',
            'country_id' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
        } else {
            $business = BusinessArea::find($request->id);
            $business->name = $request->name;
            $business->full_name = $request->full_name;
            $business->iso_code = $request->iso_code;

            if ($request->flag) {
                $flag =
                    'flag' .
                    '_' .
                    $request->name .
                    time() .
                    '.' .
                    request()->flag->getClientOriginalExtension();
                request()->flag->move(
                    public_path('admin/setting/business/flag'),
                    $flag
                );
                $business->flag = $flag;
            }
            $business->calling_code = $request->calling_code;
            $business->Currency_fk_id = $request->currency;
            // $business->status = $request->status;
            $business->country_id = $request->country_id;
            $business->save();

            if ($business) {
                return response()->json([
                    'status' => true,
                    'location' => route('business.list'),
                    'message' => 'Business Area Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong!',
                ]);
            }
        }
    }

    public function getState(Request $records)
    {
    }
}
