<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function add_coupon()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        return view('vendor.coupon.addNew',['user' => $user,'roles' => $roles]);
    }
    public function save_coupon(Request $request)
    {
        $rules = [
            'code'           => 'required|unique:vendor_coupons',
            'amount'         => 'required',
            'coupon_type'    => 'required',
            'no_of_coupons'  => 'required',
            
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $new_Content_type = new VendorCoupon();
            $data = [
                'code'             => $request->code,
                'coupon_type'      => $request->coupon_type,
                'amount'           => $request->amount,
                'expiry_date'      => $request->expiry_date,
                'no_of_coupons'    => $request->no_of_coupons,
                'used_coupons'     => 0,
                'created_by'       => Auth::id(),
            ];
            $new_Content_type = $new_Content_type->insert($data);

            if ($new_Content_type) {
                return response()->json(array('status' => true,  'location' => route('vendor.coupon.list'),   'msg' => 'Coupon created successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }
    public function coupon_list()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        return view('vendor.coupon.list',['user' => $user,'roles' => $roles]);
    }
    public function coupon_list_render(Request $request)
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
       
        $total =  VendorCoupon::select('vendor_coupons.*')->where('vendor_coupons.created_by',Auth::user()->id)
                ->Where(function ($query) use ($search) {
                        $query->orWhere('vendor_coupons.code',           'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.coupon_type',    'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.amount',         'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.expiry_date',    'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.no_of_coupons',  'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.status',         'like', '%' . $search . '%');
                    });
                    $total = $total->count();

        $coupons =  VendorCoupon::select('vendor_coupons.*')->where('vendor_coupons.created_by',Auth::user()->id)
                ->Where(function ($query) use ($search) {
                    $query->orWhere('vendor_coupons.code',           'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.coupon_type',    'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.amount',         'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.expiry_date',    'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.no_of_coupons',  'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.status',         'like', '%' . $search . '%');
                    });
             
        $coupons = $coupons->orderBy('id',$orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($coupons as $key => $coupons) {
            $action = 
            '<a href="' . route('vendor.coupon.view', $coupons->id) . '"class="px-2 btn btn-primary text-white btn-sml " id="showClient" title="View" data-toggle="tooltip"><i class="dripicons-preview"></i></a>'.
            '<a href="' . route('vendor.coupon.edit', $coupons->id) . '"class="px-2 btn btn-warning text-white btn-sml " id="editClient" title="Edit" data-toggle="tooltip"><i class="dripicons-document-edit"></i></i></a>'.
             '<button class="  px-2 btn btn-danger deleteType btn-sml " id="DeleteClient" data-id="' . $coupons->id . '" data-name="' . $coupons->code . '" title="Delete" data-toggle="tooltip"><i class="dripicons-trash"></i></button>';

             $pending = $coupons->status == "pending" ? "selected" : "";
             $publish = $coupons->status == "published" ? "selected" : "";

             $status = '<select class="change_status"   data-id="' . $coupons->id . '">
             <option value="pending" ' . $pending . '>Pending</option>
             <option value="published" ' . $publish . '>Publish</option>
             ';

            $data[] = array(
                $i + $key,
                $coupons->code,
                ucwords($coupons->coupon_type),
                $coupons->amount,
                $coupons->expiry_date,
                $coupons->no_of_coupons,
                $coupons->used_coupons,
                $status,
                $action,
            );
        }
        $records['recordsTotal'] =$total;
        $records['recordsFiltered'] =$total;
        $records['data'] = $data;
        echo json_encode($records);
    }
    public function coupons_list_status_update(Request $request)
    {
        // return $request; 
        $rules = [
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $coupon = VendorCoupon::find($request->coupon_id);
            $coupon->status = $request->status_value;
            if ($coupon->save()) {
                return response()->json(array('status' => true, 'location' =>  route('vendor.coupon.list'), 'msg' => 'Updated successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }
    public function edit_coupon($id)
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        $coupon = VendorCoupon::find($id);
        return view('vendor.coupon.edit',['user' => $user,'roles' => $roles], compact('coupon')); 
    }
    public function update_coupon(Request $request)
    {
        $rules = [
            'code' => 'required',
            'amount' => 'required',
            'no_of_coupons' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
        } else {
            $coupon = VendorCoupon::find($request->coupon_id);
            $coupon->code = $request->code;
            $coupon->coupon_type = $request->coupon_type;
            $coupon->amount = $request->amount;
            $coupon->expiry_date = $request->expiry_date;
            $coupon->no_of_coupons = $request->no_of_coupons;
            $coupon->status = $request->status;
            if ($coupon->save()) {
                return response()->json(array('status' => true, 'location' =>  route('vendor.coupon.list'), 'message' => 'Updated successfully!!'));
            } else {
                return response()->json(array('status' => false, 'message' => 'Something Went Wrong!'));
            }
        }
    }
    public function view_coupon($id)
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        $coupon = VendorCoupon::find($id);
        return view('vendor.coupon.view',['user' => $user,'roles' => $roles], compact('coupon')); 
    }

    public function pending_coupon_list()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        return view('vendor.coupon.pendingCoupon',['user' => $user,'roles' => $roles]);
    }
    public function pending_coupon_list_render(Request $request)
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
       
        $total =  VendorCoupon::select('vendor_coupons.*')
                ->where('status', 'pending')
                ->Where(function ($query) use ($search) {
                        $query->orWhere('vendor_coupons.code',           'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.coupon_type',    'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.amount',         'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.expiry_date',    'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.no_of_coupons',  'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.status',         'like', '%' . $search . '%');
                    });
                    $total = $total->count();

        $coupons =  VendorCoupon::select('vendor_coupons.*')
                ->where('status', 'pending')
                ->Where(function ($query) use ($search) {
                    $query->orWhere('vendor_coupons.code',           'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.coupon_type',    'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.amount',         'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.expiry_date',    'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.no_of_coupons',  'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.status',         'like', '%' . $search . '%');
                    });
             
        $coupons = $coupons->orderBy('id',$orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($coupons as $key => $coupons) {
            $action = '<a href="' . route('vendor.coupon.edit', $coupons->id) . '"class="px-2 btn btn-info text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>'.
            '<a href="' . route('vendor.coupon.view', $coupons->id) . '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>'.
             '<button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' . $coupons->id . '"><i class="dripicons-trash"></i></button>';

             $pending = $coupons->status == "pending" ? "selected" : "";
             $publish = $coupons->status == "published" ? "selected" : "";

             $status = '<select class="change_status form-select"   data-id="' . $coupons->id . '">
             <option value="pending" ' . $pending . '>Pending</option>
             <option value="published" ' . $publish . '>Publish</option>
             ';

            $data[] = array(
                $i + $key,
                $coupons->code,
                $coupons->coupon_type,
                $coupons->amount,
                $coupons->expiry_date,
                $coupons->no_of_coupons,
                $status,
                $action,
            );
        }
        $records['recordsTotal'] =$total;
        $records['recordsFiltered'] =$total;
        $records['data'] = $data;
        echo json_encode($records);
    }

    public function published_coupon_list()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        return view('vendor.coupon.publishedCoupon',['user' => $user,'roles' => $roles]);
    }
    public function published_coupon_list_render(Request $request)
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
       
        $total =  VendorCoupon::select('vendor_coupons.*')
                ->where('status', 'published')
                ->Where(function ($query) use ($search) {
                        $query->orWhere('vendor_coupons.code',           'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.coupon_type',    'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.amount',         'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.expiry_date',    'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.no_of_coupons',  'like', '%' . $search . '%');
                        $query->orWhere('vendor_coupons.status',         'like', '%' . $search . '%');
                    });
                    $total = $total->count();

        $coupons =  VendorCoupon::select('vendor_coupons.*')
                ->where('status', 'published')
                ->Where(function ($query) use ($search) {
                    $query->orWhere('vendor_coupons.code',           'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.coupon_type',    'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.amount',         'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.expiry_date',    'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.no_of_coupons',  'like', '%' . $search . '%');
                    $query->orWhere('vendor_coupons.status',         'like', '%' . $search . '%');
                    });
             
        $coupons = $coupons->orderBy('id',$orderRecord)->limit($limit)->offset($ofset)->get();

        $i = 1 + $ofset;
        $data = [];
        foreach ($coupons as $key => $coupons) {
            $action = '<a href="' . route('vendor.coupon.edit', $coupons->id) . '"class="px-2 btn btn-info text-white" id="editClient"><i class="dripicons-document-edit"></i></i></a>'.
            '<a href="' . route('vendor.coupon.view', $coupons->id) . '"class="px-2 btn btn-primary text-white" id="showClient"><i class="dripicons-preview"></i></a>'.
             '<button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' . $coupons->id . '"><i class="dripicons-trash"></i></button>';

             $pending = $coupons->status == "pending" ? "selected" : "";
             $publish = $coupons->status == "published" ? "selected" : "";

             $status = '<select class="change_status form-select"   data-id="' . $coupons->id . '">
             <option value="pending" ' . $pending . '>Pending</option>
             <option value="published" ' . $publish . '>Publish</option>
             ';

            $data[] = array(
                $i + $key,
                $coupons->code,
                $coupons->coupon_type,
                $coupons->amount,
                $coupons->expiry_date,
                $coupons->no_of_coupons,
                $status,
                $action,
            );
        }
        $records['recordsTotal'] =$total;
        $records['recordsFiltered'] =$total;
        $records['data'] = $data;
        echo json_encode($records);
    }
    public function delete_coupon(Request $request)
    {
        $record = VendorCoupon::findOrFail($request->id);
        if ($record->delete()) {
            return response()->json(['status' => true, 'location' =>  route('vendor.coupon.list'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }
}
