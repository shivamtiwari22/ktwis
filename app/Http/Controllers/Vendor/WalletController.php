<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Currency  as currencyModel;
use AmrShawky\LaravelCurrency\Facade\Currency;
use App\Models\currencyBalance;
use App\Models\Transaction;
use App\Models\deposits;
use App\Models\User;
use App\Models\userWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    //
    public function myWallet()
    {

        $wallet = userWallet::where('user_id', Auth::user()->id)->where('status', 'active')->first();
        if (!$wallet) {
            return redirect('vendor/dashboard')->with('message', "wallet is not active");
        }
        $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
        foreach ($balance as $item) {
            $item->currency = currencyModel::where('id', $item->currency_id)->first();
        }
        $totalAmount = $balance->sum('balance_amount');
        $currencies = currencyModel::where('status', '1')->get();


        // return $balance;
        return view('vendor.wallet.index', compact('wallet', 'balance', 'totalAmount', 'currencies'));
    }

    public function depositeFund()
    {
        $currencies = currencyModel::where('status', '1')->get();
        return view('vendor.wallet.deposit_fund', compact('currencies'));
    }

    public function currencyConversion()
    {
        $currencies = currencyModel::all(); 
        return view('vendor.currency_conversion.index',compact('currencies'));
    }

    public function postCurrencyConversion(Request $request)
    {
        try {
            if ($request->filled('currency_from')) {
                $convertedObject  = Currency::convert()
                    ->from($request->currency_from)
                    ->to($request->currency_to)
                    ->amount($request->amount);
                if ($request->date) {
                    $convertedObject = $convertedObject->date($request->date);
                }
                $converted = $convertedObject->get();
                return response()->json(['status' => true, 'data' => $converted]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function verify(Request $request)
    {

        // return $request->all();
        $transactionId = $request['data']['transaction_id']; // Replace with the actual transaction ID received from Flutterwave
        // Set your Flutterwave API keys
        $publicKey = 'FLWPUBK_TEST-8f8836aaefe8dd1ab06b32fb9d472494-X';
        $secretKey = 'FLWSECK_TEST-64f38b7b2cea810be0724fc2e9466586-X';

        $url = "https://api.flutterwave.com/v3/transactions/$transactionId/verify";

        $headers = [
            "Authorization: Bearer $secretKey",
            "Content-Type: application/json",
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);
        if ($result['status'] == 'success') {
            // Payment was successful

            $wallet = userWallet::where('user_id', Auth::user()->id)->first();

            $currency = currencyModel::where('currency_code', $result['data']['currency'])->first();
            $deposite = new deposits();
            $deposite->user_id = Auth::user()->id;
            $deposite->wallet_id =  $wallet->id;
            $deposite->currency_code = $result['data']['currency'];
            $deposite->amount = $result['data']['amount'];
            $deposite->payment_method = 'flutter Wave';
            $deposite->transaction_id = $request['data']['transaction_id'];
            $deposite->save();

            if($currency){
                $currencyBalance = currencyBalance::where('wallet_id',  $wallet->id)->where('currency_id', $currency->id)->first();
            }
                else{
                
                    $currencyBalance = currencyBalance::where('wallet_id',  $wallet->id)->where('currency_id', 2)->first() ?? null;
                }
            
            if ($currencyBalance) {
                $currencyBalance->update([
                    'balance_amount' => $currencyBalance->balance_amount + $result['data']['amount'],
                ]);
            } else {
                currencyBalance::create([
                    'wallet_id' => $wallet->id,
                    'currency_id' =>  $currency ? $currency->id : 2,
                    'balance_amount' => $result['data']['amount']
                ]);
            }
            return response()->json(['status' => true, 'msg' => "payment successfully completed", 'result' => $result]);
        } else {
            return response()->json(['status' => false, 'msg' => $result['message']]);
        }
    }

    public function transferAmount(Request $request)
    {

            $request->validate([
                'email' => 'required|email',
                'amount' => 'required|numeric',
            ]);

            $user = User::where('email', $request->email)->first();
            if ($user) {
                $receiverWallet = userWallet::where('user_id', $user->id)->first();
                if (!$receiverWallet) {
                    return response()->json(['status' => false, 'msg' => "Receiver wallet is not active"]);
                }
                $senderWallet = userWallet::where('user_id', Auth::user()->id)->first();

                $senderBalance = currencyBalance::where('wallet_id', $senderWallet->id)->where('currency_id', $request->currency_id)->first();
                if ($senderBalance->balance_amount < $request->amount) {
                    return response()->json(['status' => false, 'msg' => "Insufficient Balance"]);
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

                return response()->json(['status' => true, 'msg' => "Amount Transfer Successfully"]);
            } else {
                return response()->json(['status' => false, 'msg' => "User Not Found"]);
            }
    }


    public function moneywithdrawal(Request $request)
    {

        $wallet = userWallet::where('user_id', Auth::user()->id)->first();
        if (100  < $request->amount) {
            return response()->json(['status' => false, 'msg' => "withdrawal amount not more than 100"]);
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
        return response()->json(['status' => true, 'msg' => "Amount Transfer Successfully"]);
    }

    public function alltransaction()
    {
        $wallet = userWallet::where('user_id', Auth::user()->id)->where('status', 'active')->first();
        $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
        $totalAmount = $balance->sum('balance_amount');
        $transactions = Transaction::where('sender_wallet_id',$wallet->id)->orWhere('receiver_wallet_id', $wallet->id)->get();
        foreach ($transactions as $trans) {
            $trans->currency = currencyModel::where('id', $trans->currency_id)->first();
        }
        return view('vendor.wallet.transaction', compact('transactions', 'totalAmount'));
    }


    
}
