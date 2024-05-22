<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\currencyBalance;
use App\Models\deposits;
use App\Models\Dispute;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userWallet;
use App\Models\VendorCoupon;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class CustomerWalletApiController extends Controller
{
    public function myWallet(Request $request)
    {
        try {
            $wallet =
                userWallet::where(
                    'user_id',
                    auth('api')->user()->id
                )->first() ?? null;
            $balance =
                currencyBalance::where('wallet_id', $wallet->id)
                    ->where('currency_id', 2)
                    ->first()->balance_amount ?? null;

            $last_debit =
                Transaction::where('sender_wallet_id', $wallet->id)
                    ->where('status', 'Completed')
                    ->latest()
                    ->first()->amount ?? 0;
            $last_credit =
                Transaction::where('receiver_wallet_id', $wallet->id)
                    ->where('status', 'Completed')
                    ->latest()
                    ->first()->amount ?? 0;

            $transactions = Transaction::where('sender_wallet_id', $wallet->id)
                ->orWhere('receiver_wallet_id', $wallet->id)
                ->get([
                    'id',
                    'transaction_time',
                    'transaction_type',
                    'status',
                    'amount',
                ]);

            $data = [
                'balance' => $balance,
                'last_debit' => $last_debit,
                'last_credit' => $last_credit,
                'transactions' => $transactions,
            ];
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetched Successfully',
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

    public function verify(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'transaction_id' => 'required',
                'amount' => 'required',
                'currency' => 'required',
            ]);

            $wallet = userWallet::where(
                'user_id',
                auth('api')->user()->id
            )->first();
            return $wallet;
            $currency = Currency::where(
                'currency_code',
                $request->currency
            )->first();
            $deposite = new deposits();
            $deposite->user_id = Auth::user()->id;
            $deposite->wallet_id = $wallet->id;
            $deposite->currency_code = $request->currency;
            $deposite->amount = $request->amount;
            $deposite->payment_method = 'flutter Wave';
            $deposite->transaction_id = $request->transaction_id;
            $deposite->save();

            $currencyBalance = currencyBalance::where('wallet_id', $wallet->id)
                ->where('currency_id', $currency->id)
                ->first();
            if ($currencyBalance) {
                $currencyBalance->update([
                    'balance_amount' =>
                        $currencyBalance->balance_amount + $request->amount,
                ]);
            } else {
                currencyBalance::create([
                    'wallet_id' => $wallet->id,
                    'currency_id' => $currency ? $currency->id : 2,
                    'balance_amount' => $request->amount,
                ]);
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$deposite]],
                'timestamp' => Carbon::now(),
                'message' => 'payment successfully completed',
            ]);
            // return response()->json(['status' => true, 'msg' => "payment successfully completed", 'result' => $deposite]);
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

    public function transferAmount(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric',
            'currency_id' => 'required|integer',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $receiverWallet = userWallet::where('user_id', $user->id)->first();
            if (!$receiverWallet) {
                return response()->json(
                    [
                        'status' => false,
                        'msg' => 'Receiver wallet is not active',
                    ],
                    400
                );
            }
            $senderWallet = userWallet::where(
                'user_id',
                auth('api')->user()->id
            )->first();

            $senderBalance = currencyBalance::where(
                'wallet_id',
                $senderWallet->id
            )
                ->where('currency_id', $request->currency_id)
                ->first();
            if ($senderBalance->balance_amount < $request->amount) {
                return response()->json(
                    ['status' => false, 'msg' => 'Insufficient Balance'],
                    400
                );
            }

            $currencyBalance = currencyBalance::where(
                'wallet_id',
                $receiverWallet->id
            )
                ->where('currency_id', $request->currency_id)
                ->first();
            if ($currencyBalance) {
                $currencyBalance->update([
                    'balance_amount' =>
                        $currencyBalance->balance_amount + $request->amount,
                ]);
            } else {
                currencyBalance::create([
                    'wallet_id' => $receiverWallet->id,
                    'currency_id' => $request->currency_id,
                    'balance_amount' => $request->amount,
                ]);
            }

            $balance = currencyBalance::where('wallet_id', $senderWallet->id)
                ->where('currency_id', $request->currency_id)
                ->first();
            if ($balance) {
                $balance->update([
                    'balance_amount' =>
                        $balance->balance_amount - $request->amount,
                ]);
            }
            $transfer = new Transaction();
            $transfer->sender_wallet_id = $senderWallet->id;
            $transfer->receiver_wallet_id = $receiverWallet->id;
            $transfer->amount = $request->amount;
            $transfer->currency_id = $request->currency_id;
            $transfer->transaction_type = 'deposit';
            $transfer->status = 'Completed';
            $transfer->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$transfer]],
                'timestamp' => Carbon::now(),
                'message' => 'Amount Transfer Successfully',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'User Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => ' User not fetched!',
                ],
                404
            );
        }
    }

    public function moneywithdrawal(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric',
                'currency_id' => 'required|integer',
            ]);

            $wallet = userWallet::where(
                'user_id',
                auth('api')->user()->id
            )->first();
            if (100 < $request->amount) {
                return response()->json(
                    [
                        'status' => false,
                        'msg' => 'withdrawal amount not more than 100',
                    ],
                    400
                );
            }
            $adminwallet = userWallet::where('user_id', 1)->first();

            $trasaction = new Transaction();
            $trasaction->sender_wallet_id = $adminwallet->id;
            $trasaction->receiver_wallet_id = $wallet->id;
            $trasaction->amount = $request->amount;
            $trasaction->currency_id = $request->currency_id;
            $trasaction->transaction_type = 'withdraw';
            $trasaction->status = 'Pending';
            $trasaction->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $trasaction],
                'timestamp' => Carbon::now(),
                'message' => 'Payout Request Sent Successfully',
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

    public function alltransaction()
    {
        try {
            $wallet = userWallet::where('user_id', auth('api')->user()->id)
                ->where('status', 'active')
                ->first();
            $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
            $totalAmount = $balance->sum('balance_amount');
            $transactions = Transaction::where('sender_wallet_id', $wallet->id)
                ->orWhere('receiver_wallet_id', $wallet->id)
                ->get();
            foreach ($transactions as $trans) {
                $trans->currency = Currency::where(
                    'id',
                    $trans->currency_id
                )->first();
            }
            $data = [
                'totalAmount' => $totalAmount,
                'transactions' => $transactions,
            ];

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => ' data fetched successfully',
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

    public function dashboard(Request $request)
    {
        try {
            $orders = Order::where('user_id', Auth::user()->id)->get([
                'id',
                'user_id',
                'order_number',
                'status',
                'item_count',
                'total_amount',
                'created_at',
                'seller_id',
            ]);

            foreach ($orders as $order) {
                $order->date = date('d.m.Y', strtotime($order->created_at));
                $vendorShop = Shop::where(
                    'vendor_id',
                    $order->seller_id
                )->first();
                if ($vendorShop) {
                    $order->shop_name = $vendorShop->shop_name;
                    $order->shop_img = asset(
                        'public/vendor/shop/brand/' . $vendorShop->brand_logo
                    );
                }
            }

            $wishlistCount = Wishlist::where(
                'created_by',
                Auth::user()->id
            )->count();
            $orderCount = Order::where('user_id', Auth::user()->id)->count();
            $messageCount = Message::where(
                'received_by',
                Auth::user()->id
            )->count();
            $couponCount = VendorCoupon::where('status', 'published')->count();
            $disputeCount = Dispute::where(
                'customer_id',
                Auth::user()->id
            )->count();

            $counts = [
                'wishlistCount' => $wishlistCount,
                'orderCount' => $orderCount,
                'messageCount' => $messageCount,
                'couponCount' => $couponCount,
                'disputeCount' => $disputeCount,
            ];

            $user = new stdClass();
            $user->name = Auth::user()->name;
            $user->created_at =
                Carbon::parse(Auth::user()->created_at)->diffForHumans(
                    null,
                    true
                ) . ' ago';

                if(Auth::user()->profile_pic){
                    $user->profile_pic = asset(
                        'public/customer/profile/' . Auth::user()->profile_pic
                    );
                }
                else {
                    $user->profile_pic = null ;
                }
         

            $data = new stdClass();
            $data->orders = $orders;
            $data->counts = $counts;
            $data->user = $user;

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'data fetched Successfully',
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

    public function messages()
    {
        try {
            $perPage = 20;
            $page = request()->get('page', 1);
            $message = Message::where(function ($query) {
                $query->where('received_by', Auth::user()->id)
                      ->orWhere('created_by', Auth::user()->id);
            })
                       ->where('draft', '!=', 1)
                ->select(
                    'id',
                    'subject',
                    'message',
                    'file',
                    'created_at',
                    'created_by',
                    'received_by',
                    'read_at'
                )->orderBy('id', 'DESC')
                ->paginate($perPage, ['*'], 'page', $page);

            foreach ($message as $item) {
                $shop = Shop::where('vendor_id', $item->created_by)
                    ->orWhere('vendor_id', $item->received_by)
                    ->first();
                $item->shop_name = $shop->shop_name ?? null;
                $item->shop_img = $shop
                    ? asset('public/vendor/shop/brand/' . $shop->brand_logo)
                    : '';
                $item->file_url = asset('public/vendor/file/' . $item->file);
                $item->read = $item->read_at ? true : false;

                $item->creation_date =
                    Carbon::parse($item->created_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $message],
                'timestamp' => Carbon::now(),
                'message' => 'data fetched Successfully',
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

    public function messageMarkAsRead($id)
    {
        try {
            $message = Message::where('id', $id)->update([
                'read_at' => date('Y-m-d h:i:s'),
            ]);

            if ($message) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Message Mark As Read Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => ['error' => 'Something Went Wrong'],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
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
}
