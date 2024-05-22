<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketApiController extends Controller
{
    public function PostSupportTicket(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|integer',
                'product_id' => 'required|integer',
                'title' => 'required',
                'description' => 'required',
                'status' => 'required',

            ]);

            $ticket = new SupportTicket();
            $ticket->order_id = $request->order_id;
            $ticket->product_id = $request->product_id;
            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->status = $request->status;
            $ticket->save();

            return response()->json(['status' => true, 'message' => 'ticket store successfully', 'data' => $ticket,]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }
}
