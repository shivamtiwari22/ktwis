<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\EmailTemplate;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderSummary;
use App\Models\Product;
use App\Models\ReturnPolicy;
use App\Models\Specification;
use App\Models\Tax;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index_disputes()
    {
        return view('vendor.disputes.index');
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
        $total =  Dispute::with('disputemessages', 'order.orderItems', 'customer', 'vendor')
            ->where(function ($query) use ($search) {
                $query->whereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->where('vendor_id', $loggedin)
            ->where(function ($query) {
                $query->where('status', 'open')
                    ->orWhere('status', 'new')
                    ->orWhere('status', 'waiting');
            })->count();

        $disputes = Dispute::with('disputemessages', 'order.orderItems', 'customer', 'vendor')
            ->where(function ($query) use ($search) {
                $query->whereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->where('vendor_id', $loggedin)
            ->where(function ($query) {
                $query->where('status', 'open')
                    ->orWhere('status', 'new')
                    ->orWhere('status', 'waiting');
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

      
        $data = [];
        // return $disputes;
        foreach ($disputes as $key => $dispute) {
            $action = '<a href="' . route('vendor.disputes.view', $dispute->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct" title="View" data-toggle="tooltip"><i class="dripicons-preview"></i></a>' .
                '<a href="' . route('vendor.disputes.reply', $dispute->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"  title="Reply" data-toggle="tooltip"><i class="dripicons-reply"></i></a>';

            $customer_name = $dispute->customer->name;
            $status_dis = $dispute->status;
            $type = $dispute->type;
            if ($status_dis == "new") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">NEW</span> <a>' . $type . '</a>';
            } else if ($status_dis == "open") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">OPEN</span> <a>' . $type . '</a>';
            } else if ($status_dis == "waiting") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff;">WAITING</span> <a>' . $type . '</a>';
            } else if ($status_dis == "solved") {
                $status = 'Solved';
            } else if ($status_dis == "closed") {
                $status = 'Closed';
            }

            $refund_requested = $dispute->refund_requested;
            if ($refund_requested == "1") {
                $refund_amount = $dispute->refund_amount;
            } else {
                $refund_amount = "$0";
            }

            $response = $dispute->disputemessages->count();
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
            $orderId = Order::find($dispute->order_id);
            $orderSummary = OrderSummary::where('id',$orderId->order_summary_id)->first();
             $guarantee_charge = $orderSummary->guarantee_charge > 0 ? "Yes" : "No";
            $data[] = [
                $offset + $key + 1,
                $customer_name,
                '#'.$orderId->order_number,
                $status,
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
    public function list_disputes_show(Request $request)
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
        $total =  Dispute::with('disputemessages', 'order.orderItems', 'customer', 'vendor')
            ->where(function ($query) use ($search) {
                $query->whereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->where('vendor_id', $loggedin)
            ->where(function ($query) {
                $query->where('status', 'solved')
                    ->orWhere('status', 'closed');
            })
            ->count();

        $disputes = Dispute::with('disputemessages', 'order.orderItems', 'customer', 'vendor')
            ->where(function ($query) use ($search) {
                $query->whereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('vendor', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->where('vendor_id', $loggedin)
            ->where(function ($query) {
                $query->where('status', 'solved')
                    ->orWhere('status', 'closed');
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        // return $disputes;
        foreach ($disputes as $key => $dispute) {

            $action =
                '<a href="' . route('vendor.disputes.reply', $dispute->id) . '" class="px-2 btn btn-warning text-white mx-1" id="showproduct" title="Reply" data-toggle="tooltip"><i class="dripicons-reply" ></i></a>';

            $customer_name = $dispute->customer->name;
            $type = $dispute->type;
            $status_dis = $dispute->status;
            if ($status_dis == "new") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">NEW</span> <a>' . $type . '</a>';
            } else if ($status_dis == "open") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #ffffff;">OPEN</span> <a>' . $type . '</a>';
            } else if ($status_dis == "waiting") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff;">WAITING</span> <a>' . $type . '</a>';
            } else if ($status_dis == "solved") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f39c12; color: #fff;">Solved</span> <a>' . $type . '</a>';
            } else if ($status_dis == "closed") {
                $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f39c12; color: #fff;">closed</span> <a>' . $type . '</a>';
            }

            $refund_requested = $dispute->refund_requested;
            if ($refund_requested == "1") {
                $refund_amount = $dispute->refund_amount;
            } else {
                $refund_amount = "$0";
            }

            $response = $dispute->disputemessages->count();
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";

            $data[] = [
                $offset + $key + 1,
                $customer_name,
                $status,
                $refund_amount,
                $response,
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

    public function reply_disputes($id)
    {
        $dispute = Dispute::with('disputemessages')->where('id', $id)->first();
        return view('vendor.disputes.reply', ['dispute' => $dispute]);
    }

    public function reply_store_disputes(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'reply_status' => 'required',
            'content' => 'required',
        ]);
        $dispute = Dispute::find($validatedData['id']);

        if (!$dispute) {
            return response()->json(['status' => false, 'message' => 'Dispute not found.',], 404);
        }

        $dispute->status = $validatedData['reply_status'];
        $dispute->save();


        $message = new DisputeMessage();
        $message->dispute_id = $validatedData['id'];

        if ($request->hasFile('attachment')) {
            $image = $request->file('attachment');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('customer/dispute');
            $image->move($destinationPath, $image_name);

            $message->attachment = $image_name;
        }

        $message->message = $validatedData['content'];
        $message->response_by_id = Auth::user()->id;
        $message->save();

        // send notification to customer for dispute update 

        $FcmToken =  $FcmToken = User::whereNotNull('fcm_token')
        ->where('id', $dispute->customer_id)
        ->pluck('fcm_token')
        ->all();
        $title = "Dispute Update";
        $body = "Dispute Update: Vendor has responded to your dispute. Check your dashboard for details. If you have further concerns, reach out to support info@Ktwis.com. Thank you for your cooperation.";
        notification($title,$body,$FcmToken);

        if ($message) {
            return response()->json(['status' => true, 'message' => 'Dispute reply submitted successfully.', 'location' => route('vendor.disputes.index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }

    public function view_disputes($id)
    {
        $dispute = Dispute::with('customer', 'disputemessages', 'order.orderItems', 'vendor')->where('id', $id)->first();
        $customerID = $dispute->customer_id;

        $disputes = Dispute::whereHas('customer', function ($query) use ($customerID) {
            $query->where('customer_id', $customerID)->where('vendor_id',Auth::user()->id);
        })->get();
        $totalDisputes = $disputes->count();

        return view('vendor.disputes.view', ['dispute' => $dispute, 'totalDisputes' => $totalDisputes]);
    }

    // Message
    public function message_index()
    {


        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
        
        $datas = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->
        with([
            'roles' => function ($query) {
                $query->where('role', 'user');
            },
        ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        $message = EmailTemplate::get();

        return view('vendor.message.index', compact('datas', 'message'));
    }
    public function message_data(Request $request)
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


        $total =  Message::where(function ($query) {
            $query->where('spam', '=', null)
                ->orWhere('spam', '=', 0);
        })->where('received_by', $loggedin)
            ->where(function ($query) {
                $query->where('message', '!=', null);
            })
            ->where(function ($query) {
                $query->where('draft', '=', 0);
            })->count();

        $messages = Message::where(function ($query) {
            $query->where('spam', '=', null)
                ->orWhere('spam', '=', 0);
        })->where('received_by', $loggedin)
            ->where(function ($query) {
                $query->where('message', '!=', null);
            })->where(function ($query) {
                $query->where('draft', '=', 0);
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();


        $data = [];
        foreach ($messages as $key => $dispute) {
            $myvariable = User::find($dispute->created_by);
            $order = Order::where('id', $dispute->order_id)->first()->order_number ?? null;
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
            $combine = $myvariable->name . '|' . $myvariable->email;
            $statusdata = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . 'order:' . '#' . $dispute->order_id . '</span> <a>' . '</a>';

            $countdata = Message::where('received_by', $dispute->received_by)->where('created_by', $loggedin)->count();
            $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . $countdata . '</span> <a>' . '</a>';
            $orders =  $dispute->order_id ? $statusdata : '' ;
            $limitedMessage = substr($dispute->message, 0, 50);

             if($dispute->file ){
                $file =       '<a href="'.asset('public/vendor/file/' . $dispute->file ).'" class="anchor-deco"  download="file"> <i
                class="fa fa-download"></i>';
             }
             else {
                  $file = '';
             }
        
            

          
            $data[] = [
                '<input type="checkbox" class="task-checkbox" data-id="' . $dispute->id . '">',
                $combine,
                $limitedMessage,
                $orders,
                $file,
                $lastUpdated,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function sent_message()
    {
        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
        
        $datas = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->
        with([
            'roles' => function ($query) {
                $query->where('role', 'user');
            },
        ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        $message = EmailTemplate::get();
        return view('vendor.message.sentmessage', compact('datas', 'message'));
    }
    public function message_data_sent(Request $request)
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


        $total = Message::where('created_by', $loggedin)
        ->where(function ($query) {
            $query->where('spam', '=', null)
                ->orWhere('spam', '=', 0);
        })->where('draft',0)
        ->where(function ($query) {
            $query->where('message', '!=', null);
        })->count();

        $messages = Message::where('created_by', $loggedin)
            ->where(function ($query) {
                $query->where('spam', '=', null)
                    ->orWhere('spam', '=', 0);
            })->where('draft',0)
            ->where(function ($query) {
                $query->where('message', '!=', null);
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($messages as $key => $dispute) {
            $myvariable = User::find($dispute->received_by);
            $order = Order::where('id', $dispute->order_id)->first()->order_number ?? null;
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
            $combine = $myvariable->name . '|' . $myvariable->email;
            $statusdata = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . 'order:' . '#' .$dispute->order_id . '</span> <a>' . '</a>';

            $countdata = Message::where('received_by', $dispute->received_by)->where('created_by', $loggedin)->count();
            $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . $countdata . '</span> <a>' . '</a>';
            $orders =   $dispute->order_id ? $statusdata : '' ;
            $limitedMessage = substr(strip_tags($dispute->message), 0, 50);
            if($dispute->file ){
                $file =       '<a href="'.asset('public/vendor/file/' . $dispute->file ).'" class="anchor-deco"  download="file"> <i
                class="fa fa-download"></i>';
             }
             else {
                  $file = '';
             }

          
            $data[] = [
                '<input type="checkbox" class="task-checkbox" data-id="' . $dispute->id . '">',
                $combine,
                $limitedMessage,
                $file,
                $lastUpdated,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function draft_message()
    {
        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
        
        $datas = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->
        with([
            'roles' => function ($query) {
                $query->where('role', 'user');
            },
        ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();


        $message = EmailTemplate::get();
        return view('vendor.message.draftmessage', compact('datas', 'message'));
    }
    public function message_data_draft(Request $request)
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


        $total =    Message::where('draft', '1')->where('created_by', $loggedin)
        ->where(function ($query) {
            $query->where('message', '!=', null);
        })->count();

        $messages =
            Message::where('draft', '1')->where('created_by', $loggedin)
            ->where(function ($query) {
                $query->where('message', '!=', null);
            })

            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
    //    return $messages;
        $data = [];
        foreach ($messages as $key => $dispute) {
            $myvariable = User::find($dispute->received_by);
            $order = Order::where('id', $dispute->order_id)->first()->order_number ?? null;
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
            $combine =    $myvariable ?   $myvariable->name  . '|' . $myvariable->email  : '';
            $statusdata = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . 'order:' . '#' . $dispute->order_id . '</span> <a>' . '</a>';

            // $countdata = Message::where('received_by', $dispute->received_by)->where('created_by', $loggedin)->count();
            // $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . $countdata . '</span> <a>' . '</a>';
            $orders =   $dispute->order_id ? $statusdata : '';
            $limitedMessage = substr(strip_tags($dispute->message), 0, 50);
            if($dispute->file ){
                $file =       '<a href="'.asset('public/vendor/file/' . $dispute->file ).'" class="anchor-deco"  download="file"> <i
                class="fa fa-download"></i>';
             }
             else {
                  $file = '';
             }
          
            $data[] = [
                '<input type="checkbox" class="task-checkbox" data-id="' . $dispute->id . '">',
                $combine,
                $limitedMessage,
                $file,
                $lastUpdated,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }





    public function spams_message()
    {

        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
        
        $datas = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->
        with([
            'roles' => function ($query) {
                $query->where('role', 'user');
            },
        ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        $message = EmailTemplate::get();
        return view('vendor.message.spamsmessage', compact('datas', 'message'));
    }
    public function message_data_spam(Request $request)
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


        $total = Message::where('spam', 1)->where(function ($query) use ($loggedin){
            $query->where('created_by', $loggedin)->orWhere('received_by', $loggedin);
        })
        ->count();   

        $messages = Message::where('spam', 1)->where(function ($query) use ($loggedin){
            $query->where('created_by', $loggedin)->orWhere('received_by', $loggedin);
        })->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];

        foreach ($messages as $key => $dispute) {
            if($dispute->type == 'sent'){
                $myvariable = User::find($dispute->received_by);

            }
            else {
                $myvariable = User::find($dispute->created_by);

            }
            $order = Order::where('id', $dispute->order_id)->first()->order_number ?? null;
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
            $combine = $myvariable->name . '|' . $myvariable->email;
            $statusdata = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . 'order:' . '#' . $dispute->order_id. '</span> <a>' . '</a>';

            $countdata = Message::where('received_by', $dispute->received_by)->where('created_by', $loggedin)->count();
            $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . $countdata . '</span> <a>' . '</a>';
            $orders =  $dispute->order_id ? $statusdata : '' ;
            $limitedMessage = substr(strip_tags($dispute->message), 0, 50);
            if($dispute->file ){
                $file =       '<a href="'.asset('public/vendor/file/' . $dispute->file ).'" class="anchor-deco"  download="file"> <i
                class="fa fa-download"></i>';
             }
             else {
                  $file = '';
             }

          
            $data[] = [
                '<input type="checkbox" class="task-checkbox" data-id="' . $dispute->id . '">',
                $combine,
                $limitedMessage,
                $orders,
                $file,
                $lastUpdated,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function trash_message()
    {
        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
        
        $datas = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->
        with([
            'roles' => function ($query) {
                $query->where('role', 'user');
            },
        ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();

        $message = EmailTemplate::get();
        return view('vendor.message.trashmessage', compact('datas', 'message'));
    }
    public function trash_message_data_spam(Request $request)
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


        $total =    Message::where(function ($query) use ($loggedin) {
            $query->where('created_by',$loggedin)
                  ->orWhere('received_by',$loggedin);
        })->onlyTrashed()->count();

        $messages =
            Message::where(function ($query) use ($loggedin) {
                $query->where('created_by',$loggedin)
                      ->orWhere('received_by',$loggedin);
            })
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->onlyTrashed()->get();

        $data = [];
        foreach ($messages as $key => $dispute) {
            if($dispute->type == 'sent'){
                $myvariable = User::find($dispute->received_by);
            }
            else {
                $myvariable = User::find($dispute->created_by);
            }
            
            $order = Order::where('id', $dispute->order_id)->first()->order_number ?? null;
            $lastUpdated = Carbon::parse($dispute->updated_at)->diffForHumans(null, true) . " ago";
            $combine = $myvariable->name . '|' . $myvariable->email;
            $statusdata = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . 'order:' . '#' . $dispute->order_id . '</span> <a>' . '</a>';

            $countdata = Message::where('received_by', $dispute->received_by)->where('created_by', $loggedin)->count();
            $status = '<span class="label" style="display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">' . $countdata . '</span> <a>' . '</a>';
            $orders =  $dispute->order_id ? $statusdata : '';
            $limitedMessage = substr(strip_tags($dispute->message), 0, 50);
            if($dispute->file ){
                $file =       '<a href="'.asset('public/vendor/file/' . $dispute->file ).'" class="anchor-deco"  download="file"> <i
                class="fa fa-download"></i>';
             }
             else {
                  $file = '';
             }
          
            $data[] = [
                '<input type="checkbox" class="task-checkbox" data-id="' . $dispute->id . '">',
                $combine,
                $limitedMessage,
                 $orders ,
                 $file,
                $lastUpdated,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function spams_data(Request $request)
    {


        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->get();
        if(Count($messages) == 0){
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }

        foreach ($messages as $message) {
            $message->spam = '1';
            $message->save();
        }

        if ($message) {
            return response()->json(['status' => true, 'message' => 'Data Moved to Spam Successfully', 'location' => route('vendor.message.message_index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.'], 500);
        }
    }


    public function trash_data(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->delete();



        if ($messages) {
            return response()->json(['status' => true, 'message' => 'Data trash  successfully.', 'location' => route('vendor.message.message_index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }
    public function sent_spams_data(Request $request)
    {

        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->get();

        if(Count($messages) == 0){
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
        foreach ($messages as $message) {
            $message->spam = '1';
            $message->save();
        }

        if ($message) {
            return response()->json(['status' => true, 'message' => 'Data Moved to Spam Successfully.', 'location' => route('vendor.message.sent_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }


    public function sent_trash_data(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->delete();

        if ($messages) {
            return response()->json(['status' => true, 'message' => 'Message Moved to Trash Successfully.', 'location' => route('vendor.message.sent_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }
    public function draft_spams_data(Request $request)
    {

        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->get();
        if(Count($messages) == 0){
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }

        foreach ($messages as $message) {
            $message->spam = '1';
            $message->save();
        }

        if ($message) {
            return response()->json(['status' => true, 'message' => 'Draft data Spam successfully.', 'location' => route('vendor.message.draft_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }


    public function draft_trash_data(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->delete();



        if ($messages) {
            return response()->json(['status' => true, 'message' => 'Data trash  successfully.', 'location' => route('vendor.message.draft_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }
    public function spams_spams_data(Request $request)
    {

        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->get();
        if(Count($messages) == 0){
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }

        foreach ($messages as $message) {
            $message->spam = '0';
            $message->save();
        }

        if ($message) {
            return response()->json(['status' => true, 'message' => 'Data move to inbox successfully.', 'location' => route('vendor.message.spams_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }
    public function   spam_data_delete(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->forceDelete();

        if ($messages) {
            return response()->json(['status' => true, 'message' => 'Data Deleted Permanently.', 'location' => route('vendor.message.spams_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }
    public function trash_spams_data(Request $request)
    {

        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $message = Message::withTrashed()->where('id', $ids)->restore();

        if ($message) {
            return response()->json(['status' => true, 'message' => 'Data move to inbox successfully.', 'location' => route('vendor.message.trash_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }
    public function   trash_data_delete(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $messages = Message::whereIn('id', $ids)->forceDelete();

        if ($messages) {
            return response()->json(['status' => true, 'message' => 'Data Deleted Permanently.', 'location' => route('vendor.message.trash_message'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Please select at least one checkbox.',], 500);
        }
    }

    public function compose_new_message()
    {

        $getUserId = Order::where('seller_id',Auth::user()->id)->pluck('user_id')->toArray();
        
        $datas = User::where('created_by',Auth::user()->id)->orWhereIn('id',$getUserId)->
        with([
            'roles' => function ($query) {
                $query->where('role', 'user');
            },
        ])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'user');
            })
            ->get();


        return view('vendor.message.newcompose', compact('data'));
    }

    public function message_saved_as_draft(Request $request){
           $message = strip_tags($request->message);
        if($request->customer || $request->sub || $message){

            $myvariable = new Message();
            $myvariable->received_by = $request['customer'];
            $myvariable->subject = $request['sub'];
            $myvariable->message = $request['message'];
            $myvariable->draft = '1';
            $myvariable->created_by = Auth::user()->id;
            if ($request->hasFile('file_data')) {
                $image = $request->file('file_data');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/file');
                $image->move($destinationPath, $image_name);
                $myvariable->file = $image_name;
            }
            $myvariable->save();
        }
            
        return response()->json(['status' => true ]);
    }


    public function composer_data_send(Request $request)
    {
        $validatedData = $request->validate([
            'customer' => 'required',
            'subject' => 'required',
            'message' => 'required',

        ]);

        if (!$validatedData) {
            return response()->json(['status' => false, 'message' => 'Dispute not found.',], 404);
        }
        $user = Auth::user();

        $myvariable = new Message();
        $myvariable->received_by = $validatedData['customer'];
        $myvariable->subject = $validatedData['subject'];
        $myvariable->message = $validatedData['message'];
        $myvariable->draft = '1';
        $myvariable->type = 'sent';
        $myvariable->created_by = $user->id;
        if ($request->hasFile('file_data')) {
            $image = $request->file('file_data');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/file');
            $image->move($destinationPath, $image_name);
            $myvariable->file = $image_name;
        }
        $myvariable->save();

        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Message Saved As Draft Successfully.', 'location' => route('vendor.message.message_index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }
    public function composer_data_send_save(Request $request)
    {
        $validatedData = $request->validate([
            'customer' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);
        if (!$validatedData) {
            return response()->json(['status' => false, 'message' => 'Dispute not found.',], 404);
        }
        $user = Auth::user();

        $myvariable = new Message();
        $myvariable->received_by = $validatedData['customer'];
        $myvariable->subject = $validatedData['subject'];
        $myvariable->message = $validatedData['message'];
        $myvariable->type = 'sent';
        $myvariable->draft = '0';
        $myvariable->created_by = $user->id;
        if ($request->hasFile('file_data')) {
            $image = $request->file('file_data');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/file');
            $image->move($destinationPath, $image_name);
            $myvariable->file = $image_name;
        }
        $myvariable->save();

        // send notification to customer 
        $FcmToken =  $FcmToken = User::whereNotNull('fcm_token')
        ->where('id', $validatedData['customer'])
        ->pluck('fcm_token')
        ->all();

           $title = "Message";
            $body = "You have received vendor message please check your dashboard";
            notification($title,$body,$FcmToken);

        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Message Saved Successfully.', 'location' => route('vendor.message.message_index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }


    public function email_template(Request $request)
    {
        $validatedData = $request->validate([
            'customer' => 'required',
            'email' => 'required',

        ]);

        if (!$validatedData) {
            return response()->json(['status' => false, 'message' => 'Dispute not found.',], 404);
        }
        $user = Auth::user();

        $myvariable = new Message();
        $myvariable->draft = '0';
        $myvariable->created_by = $user->id;
        $myvariable->received_by = $validatedData['customer'];
        $myvariable->email_id = $validatedData['email'];
        if ($request->hasFile('file_data')) {
            $image = $request->file('file_data');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/file');
            $image->move($destinationPath, $image_name);
            $myvariable->file = $image_name;
        }
        $myvariable->save();

       

        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Data save  successfully.', 'location' => route('vendor.message.message_index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }
    public function email_template_draft(Request $request)
    {
        $validatedData = $request->validate([
            'customer' => 'required',
            'email' => 'required',

        ]);

        if (!$validatedData) {
            return response()->json(['status' => false, 'message' => 'Dispute not found.',], 404);
        }
        $user = Auth::user();

        $myvariable = new Message();
        $myvariable->draft = '1';
        
        $myvariable->created_by = $user->id;
        $myvariable->received_by = $validatedData['customer'];
        $myvariable->email_id = $validatedData['email'];
        if ($request->hasFile('file_data')) {
            $image = $request->file('file_data');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/file');
            $image->move($destinationPath, $image_name);
            $myvariable->file = $image_name;
        }
        $myvariable->save();

        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Message Save As Draft Successfully.', 'location' => route('vendor.message.message_index'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }


    public function  specifications()
    {
        // $categorys =Product::all();
        $products = Product::where('created_by',auth()->user()->id)->get();

        $categorys = [];

        foreach ($products as $product) {
            $productId = $product->id;
            $specifications = Specification::where('product_id', $productId)->get();
            if ($specifications->isEmpty()) {
                $categorys[] = $productId;
            }
        }

        $productsWithoutSpecifications = Product::whereIn('id', $categorys)->get();
        return view('vendor.specification.index', compact('productsWithoutSpecifications'));
    }


    public function specifications_data(Request $request)
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


        $total =  Specification::join('products','specifications.product_id','products.id')->select('specifications.*','products.name')->where(function ($query) use ($search) {
            $query
                ->orWhere('products.name','like', '%' . $search . '%');

        }) ->  where('specifications.created_by',$loggedin)->count();

        $messages = Specification::
            join('products','specifications.product_id','products.id')->select('specifications.*','products.name')->where(function ($query) use ($search) {
                $query
                    ->orWhere('products.name','like', '%' . $search . '%');
    
            })->
            where('specifications.created_by',$loggedin)
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($messages as $key => $dispute) {
            $action = '
                    <p class="btn btn-warning rounded px-2 py-1 openModalButton" data-bs-toggle="modal" data-bs-target="#exampleModal" data-dispute="' . $dispute->id . '"   title="Edit" data-toggle="tooltip">
                        <i class="dripicons-document-edit"></i>
                    </p>' .
                '<p class="btn btn-danger rounded px-2 py-1 deleteType" style="    margin-left: 3%;" data-bs-toggle="modal" data-bs-target="#exampleModal" id="DeleteClient" data-id="' . $dispute->id .  '" title="Delete" data-toggle="tooltip" >
                    <i class="dripicons-trash"></i>
                    </p>';
            $mydataes = Product::where('id', $dispute->product_id)->first();
            $myvariable = User::find($dispute->created_by); 
            $content = strip_tags($dispute->message);
            $shortDescription = implode(' ', array_slice(str_word_count($content, 1), 0, 10));
            $shortDescription .= '...';
            $data[] = [
                $key + 1,
                $mydataes->name,
                $shortDescription,
                $action,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }
    public function spacification_edit(Request $request)
    {
        $disputeId = $request->input('disputeId');
        $data = Specification::find($disputeId);
        return response()->json(compact('data'));
    }
    public function update_specification(Request $request)
    {
        $ids = $request->id;
        $myvariable = Specification::find($ids);
        $myvariable->message = $request->message;
        $myvariable->save();
        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Data update  successfully.', 'location' => route('vendor.specifications'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }

    public function return_cancellation()
    {
        // $categorys =Category::all();
        $products = Category::all();
        $categorys = [];

        foreach ($products as $product) {
            $productId = $product->id;
            $specifications = ReturnPolicy::where('category_id', $productId)->where('created_by',Auth::user()->id)->get();
            if ($specifications->isEmpty()) {
                $categorys[] = $productId;
            }
        }

        $productsWithoutSpecifications = Category::whereNull('parent_category_id')->whereIn('id', $categorys)->get();

        $categories = Category::whereNull('parent_category_id')->get();
        return view('vendor.return_policy.index', compact('productsWithoutSpecifications','categories'));
    }
    public function return_policy_add(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'category' => 'required',
        ]);

        $exists = ReturnPolicy::where('category_id', $validatedData['category'])->where('created_by', Auth::user()->id)->exists();
        if($exists){
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Return & Policy Exists',
                ],
                409
            );
        }

        $loggedin = Auth::user()->id;
        $data = new ReturnPolicy();
        $data->subject = $validatedData['subject'];
        $data->message = $validatedData['message'];
        $data->category_id = $validatedData['category'];
        $data->created_by = $loggedin;
        $data->save();
        
        if ($data) {
            return response()->json(['status' => true, 'message' => 'Data update  successfully.', 'location' => route('vendor.return_cancellation'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }

    public function     return_cancellation_data(Request $request)
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


        
        $total =  ReturnPolicy::where('created_by',$loggedin)
        ->where(function ($query) use ($search) {
            $query
                // ->orWhere('categories.category_name','like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%');
        })->
        count();
        $messages = ReturnPolicy::
        where('created_by',$loggedin)
        ->where(function ($query) use ($search) {
            $query
                // ->orWhere('categories.category_name','like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%');
        })
        ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        foreach ($messages as $key => $dispute) {
            $action = '
            <p class="btn btn-warning openModalButton" data-bs-toggle="modal" data-bs-target="#exampleModal" data-dispute="' . $dispute->id . '"  title="Edit" data-toggle="tooltip">
                <i class="dripicons-document-edit"></i>
            </p>' . '<p class="btn btn-danger rounded   deleteType" style="    margin-left: 3%;" data-bs-toggle="modal" data-bs-target="#exampleModal" id="DeleteClient" data-id="' . $dispute->id .  '"  title="Delete" data-toggle="tooltip">
            <i class="dripicons-trash"></i>
            </p>';
            $mydataes = Category::where('id', $dispute->category_id)->first()->category_name ?? "uncatgorized";
            $content = strip_tags($dispute->message);
            $shortDescription = implode(' ', array_slice(str_word_count($content, 1), 0, 4));
            $shortDescription .= '...';
            $subject = strip_tags($dispute->subject);
            $shortDescriptionSubject = implode(' ', array_slice(str_word_count($content, 1), 0, 4));
            $shortDescriptionSubject .= '...';
            $data[] = [
                $key + 1,
                $mydataes ,
                $subject,
                $shortDescription,
                $action,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function delete_return_policy(Request $request)
    {
        $category = ReturnPolicy::find($request->id);
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('vendor.return_cancellation'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }

    public function return_policy_edit(Request $request)
    {

        $disputeId = $request->input('disputeId');
        $data = ReturnPolicy::find($disputeId);
        return response()->json(compact('data'));
    }
    public function update_return_policy(Request $request)
    {
        $ids = $request->id;
        $myvariable = ReturnPolicy::find($ids);
        $myvariable->message = $request->message;
        $myvariable->subject = $request->subject;
        $myvariable->category_id = $request['category'];
        $myvariable->save();
        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Data update  successfully.', 'location' => route('vendor.return_cancellation'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }


    public function add_specification(Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required',
            'category' => 'required',
        ]);


        $loggedin = Auth::user()->id;
        $data = new Specification();
        $data->message = $validatedData['message'];
        $data->product_id = $validatedData['category'];
        $data->created_by = $loggedin;
        $data->save();
        if ($data) {
            return response()->json(['status' => true, 'message' => 'Data update  successfully.', 'location' => route('vendor.specifications'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }
    }
    public function delete_specification(Request $request)
    {
        $category = Specification::find($request->id);
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('vendor.specifications'), 'msg' => "Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }
}
