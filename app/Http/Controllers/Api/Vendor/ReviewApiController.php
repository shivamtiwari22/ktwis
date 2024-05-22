<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Order;
use App\Models\CategoryProduct;
use App\Models\VendorCoupon;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewApiController extends Controller
{
    public function add_review(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'rating' => 'required|integer|min:1',
                'comment' => 'required|min:5|max:256',
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

            $review = new Review();
            $review->product_id = $request->product_id;
            $review->user_id = Auth::user()->id;
            $review->rating = $request->rating;
            $review->comment = $request->comment;
            $review->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $review],
                'timestamp' => Carbon::now(),
                'message' => 'Review Stored Successfully',
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

    public function get_review_by_productId($id){

             try{

                $reviews = Review::with(['user' => function ($query) {
                    $query->select('id', 'name'); 
                }])
                ->where('product_id', $id)
                ->get();
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $reviews],
                    'timestamp' => Carbon::now(),
                    'message' => 'Review Stored Successfully',
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

    public function reviews()
    {
        try {
            $currentUserId = Auth::user()->id;
            $query = Product::with('reviews')
                ->whereHas('reviews')
                ->where('created_by', $currentUserId)
                ->get();

            foreach ($query as $order) {
                $featured_image =
                asset(
                    'public/vendor/featured_image/' . $order->featured_image
                );

                $totalRatings = 0;
            $totalReviews = count($order->reviews);
                if ($order) {
                    foreach ($order->reviews as $review) {
                        $totalRatings += $review->rating;
                    }
                    $averageRating =
                    $totalReviews > 0 ? $totalRatings / $totalReviews : 0;
                    $data[] = [
                        'product_id' => $order->id,
                         'product_name' =>$order->name ,
                         'featured_image' => $featured_image,
                        'rating' => $totalRatings,
                        'average_rating' => $averageRating
                    ];
                }
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Reviews Fetched Successfully',
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


    public function viewReviews($id){
        try {
            $currentUserId = Auth::user()->id;
            $products = Review::with(['user' => function($query){
                  $query->select('id','name','email');
                
            }])
                ->where('product_id', $id)
                ->WhereHas('product', function ($query) use ($currentUserId) {
                    $query->where('created_by', $currentUserId);
                })
                ->get(['id','product_id','rating','comment','created_at','user_id']);

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $products],
                    'timestamp' => Carbon::now(),
                    'message' => 'Reviews Fetched Successfully',
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

    public function invoices()
    {
        try {
            $currentUserId = Auth::user()->id;
            $vendor = Vendor::where('user_id', $currentUserId)->first();
            $vendor_user_id = $vendor->id;

            $invoices = Invoice::with([
                'order.user',
                'order.orderItems' => function ($query) use ($vendor_user_id) {
                    $query->where('seller_id', $vendor_user_id);
                },
            ])
                ->whereHas('order', function ($query) use ($vendor_user_id) {
                    $query->whereHas('orderItems', function ($query) use (
                        $vendor_user_id
                    ) {
                        $query->where('seller_id', $vendor_user_id);
                    });
                })
                ->get();

            $data = [];
            foreach ($invoices as $key => $invoice) {
                $invoice_name = $invoice->invoice_number;
                $customer_name = $invoice->order->user->name;
                $data[] = [
                    'order_id' => $invoice_name,
                    'name' => $customer_name,
                ];
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Inventory without Variant fetched Successfully',
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

    public function report()
    {
        try {
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
            $sumAmountperyear = Order::join('users', 'orders.seller_id', '=', 'users.id')
            ->where('users.id', $user->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('orders.total_amount');
        $sumAmountperyear = number_format($sumAmountperyear, 2);

            //avgorder_value
            $avgAmountperyear =Order::join('users', 'orders.seller_id', '=', 'users.id')
            ->where('users.id', $user->id)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->avg('orders.total_amount');
            $avgAmountperyear = number_format($avgAmountperyear, 2);

            //revenure_week
            $currentweekrevenueAmount = Order::join('vendors', 'orders.seller_id', '=', 'vendors.id')
            ->where('vendors.user_id', $user->id)
            ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
            ->sum('orders.total_amount');

            $previousweekrevenueAmount = Order::join('vendors', 'orders.seller_id', '=', 'vendors.id')
            ->where('vendors.user_id', $user->id)
            ->whereBetween('orders.created_at', [$previousWeekStart, $previousWeekEnd])
            ->sum('orders.total_amount');

          
        $dayRevenueAmount = Order::join('vendors', 'orders.seller_id', '=', 'vendors.id')
        ->where('vendors.user_id', $user->id)
        ->whereDate('orders.created_at', $currentDate)
        ->sum('orders.total_amount');

        $topSellingProducts = OrderItem::with('product.inventory', 'product.inventoryVariants.variants')->
        join('orders','order_items.order_id','=','orders.id')->
        join('users', 'orders.seller_id', '=', 'users.id')
        ->where('users.id', $user->id)
        ->groupBy('order_items.product_id', 'order_items.variant_id', 'order_items.quantity', 'order_items.total_amount')
        ->orderByRaw('COUNT(*) DESC')
        ->limit(5)
        ->select('order_items.product_id', 'order_items.variant_id', DB::raw('SUM(order_items.quantity) as quantity'), DB::raw('SUM(order_items.total_amount) as total_sub_total'))
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
            $top_users = User::with(['order' => function ($query){
                     $query->where('seller_id', Auth::user()->id );
            }])
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
                ->get();
                
            $currentUserId = Auth::user()->id;
            $vendor = Vendor::where('user_id', $currentUserId)->first();
            // $vendor_user_id = $vendor->id;
            $data[] = [
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
            ];

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Report fetched Successfully',
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
        }    }


}
