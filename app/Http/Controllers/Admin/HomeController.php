<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessArea;
use App\Models\CancelOrderRequest;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Currency;
use App\Models\disputeText;
use App\Models\GlobalSetting;
use App\Models\Language;
use App\Models\Order;
use App\Models\SocialMedia;
use App\Models\State;
use App\Models\SystemSettings;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wishlist;
use Flasher\Laravel\Http\Response;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Validator;

use function Ramsey\Uuid\v1;

class HomeController extends Controller
{
    public function dashboard()
    {
        $order = Order::count();
        $wishlist = Wishlist::whereNotNull('created_by')->get()->groupBy('created_by')->count();
        $cart = Cart::join('users','carts.user_id','=','users.id')->count();
        $cancellation = CancelOrderRequest::join('orders','cancel_order_requests.order_id','=','orders.id')
        ->join('users','orders.user_id','users.id')
        ->join('shops','orders.seller_id','shops.vendor_id')->where('cancel_order_requests.status','NEW')->get()->groupBy('order_id')->count();
        // dd($cancellation);
        return view('admin.dashboard', [
            'order' => $order,
            'wishlist' => $wishlist,
            'cart' => $cart,
            'cancellation'=> $cancellation,
        ]);
    }

    public function profile_edit()
    {
        $user = Auth::user();
        $role = $user->roles->first();
        return view('admin.auth.profile_edit', compact('user', 'role'));
    }

    public function profile_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $user = User::find($request->id);
        $user->name = $request->name;

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();

            $destinationPath = public_path('admin/profile_pic');
            $image->move($destinationPath, $image_name);

            $user->profile_pic = $image_name;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'location' => route('dashboard'),
            'msg' => 'Profile Updated successfully!!',
        ]);
    }

    public function system_setting()
    {
        $business = BusinessArea::all();
        $countries = Country::all();
        $language = Language::all();
        $currency = Currency::all();


        $settings = SystemSettings::where('user_id', Auth::user()->id)->first();

        return view(
            'admin.settings.system_setting',
            compact('business', 'currency', 'language', 'countries', 'settings')
        );
    }

    public function update_system_setting(Request $request)
    {
        $rules = [
            'system_name' => 'required',
            'legal_name' => 'required',
            'email_address' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
        }

        $system = SystemSettings::where('user_id', Auth::user()->id)->first();
        if ($system) {
            $system->system_name = $request->system_name;
            $system->legal_name = $request->legal_name;
            $system->email_address = $request->email_address;
            $system->business_id = $request->business_id;
            $system->country_id = $request->country_id;
            $system->lang_id = $request->lang_id;
            $system->currency_id = $request->currency_id;
            if ($request->brand_logo) {
                $image = $request->file('brand_logo');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/system');
                $image->move($destinationPath, $image_name);

                $system->brand_logo = $image_name;
            }
            if ($request->icon) {
                $image = $request->file('icon');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/system');
                $image->move($destinationPath, $image_name);

                $system->icon = $image_name;
            }

            $system->save();

            return response()->json([
                'message' => 'System Settings Update Successfully',
                'status' => true,
            ]);
        } else {
            $setting = new SystemSettings();
            $setting->system_name = $request->system_name;
            $setting->user_id = Auth::user()->id;
            $setting->legal_name = $request->legal_name;
            $setting->email_address = $request->email_address;
            $setting->business_id = $request->business_id;
            $setting->country_id = $request->country_id;
            $setting->lang_id = $request->lang_id;
            $setting->currency_id = $request->currency_id;
            if ($request->brand_logo) {
                $image = $request->file('brand_logo');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/system');
                $image->move($destinationPath, $image_name);

                $setting->brand_logo = $image_name;
            }
            if ($request->icon) {
                $image = $request->file('icon');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/system');
                $image->move($destinationPath, $image_name);

                $setting->icon = $image_name;
            }

            $setting->save();

            return response()->json([
                'message' => 'System Settings Update Successfully',
                'status' => true,
            ]);
        }
    }

    public function global_setting(){
         $settings = GlobalSetting::first();

         if($settings){
             $social = SocialMedia::where('global_id',$settings->id)->get();
         }
         else {
            $social = null;
         }

        return view(
        'admin.settings.global_setting',
        compact('settings','social')
      );
    }

    public function update_global_setting(Request $request){
        $rules = [
            'google_analytic' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
        }

        $system = GlobalSetting::first();
        if ($system) {
           $system->google_analytic = $request->google_analytic;
            $system->meta_title = $request->meta_title;
            $system->meta_description = $request->meta_description;
            $system->keywords = $request->keywords;
            $system->ogtag = $request->ogtag;
            $system->schema_markup = $request->schema_markup;
            $system->google_tag_manager = $request->google_tag_manager;
            $system->search_console = $request->search_console;

            $system->facebook_pixel = $request->facebook_pixel;
            $system->phone = $request->phone;
            $system->email = $request->email;
            $system->address = $request->address;
            $system->android_link = $request->android_link;
            $system->android_url = $request->android_url;
            $system->iphone_link = $request->iphone_link;
            $system->iphone_url = $request->iphone_url;
            $system->copywrite_text = $request->copywrite_text;
            
            if ($request->logo) {
                $image = $request->file('logo');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/global');
                $image->move($destinationPath, $image_name);

                $system->logo = $image_name;
            }

            if ($request->qr_code) {
                $image = $request->file('qr_code');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/global');
                $image->move($destinationPath, $image_name);
                $system->qr_code = $image_name;
            }
           
            $system->save();

            if($request->media_name){
                SocialMedia::where('global_id',$system->id)->delete();
                $social_count = count($request->media_name);
                for ($a = 0 ; $a < $social_count; $a ++){
                       $media = new SocialMedia();
                       $media->global_id = $system->id;
                       $media->name = $request->media_name[$a];
                       $media->url = $request->url[$a];
                       $media->save();
            }
          
            }
            return response()->json([
                'message' => 'Global Settings Update Successfully',
                'status' => true,
            ]);
        } else {
            $setting = new GlobalSetting();
            $setting->google_analytic = $request->google_analytic;
            $setting->meta_title = $request->meta_title;
            $setting->meta_description = $request->meta_description;
            $setting->keywords = $request->keywords;
            $setting->ogtag = $request->ogtag;
            $setting->schema_markup = $request->schema_markup;
            $setting->google_tag_manager = $request->google_tag_manager;
            $setting->search_console = $request->search_console;

            $setting->facebook_pixel = $request->facebook_pixel;
            $setting->phone = $request->phone;
            $setting->email = $request->email;
            $setting->address = $request->address;
            $setting->android_link = $request->android_link;
            $setting->android_url = $request->android_url;
            $setting->iphone_link = $request->iphone_link;
            $setting->iphone_url = $request->iphone_url;
            $setting->copywrite_text = $request->copywrite_text;
            
            if ($request->logo) {
                $image = $request->file('logo');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/global');
                $image->move($destinationPath, $image_name);

                $setting->logo = $image_name;
            }

            if ($request->qr_code) {
                $image = $request->file('qr_code');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('admin/global');
                $image->move($destinationPath, $image_name);
                $setting->qr_code = $image_name;
            }
           
            $setting->save();

            if($request->media_name){
            $social_count = count($request->media_name);
            for ($a = 0 ; $a < $social_count; $a ++){
                   $media = new SocialMedia();
                   $media->global_id = $setting->id;
                   $media->name = $request->media_name[$a];
                   $media->url = $request->url[$a];
                   $media->save();
            }
        }

            return response()->json([
                'message' => 'Global Settings Update Successfully',
                'status' => true,
            ]);
        }

    }

    public function dispute_text(){
        $dispute = disputeText::first();
        return view(
            'admin.settings.dispute_text',compact('dispute'));
    }

    public function update_dispute_text(Request $request){
        $rules = [
            'dispute_left_text' => 'required',
            'dispute_right_text' => 'required',
        ];

        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'message' => $val->errors()->first(),
            ]);
        }

        $dispute = disputeText::first();

        if(!$dispute){
           $text = disputeText::create($request->all());
        }
        else{
         
          $text =  disputeText::where('id',1)->update([
             'dispute_left_text'=> $request->dispute_left_text,
             'dispute_right_text' => $request->dispute_right_text,
          ]);
        }

        return response()->json([
            'message' => 'Dispute Updated Successfully',
            'status' => true,
        ]);
    }


    public function client_lists(){
        $clients = Client::orderBy('created_at','desc')->get();
        return view(
            'admin.settings.clients',compact('clients'));
    }

    public function addClient(Request $request){
        $rules = [
            'name' => 'required',
            'redirect_url' => 'required',
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $val->errors()->first(),
            ]);
        }

        $client = new Client();
        $client->name = $request->name;
        $client->id = Str::random(36);
        $client->secret  =Str::random(40);
        $client->redirect = $request->redirect_url; // Your callback URL
        $client->personal_access_client = 0; // Set to 0 if not a personal access client
        $client->password_client = $request->status ? 1 : 0; // Set to 1 for password grant client
       $client->revoked = 0;
       $client->save();

       return response()->json([
        'msg' => 'Client Added Successfully',
        'status' => true,
    ]);
    }

    public function client_statusChange(Request $request){
        $zone = Client::where('id',$request->id)->first();
        $zone->password_client =    $zone->password_client == '0' ? '1' : '0';
        $zone->save();
        if ($zone) {
            return response()->json(array('status' => true,  'msg' => 'Status Changed Successfully!'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
        }
    }

    public function client_delete(Request $request){           
          $id = $request->id; 
        $delete = Client::where('id', $id)->delete(); 
        if ($delete) {  // Check for false explicitly
            return response()->json([
                'status' => true,
                'msg' => 'Data delete successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error occurred while deleting data, please try again',
            ]);
        }
  
    }


    public function AllCustomer(){

         // get those customers id's who buys your product
     $customers = User::with([
             'roles' => function ($query) {
                 $query->where('role', 'user');
             },
             'address',
         ])
         ->whereHas('roles', function ($query) {
             $query->where('role', 'user');
         })
         ->get();
     $countries = Country::all();
     $state = State::all();

     foreach ($customers as $customer) {
         $customer->user_address =
             UserAddress::where('user_id', $customer->id)->first() ?? null;
     }

        return view(
            'admin.customer.index',
            compact('customers', 'countries', 'state')
        );
    }


    public function getCustomer(Request $request){
        $customer = User::with([
            'address',
        ])
            ->where('id',$request->customer_id)
        ->first();

        $states = [];
     $customer->user_address = UserAddress::where('user_id', $customer->id)->first() ?? null;
                if($customer->user_address ){
                      $states[] = State::where('country_id',$customer->user_address->country)->get();
                }


     $countries = Country::all();


        return Response()->json([
            'status' => true,
            'data' => $customer,
            'state' => $states,
            'countries' => $countries
        ]);
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


    public function editCustomer(Request $request)
    {

        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address_line1' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first(),
            ]);
        }

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('customer/profile');
            $image->move($destinationPath, $image_name);
        }

        $customer = User::where('id', $request->id)->update([
            'name' => $request->name,
            'nickname' => $request->nick_name,
            'dob' => $request->dob,
            'details' => $request->description,
            'mobile_number' => $request->phone,
            'profile_pic' => $request->profile_pic ? $image_name : '',
        ]);

        $address = UserAddress::find($request->address_id);
        $address->floor_apartment = $request->address_line1;
        $address->address = $request->address_line2;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->country = $request->country;
        $address->zip_code = $request->zip_code;
        $address->save();

        return response()->json([
            'status' => true,
            'data' => $customer,
            'msg' => 'Customer Updated Successfully',
        ]);
    }

    public function customerStatus(Request $request){
          
          $user = User::find($request->id);
          $user->customer_status = $request->status_value;
          $user->save();

          return response()->json([
            'status' => true ,
             'msg' =>   $request->status_value == 1 ? "Customer Active Successfully" : "Customer Inactive Successfully"
          ]);
    }
 
}
