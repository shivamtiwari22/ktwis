<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function add_currency()
    {
        return view('admin.settings.currencies.create');
    }

    public function save_currency(Request $request)
    {
        $rules = [
            'currency_code'    => 'required',
            'currency_name'    => 'required',
            'currency_flag'    => 'mimes:jpeg,jpg,png,gif|max:10000'
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            if ($request->currency_flag) {
                $currency_flag = 'currency_flag' . '_' . $request->currency_name . time() . '.' . request()->currency_flag->getClientOriginalExtension();
                request()->currency_flag->move(public_path('admin/setting/currency/currency_flag'), $currency_flag);
            } else {
                $currency_flag = null;
            }

            $new_currency = new Currency();
            $data = [
                'currency_code'          => $request->currency_code,
                'currency_name'          => $request->currency_name,
                'exchange_rate'          => $request->exchange_rate,
                'symbol'          => $request->symbol,
                'currency_flag'          => $currency_flag,
            ];
            $new_currency = $new_currency->insert($data);

            if ($new_currency) {
                return response()->json(array('status' => true,  'location' => route('currencies.list'),   'msg' => 'Currency created successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }

    public function currency_list()
    {
        return view('admin.settings.currencies.list');
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

        $total =  Currency::select('currencies.*')
            ->Where(function ($query) use ($search) {
                $query->orWhere('currencies.currency_name', 'like', '%' . $search . '%');
                $query->orWhere('currencies.currency_code', 'like', '%' . $search . '%');
                $query->orWhere('currencies.currency_flag', 'like', '%' . $search . '%');
                $query->orWhere('currencies.exchange_rate', 'like', '%' . $search . '%');

                $searchLower = strtolower($search);
                if ($searchLower === 'yes' || $searchLower === 'y' || $searchLower === 'ye') {
                    $query->orWhere('currencies.is_default', 1);
                }
                $searchLower = strtolower($search);
                if ($searchLower === 'no' || $searchLower === 'n') {
                    $query->orWhere('currencies.is_default', 0);
                }
            });
        $total = $total->count();

        $currency =  Currency::select('currencies.*')
            ->Where(function ($query) use ($search) {
                $query->orWhere('currencies.currency_name', 'like', '%' . $search . '%');
                $query->orWhere('currencies.currency_code', 'like', '%' . $search . '%');
                $query->orWhere('currencies.currency_flag', 'like', '%' . $search . '%');
                $query->orWhere('currencies.exchange_rate', 'like', '%' . $search . '%');

                $searchLower = strtolower($search);
                if ($searchLower === 'yes' || $searchLower === 'y' || $searchLower === 'ye') {
                    $query->orWhere('currencies.is_default', 1);
                }
                $searchLower = strtolower($search);
                if ($searchLower === 'no' || $searchLower === 'n') {
                    $query->orWhere('currencies.is_default', 0);
                }
            });

        $currency = $currency->orderBy('id', $orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($currency as $key => $currency) {


                  $edit =   $currency->currency_code != 'USD' ?  '<a href="' . route('currencies.edit', $currency->id) . '"class="px-2 btn btn-info text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a> ' : '';
            
            $action =        $edit   .' 
            <a href="' . route('currencies.view', $currency->id) . '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>
             ';
            //  <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' . $currency->id . '" data-name="' . $currency->currency_name . '"><i class="dripicons-trash"></i></button>

            if ($currency->currency_flag) {
                $currency_flag = '<img src="' . asset('public/admin/setting/currency/currency_flag/' . $currency->currency_flag) . '" alt="' . $currency->currency_name . '" width="40px">';
            } else {
                $currency_flag = null;
            }

            if ($currency->is_default) {
                $default = "Yes";
            } else {
                $default = "No";
            }


            if ($currency->status == 0) {
                $status = '
                        <input type="checkbox" id="switch2_' . $currency->id . '" data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
                        <label for="switch2_' . $currency->id . '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' . $currency->id . '"></label>
                        ';
            } else {
                $status = '
                        <input type="checkbox" id="switch2_' . $currency->id . '" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
                        <label for="switch2_' . $currency->id . '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' . $currency->id . '"></label>
                        ';
            }

            $data[] = array(
                $i + $key,
                $currency->currency_name,
                $currency->currency_code,
                $currency_flag,
                $currency->symbol,
                $status,
                $action,
            );
        }
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        echo json_encode($records);
    }

    public function edit($id)
    {
        $currency = Currency::find($id);
        return view('admin.settings.currencies.edit', compact('currency'));
    }

    public function view($id)
    {
        $currency = Currency::find($id);
        return view('admin.settings.currencies.view', compact('currency'));
    }

    public function update(Request $request)
    {
        $rules = [
            'currency_code'    => 'required',
            'currency_name'    => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
        } else {
            $currency = Currency::find($request->id);
            if ($request->currency_flag) {
                $currency_flag = 'currency_flag' . '_' . $request->currency_name . time() . '.' . request()->currency_flag->getClientOriginalExtension();
                request()->currency_flag->move(public_path('admin/setting/currency/currency_flag'), $currency_flag);
            } else {
                $currency_flag = $currency->currency_flag;
            }
            $currency->currency_name = $request->currency_name;
            $currency->currency_code = $request->currency_code;
            $currency->currency_flag = $currency_flag;
            $currency->exchange_rate = $request->exchange_rate;
            $currency->symbol = $request->symbol;
            if ($currency->save()) {
                return response()->json(array('status' => true, 'location' =>  route('currencies.list'), 'message' => 'Currency Updated Successfully'));
            } else {
                return response()->json(array('status' => false, 'message' => 'Something Went Wrong!'));
            }
        }
    }

    public function delete(Request $request)
    {
        $category = Currency::find($request->id);
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('currencies.list'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }



    public function currency_status_update(Request $request)
    {
        $rules = [
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $zone = Currency::find($request->id);
            $zone->status = $zone->status == 0 ? 1 : 0;
            $zone->save();
            if ($zone) {
                return response()->json(array('status' => true, 'msg' => 'Status updated successfully'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }
}
