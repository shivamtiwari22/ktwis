<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Product;
use App\Models\State;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    public function create()
    {
        $countries = Country::all();
        $states = State::all();

        return view('vendor.settings.tax.create', compact('countries', 'states'));
    }

    public function get_states($countryId)
    {
        $states = State::where('country_id', $countryId)->get();
        return response()->json(['states' => $states]);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'tax_name' => 'required',
            'tax_rate' => 'required|numeric',
            'status' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
        ]);

        $user = Auth::user();
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
            return response()->json(array('status' => true,  'location' => route('vendor.settings.tax.index'),   'msg' => 'Tax stored successfully'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
        }
    }

    public function tax_index()
    {
        $countries = Country::all();
        $states = State::all();

        return view('vendor.settings.tax.index', compact('countries', 'states'));
    }

    public function list_tax(Request $request)
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
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = Tax::where(function ($query) use ($search) {
            $query->orWhere('tax_name', 'like', '%' . $search . '%')
                ->orWhere('tax_rate', 'like', '%' . $search . '%')
                ->orWhereHas('country', function ($query) use ($search) {
                    $query->where('country_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('state', function ($query) use ($search) {
                    $query->where('state_name', 'like', '%' . $search . '%');
                });
        })->with(['country', 'state'])->count();

        $products = Tax::where(function ($query) use ($search) {
            $query->orWhere('tax_name', 'like', '%' . $search . '%')
                ->orWhere('tax_rate', 'like', '%' . $search . '%')
                ->orWhereHas('country', function ($query) use ($search) {
                    $query->where('country_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('state', function ($query) use ($search) {
                    $query->where('state_name', 'like', '%' . $search . '%');
                });
        })
            ->with(['country', 'state'])
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        foreach ($products as $key => $tax) {
            $action = '<a href="' . route('vendor.settings.tax.view', $tax->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>' .
                '<a href="' . route('vendor.settings.tax.edit', $tax->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
                '<button class="px-2 btn btn-danger deletetax" id="delete_tax" data-id="' . $tax->id . '" data-name="' . $tax->name . '"><i class="dripicons-trash"></i></button>';

            $tax_name = $tax->tax_name;

            $tax_rate = $tax->tax_rate;
            $tax_per = '<span>' . $tax_rate . ' % </span>';

            $country_name = $tax->country->country_name;
            $state_name = $tax->state->state_name;

            $region = '<span>' . $state_name . ' :: ' . $country_name . ' </span>';
            $status_tax = $tax->status;

            if ($status_tax == "active") {
                $status = 'Active';
            } else if ($status_tax == "inactive") {
                $status = 'Inactive';
            }

            $data[] = [
                $offset + $key + 1,
                $tax_name,
                $tax_per,
                $region,
                $status,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function edit($id)
    {
        $tax = Tax::where('id', $id)->first();
        $countries = Country::all();
        $states = State::all();

        return view('vendor.settings.tax.edit', compact('countries', 'states', 'tax'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'tax_name' => 'required',
            'tax_rate' => 'required|numeric',
            'status' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
        ]);

        $user = Auth::user();
        $tax = Tax::find($request->id);
        $tax->tax_name = $request->input('tax_name');
        $tax->tax_rate = $request->input('tax_rate');
        $tax->status = $request->input('status');
        $tax->country_id = $request->input('country_id');
        $tax->state_id = $request->input('state_id');
        $tax->updated_by = $user->id;
        $tax->save();

        if ($tax) {
            return response()->json(array('status' => true,  'location' => route('vendor.settings.tax.index'),   'msg' => 'Tax updated successfully'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
        }
    }

    public function view($id)
    {
        $tax = Tax::where('id', $id)->first();
        $countries = Country::all();
        $states = State::all();

        return view('vendor.settings.tax.view', compact('countries', 'states', 'tax'));
    }

    public function delete(Request $request)
    {
        $tax_delete = Tax::where('id', $request->id)->delete();
        if ($tax_delete) {
            return response()->json(['status' => true, 'location' => route('vendor.settings.tax.index'), 'msg' => "Tax Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }
}
