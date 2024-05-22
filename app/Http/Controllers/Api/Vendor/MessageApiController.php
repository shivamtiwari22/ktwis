<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\Message;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageApiController extends Controller
{
    public function message_add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required',
                'message' => 'required',
                'received_by' => 'required|integer'
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
            $user = Auth::user();
            $product = new Message();
            $product->subject = $request->input('subject');
            $product->message = $request->input('message');
            $product->created_by = $user->id;
            $product->received_by = $request->input('received_by');
            $product->save();
            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/file');
                $image->move($destinationPath, $image_name);
                $product->file = $image_name;
            }
            $product->save();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Message send successfully',
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



    public function compose_new(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'message' => 'required',
                'draft' => 'required',
                'subject' => 'required'

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
            $user = Auth::user();
            $product = new Message();
            $product->received_by = $request->input('customer_id');
            $product->subject = $request->input('subject');
            $product->message = $request->input('message');
            $product->created_by = $user->id;
            $product->draft = $request->input('draft');

            if ($request->hasFile('file')) {
                $image = $request->file('file');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/file');
                $image->move($destinationPath, $image_name);
                $product->file = $image_name;
            }

            $product->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Message send successfully',
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

    public function inbox_message()
    {
        try {
            $data = Message::
            where(function ($query) {
                $query->where('spam', '=', null)
                    ->orWhere('spam', '=', 0);
            })
            ->where(function ($query) {
                $query->where('message', '!=', null);
            })
            ->get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'All message  show successfully',
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
    public function sent_message()
    {
        try {
            $loggedin = Auth::user()->id;

            $data = Message::where('created_by', $loggedin)
                ->where(function ($query) {
                    $query->where('spam', '=', null)
                        ->orWhere('spam', '=', 0);
                })
                ->where(function ($query) {
                    $query->where('message', '!=', null);
                })->get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'sent Message data show successfully',
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
    public function draft_message()
    {
        try {
            $loggedin = Auth::user()->id;
            $data = Message::where('draft', '1')->where('created_by', $loggedin)
                ->where(function ($query) {
                    $query->where('message', '!=', null);
                })->get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Draft Message  show successfully',
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
    public function spams_message()
    {
        try {
            $loggedin = Auth::user()->id;


            $data = 
            Message::where('created_by', $loggedin)
            ->where(function ($query) {
                $query->where('spam', '=', 1);
            })
              
                ->get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'spam Message  show successfully',
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
    public function trash_message()
    {
        try {
            $loggedin = Auth::user()->id;


            $data = 
            Message::where('created_by', $loggedin)
            ->where(function ($query) {
                $query->where('spam', '=', 1);
            })
              
                ->get();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Trash Message  show successfully',
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

    public function send_template(Request $request)
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

            $review = new Message();
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
                'message' => 'data Stored Successfully',
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
    public function email_template(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'template_id' => 'required',
                'draft' => 'required',
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
            $user = Auth::user();
            $product = new Message();
            $product->received_by = $request->input('customer_id');
            $product->email_id = $request->input('template_id');
            $product->created_by = $user->id;
            $product->draft = $request->input('draft');

            if ($request->hasFile('file_data')) {
                $image = $request->file('file_data');
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/file');
                $image->move($destinationPath, $image_name);
                $product->file = $image_name;
            }

            $product->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Email Template Message send successfully',
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
    public function spam_message(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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

            $product =Message::whereIn('id',$request->id)->update(['spam' => 1]);
           
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Message Spam Successfully',
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

    public function trash_data(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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
            // $ids = $request->input('id');

            // if (!is_array($ids)) {
            //     $ids = [$ids];
            // }
            $messages = Message::whereIn('id', $request->id)->delete();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Message deleted  successfully',
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
    public function move_to_inbox(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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
            $product =Message::whereIn('id',$request->id)->update(['spam' => 1]);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Message move to inbox message successfully',
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
    public function message_delete_permanent(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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
            // $ids = $request->input('id');

            // if (!is_array($ids)) {
            //     $ids = [$ids];
            // }
            $messages = Message::whereIn('id', $request->id)->forceDelete();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Message Permanently Deleted successfully',
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
    public function message_move_inbox(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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
            // $ids = $request->input('id');

            // if (!is_array($ids)) {
            //     $ids = [$ids];
            // }
            $message = Message::withTrashed()->where('id', $request->id)->restore();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $message],
                'timestamp' => Carbon::now(),
                'message' => 'Message Restore Successfully',
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

    public function inbox_message_data()
    {
        try{
            $loggedin = Auth::user()->id;

            $messages = Message::where(function ($query) {
                $query->where('spam', '=', null)
                    ->orWhere('spam', '=', 0);
            })->where('received_by', $loggedin)
                ->where(function ($query) {
                    $query->where('message', '!=', null);
                })->get();

                foreach($messages as $item){
                    $item->user = User::where('id',$item->received_by)->first(['id','name','email']);
                    $item->file = asset('public/vendor/file/'. $item->file);
            }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Show all successfully',
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
    public function sent_message_data()
    {
        try{
            $loggedin = Auth::user()->id;

            $messages = Message::where(function ($query) {
                $query->where('spam', '=', null)
                    ->orWhere('spam', '=', 0);

            })->where('created_by', $loggedin)
               ->where('draft', 0)
                ->where(function ($query) {
                    $query->where('message', '!=', null);

                })->get();

                foreach($messages as $item){
                    $item->user = User::where('id',$item->received_by)->first(['id','name','email']);
                    $item->file = asset('public/vendor/file/'. $item->file);
            }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Show all successfully',
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
    public function draft_message_data()
    {
        try{
            $loggedin = Auth::user()->id;
             $messages =
                Message::where('draft', '1')->where('created_by', $loggedin)    
                ->where(function ($query) {
                    $query->where('message', '!=', null);
                })->get();

                foreach($messages as $item){
                      $item->user = User::where('id',$item->received_by)->first(['id','name','email']);
                      $item->file = asset('public/vendor/file/'. $item->file);
              }

        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Show all successfully',
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
    public function spam_message_data()
    {
        try{
            $loggedin = Auth::user()->id;

            $messages = Message::where('spam',1)
            ->where(function ($query) use ($loggedin) {
                $query->where('created_by',$loggedin)
                      ->orWhere('received_by',$loggedin);
            })->get();

            foreach($messages as $item){
                if($item->created_by != Auth::user()->id){
                  $item->user = User::where('id',$item->created_by)->first(['id','name','email']);
                }
                else if($item->received_by != Auth::user()->id){
                  $item->user = User::where('id',$item->received_by)->first(['id','name','email']);
                }

                $item->file = asset('public/vendor/file/'. $item->file);
          }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Show all successfully',
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
    public function trash_message_data()
    {
        try{
            $loggedin = Auth::user()->id;

            $messages =
            Message::where(function ($query) use ($loggedin) {
                $query->where('created_by',$loggedin)
                      ->orWhere('received_by',$loggedin);
            })
            ->onlyTrashed()->get();


            foreach($messages as $item){
                  if($item->created_by != Auth::user()->id){
                    $item->user = User::where('id',$item->created_by)->first(['id','name','email']);
                  }
                  else if($item->received_by != Auth::user()->id){
                    $item->user = User::where('id',$item->received_by)->first(['id','name','email']);
                  }
                  $item->file = asset('public/vendor/file/'. $item->file);
            }
        
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $messages],
                'timestamp' => Carbon::now(),
                'message' => 'Show all successfully',
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


    public function getEmailTemplates(){
        try {
            $template = EmailTemplate::get();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $template],
                'timestamp' => Carbon::now(),
                'message' => 'template fetch successfully',
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


}
