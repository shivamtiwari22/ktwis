<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\currencyBalance;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userWallet;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index_payouts()
    {
        return view('admin.reports.payouts.index');
    }

    public function list_payout(Request $request)
    {
        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }

        $typeFilter = $request->input('type_filter');
        $statusFilter = $request->input('status_filter');

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

        $query = Transaction::with('userWallet.user.shops')
            ->where(function ($query) use ($search) {
                $query->orWhere('transaction_type', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('amount', 'like', '%' . $search . '%')
                    ->orWhereHas('userWallet.user.shops', function ($query) use ($search) {
                        $query->where('shop_name', 'like', '%' . $search . '%');
                    });
            });

        if ($typeFilter) {
            $query->where('transaction_type', $typeFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $total = $query->count();

        $transactions = $query->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($transactions as $transaction) {

            $status=$transaction->status;
            $date_times = $transaction->created_at;
            $dateTime = new \DateTime($date_times);
            $date_time = $dateTime->format('M j, Y');

            $wallet_id = $transaction->receiver_wallet_id;
            if ($wallet_id) {
                $wallet = userWallet::where('id', $wallet_id)->first();
                if ($wallet) {
                    $user = User::where('id', $wallet->user_id)->first();
                    if ($user) {
                        $shop = Shop::where('created_by', $user->id)->first();
                        $shop_name = $shop->shop_name;
                    } else {
                        $shop_name = "null";
                    }
                } else {
                    $shop_name = "null";
                }
            } else {
                $shop_name = "null";
            }

            $type = $transaction->transaction_type;

            $balance = currencyBalance::where('wallet_id', $wallet_id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($balance) {

                $balance_amount = $balance->balance_amount;
            } else {
                $balance_amount = "0";
            }
            $amount = $transaction->amount;

            $data[] = [
                $date_time,
                $date_time,
                // $shop_name,
                $type,
                $status,
                $balance_amount,
                $amount,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function index_performance()
    {
        //active vendor
        $vendorCount = User::whereHas('roles', function ($query) {
            $query->where('role', 'vendor');
        })
            ->where('admin_approval', 'publish')
            ->count();

        //trailing vendor
        $vendor_trail_Count = User::whereHas('roles', function ($query) {
            $query->where('role', 'vendor');
        })
            ->where('admin_approval', 'reject')
            ->count();
        
        return view('admin.reports.performance.index', [
            'vendorCount' => $vendorCount,
            'vendor_trail_Count' => $vendor_trail_Count,
        ]);
    }
}
