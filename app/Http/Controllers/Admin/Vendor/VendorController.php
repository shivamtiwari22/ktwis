<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\currencyBalance;
use App\Http\Controllers\Controller;
use App\Mail\VendorPassword;
use App\Mail\verificationMail;
use App\Models\Country;
use App\Models\currencyBalance as ModelsCurrencyBalance;
use App\Models\Shop;
use App\Models\State;
use App\Models\User;
use App\Models\userWallet;
use App\Models\Vendor;
use App\Models\VendorAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function vendor_applications()
    {
        return view('admin.vendor.vendor_application');
    }
    public function vendor_applications_list(Request $request)
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

        $total = User::whereHas('roles', function ($query) {
            $query->where('role', 'vendor');
        })
        ->whereHas('shops', function ($query) {
            $query->where('maintenance_mode', '0');    
        })
        ->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('admin_approval', 'like', '%' . $search . '%');
        })
        ->orWhereHas('vendor', function ($query) use ($search) {
            $query->where('tax_pin', 'like', '%' . $search . '%')
                ->orWhere('tax_type', 'like', '%' . $search . '%');
        })
        ->WhereHas('shops', function ($query) use ($search) {
            $query->where('maintenance_mode', 0);
              
        })
        ->count();
    
        $users = User::with(['roles', 'vendor','shops'])
        ->whereHas('roles', function ($query) {
            $query->where('role', 'vendor');    
        })
        ->whereHas('shops', function ($query) {
            $query->where('maintenance_mode', 0);    
        })
        ->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('admin_approval', 'like', '%' . $search . '%');
        })
        ->orWhereHas('shops', function ($query) use ($search) {
            $query->where('shop_name', 'like', '%' . $search . '%');
        })
        ->whereHas('shops', function ($query) {
            $query->where('maintenance_mode', 0);    
        })
        ->orderBy('id', $orderRecord)
        ->limit($limit)
        ->offset($offset)
        ->get();
        $data = [];

        foreach ($users as $key => $user) {
            $action = '<a href="' . route('admin.vendor.applications.view.application', $user->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showUser"><i class="dripicons-preview"></i></a>'.
                      '<a href="' . route('admin.vendor.applications.edit.application', $user->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showUser"><i class="dripicons-document-edit"></i></a>'.
                      '<a href="' . route('admin.vendor.applications.edit.address', $user->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showUser"><i class="uil-plus"  title="Update Address"  data-toggle="tooltip" data-placement="top" data-original-title="Add address"></i></a>'.
                      '<button class="px-2 btn btn-danger deleteUser" id="DeleteUser" data-id="' . $user->id . '" data-name="' . $user->name . '"><i class="dripicons-trash"></i></button>';


               $avatar =   $user->profile_pic ?     '<img src="' .
               asset(
                   'public/vendor/profile_pic/' .
                       $user->profile_pic
               ) .
               '" alt="Avatar" width="40px">'  : '<img src="https://www.gravatar.com/avatar/f82262222694aaf364eae2a611272f7b?s=30&d=mm 
               " alt="Avatar" width="40px">' ;
            $pending = $user->admin_approval == "pending" ? "selected" : "";
            $publish = $user->admin_approval == "publish" ? "selected" : "";
            $reject = $user->admin_approval == "reject" ? "selected" : "";

            $taxType = $user->vendor ? $user->vendor->tax_type : '';
            $taxPin = $user->vendor ? $user->vendor->tax_pin : '';

            $shop_name = $user->shops ? $user->shops->shop_name  : ''; 

            $status = $user->shops ? $user->shops->status  : 'inactive';


            $approval = '<select class="change_status form-select"   data-id="' . $user->id . '">
            <option value="Pending" ' . $pending . '>Pending</option>
            <option value="publish" ' . $publish . '>Published</option>
            <option value="reject" ' . $reject . '>Rejected</option>
            ';
            $data[] = [
                $offset + $key + 1,
                $avatar,
                $user->name,
                $user->email,
                $shop_name,
               ucwords($status),
                $action,
            ];
        }           

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
    public function vendor_applications_list_update_status(Request $request)
    {
        $rules = [
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $user = User::find($request->application_id);
            $user->admin_approval = $request->status_value;

            $wallet = userWallet::where('user_id',$user->id)->update([
                'status' => 'active'
            ]);
           
          
            if ($user->save()) {
                return response()->json(array('status' => true, 'location' =>  route('admin.vendor.applications'), 'msg' => 'Updated successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }
    public function vendor_applications_list_delete(Request $request){

           $shop =   Shop::where('vendor_id', $request->id)->delete();
           if($shop){
            Shop::where('vendor_id', $request->id)->forceDelete();
           }
         
        userWallet::where('user_id', $request->id)->delete();
        userWallet::where('user_id', $request->id)->forceDelete();
        $delete = User::where('id', $request->id)->delete();

        if ($delete) {
            return response()->json(['status' => true, 'location' =>  route('admin.vendor.applications'), 'msg' => "Deleted Successfully"]);
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }
    public function vendor_view_applications($id)
    {
        $user = User::with('vendor','shops','shopAddress')->find($id);
        return view('admin.vendor.view', compact('user'));
    }
    public function vendor_edit_applications($id){
        $user = User::with('vendor')->find($id);

        $shop = Shop::where('vendor_id',$id)->first() ?? null ;
        return view('admin.vendor.edit', compact('user','shop'));
    }
    public function vendor_rejected_applications(){
        return view('admin.vendor.rejected_vender_applications');
    }
    public function vendor_rejected_applications_list(Request $request)
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

        $total = User::whereHas('roles', function ($query) {    
            $query->where('role', 'vendor')
                  ->where('admin_approval', 'reject');
        })
        ->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('admin_approval', 'like', '%' . $search . '%');
        })
        ->orWhereHas('vendor', function ($query) use ($search) {    
            $query->where('tax_pin', 'like', '%' . $search . '%')
                ->orWhere('tax_type', 'like', '%' . $search . '%');
        }) 
        ->where('admin_approval','reject')
        ->count();
    
    
        $users = User::whereHas('roles', function ($query) {
            $query->where('role', 'vendor')
                  ->where('admin_approval', 'reject');
        })
        ->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('admin_approval', 'like', '%' . $search . '%');
        })
        ->orWhereHas('vendor', function ($query) use ($search) {
            $query->where('tax_pin', 'like', '%' . $search . '%')
                ->orWhere('tax_type', 'like', '%' . $search . '%');
        }) 
        ->where('admin_approval','reject')
        ->orderBy('id', $orderRecord)
        ->limit($limit)
        ->offset($offset)
        ->get();
        $data = [];
        foreach ($users as $key => $user) {
            $action = '<a href="' . route('admin.vendor.applications.view.application', $user->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showUser"><i class="dripicons-preview"></i></a>'.
                      '<a href="' . route('admin.vendor.applications.edit.application', $user->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showUser"><i class="dripicons-document-edit"></i></a>'.
                      '<button class="px-2 btn btn-danger deleteUser" id="DeleteUser" data-id="' . $user->id . '" data-name="' . $user->name . '"><i class="dripicons-trash"></i></button>';


            $pending = $user->admin_approval == "pending" ? "selected" : "";
            $publish = $user->admin_approval == "publish" ? "selected" : "";
            $reject = $user->admin_approval == "reject" ? "selected" : "";

            $taxType = $user->vendor ? $user->vendor->tax_type : '';
            $taxPin = $user->vendor ? $user->vendor->tax_pin : '';

            $approval = '<select class="change_status form-select"   data-id="' . $user->id . '">
            <option value="Pending" ' . $pending . '>Pending</option>
            <option value="publish" ' . $publish . '>Published</option>
            <option value="reject" ' . $reject . '>Rejected</option>
            ';
            $data[] = [
                $offset + $key + 1,
                $user->name,
                $user->email,
                $taxType,
                $taxPin,
                $approval,
                $action,
            ];
        }           

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
    public function vendor_pending_applications(){
        return view('admin.vendor.pending_vender_applications');
    }

    public function vendor_pending_applications_list(Request $request)
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

            $total = User::whereHas('roles', function ($query) {
                $query->where('role', 'vendor')
                      ->where('admin_approval', 'pending');
            })
            ->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('admin_approval', 'like', '%' . $search . '%');
            })
            ->orWhereHas('vendor', function ($query) use ($search) {
                $query->where('tax_pin', 'like', '%' . $search . '%')
                    ->orWhere('tax_type', 'like', '%' . $search . '%');
            }) 
            ->where('admin_approval','pending')
            ->count();
        
        
            $users = User::whereHas('roles', function ($query) {
                $query->where('role', 'vendor')
                      ->where('admin_approval', 'pending');
            })
            ->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('admin_approval', 'like', '%' . $search . '%');
            })
            ->orWhereHas('vendor', function ($query) use ($search) {
                $query->where('tax_pin', 'like', '%' . $search . '%')
                    ->orWhere('tax_type', 'like', '%' . $search . '%');
            }) 
            ->where('admin_approval','pending')
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
            $data = [];
            foreach ($users as $key => $user) {
                $action = '<a href="' . route('admin.vendor.applications.view.application', $user->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showUser"><i class="dripicons-preview"></i></a>'.
                        '<a href="' . route('admin.vendor.applications.edit.application', $user->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showUser"><i class="dripicons-document-edit"></i></a>'.
                        '<button class="px-2 btn btn-danger deleteUser" id="DeleteUser" data-id="' . $user->id . '" data-name="' . $user->name . '"><i class="dripicons-trash"></i></button>';


                $pending = $user->admin_approval == "pending" ? "selected" : "";
                $publish = $user->admin_approval == "publish" ? "selected" : "";
                $reject = $user->admin_approval == "reject" ? "selected" : "";

                $taxType = $user->vendor ? $user->vendor->tax_type : '';
                $taxPin = $user->vendor ? $user->vendor->tax_pin : '';

                $approval = '<select class="change_status form-select"   data-id="' . $user->id . '">
                <option value="Pending" ' . $pending . '>Pending</option>
                <option value="publish" ' . $publish . '>Published</option>
                <option value="reject" ' . $reject . '>Rejected</option>
                ';
                $data[] = [
                    $offset + $key + 1,
                    $user->name,
                    $user->email,
                    $taxType,
                    $taxPin,
                    $approval,
                    $action,
                ];
            }           

            $records['draw'] = intval($request->input('draw'));
            $records['recordsTotal'] = $total;
            $records['recordsFiltered'] = $total;
            $records['data'] = $data;

            echo json_encode($records);
        }
    public function vendor_publish_applications(){
        return view('admin.vendor.publish_vender_applications');
    }
    public function vendor_publish_applications_list(Request $request)
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

        $total = User::whereHas('roles', function ($query) {
            $query->where('role', 'vendor')
                  ->where('admin_approval', 'publish');
        })
        ->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('admin_approval', 'like', '%' . $search . '%');
        })
        ->orWhereHas('vendor', function ($query) use ($search) {
            $query->where('tax_pin', 'like', '%' . $search . '%')
                ->orWhere('tax_type', 'like', '%' . $search . '%');
        }) 
        
        ->where('admin_approval','publish')
        ->count();
    
    
        $users = User::whereHas('roles', function ($query) {
            $query->where('role', 'vendor')
                  ->where('admin_approval', 'publish');
        })
        ->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('admin_approval', 'like', '%' . $search . '%');
        })
        ->orWhereHas('vendor', function ($query) use ($search) {
            $query->where('tax_pin', 'like', '%' . $search . '%')
                ->orWhere('tax_type', 'like', '%' . $search . '%');
        }) 
        ->where('admin_approval','publish')
        ->orderBy('id', $orderRecord)
        ->limit($limit)
        ->offset($offset)
        ->get();
        $data = [];
        foreach ($users as $key => $user) {
            $action = '<a href="' . route('admin.vendor.applications.view.application', $user->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showUser"><i class="dripicons-preview"></i></a>'.
                      '<a href="' . route('admin.vendor.applications.edit.application', $user->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showUser"><i class="dripicons-document-edit"></i></a>'.
                      '<button class="px-2 btn btn-danger deleteUser" id="DeleteUser" data-id="' . $user->id . '" data-name="' . $user->name . '"><i class="dripicons-trash"></i></button>';


            $pending = $user->admin_approval == "pending" ? "selected" : "";
            $publish = $user->admin_approval == "publish" ? "selected" : "";
            $reject = $user->admin_approval == "reject" ? "selected" : "";

            $taxType = $user->vendor ? $user->vendor->tax_type : '';
            $taxPin = $user->vendor ? $user->vendor->tax_pin : '';

            $approval = '<select class="change_status form-select"   data-id="' . $user->id . '">
            <option value="Pending" ' . $pending . '>Pending</option>
            <option value="publish" ' . $publish . '>Published</option>
            <option value="reject" ' . $reject . '>Rejected</option>
            ';
            $data[] = [
                $offset + $key + 1,
                $user->name,
                $user->email,
                $taxType,
                $taxPin,
                $approval,
                $action,
            ];
        }           

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }



    public function vendor_update_applications(Request $request){

        $validatedData = $request->validate([
            'shop_name' => 'required',
            'legal_name' => 'required',
            'email' => 'required|email',
            'timezone' => 'required',
        ]);


        $user = Auth::user();
        $shop = Shop::find($request->shop_id);
        $shop->shop_name = $request->input('shop_name');
        $shop->shop_url = $request->input('shop_url');
        $shop->legal_name = $request->input('legal_name');
        $shop->email = $request->input('email');
        $shop->timezone = $request->input('timezone');
        $shop->description = $request->input('description');
        $shop->status = $request->input('status');
        $shop->updated_by = $user->id;

        if ($request->hasFile('brand_logo')) {
            $image = $request->file('brand_logo');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/shop/brand');
            $image->move($destinationPath, $image_name);
            $shop->brand_logo = $image_name;
        }

        if ($request->hasFile('cover_logo')) {
            $image = $request->file('cover_logo');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/shop/cover');
            $image->move($destinationPath, $image_name);
            $shop->cover_image = $image_name;
        }

        $shop->save();
        if ($shop) {
            return response()->json(array('status' => true,  'location' => route('admin.vendor.applications'),   'msg' => 'Vendor Shop updated successfully!!'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
        }


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


    public function vendor_edit_address($id){
        $user = User::with('vendor')->find($id);
        $shop = Shop::where('vendor_id',$id)->first() ?? null ;
        $shop_address = VendorAddress::where('vendor_id', $id)->first() ?? null;
        $country= Country::all();
        $state = State::all();
        return view('admin.vendor.edit_address',compact('country','state','shop_address','shop'));
    }

    public function vendor_update_address(Request $request){

        
        $validatedData = $request->validate([
            'address_line1' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'state' => 'required',
        ]);

            $address = VendorAddress::where('id',$request->address_id)->first();
            $shop = Shop::find($request->shop_id);
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
                'vendor_id'=> $shop->vendor_id
            ]);
         }

         if($address){
            return response()->json(array('status' => true, 'msg' => 'Shop Address Updated Successfully!', 'location' => route('admin.vendor.applications')));
         }
         else{
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));

         }
    }

    public function add_vendor(Request $request){

         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:16',
            'shop_name' => 'required|unique:shops',
            'legal_name' => 'required',
        ]);

         if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'msg' =>  $validator->errors()->first(),
                ],
                422
            );
        }
        
        $user = new User();
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->userID = 'user'.strval(random_int(1000, 9999));
        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/profile_pic');
            $image->move($destinationPath, $image_name);

            $user->profile_pic = $image_name;
        }
        $user->save();

        $shop = new Shop();
       $shop->shop_name = $request->shop_name;
       $shop->email = $request->email;
       $shop->vendor_id = $user->id;
       $shop->status = $request->status; 
       $shop->maintenance_mode = 0 ;
       $shop->timezone = $request->timezone;
       $shop->legal_name = $request->legal_name;
       $shop->shop_url = $request->shop_url;
       $shop->save();

        $role_id = 2;
        $user->roles()->attach($role_id, ['user_id' => $user->id]);
        $user->roles()->attach(3, ['user_id' => $user->id]);

        $data = [
            "username" => $request->name ,
            "email" => $request->email,
            "password" => $request->password
        ];

         Mail::to($request->email)->send(new VendorPassword($data));

         if($user){
            return response()->json(array('status' => true, 'msg' => 'Vendor Added  Successfully!'));
         }
         else{
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));

         }

    }
}