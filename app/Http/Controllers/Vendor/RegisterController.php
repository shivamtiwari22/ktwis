<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\verificationMail;
use App\Models\BusinessArea;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\currencyBalance;
use App\Models\Order;
use App\Models\Pages;
use Twilio\Rest\Client as RestClient;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\userWallet;
use App\Models\Vendor;
use App\Models\VendorCoupon;
use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('role_id', 1);
        })->first();
        if ($user) {
            $userName = ucfirst($user->name);
        } else {
            $userName = 'Ktwis';
        }
        $condition = Pages::where([
            'type' => 'temrs_for_merchants',
            'status' => 'active',
        ])->first();
        if (!$condition) {
            $condition = null;
        }

        $areas = BusinessArea::where('status', 1)->get();
        return view('vendor.auth.register', [
            'userName' => $userName,
            'condition' => $condition,
            'areas' => $areas
        ]);
    }

    public function register_store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'mobile_number' => 'required|min:7|max:15|unique:users',
            'shop_name' => 'required|unique:shops',
            'password' => 'required|confirmed|min:6|max:16',
            'country_code' => 'required'
        ]);

        $otp = strval(random_int(1000, 9999));
        try {
            $message = "Your One Time Password for verification is: ". $otp .". Please enter this code to verify your identity. Do not share this code with anyone";
            $number = $request->country_code.$validatedData['mobile_number'];
            sms($number, $message);
        }
        catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Your phone number is invalid",
            ]);
        }
    

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->mobile_number = $validatedData['mobile_number'];
        $user->password = Hash::make($validatedData['password']);
        $user->country_code = $request->country_code;
        $user->is_verified = 0;
        $user->userID = 'user'.strval(random_int(1000, 9999));
        $user->save();

        $shop = new Shop();
        $shop->shop_name = $request->shop_name;
        $shop->email = $request->email;
        $shop->vendor_id = $user->id;
        $shop->status = 'inactive';
        $shop->save();
        

        $update = User::where('id',$user->id)->update([
            'otp' => $otp,
            'otp_created_at' => Carbon::now()
        ]);
        
        $role_id = 2;
        $user->roles()->attach($role_id, ['user_id' => $user->id]);
        $user->roles()->attach(3, ['user_id' => $user->id]);
        
   
        Mail::to($user->email)->send(new verificationMail($user->id));
        Session::put('email', $user->email);

     
        $area = BusinessArea::where('calling_code',$request->country_code)->first();
        if($area){
            $currency = Currency::where('id',$area->Currency_fk_id)->first()->currency_code ?? null;
        }

        $headers = [
            'Accept' => 'application/json',
            'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU'
        ];
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'c_password' => $request->password,
            'contact_number' => $request->mobile_number,
            'country_code' => $request->country_code,
            'currency_code' => $currency ,
             'country_id' => $area->country_id ,
            'shop_name' => $request->shop_name ,
            'userID' => $user->userID
        ];
        $url = app('api_url');
        $response = Http::withHeaders($headers)->post($url .'register', $data);

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor registered successfully',
                'location' => route('vendor.verification'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Something Went Wrong",
            ]);
        }
    }

    public function terms()
    {
        $term = Pages::where('type', 'Terms & Conditions For Merchants')
            ->where('status', 'active')
            ->first();

        if (!$term) {
            $term = null;
        }

        return view('vendor.auth.terms_condition', compact('term'));
    }

    public function login()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('role_id', 1);
        })->first();
        if ($user) {
            $userName = ucfirst($user->name);
        } else {
            $userName = 'Ktwis';
        }
        return view('vendor.auth.login', ['userName' => $userName]);
    }

    public function login_vendor(Request $request)
    {
            $user = User::where(function ($query) use ($request) {
                $query
                    ->where('email',$request->input('email'))
                    ->orWhere('mobile_number', $request->input('email'));
            })
                ->whereHas('roles', function ($query) {
                    $query->where('role', 'vendor');
                })
                ->first();

        if ($user) {
            $email = $user->email;
            $password = $request->input('password');
            $credentials = [
                'email' => $email,
                'password' => $password,
            ];

            if (Hash::check($request->password, $user->password)) {
                if($user->is_verified == '0' ){

                    $otp = strval(random_int(1000, 9999));
                    $user->otp = $otp;
                    $user->save();

                    $message = "Your One Time Password for verification is: ". $otp .". Please enter this code to verify your identity. Do not share this code with anyone";
                    $number = $user->country_code.$user->mobile_number ;
                    sms($number, $message);

                    Session::put('email', $user->email);
                    return response()->json([
                        'success' => true,
                        'message' => 'Your Account is not Verified.',
                        'error' => 'Not Verified'
                    ]);
                }
                else{
                    Auth::attempt($credentials);
                    if ($request->remember_me && $request->remember_me == 'true') {
                        setcookie('email', $request->email, time() + 3600);
                        setcookie('password', $request->password, time() + 3600);
                    } else {
                        setcookie('email', '');
                        setcookie('password', '');
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully logged in.',
                    ]);
                }
               
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ]);
        }
    }

    public function dashboard()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        //customer

        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
          
        $userCount = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)
            ->get()
            ->count();
         
        $currentMonth = date('m');
        $previousMonth = date('m', strtotime('-1 month'));
        $currentYear = date('Y');
        $previousYear = date('Y', strtotime('-1 month'));
        $currentWeekStart = now()->startOfWeek();
        $currentWeekEnd = now()->endOfWeek();
        $previousWeekStart = now()
            ->subWeek()
            ->startOfWeek();
        $previousWeekEnd = now()
            ->subWeek()
            ->endOfWeek();
        $currentDate = now()->toDateString();

        $currentCount = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get()
            ->count();

        $previousCount = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)
            ->whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->get()
            ->count();

        //  dd($previousCount);

        if($previousCount > 0  ){
            $percentageIncreaseUser =
            (($currentCount - $previousCount) / $previousCount) * 100;

        }
        else {
            $percentageIncreaseUser = 0;
        }
  

        if($previousCount > 0){
        $percentageDecreaseUser =
            (($previousCount - $currentCount) / $previousCount) * 100;
        }
        else {
            $percentageDecreaseUser = 0;
        }

        //order
        $orderCount = Order::join(
            'order_items',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->where('users.id', $user->id)
            ->count();

        $currentOrderCount = Order::join(
            'order_items',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->where('users.id', $user->id)
            ->whereMonth('orders.created_at', $currentMonth)
            ->whereYear('orders.created_at', $currentYear)
            ->count();

        $previousOrderCount = Order::join(
            'order_items',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->where('users.id', $user->id)
            ->whereMonth('orders.created_at', $previousMonth)
            ->whereYear('orders.created_at', $previousYear)
            ->count();

        $percentageIncreaseOrder =
            $previousOrderCount != 0
                ? (($currentOrderCount - $previousOrderCount) /
                        $previousOrderCount) *
                    100
                : 0;
        $percentageDecreaseOrder =
            $previousOrderCount != 0
                ? (($previousOrderCount - $currentOrderCount) /
                        $previousOrderCount) *
                    100
                : 0;

        //products
        $productCount = Product::where('created_by', $user->id)->count();

        $currentProductCount = Product::where('created_by', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousProductCount = Product::where('created_by', $user->id)
            ->whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count();

        $percentageIncreaseProduct =
            $previousProductCount != 0
                ? (($currentProductCount - $previousProductCount) /
                        $previousProductCount) *
                    100
                : 0;
        $percentageDecreaseProduct =
            $previousProductCount != 0
                ? (($previousProductCount - $currentProductCount) /
                        $previousProductCount) *
                    100
                : 0;

        //coupons
        $couponCount = VendorCoupon::where('created_by', $user->id)->count();

        $currentCouponCount = VendorCoupon::where('created_by', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousCouponCount = VendorCoupon::where('created_by', $user->id)
            ->whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count();

        $percentageIncreaseCoupon =
            $previousCouponCount != 0
                ? (($currentCouponCount - $previousCouponCount) /
                        $previousCouponCount) *
                    100
                : 0;
        $percentageDecreaseCoupon =
            $previousCouponCount != 0
                ? (($previousCouponCount - $currentCouponCount) /
                        $previousCouponCount) *
                    100
                : 0;

        //revenue
        $revenueAmount = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->sum('orders.total_amount');

        $currentrevenueAmount = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereMonth('orders.created_at', $currentMonth)
            ->whereYear('orders.created_at', $currentYear)
            ->sum('orders.total_amount');

        $previousrevenueAmount = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereMonth('orders.created_at', $previousMonth)
            ->whereYear('orders.created_at', $previousYear)
            ->sum('orders.total_amount');

        $percentageIncreaseRevenue =
            $previousrevenueAmount != 0
                ? (($currentrevenueAmount - $previousrevenueAmount) /
                        $previousrevenueAmount) *
                    100
                : 0;
        $percentageDecreaseRevenue =
            $previousrevenueAmount != 0
                ? (($previousrevenueAmount - $currentrevenueAmount) /
                        $previousrevenueAmount) *
                    100
                : 0;

        //revenure_week
        $currentweekrevenueAmount = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereBetween('orders.created_at', [
                $currentWeekStart,
                $currentWeekEnd,
            ])
            ->sum('orders.total_amount');

        $previousweekrevenueAmount = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereBetween('orders.created_at', [
                $previousWeekStart,
                $previousWeekEnd,
            ])
            ->sum('orders.total_amount');

        $dayRevenueAmount = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereDate('orders.created_at', $currentDate)
            ->sum('orders.total_amount');

        $topSellingProducts = OrderItem::join(
            'orders',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->with('product.inventory', 'product.inventoryVariants.variants')
            ->where('users.id', $user->id)
            ->groupBy(
                'order_items.product_id',
                'order_items.variant_id',
                'order_items.quantity',
                'order_items.total_amount'
            )
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->select(
                'order_items.product_id',
                'order_items.variant_id',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total_amount) as total_sub_total')
            )
            ->get();

        return view('vendor.dashboard', [
            'user' => $user,
            'userCount' => $userCount,
            'currentCount' => $currentCount,
            'previousCount' => $previousCount,
            'percentageIncreaseUser' => $percentageIncreaseUser,
            'percentageDecreaseUser' => $percentageDecreaseUser,
            'orderCount' => $orderCount,
            'currentOrderCount' => $currentOrderCount,
            'previousOrderCount' => $previousOrderCount,
            'previousOrderCount' => $previousOrderCount,
            'percentageIncreaseOrder' => $percentageIncreaseOrder,
            'percentageDecreaseOrder' => $percentageDecreaseOrder,
            'productCount' => $productCount,
            'currentProductCount' => $currentProductCount,
            'previousProductCount' => $previousProductCount,
            'percentageIncreaseProduct' => $percentageIncreaseProduct,
            'percentageDecreaseProduct' => $percentageDecreaseProduct,
            'couponCount' => $couponCount,
            'currentCouponCount' => $currentCouponCount,
            'previousCouponCount' => $previousCouponCount,
            'percentageIncreaseCoupon' => $percentageIncreaseCoupon,
            'percentageDecreaseCoupon' => $percentageDecreaseCoupon,
            'revenueAmount' => $revenueAmount,
            'currentrevenueAmount' => $currentrevenueAmount,
            'previousrevenueAmount' => $previousrevenueAmount,
            'percentageIncreaseRevenue' => $percentageIncreaseRevenue,
            'percentageDecreaseRevenue' => $percentageDecreaseRevenue,
            'currentweekrevenueAmount' => $currentweekrevenueAmount,
            'previousweekrevenueAmount' => $previousweekrevenueAmount,
            'dayRevenueAmount' => $dayRevenueAmount,
            'topSellingProducts' => $topSellingProducts,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        return view('vendor.auth.profile', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update_profile(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_number' => [
                'required',
                Rule::unique('users')->ignore($request->user_id),
            ],
            'dob' => 'required',
            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000'
        ]);

        $customMessages = [
            'profile_pic.max' => 'The profile picture must not be larger than 2 MB.',
        ];
        $validator->setCustomMessages($customMessages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => $validator->errors()->first(),
                ],
                409
            );
        }

        $userId = $request->user_id;
        $user = User::with('vendor')->findOrFail($userId);

        // Update the user and vendor data
        $user->name = $request->name;
        $user->mobile_number = $request->mobile_number;
        $user->dob = $request->dob;

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

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'mobile_number' => $user->mobile_number,
            'dob' => $user->dob,
             'status' => true,
             'msg'=> "Profile Updated Successfully"
        ]);
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();

        return redirect()
            ->route('vendor.login')
            ->with('success', 'Logout Successfully');
    }

    public function switchLang($lang)
    {
        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('applocale', $lang);
        }
        return back();
    }

    public function forgot_password()
    {
        return view('vendor.auth.forgot_password');
    }

    public function send_mail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $isAdmin = $user
                ->roles()
                ->where('role', 'vendor')
                ->exists();
            if ($isAdmin) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/reset-password?token=' . $token;

                $otp = strval(random_int(1000, 9999));
                $data['otp'] = $otp;
                $data['email'] = $request->email;
                $data['title'] = 'OTP Verification';
                $data['body'] = 'Your OTP is: ' . $otp;

                Mail::send(
                    'email.forgotPasswordMail',
                    ['data' => $data],
                    function ($message) use ($data) {
                        $message
                            ->from('mail@dilamsys.com', 'Ktwis')
                            ->to($data['email'])
                            ->subject($data['body']);
                    }
                );
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                $user = User::where('email', $request->email)->first();
                $user->otp = $otp;
                // $user->otp_created_at = Carbon::now();
                $user->save();

                $location = route('vendor.otp', ['email' => $user->email]);
                return response()->json([
                    'status' => true,
                    'location' => $location,
                    'msg' => 'OTP Sent Successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Vendor not found!',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Invalid Email!',
            ]);
        }
    }

    public function otp($email)
    {
        $user = User::where('email', $email)->first();
        $otp_created_at = $user->otp_created_at;
        return view('vendor.auth.verify_otp', [
            'email' => $email,
            'otp_created_at' => $otp_created_at,
        ]);
    }

    public function otp_verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp1' => 'required',
            'otp2' => 'required',
            'otp3' => 'required',
            'otp4' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'Validation Error.' => false,
                    'message' => $validator->errors()->first(),
                ],
                409
            );
        }
        $otp1 = $request->input('otp1');
        $otp2 = $request->input('otp2');
        $otp3 = $request->input('otp3');
        $otp4 = $request->input('otp4');
        $combinedOTP = $otp1 . $otp2 . $otp3 . $otp4;

        $otpCreatedAt = Carbon::parse($request->input('otp_created_at'));
        $expirationDuration = CarbonInterval::minutes(2);

        $otpExpirationTime = $otpCreatedAt->add($expirationDuration);

        if (Carbon::now()->gt($otpExpirationTime)) {
            return response()->json(
                [
                    'status' => false,
                    'location' => route('vendor.login'),
                    'msg' => 'TimeOut! Please Try Again.',
                ],
                404
            );
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user && $user->otp == $combinedOTP) {
                $location = route('vendor.new_password', [
                    'email' => $user->email,
                ]);
                return response()->json([
                    'status' => true,
                    'location' => $location,
                    'msg' => 'OTP Verified Successfully!',
                ]);
            } else {
                return response()->json(
                    ['status' => false, 'msg' => 'Invalid OTP'],
                    404
                );
            }
        }
    }

    public function new_password($email)
    {
        return view('vendor.auth.new_password', ['email' => $email]);
    }

    public function new_password_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'new_pass' => 'required|min:6',
            'confirm_pass' => 'same:new_pass|required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'Validation Error.' => false,
                    'msg' => $validator->errors()->first(),
                ],
                409
            );
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->new_pass);
            $user->otp = null;
            $user->save();

            $location = route('vendor.login');
            return response()->json([
                'status' => true,
                'location' => $location,
                'msg' => 'Password reset successfully',
            ]);
        } else {
            return response()->json(
                ['status' => false, 'msg' => 'Invalid Credentials'],
                404
            );
        }
    }

    public function change_password()
    {
        $user = Auth::user();
        return view('vendor.auth.change_password', compact('user'));
    }

    public function change_password_store(Request $request)
    {
        $request->validate([
            'old_pass' => 'required',
            'new_pass' => 'required|min:6',
            'confirm_pass' => 'required|same:new_pass',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->old_pass, $user->password)) {
            return response()->json(
                ['message' => 'The Old Password is Incorrect.'],
                422
            );
        }

        $pass = User::find(Auth::user()->id);
        $pass->password = Hash::make($request->new_pass);
        $pass->save();

        return response()->json([
            'status' => true,
            'message' => 'Password Updated Successfully.',
            'location' => route('vendor.dashboard'),
        ]); 
    }

    public function verification_mail()
    {
        $sent = Mail::to(Auth::user()->email)->send(new verificationMail(Auth::user()->id));
        return redirect()
            ->back()
            ->with('message', 'Mail Sent Successfully');
    }

    public function verification_confirmation($id)
    {
        $shop = Shop::where('vendor_id', $id)->first();
        $shop->email_is_verified = 1;
        $shop->save();
        return redirect('vendor/dashboard')->with(
            'message',
            'Email is Verified Successfully'
        );
    }

    public function otp_verification()
    {     $email = Session::get('email');
        return view('vendor.auth.otp_verification', compact('email'));
    }

    public function vendor_verify(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp1' => 'required',
            'otp2' => 'required',
            'otp3' => 'required',
            'otp4' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'Validation Error.' => false,
                    'message' => $validator->errors()->first(),
                ],
                409
            );
        }
        $otp1 = $request->input('otp1');
        $otp2 = $request->input('otp2');
        $otp3 = $request->input('otp3');
        $otp4 = $request->input('otp4');
        $combinedOTP = $otp1 . $otp2 . $otp3 . $otp4;

        $user = User::where('email', $request->email)->first();
        if($user){
            if ($user && $user->otp == $combinedOTP) {   
                $user->otp = null;
                $user->otp_created_at = null;
                $user->is_verified = 1;
                $user->save();
                Auth::login($user);
                return response()->json([
                    'status' => true,
                    'location' => route('vendor.dashboard'),
                    'msg' => 'OTP Verified Successfully!',
                ]);
            } else {
                return response()->json(
                    ['status' => false, 'msg' => 'Invalid OTP'],
                    404
                );
            }
        }
    }

    public function resendOtp(Request $request){
        $user = User::where('email',$request->email)->first();
        if ($user) {

            $otp = strval(random_int(1000, 9999));
            $user->otp = $otp;
            // $user->otp_created_at = Carbon::now();
            $user->save();

            $data['otp'] = $otp;
            $data['email'] = $request->email;
            $data['title'] = 'OTP Verification';
            $data['body'] = 'Your OTP is: ' . $otp;

            Mail::send(
                'email.forgotPasswordMail',
                ['data' => $data],
                function ($message) use ($data) {
                    $message
                        ->from('mail@dilamsys.com', 'Ktwis')
                        ->to($data['email'])
                        ->subject($data['body']);
                }
            );


            $message = "Your OTP for verification is: ". $otp .". Please enter this code to verify your identity. Do not share this code with anyone";
            $number = $user->country_code.$user->mobile_number ;
            sms($number, $message);


            return response()->json([
                'status' => true,
                'msg' => 'OTP send successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'msg' => 'User Not Found',
            ]);

        }
    }
}
