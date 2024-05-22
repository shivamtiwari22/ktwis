<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\currencyBalance;
use App\Models\deposits;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletApiController extends Controller
{
    //
    public function myWallet()
    {
        try {
            $wallet = userWallet::where('user_id', auth('api')->user()->id)->first();
            $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
            foreach ($balance as $item) {
                $item->currency = Currency::where('id', $item->currency_id)->first();
            }
            $totalAmount = $balance->sum('balance_amount');
            $data = [
                'wallet' => $wallet,
                'balance' => $balance,
                'totalAmount' => $totalAmount
            ];
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Attribute by Id fetched successfully',
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
            );        }
    }


    public function verify(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required',
                'amount' => 'required',
                'currency' => 'required',
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
            $wallet = userWallet::where('user_id', auth('api')->user()->id)->first();
            $currency = Currency::where('currency_code', $request->currency)->first();
            $deposite = new deposits();
            $deposite->user_id = Auth::user()->id;
            $deposite->wallet_id =  $wallet->id;
            $deposite->currency_code = $request->currency;
            $deposite->amount = $request->amount;
            $deposite->payment_method = 'flutter Wave';
            $deposite->transaction_id = $request->transaction_id;
            $deposite->save();
            $currencyBalance = currencyBalance::where('wallet_id',  $wallet->id)->where('currency_id', $currency->id)->first();
            if ($currencyBalance) {
                $currencyBalance->update([
                    'balance_amount' => $currencyBalance->balance_amount + $request->amount,
                ]);
            } else {
                currencyBalance::create([
                    'wallet_id' => $wallet->id,
                    'currency_id' =>  $currency ? $currency->id : 2,
                    'balance_amount' => $request->amount
                ]);
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $deposite],
                'timestamp' => Carbon::now(),
                'message' => "payment successfully completed",
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
            );        }
    }
    public function transferAmount(Request $request)
    {

     
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'amount' => 'required|numeric',
            'currency_id' => 'required|integer',
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
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $receiverWallet = userWallet::where('user_id', $user->id)->first();
            if (!$receiverWallet) {
                return response()->json(['status' => false, 'msg' => "Receiver wallet is not active"],400);
            }
            $senderWallet = userWallet::where('user_id', auth('api')->user()->id)->first();

            $senderBalance = currencyBalance::where('wallet_id', $senderWallet->id)->where('currency_id', $request->currency_id)->first();
            if ($senderBalance->balance_amount < $request->amount) {
                return response()->json(['status' => false, 'msg' => "Insufficient Balance"],400);
            }

            $currencyBalance = currencyBalance::where('wallet_id', $receiverWallet->id)->where('currency_id', $request->currency_id)->first();
            if ($currencyBalance) {
                $currencyBalance->update([
                    'balance_amount' => $currencyBalance->balance_amount + $request->amount,
                ]);
            } else {
                currencyBalance::create([
                    'wallet_id' => $receiverWallet->id,
                    'currency_id' => $request->currency_id,
                    'balance_amount' => $request->amount,
                ]);
            }

            $balance = currencyBalance::where('wallet_id', $senderWallet->id)->where('currency_id', $request->currency_id)->first();
            if ($balance) {
                $balance->update([
                    'balance_amount' => $balance->balance_amount - $request->amount,
                ]);
            }
            $transfer = new Transaction();
            $transfer->sender_wallet_id = $senderWallet->id;
            $transfer->receiver_wallet_id = $receiverWallet->id;
            $transfer->amount = $request->amount;
            $transfer->currency_id = $request->currency_id;
            $transfer->transaction_type = "deposit";
            $transfer->status = "Completed";
            $transfer->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $transfer],
                'timestamp' => Carbon::now(),
                'message' => 'Amount Transfer Successfully',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => '404',
                    'status' => false,
                    'context' => ['error' => 'Data Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data Not Found',
                ],
                404
            );
        }
    }

    public function moneywithdrawal(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric',
                'currency_id' => 'required|integer'
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
            $wallet = userWallet::where('user_id',auth('api')->user()->id)->first();
            if (100  < $request->amount) {
                return response()->json(['status' => false, 'msg' => "withdrawal amount not more than 100"],400);
            }
            $adminwallet = userWallet::where('user_id', 1)->first();

            $trasaction = new Transaction();
            $trasaction->sender_wallet_id = $adminwallet->id;
            $trasaction->receiver_wallet_id = $wallet->id;
            $trasaction->amount = $request->amount;
            $trasaction->currency_id = $request->currency_id;
            $trasaction->transaction_type = "withdraw";
            $trasaction->status = "Pending";
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
            );        }
    }

    public function alltransaction()
    {
        try {
            $wallet = userWallet::where('user_id', auth('api')->user()->id)->where('status', 'active')->first();
            $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
            $totalAmount = $balance->sum('balance_amount');
            $transactions = Transaction::where('sender_wallet_id', $wallet->id)->orWhere('receiver_wallet_id', $wallet->id)->get();
            foreach ($transactions as $trans) {
                $trans->currency = Currency::where('id', $trans->currency_id)->first();
            }
            $data = [
                'totalAmount' => $totalAmount,
                'transactions' => $transactions
            ];
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'user data  disply successfully',
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
            );        }
    }


}
