<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function invoice_index()
    {
        return view('vendor.invoices.index');
    }

    public function list_invoice(Request $request)
    {
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

        $currentUserId = Auth::user()->id;
        $vendor = Vendor::where('user_id', $currentUserId)->first();
        $vendor_user_id = $vendor->id;

        $query = Invoice::with('order.user', 'order.orderItems')
            ->where(function ($query) use ($search) {
                $query->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhereHas('order.user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->whereHas('order', function ($query) use ($vendor_user_id) {
                $query->whereHas('orderItems', function ($query) use ($vendor_user_id) {
                    $query->where('seller_id', $vendor_user_id);
                });
            });

        $total = $query->count();


        $invoices = $query->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($invoices as $key => $invoice) {
            $action = '<a href="' . route('vendor.invoice.view', $invoice->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>';

            $invoice_name = $invoice->invoice_number;
            $customer_name = $invoice->order->user->name;

            $data[] = [
                $offset + $key + 1,
                $invoice_name,
                $customer_name,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function invoice_view($id)
    {
        $invoice = Invoice::with('order.orderItems.product.inventory', 'order.orderItems.product.inventoryVariants.variants', 'order.shippingAddress.states.country','order.user')->where('id', $id)->first();
        return view('vendor.invoices.invoice',['invoice' => $invoice]);
        // return $invoice;die();
    }
}