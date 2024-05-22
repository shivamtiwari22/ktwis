<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\BusinessArea;
use App\Models\Commision;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommisionController extends Controller
{
    public function create()
    {
        $business = BusinessArea::all();
        $countries = Country::all();
        return view(
            'admin.settings.commisions.create',
            compact('business', 'countries')
        );
    }

    public function store(Request $request)
    {
        $rules = [
            'business_area' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
            exit();
        } else {
            $commision = new Commision();
            $data = [
                'business_area_fk_id' => $request->business_area,
                'platform_charges' => $request->platform_charges,
                'transaction_charges' => $request->transaction_charges,
                'total_charges' => $request->total_charges,
                'status' => $request->status,
                'countries' => implode(',', $request->countries),
            ];
            $commision = $commision->insert($data);

            if ($commision) {
                return response()->json([
                    'status' => true,
                    'location' => route('commision.list'),
                    'msg' => 'Commission created successfully!!',
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
        return view('admin.settings.commisions.list');
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

        $total = Commision::select('commisions.*')
            ->join(
                'business_areas',
                'business_areas.id',
                '=',
                'commisions.business_area_fk_id'
            )
            ->Where(function ($query) use ($search) {
                $query->orWhere(
                    'commisions.total_charges',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'commisions.platform_charges',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'commisions.transaction_charges',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'business_areas.name',
                    'like',
                    '%' . $search . '%'
                );

                $searchLower = strtolower($search);
                if (
                    $searchLower === 'off' ||
                    $searchLower === 'of' ||
                    $searchLower === 'o'
                ) {
                    $query->orWhere('commisions.status', 0);
                }
                $searchLower = strtolower($search);
                if ($searchLower === 'on' || $searchLower === 'o') {
                    $query->orWhere('commisions.status', 1);
                }
            });
        $total = $total->count();

        $commision = Commision::select('commisions.*')
            ->join(
                'business_areas',
                'business_areas.id',
                '=',
                'commisions.business_area_fk_id'
            )
            ->Where(function ($query) use ($search) {
                $query->orWhere(
                    'commisions.total_charges',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'commisions.platform_charges',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'commisions.transaction_charges',
                    'like',
                    '%' . $search . '%'
                );
                $query->orWhere(
                    'business_areas.name',
                    'like',
                    '%' . $search . '%'
                );

                $searchLower = strtolower($search);
                if (
                    $searchLower === 'off' ||
                    $searchLower === 'of' ||
                    $searchLower === 'o'
                ) {
                    $query->orWhere('commisions.status', 0);
                }
                $searchLower = strtolower($search);
                if ($searchLower === 'on' || $searchLower === 'o') {
                    $query->orWhere('commisions.status', 1);
                }
            });

        $commision = $commision
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($ofset)
            ->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($commision as $key => $commision) {
            $action =
                '
            <a href="' .
                route('commision.view', $commision->id) .
                '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>
            <a href="' .
                route('commision.edit', $commision->id) .
                '"class="px-2 btn btn-warning text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>
             <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' .
                $commision->id .
                '" data-name="' .
                $commision->currency_name .
                '"><i class="dripicons-trash"></i></button>';

            if ($commision->flag) {
                $currency_flag =
                    '<img src="' .
                    asset(
                        'public/admin/setting/business/flag/' . $commision->flag
                    ) .
                    '" alt="' .
                    $commision->name .
                    '" width="40px">';
            } else {
                $currency_flag = null;
            }

            if ($commision->status == 0) {
                $status =
                    '
            <input type="checkbox" id="switch2_' .
                    $commision->id .
                    '" data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' .
                    $commision->id .
                    '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' .
                    $commision->id .
                    '" my-value="1"></label>
            ';
            } else {
                $status =
                    '
            <input type="checkbox" id="switch2_' .
                    $commision->id .
                    '" checked data-switch="primary" name="status" value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2_' .
                    $commision->id .
                    '" data-on-label="On" data-off-label="Off" class="ChangeStatus"  data-id="' .
                    $commision->id .
                    '" my-value="0"></label>
            ';
            }

            $business_area = BusinessArea::find($commision->business_area_fk_id)
                ->name;
            $data[] = [
                $i + $key,
                $business_area,
                $commision->platform_charges,
                $commision->transaction_charges,
                $commision->total_charges,
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
        $business = Commision::find($request->id);

        $business->status = $business->status == '0' ? '1' : '0';
        $save = $business->save();
        if ($save) {
            return response()->json([
                'status' => true,
                'msg' => 'Status changed successfully',
                'location' => route('commision.list'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error occurred. Please try again',
            ]);
        }
    }

    public function view($id)
    {
        $business = BusinessArea::all();
        $commision = Commision::find($id);
        $countries = Country::all();

        return view(
            'admin.settings.commisions.view',
            compact('business', 'commision', 'countries')
        );
    }

    public function edit($id)
    {
        $business = BusinessArea::all();
        $commision = Commision::find($id);
        $countries = Country::all();
        return view(
            'admin.settings.commisions.edit',
            compact('business', 'commision', 'countries')
        );
    }

    public function update(Request $request)
    {
        $rules = [
            'business_area' => 'required',
            'countries' => 'required',
            'platform_charges' => 'required',
            'transaction_charges' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'message' => $val->errors()->first(),
            ]);
        } else {
            $commision = Commision::find($request->id);
            $commision->business_area_fk_id = $request->business_area;
            $commision->status = $request->status;
            $commision->platform_charges = $request->platform_charges;
            $commision->transaction_charges = $request->transaction_charges;
            $commision->total_charges = $request->total_charges;
            $commision->countries = implode(',', $request->countries);
            if ($commision->save()) {
                return response()->json([
                    'status' => true,
                    'location' => route('commision.list'),
                    'message' => 'Updated successfully!!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong!',
                ]);
            }
        }
    }

    public function delete(Request $request)
    {
        $category = Commision::find($request->id);
        if ($category->delete()) {
            return response()->json([
                'status' => true,
                'location' => route('commision.list'),
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
}
