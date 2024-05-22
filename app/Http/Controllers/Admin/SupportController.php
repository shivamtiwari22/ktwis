<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SupportTicketReplyController;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\SupportTicketReply;
use Carbon\Carbon;
use App\Mail\MyCustomMail;
use App\Models\CommissionCharge;
use App\Models\Country;
use App\Models\OrderSummary;
use App\Models\State;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\Testimonial;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function index_disputes()
    {
        return view('admin.support_desk.disputes.index');
    }

    public function list_disputes(Request $request)
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

        $loggedin = Auth::user()->id;
        $total = Dispute::with('disputemessages', 'order', 'customer', 'vendor')
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->where(function ($query) {
                $query
                    ->where('status', 'open')
                    ->orWhere('status', 'new')
                    ->orWhere('status', 'waiting');
            })
            ->count();

        $disputes = Dispute::with(
            'disputemessages',
            'order',
            'customer',
            'vendor'
        )
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->where(function ($query) {
                $query
                    ->where('status', 'open')
                    ->orWhere('status', 'new')
                    ->orWhere('status', 'waiting');
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        // return $disputes;

        $data = [];
        foreach ($disputes as $key => $dispute) {
            $vendor_name = User::find($dispute->vendor_id)->name;
            $action =
                '<a href="' .
                route('admin.disputes.view', $dispute->id) .
                '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>' .
                '<a href="' .
                route('admin.disputes.reply', $dispute->id) .
                '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-reply"></i></a>';

            $customer_name =
                $dispute->customer->name .
                '<br> <span><strong>Vendor</strong> : ' .
                $vendor_name .
                '<span/>';
            $status_dis = $dispute->status;
            $type = $dispute->type;
            if ($status_dis == 'new') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">NEW</span> <a>' .
                    $type .
                    '</a>';
            } elseif ($status_dis == 'open') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">OPEN</span> <a>' .
                    $type .
                    '</a>';
            } elseif ($status_dis == 'waiting') {
                $status =
                    '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff;">WAITING</span> <a>' .
                    $type .
                    '</a>';
            } elseif ($status_dis == 'solved') {
                $status = 'Solved';
            } elseif ($status_dis == 'closed') {
                $status = 'Closed';
            }
            $orderdata = $dispute->order->order_number;
            $refund_requested = $dispute->refund_requested;
            $refund_amount = $dispute->refund_amount;
            $response = $dispute->disputemessages->count();
            $lastUpdated =
                Carbon::parse($dispute->updated_at)->diffForHumans(null, true) .
                ' ago';

                $guarantee_charge = OrderSummary::find($dispute->order->order_summary_id)->guarantee_charge > 0 ? "Yes" : "No";

            $data[] = [
                $offset + $key + 1,
                $customer_name,
                $orderdata,
                $status,
                $refund_requested,
                $refund_amount,
                $response,
                $guarantee_charge,
                $lastUpdated,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
    public function list_disputes_close(Request $request)
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

        $loggedin = Auth::user()->id;
        $total = Dispute::with('disputemessages', 'order', 'customer', 'vendor')
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->count();

        $disputes = Dispute::with(
            'disputemessages',
            'order',
            'customer',
            'vendor'
        )
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })

            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($disputes as $key => $dispute) {
            if (
                isset($dispute['status']) &&
                strtolower($dispute['status']) === 'closed'
            ) {
                $vendor_name = User::find($dispute->vendor_id)->name;
                $action =
                    '<a href="' .
                    route('admin.disputes.reply', $dispute->id) .
                    '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-reply"></i></a>';

                $customer_name =
                    $dispute->customer->name .
                    '<br> <span><strong>Vendor</strong> : ' .
                    $vendor_name .
                    '<span/>';
                $status_dis = $dispute->status;
                $type = $dispute->type;
                if ($status_dis == 'closed') {
                    $status =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">closed</span> <a>' .
                        $type .
                        '</a>';
                } elseif ($status_dis == 'open') {
                    $status =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">OPEN</span> <a>' .
                        $type .
                        '</a>';
                } elseif ($status_dis == 'waiting') {
                    $status =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff;">WAITING</span> <a>' .
                        $type .
                        '</a>';
                } elseif ($status_dis == 'solved') {
                    $status = 'Solved';
                } elseif ($status_dis == 'closed') {
                    $status = 'Closed';
                }

                $refund_requested = $dispute->refund_requested;
                $refund_amount = $dispute->refund_amount;
                $response = $dispute->disputemessages->count();
                $lastUpdated =
                    Carbon::parse($dispute->updated_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';

                $data[] = [
                    $offset + $key + 1,
                    $customer_name,
                    $status,
                    $refund_requested,
                    $refund_amount,
                    $response,
                    $lastUpdated,
                    $action,
                ];
            }
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function reply_disputes($id)
    {
        $dispute = Dispute::with('disputemessages')
            ->where('id', $id)
            ->first();
        return view('admin.support_desk.disputes.reply', [
            'dispute' => $dispute,
        ]);
    }

    public function reply_store_disputes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'reply_status' => 'required',
            'content' => 'required',
            'attachment' => 'mimes:jpeg,jpg,png,gif|max:20000',
        ]);

        $validator->setCustomMessages([
            'attachment.max' => 'The attachment size must not exceed 20 MB.',
        ]);


        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ],
            );
        }


        $dispute = Dispute::find($request['id']);

        if (!$dispute) {
            return response()->json(
                ['status' => false, 'message' => 'Dispute not found.'],
                404
            );
        }

        $dispute->status = $request['reply_status'];
        $dispute->save();

        $message = new DisputeMessage();
        $message->dispute_id = $request['id'];

        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();

            $destinationPath = public_path('admin/attachment');
            $image->move($destinationPath, $image_name);

            $message->attachment = $image_name;
        }

        $message->message = $request['content'];
        $message->response_by_id = Auth::user()->id;
        $message->save();

        if ($message) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dispute reply submitted successfully.',
                    'location' => route('admin.disputes.index'),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }

    public function view_disputes($id)
    {
        $dispute = Dispute::find($id);
        $customer = User::where('id', $dispute->customer_id)->first();
        $vendor = User::where('id', $dispute->vendor_id)->first();
        $totalDisputes = Dispute::where('customer_id', $dispute->customer_id)
            ->where('order_id', $dispute->order_id)
            ->get();
        $totalVendorDisputes = Dispute::where([
            'order_id' => $dispute->order_id,
            'vendor_id' => $dispute->vendor_id,
        ])->get();

        $order = Order::find($dispute->order_id);

        $disputeMessages = DisputeMessage::where('dispute_id', $id)->get();
        foreach ($disputeMessages as $message) {
            $message->merchant = User::where(
                'id',
                $message->response_by_id
            )->first();
        }
        return view(
            'admin.support_desk.disputes.view',
            compact(
                'dispute',
                'disputeMessages',
                'customer',
                'totalDisputes',
                'vendor',
                'totalVendorDisputes',
                'order'
            )
        );
    }
    public function order_details($id)
    {
        $dispute = Dispute::find($id);
        $orderdata = OrderItem::where('order_id', $dispute->order_id)->get();
        $data = Product::where('id', $dispute->p_id)->get();
        $orderstatus = Order::where('id', $dispute->order_id)->first();
        $orderSummary = OrderSummary::where(
            'id',
            $orderstatus->order_summary_id
        )->first();
        $dataes = Payment::where(
            'order_summary_id',
            $orderSummary->id
        )->first();
        $customer = User::where('id', $dispute->customer_id)->first();
        $vendor = User::where('id', $dispute->vendor_id)->first();
        $totalDisputes = Dispute::where('customer_id', $dispute->customer_id)
            ->where('order_id', $dispute->order_id)
            ->get();
        $totalVendorDisputes = Dispute::where([
            'order_id' => $dispute->order_id,
            'vendor_id' => $dispute->vendor_id,
        ])->get();
        $disputeMessages = DisputeMessage::where('dispute_id', $id)->get();
        foreach ($disputeMessages as $message) {
            $message->merchant = User::where(
                'id',
                $message->response_by_id
            )->first();
        }
        return view(
            'admin.support_desk.disputes.order_view',
            compact(
                'dispute',
                'orderdata',
                'data',
                'orderstatus',
                'dataes',
                'disputeMessages',
                'customer',
                'totalDisputes',
                'vendor',
                'totalVendorDisputes'
            )
        );
    }
    public function Ticket()
    {
        return view('admin.support_desk.tickets.index');
    }
    public function list_tickets(Request $request)
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

        $loggedin = Auth::user()->id;
        $total = SupportTicket::select('support_tickets.*')
            ->with('responSupports')
            ->withCount('responSupports');

        $total = $total->count();

        $contact_us = SupportTicket::select('support_tickets.*')
            ->with('responSupports')
            ->withCount('responSupports');

        $disputes = $contact_us
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $i = 1 + $offset;
        $data = [];
        foreach ($disputes as $dispute) {
            if (
                isset($dispute['status']) &&
                (strtolower($dispute['status']) !== 'span' &&
                    strtolower($dispute['status']) !== 'solved')
            ) {
                $count = $dispute->respon_supports_count;

                $action =
                    '<a href="' .
                    route('admin.disputes.ticket_reply', $dispute->id) .
                    '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-reply"></i></a>' .
                    '<a href="' .
                    route('admin.disputes.update_ticket', $dispute->id) .
                    '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
                    '<a href="' .
                    route('admin.disputes.assign_ticker', $dispute->id) .
                    '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-crosshair"></i></a>';
                $lastUpdated =
                    Carbon::parse($dispute->updated_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
                $status_dis = $dispute->priority;
                if ($status_dis == 'Critical') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">Critical</span> <a>' .
                        '</a>';
                } elseif ($status_dis == 'High') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f39c12; color: #ffffff;">High</span> <a>' .
                        '</a>';
                } elseif ($status_dis == 'Normal') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color:#ffffff ;">Normal</span> <a>' .
                        '</a>';
                } elseif ($status_dis == 'Low') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">Low</span> <a>' .
                        '</a>';
                }

                $status_dises = $dispute->status;
                if ($status_dises == 'New') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f8f9fa; color: #3c8dbc;">new</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Open') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color: #ffffff;">open</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Pending') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color:#ffffff ;">pending</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Solved') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00a65a ; color: #ffffff;">solved</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Closed') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color:#ffffff ;">closed</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Span') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">span</span> <a>' .
                        '</a>';
                }

                $myvariable = $dispute->user_id;
                $myvariable = $dispute->user_id;

                $userData = User::find($myvariable);
                $userDataes = User::find($dispute->assigned_to);
                $myname = SupportTicketReply::find($dispute->id);
                $lastUpdated =
                    Carbon::parse($dispute->updated_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
                //    $combined=$dstatus;
                $combined = $dstatus . $dispute->type;

                $data[] = [
                    $userData->name,
                    $combined,
                    $statuses,
                    $count,
                    $userDataes->name,
                    $lastUpdated,
                    $action,
                ];
            }
        }
        $records = [];
        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        return response()->json($records);
    }
    public function update_ticket($id)
    {
        $dispute = SupportTicket::where('id', $id)->first();
        return view('admin.support_desk.tickets.reply', compact('dispute'));
    }
    public function up_ticket_data(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'reply_status' => 'required',
            'reply_priority' => 'required',
        ]);
        $dispute = SupportTicket::find($validatedData['id']);

        if (!$dispute) {
            return response()->json(
                ['status' => false, 'message' => 'Dispute not found.'],
                404
            );
        }
        $dispute->status = $validatedData['reply_status'];
        $dispute->priority = $validatedData['reply_priority'];
        $dispute->save();

        if ($dispute) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dispute reply submitted successfully.',
                    'location' => route('admin.disputes.Ticket'),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }
    public function assign_ticker($id)
    {
        $dispute = SupportTicket::where('id', $id)->first();
        $data = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereIn('role', ['admin', 'vendor']);
            })
            ->get();
        return view(
            'admin.support_desk.tickets.assign',
            compact('dispute', 'data')
        );
    }
    public function update_assign_ticket(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'user_name' => 'required',
        ]);
        $dispute = SupportTicket::find($validatedData['id']);

        if (!$dispute) {
            return response()->json(
                ['status' => false, 'message' => 'Dispute not found.'],
                404
            );
        }
        $dispute->assigned_to = $validatedData['user_name'];
        $dispute->save();

        if ($dispute) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dispute reply submitted successfully.',
                    'location' => route('admin.disputes.Ticket'),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }
    public function ticket_reply($id)
    {
        $dispute = SupportTicket::where('id', $id)->first();
        return view(
            'admin.support_desk.tickets.tickets_assign',
            compact('dispute')
        );
    }
    public function update_reply(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'reply_status' => 'required',
            'reply_priority' => 'required',
        ]);

        $dispute = SupportTicket::find($validatedData['id']);

        if (!$dispute) {
            return response()->json(
                ['status' => false, 'message' => 'Dispute not found.'],
                404
            );
        }
        $dispute->status = $validatedData['reply_status'];
        $dispute->priority = $validatedData['reply_priority'];
        $dispute->save();

        $newReply = new SupportTicketReply();
        if ($request->attachment) {
            $file = $request->attachment;
            $destinationPath = public_path() . '/public/ticket/';
            $extension = $file->getClientOriginalExtension();
            $filename = time() . rand(1, 100) . '.' . $extension;
            $publicparth = 'public/ticket/';
            $path = $publicparth . $filename;
            $newReply->image = $path;
            $file->move($destinationPath, $filename);
        } else {
            $newReply->image = null;
        }

        $newReply->message = $request->content;
        $newReply->support_tickets_id = $validatedData['id'];

        $newReply->save();
        if ($newReply) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dispute reply submitted successfully.',
                    'location' => route('admin.disputes.Ticket'),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }
    public function Close_list_tickets(Request $request)
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

        $loggedin = Auth::user()->id;

        $total = SupportTicket::select('support_tickets.*')->get();
        // return $total;
        $total = $total->count();

        $contact_us = SupportTicket::select('support_tickets.*');

        $disputes = $contact_us
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $i = 1 + $offset;
        $data = [];
        foreach ($disputes as $dispute) {
            if (
                isset($dispute['status']) &&
                (strtolower($dispute['status']) === 'span' ||
                    strtolower($dispute['status']) === 'solved')
            ) {
                $action =
                    ' <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' .
                    $dispute->id .
                    '"><i class="dripicons-time-reverse"></i></button>';

                $lastUpdated =
                    Carbon::parse($dispute->updated_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
                $status_dis = $dispute->priority;
                if ($status_dis == 'Critical') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">Critical</span> <a>' .
                        '</a>';
                } elseif ($status_dis == 'High') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f39c12; color: #ffffff;">High</span> <a>' .
                        '</a>';
                } elseif ($status_dis == 'Normal') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color:#ffffff ;">Normal</span> <a>' .
                        '</a>';
                } elseif ($status_dis == 'Low') {
                    $statuses =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">Low</span> <a>' .
                        '</a>';
                }

                $status_dises = $dispute->status;
                if ($status_dises == 'New') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f8f9fa; color: #3c8dbc;">new</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Open') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color: #ffffff;">open</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Pending') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color:#ffffff ;">pending</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Solved') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00a65a ; color: #ffffff;">solved</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Closed') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color:#ffffff ;">closed</span> <a>' .
                        '</a>';
                } elseif ($status_dises == 'Span') {
                    $dstatus =
                        '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">span</span> <a>' .
                        '</a>';
                }

                $myvariable = $dispute->user_id;
                $myvariable = $dispute->user_id;

                $userData = User::find($myvariable);
                $userDataes = User::find($dispute->assigned_to);
                $myname = SupportTicketReply::find($dispute->id);
                $lastUpdated =
                    Carbon::parse($dispute->updated_at)->diffForHumans(
                        null,
                        true
                    ) . ' ago';
                $combined = $dstatus . $dispute->type;

                $data[] = [
                    $userData->name,
                    $combined,
                    $statuses,
                    $userDataes->name,
                    $lastUpdated,
                    $action,
                ];
            }
        }
        $records = [];
        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        return response()->json($records);

        // if (isset($request->search['value'])) {
        //     $search = $request->search['value'];
        // } else {
        //     $search = '';
        // }

        // if (isset($request->length)) {
        //     $limit = $request->length;
        // } else {
        //     $limit = 10;
        // }

        // if (isset($request->start)) {
        //     $offset = $request->start;
        // } else {
        //     $offset = 0;
        // }

        // $orderRecord = $request->order[0]['dir'];
        // $nameOrder = $request->columns[$request->order[0]['column']]['name'];
        // $total =  SupportTicket::select('support_tickets.*')
        //     ->Where(function ($query) use ($search) {
        //         $query->orWhere('support_tickets.title', 'like', '%' . $search . '%');
        //         $query->orWhere('support_tickets.merchant', 'like', '%' . $search . '%');
        //     });
        // $total = $total->count();

        // $contact_us =  SupportTicket::select('support_tickets.*')
        //     ->Where(function ($query) use ($search) {
        //         $query->orWhere('support_tickets.title', 'like', '%' . $search . '%');
        //         $query->orWhere('support_tickets.merchant', 'like', '%' . $search . '%');
        //     });

        // $disputes = $contact_us->orderBy('id', $orderRecord)->limit($limit)->offset($offset)->get();

        // $i = 1 + $offset;

        // $data = [];
        // foreach ($disputes as $dispute) {
        // if (isset($dispute['status']) && (strtolower($dispute['status']) === 'spam' || strtolower($dispute['status']) === 'solved')) {
        //         // $action = '<a href="' . route('admin.disputes.restore', $dispute->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-reply"></i></a>';
        //         // $action = '<a href="'.route('admin.disputes.restore', $dispute->id)." class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-reply"></i></a>';
        //         $action = ' <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' . $dispute->id . '"><i class="dripicons-time-reverse"></i></button>';
        //         $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
        //         $status_dis =  $dispute->priority;
        //         if ($status_dis == "Critical") {
        //             $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">Critical</span> <a>'  . '</a>';
        //         } else if ($status_dis == "High") {
        //             $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f39c12; color: #ffffff;">High</span> <a>' .  '</a>';
        //         } else if ($status_dis == "Normal") {
        //             $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color:#ffffff ;">Normal</span> <a>' .  '</a>';
        //         } else if ($status_dis == "Low") {
        //             $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">Low</span> <a>' .  '</a>';
        //         }
        //         $status_dises =  $dispute->status;
        //         if ($status_dises == "new") {
        //             $dstatus = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">new</span> <a>'  . '</a>';
        //         } else if ($status_dises == "open") {
        //             $dstatus = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color: #ffffff;">open</span> <a>' .  '</a>';
        //         } else if ($status_dises == "pending") {
        //             $dstatus = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color:#ffffff ;">pending</span> <a>' .  '</a>';
        //         } else if ($status_dises == "solved") {
        //             $dstatus = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00a65a ; color: #ffffff;">solved</span> <a>' .  '</a>';
        //         } else if ($status_dises == "closed") {
        //             $dstatus = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #3c8dbc; color:#ffffff ;">closed</span> <a>' .  '</a>';
        //         } else if ($status_dises == "spam") {
        //             $dstatus = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: red; color: #ffffff;">spam</span> <a>' .  '</a>';
        //         }
        //         $combinedData = $dstatus . $dispute->title;
        //         $data[] = [
        //             $dispute->merchant,
        //             $combinedData,
        //             $status,
        //             $dispute->assigned,
        //             $lastUpdated,
        //             $action,
        //         ];
        //     }
        // }
        // $records = [];
        // $records['draw'] = intval($request->input('draw'));
        // $records['recordsTotal'] = $total;
        // $records['recordsFiltered'] = $total;
        // $records['data'] = $data;
        // return response()->json($records);
    }
    public function restore(Request $request)
    {
        // return $request->id;
        $dispute = SupportTicket::where('id', $request->id)->first();
        $dispute->status = 'open';
        $dispute->save();
        if ($dispute->save()) {
            return response()->json([
                'status' => true,
                'location' => route('admin.disputes.Ticket'),
                'msg' => ' Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function add_amin_note(Request $request)
    {
        $id = $request->id;

        $dispute = Dispute::find($request->id);

        $orderstatus = Order::where('id', $dispute->order_id)->first();
        $orderstatus->status = $request->reply_status;
        $orderstatus->save();

        if ($orderstatus) {
            return response()->json([
                'status' => true,
                'message' => 'Successfully',
            ]);
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }
    public function initiate_refund(Request $request)
    {
        $dispute = Dispute::find($request->id);

        $orderstatus = Order::where('id', $dispute->order_id)->first();
        $dataes = Payment::where('id', $orderstatus->payment_id)->first();
        if ($dataes->status === 'paid') {
            $dataes->status = 'unpaid';
        } else {
            $dataes->status = 'paid';
        }
        $dataes->save();
        if ($dataes) {
            return response()->json([
                'status' => true,
                'message' => 'Successfully',
            ]);
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }
    public function refund_data($id)
    {
        $dispute = Dispute::find($id);
        return view('admin.support_desk.disputes.refund', compact('dispute'));
    }

    public function update_refund(Request $request)
    {
        $id = $request->id;
        $dispute = Dispute::find($request->id);
        $dispute->description = $request->content;
        $dispute->refund_amount = $request->attachment;
        // $dispute->re_status = $request->priority;
        $dispute->save();
        if ($dispute) {
            return response()->json([
                'status' => true,
                'location' => route('admin.disputes.order_details', [
                    'id' => $id,
                ]),
                'message' => ' Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred, Please try again',
            ]);
        }
    }

    // testimonial

    public function test_monial()
    {
        return view('admin.testimonial.index');
    }
    public function test_show(Request $request)
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

        $total = Testimonial::count();

        $disputes = Testimonial::orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($disputes as $key => $dispute) {
            $action =
                '<a href="' .
                route('admin.test.edit_testimonial', $dispute->id) .
                '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
                ' <button class="  px-2 btn btn-danger deleteType " id="DeleteClient" data-id="' .
                $dispute->id .
                '"><i class="dripicons-trash"></i></button>';
            $datees = Carbon::parse($dispute->created_at)->format('Y-m-d');
            $banner_image =
                '<img src="' .
                asset('public/admin/testimonial/' . $dispute->profile_pics) .
                '" alt="Banner Image" width="40px">';
            $data[] = [
                $offset + $key + 1,
                $banner_image,
                $dispute->name,
                mb_substr($dispute->testimonial, 0,500),
                $datees,
                $dispute->rating,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
    public function test_monial_add()
    {
        return view('admin.testimonial.add');
    }
    public function add_testimonial(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required',
            'rating' => 'required',
            'file' => 'required',
            'status' => 'required',
            'content' => 'required',
        ]);

    
        if ($validatedData->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validatedData->errors()->first(),
                ],
            );
        }

        $dispute = new Testimonial();
        $dispute->name = $request['name'];
        $dispute->status = $request['status'];
        $dispute->testimonial = $request['content'];
        $dispute->rating = $request['rating'];

        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('admin/testimonial');
            $image->move($destinationPath, $image_name);
            $dispute->profile_pics = $image_name;
        } 

        $dispute->save();
        if ($dispute) {
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Data submitted successfully.',
                    'location' => route('admin.test.test_monial'),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to submit dispute reply.',
                ],
                500
            );
        }
    }
    public function edit_testimonial($id)
    {
        $dispute = Testimonial::find($id);
        return view('admin.testimonial.edit', compact('dispute'));
    }
    public function update_testimonial(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'rating' => 'required',
            'content' => 'required',
        ]);

    
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ],
            );
        }

        $dispute = Testimonial::find($request->id);
        $dispute->testimonial = $request->content;
        $dispute->name = $request['name'];
        $dispute->rating = $request->rating;
        $dispute->status = $request->status;

        if ($request->hasFile('name_file')) {
            $image = $request->file('name_file');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();
            $destinationPath = public_path('admin/testimonial');
            $image->move($destinationPath, $image_name);
            $dispute->profile_pics = $image_name;
        }
        $dispute->save();
        if ($dispute) {
            return response()->json([
                'status' => true,
                'location' => route('admin.test.test_monial'),
                'message' => 'Testimonial Data Updated Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error Occurred, Please try again',
            ]);
        }
    }
    public function delete_testimonial(Request $request)
    {
        $data = Testimonial::find($request->id);
        $data->delete();
        if ($data) {
            return response()->json([
                'status' => true,
                'location' => route('admin.test.test_monial'),
                'msg' => 'Testimonial Deleted Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function Invoicing($id)
    {
        return view('admin.testimonial.invoicing');
    }

    public function refund_datas()
    {
        return view('admin.support_desk.refunds.index');
    }
    public function order_list(Request $request)
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

        $loggedin = Auth::user()->id;
        $total = Order::with('user', 'vendor')
            ->where(function ($query) use ($search) {
                $query
                    ->whereHas('user', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->Where(function ($query) {
                $query->where('status', 'initiate_refund');
            })
            ->count();

          
        $disputes = Order::with('user', 'vendor')
        ->where(function ($query) use ($search) {
            $query
                ->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('vendor', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                });
        })
            ->Where(function ($query) {
                $query->where('status', 'initiate_refund');
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($disputes as $key => $dispute) {
            $vendor_name = User::find($dispute->seller_id)->name;
            $customer_names = User::find($dispute->user_id)->name;
            $action =
                '<a href="' .
                route('admin.refund.refund_customer_data', $dispute->id) .
                '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>' .
                '<a href="' .
                route('admin.refund.customer_data', $dispute->id) .
                '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-reply"></i></a>';

            $customer_name =
                $dispute->user->name .
                '<br> <span><strong>Vendor</strong> : ' .
                $vendor_name .
                '<span/>';
            $status_dis = "Initiate Refund";
            // $type = $dispute->type;
            // if ($status_dis == 'new') {
            //     $status =
            //         '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">NEW</span> <a>' .
            //         '</a>';
            // } elseif ($status_dis == 'open') {
            //     $status =
            //         '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">OPEN</span> <a>' .
            //         '</a>';
            // } elseif ($status_dis == 'waiting') {
            //     $status =
            //         '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff;">WAITING</span> <a>' .
            //         '</a>';
            // } elseif ($status_dis == 'solved') {
            //     $status = 'Solved';
            // } elseif ($status_dis == 'closed') {
            //     $status = 'Closed';
            // }
            $orderdata = $dispute->order_number;
            $guarantee_charge = OrderSummary::find($dispute->order_summary_id)->guarantee_charge > 0 ? "Yes" : "No";
            // $refund_requested = $dispute->refund_requested;
            $refund_amount = $dispute->total_refund_amount;
            // $response = $dispute->disputemessages->count();
            $lastCreated =
                Carbon::parse($dispute->created_at)->diffForHumans(
                    null,
                    true
                ) . ' ago';
            $lastUpdated =
                Carbon::parse($dispute->updated_at)->diffForHumans(
                    null,
                    true
                ) . ' ago';
            // $goodReceivedStatus = $dispute->good_received === 1 ? 'yes' : 'no';

            $data[] = [
                $offset + $key + 1,
                $orderdata,
                $vendor_name,
                $dispute->total_amount,
                $refund_amount,
                $status_dis,
                $guarantee_charge,
                $lastCreated,
                $lastUpdated,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
    public function refund_customer_data($id)
    {
        $dispute = Order::find($id);
        $customername = User::find($dispute->user_id);
        $useraddress = UserAddress::withTrashed()->where(
            'id',
            $dispute->shipping_address_id
        )->first();


        if($useraddress){
            $useraddress->country = Country::where('id',$useraddress->country)->orWhere('country_name',  $useraddress->country )->value('country_name');
            $useraddress->state = State::where('id', $useraddress->state) ->orWhere('state_name',   $useraddress->state )  ->value('state_name');
        }

        $orders = Order::
            where('id', $id)
            ->get();
        $totalAmount = 0;

        foreach ($orders as $dispute) {
            $orderSummary = OrderSummary::find($dispute->order_summary_id);
            $dispute->payment = Payment::where(
                'order_summary_id',
                $orderSummary->id
            )->first();

            if ($dispute->order) {
                $totalAmount += $dispute->total_amount;
            }
        }

        $orderCount = $orders->count();
        return view(
            'admin.support_desk.refunds.customer',
            compact(
                'dispute',
                'customername',
                'useraddress',
                'orders',
                'orderCount',
                'totalAmount'
            )
        );
    }

    public function customer_data($id)
    {
        $dispute = Order::find($id);
        $customername = User::find($dispute->user_id);
        $useraddress = UserAddress::withTrashed()->where(
            'id',
            $dispute->shipping_address_id
        )->first();

        
        if($useraddress){
            $useraddress->country = Country::where('id',$useraddress->country)->orWhere('country_name',  $useraddress->country )->value('country_name');
            $useraddress->state = State::where('id', $useraddress->state) ->orWhere('state_name',   $useraddress->state )  ->value('state_name');
        }
        
        $orders = Order::
            where('id', $id)
            ->get();
        $totalAmount = 0;


        $orderItem = OrderItem::where('order_id',$id)->get();

        foreach ($orders as $dispute) {
            $orderSummary = OrderSummary::find($dispute->order_summary_id);
            $dispute->payment = Payment::where(
                'order_summary_id',
                $orderSummary->id
            )->first();

            $dispute->orderSummary = $orderSummary;



            if ($dispute->order) {
                $totalAmount += $dispute->total_amount;
            }
        }

        $orderCount = $orders->count();
        return view(
            'admin.support_desk.refunds.customerdetails',
            compact(
                'dispute',
                'customername',
                'useraddress',
                'orders',
                'orderCount',
                'totalAmount'
            )
        );
    }

    public function payment_approve(Request $request)
    {

        $id = $request->datashow;
        $dispute = Order::find($id);
        // $dispute->refund_payment_status = $request->myapprove;
        // $dispute->save();

        if($request->myapprove == "approve"){
            $user = User::where('id',$dispute->user_id)->first();
            $arr = array();
            $arr['email'] =  $user->email;
            $arr['subject'] = 'Refund Initiated';
            $order = Order::where('id',$dispute->id)->first();
            $order->status = "refunded";
            $order->save();
        
            $data = array('order' => $order , 
              'dispute' => $dispute,
              'user' => $user);
            Mail::send('email.refundIniciated', ['data' => $data], function ($message) use ($arr) {
                $message->from('mail@dilamsys.com', "Ktwis")->to($arr['email'])->subject($arr['subject']);
            });


        }
      
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Refund submitted successfully.',
                    'location' => route('admin.refund.refund_datas'),
                ],
                200
            );
      
    }

    public function oreder_sataus(Request $request)
    {
        return $request->input('reply_status');
    }


    public function payoutDetails(){
         // get charge amount per product sale 
        $charge = CommissionCharge::first()->amount ?? 3;
        $orders = Order::whereNull('payment_url')->orderBy('id','DESC')
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

              $item->vendor = User::where('id',$item->seller_id)->first();
              $amount = $item->sub_total - $item->discount_amount ;
              $charge_amount = $item->item_count * $charge;
              $percentage_amount = $amount * $charge_amount/100;
              $item->commission =  $percentage_amount  ;
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

        return view('admin.reports.payouts.payout_details', compact('orders','totalAmount','paidAmount','returnAmount','balanceAmount','totalCommission'));
    }

    public function postPayoutDetails(Request $request){

        
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

        $total =  Order::
        whereNull('payment_url')
        ->join('users','orders.seller_id','users.id')
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
                ->orWhere('users.name','like','%'. $search . '%')
                ->orWhere('users.email','like','%'. $search . '%')
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

        $orders =  Order::
        whereNull('payment_url')
        ->join('users','orders.seller_id','users.id')
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
            ->orWhere('users.name','like','%'. $search . '%')
            ->orWhere('users.email','like','%'. $search . '%')
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


        $summary_id =  $orders->pluck('order_summary_id')->unique()->toArray();
        $order_summary = OrderSummary::whereIn('id', $summary_id)->sum('guarantee_charge');

        $charge = CommissionCharge::first()->amount ?? 3;

        $data = [];
        foreach ($orders as $key => $order) {

            $order->guarantee_charge =
            OrderSummary::find($order->order_summary_id)
                ->guarantee_charge > 0
                ? 'Yes'
                : 'No';

         $order->vendor = User::where('id',$order->seller_id)->first();     
         $amount = $order->sub_total - $order->discount_amount ;
         $charge_amount = $order->item_count * $charge;
         $percentage_amount = $amount * $charge_amount/100;

       $order->amount = $amount - $percentage_amount;

            $data[] = [
                $offset + $key + 1,
                '<a href="'.route('admin.order.show_product_detail_admin', ['id' => $order->id]).'" target="_blank">'. $order->order_number .'</a>' ,
                $order->vendor->name ,
                $order->vendor->email ,
                $order->created_at->format('M d,Y  H:i:s'),
                $order->guarantee_charge,
                $percentage_amount,
                ucwords($order->payment_release_status) ,
                date('M d, Y', strtotime($order->created_at . '+5 days')) ,
                $order->amount
               
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;
        $records['summary_total'] = $order_summary;

        echo json_encode($records);

    }
}
