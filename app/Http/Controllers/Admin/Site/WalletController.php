<?php

namespace App\Http\Controllers\Admin\Site;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\currencyBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userWallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function myWallet()
    {

        $wallet = userWallet::where('user_id', Auth::user()->id)->where('status', 'active')->first();

        if (!$wallet) {
            return redirect('vendor/dashboard')->with('message', "wallet is not active");
        }
        $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
        foreach ($balance as $item) {
            $item->currency = Currency::where('id', $item->currency_id)->first();
        }
        $totalAmount = $balance->sum('balance_amount');

        return view('admin.site.wallet.index', compact('wallet', 'balance', 'totalAmount'));
    }

    public function alltransaction()
    {

        $wallet = userWallet::where('user_id', Auth::user()->id)->where('status', 'active')->first();
        $balance = currencyBalance::where('wallet_id', $wallet->id)->get();
        $totalAmount = $balance->sum('balance_amount');
        $transactions = Transaction::all();
        foreach ($transactions as $trans) {
            $sender_id = userWallet::where('id', $trans->sender_wallet_id)->first();
            $receiver_id = userWallet::where('id', $trans->receiver_wallet_id)->first();
            $trans->sender =  User::where('id', $sender_id->user_id)->first();
            $trans->receiver = User::where('id', $receiver_id->user_id)->first();
            $trans->currency = Currency::where('id', $trans->currency_id)->first();
        }
        return view('admin.site.wallet.transactions', compact('transactions', 'totalAmount'));
    }


    public function changeStatus(Request $request)
    {
        $transaction = Transaction::find($request->id);
        $transaction->status = $request->status;
        $transaction->save();

        if ($transaction  &&  $request->status == "Completed") {
            $wallet = userWallet::find($transaction->receiver_wallet_id);
            $balance = currencyBalance::where('wallet_id', $transaction->receiver_wallet_id)->where('currency_id', $transaction->currency_id)->first();
            $balance->balance_amount = $balance->balance_amount -  $transaction->amount;
            $balance->save();

            $withdraw = new Withdrawal();
            $withdraw->user_id = $wallet->user_id;
            $withdraw->wallet_id = $wallet->id;
            $withdraw->currency_id = $transaction->currency_id;
            $withdraw->amount =  $transaction->amount;
            $withdraw->payment_method = "Online";
            $withdraw->save();
        }

        return response()->json(['status' => true, 'msg' => "Status Change Successfully"]);
    }
}
