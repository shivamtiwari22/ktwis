<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\CommissionCharge;
use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\VendorCoupon;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ReportsController extends Controller
{
    public function index_performance()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        //customer
        $userCount = Role::where('role', 'user')
            ->first()
            ->users()
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

        $startDate = Carbon::now()
            ->subMonths(12)
            ->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        //sumorder_value
        $sumAmountperyear = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('orders.total_amount');
        $sumAmountperyear = number_format($sumAmountperyear, 2);

        //avgorder_value
        $avgAmountperyear = Order::join(
            'users',
            'orders.seller_id',
            '=',
            'users.id'
        )
            ->where('users.id', $user->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->avg('orders.total_amount');
        $avgAmountperyear = number_format($avgAmountperyear, 2);

        //revenure_week
        $currentweekrevenueAmount = Order::join(
            'vendors',
            'orders.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('orders.created_at', [
                $currentWeekStart,
                $currentWeekEnd,
            ])
            ->sum('orders.total_amount');

        $previousweekrevenueAmount = Order::join(
            'vendors',
            'orders.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('orders.created_at', [
                $previousWeekStart,
                $previousWeekEnd,
            ])
            ->sum('orders.total_amount');

        $dayRevenueAmount = Order::join(
            'vendors',
            'orders.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereDate('orders.created_at', $currentDate)
            ->sum('orders.total_amount');

        $topSellingProducts = OrderItem::with([
             'product' =>function ($query) {
                $query->withTrashed();
            },
            'product.inventory',
            'product.inventoryVariants.variants'
        ]
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.seller_id', '=', 'users.id')
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

        //top users
        $topUsers = Order::select(
            'user_id',
            DB::raw('count(*) as total_orders')
        )
            ->where('seller_id', Auth::user()->id)
            ->groupBy('user_id')
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get();

        $userIds = $topUsers->pluck('user_id')->toArray();
        $top_users = User::with('order')
            ->whereIn('id', $userIds)
            ->get();

        //category
        $topcategories = OrderItem::select('product_id')
            ->limit(10)
            ->get();

        $categoryProducts = CategoryProduct::whereIn(
            'product_id',
            $topcategories->pluck('product_id')->toArray()
        )
            ->with('product.orderItems', 'category')
            ->get()
            ->groupBy('category_id');

        // return response()->json($categoryProducts);die();

     
        return view('vendor.reports.index_performance', [
            'user' => $user,
            'userCount' => $userCount,
            'currentweekrevenueAmount' => $currentweekrevenueAmount,
            'previousweekrevenueAmount' => $previousweekrevenueAmount,
            'dayRevenueAmount' => $dayRevenueAmount,
            'topSellingProducts' => $topSellingProducts,
            'avgAmountperyear' => $avgAmountperyear,
            'sumAmountperyear' => $sumAmountperyear,
            'top_users' => $top_users,
            'categoryProducts' => $categoryProducts,
        ]);
    }

    public function AllCustomer()
    {
        // get those customers id's who buys your product
        $getUserId = Order::where('seller_id', Auth::user()->id)
            ->pluck('user_id')
            ->toArray();
        $customers = User::where('created_by', Auth::user()->id)
            ->orWhereIn('id', $getUserId)
            ->with([
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
            'vendor.customer.index',
            compact('customers', 'countries', 'state')
        );
    }

    public function createCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|same:password',
            'profile_pic' => 'mimes:jpeg,jpg,png|max:2000',
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

        $customer = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request['password']),
            'nickname' => $request->nick_name,
            'dob' => $request->dob,
            'details' => $request->description,
            'mobile_number' => $request->phone,
            'created_by' => Auth::user()->id,
        ]);

        $role = Role::where('role', 'user')->first();
        $customer->roles()->attach($role->id, ['user_id' => $customer->id]);
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

            $customer->profile_pic = $image_name;
        }

        $customer->save();
        $address = new UserAddress();
        $address->user_id = $customer->id;
        $address->address_type = 'shipping';
        $address->contact_person = $request->name;
        $address->contact_no = $request->phone;
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
            'msg' => 'Customer Created Successfully',
        ]);
    }

    public function editCustomer(Request $request)
    {
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

    public function deleteCustomer(Request $request)
    {
        $user = User::destroy($request->id);
        if ($user) {
            return response()->json([
                'status' => true,
                'msg' => 'Customer Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Something Went Wrong ',
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|Confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first(),
            ]);
        }

        $user = User::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();
        if ($user) {
            return response()->json([
                'status' => true,
                'msg' => 'Password Change Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Something Went Wrong',
            ]);
        }
    }

    public function payout_detail()
    {
        // get charge amount per product sale 
          $charge = CommissionCharge::first()->amount ?? 3;
        $orders = Order::where('seller_id', Auth::user()->id)
            ->whereNull('payment_url')
            ->get();

            $totalAmount = 0;   
            $paidAmount = 0; 
            $returnAmount = 0; 
            $totalCommission = 0;

            foreach($orders as $item){
                $item->guarantee_charge =
                OrderSummary::find($item->order_summary_id)
                    ->guarantee_charge > 0
                    ? 'Yes'
                    : 'No';


             $amount = $item->sub_total - $item->discount_amount ;
             $charge_amount = $item->item_count * $charge;
             $percentage_amount = $amount * $charge_amount/100;

           $item->amount = $amount - $percentage_amount;

               
           $totalAmount += $item->amount;
           $totalCommission += $percentage_amount ;

           if($item->payment_release_status == "released"){
               $paidAmount +=$item->amount; 
           }

           if($item->payment_release_status == "return_to_customer"){
               $returnAmount +=$item->amount; 
           }

            }
        
            $balanceAmount = $totalAmount - $paidAmount;

        return view('vendor.reports.payout_details', compact('orders','totalAmount','paidAmount','returnAmount','balanceAmount','totalCommission'));
    }

    public function post_payout_detail(Request $request){

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

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $guarantee_charge = $request->input('guarantee_charge');

        $total =  Order::where('seller_id', Auth::user()->id)
        ->whereNull('payment_url')
        ->join(
            'order_summaries',
            'orders.order_summary_id',
            'order_summaries.id'
        )
        ->join(
            'payments',
            'orders.order_summary_id',
            'payments.order_summary_id'
        )
            ->where(function ($query) use ($search) {
                $query
                ->where('orders.order_number', 'like', '%' . $search . '%')
                ->orWhere('orders.payment_release_status', 'like', '%' . $search . '%')
                ->orWhere(
                    'orders.total_amount',
                    'like',
                    '%' . $search . '%'
                );
            })
            ->where(function ($query) use ($from_date , $to_date  , $guarantee_charge) {
                if ($from_date != null) {
                    $query->where('orders.created_at', '>=', $from_date);
                }

                if ($to_date != null) {
                    $query->where('orders.created_at', '<=', $to_date);
                }


                if($guarantee_charge){
                    if($guarantee_charge == "yes"){
                        $query->where('order_summaries.guarantee_charge', '>' , 0);
                    }
                    elseif ($guarantee_charge == "no"){
                        $query->where('order_summaries.guarantee_charge', 0);

                    }
                }

            })
            ->select('orders.*')
            ->count();

        $orders =  Order::where('seller_id', Auth::user()->id)
        ->whereNull('payment_url')
        ->join(
            'order_summaries',
            'orders.order_summary_id',
            'order_summaries.id'
        )
        ->join(
            'payments',
            'orders.order_summary_id',
            'payments.order_summary_id'
        )
        ->where(function ($query) use ($search) {
            $query
            ->where('orders.order_number', 'like', '%' . $search . '%')
            ->orWhere('orders.payment_release_status', 'like', '%' . $search . '%')
            ->orWhere(
                'orders.total_amount',
                'like',
                '%' . $search . '%'
            );
        })
        ->where(function ($query) use ($from_date , $to_date  , $guarantee_charge) {
            if ($from_date != null) {
                $query->where('orders.created_at', '>=', $from_date);
            }

            if ($to_date != null) {
                $query->where('orders.created_at', '<=', $to_date);
            }

            if($guarantee_charge){
                if($guarantee_charge == "yes"){
                    $query->where('order_summaries.guarantee_charge', '>' , 0);
                }
                elseif ($guarantee_charge == "no"){
                    $query->where('order_summaries.guarantee_charge', 0);

                }
            }

        })
        ->select('orders.*')
            ->orderBy('id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();

        // return $cancel;

        $charge = CommissionCharge::first()->amount ?? 3;

        $data = [];


        foreach ($orders as $key => $order) {
     

            $order->guarantee_charge =
            OrderSummary::find($order->order_summary_id)
                ->guarantee_charge > 0
                ? 'Yes'
                : 'No';

         $amount = $order->sub_total - $order->discount_amount ;
         $charge_amount = $order->item_count * $charge;
         $percentage_amount = $amount * $charge_amount/100;


       $order->amount = $amount - $percentage_amount;

            $data[] = [
                $offset + $key + 1,
                '<a href="'.route('vendor.order.show_product_detail', ['id' => $order->id]).'" target="_blank">'. $order->order_number .'</a>' ,
                $order->created_at->format('M d,Y  H:i:s'),
                $order->guarantee_charge,
                ucwords($order->payment_release_status) ,
                date('M d, Y', strtotime($order->created_at . '+5 days')) ,
                $order->amount
               
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
       

        echo json_encode($records);
    }
}
