<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\CommissionCharge;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use App\Models\VendorCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendorDashboardAPIController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $roles = $user->roles->first();

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

        $currentCount = Role::where('role', 'user')
            ->first()
            ->users()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousCount = Role::where('role', 'user')
            ->first()
            ->users()
            ->whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count();

        $percentageIncreaseUser =
            (($currentCount - $previousCount) / $previousCount) * 100;
        $percentageDecreaseUser =
            (($previousCount - $currentCount) / $previousCount) * 100;

        //order
        $orderCount = Order::join(
            'order_items',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('vendors', 'order_items.seller_id', '=', 'vendors.id')
            ->where('vendors.user_id', $user->id)
            ->count();

        $currentOrderCount = Order::join(
            'order_items',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('vendors', 'order_items.seller_id', '=', 'vendors.id')
            ->where('vendors.user_id', $user->id)
            ->whereMonth('orders.created_at', $currentMonth)
            ->whereYear('orders.created_at', $currentYear)
            ->count();

        $previousOrderCount = Order::join(
            'order_items',
            'order_items.order_id',
            '=',
            'orders.id'
        )
            ->join('vendors', 'order_items.seller_id', '=', 'vendors.id')
            ->where('vendors.user_id', $user->id)
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
        $revenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->sum('order_items.sub_total');

        $currentrevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereMonth('order_items.created_at', $currentMonth)
            ->whereYear('order_items.created_at', $currentYear)
            ->sum('order_items.sub_total');

        $previousrevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereMonth('order_items.created_at', $previousMonth)
            ->whereYear('order_items.created_at', $previousYear)
            ->sum('order_items.sub_total');

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
        $currentweekrevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('order_items.created_at', [
                $currentWeekStart,
                $currentWeekEnd,
            ])
            ->sum('order_items.sub_total');

        $previousweekrevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('order_items.created_at', [
                $previousWeekStart,
                $previousWeekEnd,
            ])
            ->sum('order_items.sub_total');

        $dayRevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereDate('order_items.created_at', $currentDate)
            ->sum('order_items.sub_total');

        $topSellingProducts = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->with('product.inventory', 'product.inventoryVariants.variants')
            ->where('vendors.user_id', $user->id)
            ->groupBy(
                'order_items.product_id',
                'order_items.variant_id',
                'order_items.quantity',
                'order_items.sub_total'
            )
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->select(
                'order_items.product_id',
                'order_items.variant_id',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.sub_total) as total_sub_total')
            )
            ->get();
        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => [
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
            ],
            'timestamp' => Carbon::now(),
            'message' => 'Vender all data show Successfully',
        ]);
    }

    public function support_desk_dispute()
    {
        try {
            $disputes = Dispute::with(
                'disputemessages',
                'order',
                'customer',
                'vendor'
            )   ->where('vendor_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('status', 'open')
                    ->orWhere('status', 'new')
                    ->orWhere('status', 'waiting')
                    ->orWhere('status','solved')
                    ->orWhere('status','closed');

            })
            ->get();

            foreach($disputes as $dispute){
                foreach($dispute->disputemessages  as $message){
                         $message->attachment = asset('public/customer/dispute/'. $message->attachment);
                        $message->response_by = User::where('id',$message->response_by_id)->first(['id','name','email']);
                }
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $disputes],
                'timestamp' => Carbon::now(),
                'message' => 'Dispute data show Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }

    public function support_desk_dispute_id(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'dispute_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()->first()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }
            $disputes = Dispute::with(
                'disputemessages',
                'order',
                'customer',
                'vendor'
            )
                ->where('id', $request->dispute_id)
                ->get()->map(function($items){
                        foreach($items as $item){
                                $item->attachment = asset('public/vendor/attachment/'. $item->attachment);
                        }

                        return $items;
                });

            if ($disputes->isEmpty()) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Dispute Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Dispute data Not Found',
                    ],
                    404
                );
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$disputes]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Dispute show Successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }

    public function support_desk_reply_store_disputes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'dispute_id' => 'required',
                'reply_status' => 'required',
                'content' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()->first()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }
            // $validatedData = $request->validate([
            //     'dispute_id' => 'required',
            //     'reply_status' => 'required',
            //     'content' => 'required',
            // ]);
            $dispute = Dispute::find($request->dispute_id);

            if (!$dispute) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => [
                            'error' => 'Dispute not found.',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Dispute not found.',
                    ],
                    404
                );
            }

            $dispute->status = $request->reply_status;
            $dispute->save();

            $message = new DisputeMessage();
            $message->dispute_id = $request->dispute_id;

            if ($request->hasFile('attachment')) {
                $image = $request->file('attachment');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path('customer/dispute');
                $image->move($destinationPath, $image_name);

                $message->attachment = $image_name;
            }

            $message->message = $request->content;
            $message->response_by_id = Auth::user()->id;
            $message->save();

            if ($message) {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $message],
                        'timestamp' => Carbon::now(),
                        'message' => 'Dispute reply submitted successfully',
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' => 'An unexpected error occurred',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Failed to submit dispute reply',
                    ],
                    500
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
            // return response()->json(['status' => false, 'm   sg' => $e->getMessage()], 401);
        }
    }

    public function index_performance()
    {
        $user = Auth::user();
        $roles = $user->roles->first();
        //customer
        $userCount = Role::where('role', 'user')
            ->first()
            ->users()
            ->count();
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
        $sumAmountperyear = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->sum('order_items.sub_total');
        $sumAmountperyear = number_format($sumAmountperyear, 2);

        //avgorder_value
        $avgAmountperyear = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->avg('order_items.sub_total');
        $avgAmountperyear = number_format($avgAmountperyear, 2);

        //revenure_week
        $currentweekrevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('order_items.created_at', [
                $currentWeekStart,
                $currentWeekEnd,
            ])
            ->sum('order_items.sub_total');

        $previousweekrevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereBetween('order_items.created_at', [
                $previousWeekStart,
                $previousWeekEnd,
            ])
            ->sum('order_items.sub_total');

        $dayRevenueAmount = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->where('vendors.user_id', $user->id)
            ->whereDate('order_items.created_at', $currentDate)
            ->sum('order_items.sub_total');

        $topSellingProducts = OrderItem::join(
            'vendors',
            'order_items.seller_id',
            '=',
            'vendors.id'
        )
            ->with('product.inventory', 'product.inventoryVariants.variants')
            ->where('vendors.user_id', $user->id)
            ->groupBy(
                'order_items.product_id',
                'order_items.variant_id',
                'order_items.quantity',
                'order_items.sub_total'
            )
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->select(
                'order_items.product_id',
                'order_items.variant_id',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.sub_total) as total_sub_total')
            )
            ->get();

        //top users
        $topUsers = Order::select(
            'user_id',
            DB::raw('count(*) as total_orders')
        )
            ->groupBy('user_id')
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get();

        $userIds = $topUsers->pluck('user_id')->toArray();
        $top_users = User::with('order.orderItems')
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

        return response()->json([
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

    public function dispute_reply()
    {
        return 'hello';
    }

    public function getVendorDetail($id)
    {
        try {
            $vendor = User::where('id', $id)->first([
                'id',
                'name',
                'email',
                'mobile_number',
            ]);
            if ($vendor) {
                $vendor->shop = Shop::where('vendor_id', $id)->first([
                    'id',
                    'shop_name',
                    'legal_name',
                    'email',
                    'description',
                    'brand_logo',
                    'banner_img'
                ]);
                if ($vendor->shop) {
                    $vendor->shop->brand_logo = asset(
                        'public/vendor/shop/brand/' . $vendor->shop->brand_logo
                    );
                    $vendor->banner_img_url = asset(
                        'public/vendor/shop/banner/' . $vendor->shop->banner_img
                    );
                }
                // vendor rating 
                $VendorProducts = Product::with(
                    'reviews'
                )->where('status','active')->where('created_by', $id)->pluck('id')->toArray();
                $reviews = Review::whereIn('product_id',$VendorProducts)->get();
                $vendor->total_reviews = count($reviews);
                
                $total_rating = $reviews->sum('rating');
                $vendor->average_reviews = number_format($vendor->total_reviews > 0
                ? $total_rating / $vendor->total_reviews
                : 0, 2,'.','');
            
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $vendor],
                    'timestamp' => Carbon::now(),
                    'message' => 'Vendor Details ',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' => 'An unexpected error occurred',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Something Went Wrong',
                    ],
                    500
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }


    public function getPayoutDetail(){
        try {

            $totalAmount = 0;   
            $paidAmount = 0; 
            $returnAmount = 0; 

            $charge = CommissionCharge::first()->amount ?? 3;
            $orders = Order::where('seller_id', Auth::user()->id)
                ->whereNull('payment_url')
                ->get(['id','seller_id','order_number','item_count','sub_total','discount_amount','order_summary_id','status','payment_release_status','created_at']);
                foreach($orders as $item) {
                    $item->guarantee_charge =
                        OrderSummary::find($item->order_summary_id)
                            ->guarantee_charge > 0
                            ? 'Yes'
                            : 'No';
                     $amount = $item->sub_total - $item->discount_amount ;
                     $charge_amount = $item->item_count * $charge;
                     $item->amount = $amount - $charge_amount;

                     $item->date_time = date('M d,Y  H:i:s', strtotime($item->created_at));
                     $item->expected_payout_date =  date('M d, Y', strtotime($item->created_at . '+5 days'));

                     $totalAmount += $item->amount;

                     if($item->payment_release_status == "released"){
                         $paidAmount +=$item->amount; 
                     }
     
                     if($item->payment_release_status == "return_to_customer"){
                         $returnAmount +=$item->amount; 
                     }
                       
               
                }
             
                $balanceAmount =  $totalAmount - $paidAmount;

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => ['payout' => $orders , 'totalAmount' => $totalAmount , 'paidAmount' => $paidAmount , 'returnAmount' => $returnAmount , 'balanceAmount'=> $balanceAmount]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Vendor Details ',
                ]);
        }
        catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => $e->getMessage()],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }

    }
}
