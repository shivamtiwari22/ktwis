<?php

namespace App\Http\Controllers\Vendor\Setting;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\State;
use App\Models\VendorAddress;
use App\Models\VendorBankDetail;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index_shop()
    {
        return view('vendor.settings.shop.index');
    }

    public function create_shop()
    {
        $country = Country::all();
        $state = State::all();
        $user = Auth::user();
        $shop = Shop::where('vendor_id', $user->id)->first() ?? null;

        $statePluck = state::pluck('state_name','id');
        $countryPluck = country::pluck('country_name','id');

        $shop_address = VendorAddress::where(['shop_id'=> $shop->id , 'vendor_id' => Auth::user()->id])->first() ?? null;
        $bank_detail = VendorBankDetail::where(['shop_id'=> $shop->id , 'vendor_id' => Auth::user()->id])->first() ?? null ;
        return view('vendor.settings.shop.create',compact('shop','country','state','shop_address','bank_detail','countryPluck','statePluck'));
    }

    public function store_shop(Request $request)
    {
            $validatedData = [
                'shop_name' =>  [
                    'required',
                    'min:2',
                    'max:46',
                    Rule::unique('shops')->ignore($request->input('shop_id')),
                ],
                'legal_name' => 'required',
                'email' => 'required|email',
                'timezone' => 'required',
            ];


            $validator = Validator::make($request->all(), $validatedData);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,  
                        'msg' => $validator->errors()->first(),
                    ],   
                );
            }


        $user = Auth::user();
        $shop = Shop::firstOrNew(['id' => $request->input('shop_id')]);
        $shop->shop_name = $request->input('shop_name');
        $shop->shop_url = $request->input('shop_url');
        $shop->legal_name = $request->input('legal_name');
        $shop->email = $request->input('email');
        $shop->timezone = $request->input('timezone');
        $shop->description = $request->input('description');
        $shop->vendor_id = $user->id;
        $shop->created_by = $user->id;
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

        if ($request->hasFile('banner_img')) {
            $image = $request->file('banner_img');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/shop/banner');
            $image->move($destinationPath, $image_name);
            $shop->banner_img = $image_name;
        }

        $shop->save();
        if ($shop) {
            return response()->json(array('status' => true,  'location' => route('vendor.settings.shops.create'),   'msg' => 'Shop updated successfully!!'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
        }
    }


    public function update_maintenance_mode(Request $request){
    
            $zone = Shop::where('vendor_id',Auth::user()->id)->first();
            $zone->maintenance_mode = $request->value;
            $zone->save();
            if ($zone) {
                return response()->json(array('status' => true, 'msg' => 'Action has been done successfully!!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        
    }


    public function address_update(Request $request){

        $validatedData = $request->validate([
            'address_line1' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'state' => 'required',
        ]);

            $address = VendorAddress::where(['shop_id'=> $request->shop_id , 'vendor_id' => Auth::user()->id])->first();
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
                'vendor_id'=> Auth::user()->id
            ]);
         }

         if($address){
            return response()->json(array('status' => true, 'msg' => 'Shop Address Updated Successfully'));

         }
         else{
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));

         }

    }

    public function bank_update(Request $request){

        $validatedData = $request->validate([
            'account_holder_name' => 'required',
            'account_number' => 'required',
            'account_type' => 'required',
            'routing_number' => 'required',
            'bic_code' => 'required',
            'iban_number' => 'required',
            'bank_address' => 'required'
        ]);

            $bank = VendorBankDetail::where(['shop_id'=> $request->shop_id , 'vendor_id' => Auth::user()->id])->first();
         if($bank){
            $bank->update($request->all());
         }
         else{
            $bank =  VendorBankDetail::create($request->all() + ['vendor_id' => Auth::user()->id]);
         }

         if($bank){
            return response()->json(array('status' => true, 'msg' => 'Bank details updated successfully!'));

         }
         else{
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));

         }

    }

}
