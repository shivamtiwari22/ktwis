<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSummary;
use App\Models\Payment;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use PDO;

class UserAccountController extends Controller
{
    //

    public function update_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'http_status_code' => '422',
                    'status' => false,
                    'context' => ['error' => $validator->errors()->first()],
                    'timestamp' => Carbon::now(),
                    'message' => 'Validation failed',
                ],
                422
            );
        }
        $user = User::find($request->user_id);
        $user->name = $request->name;
        $user->email = $request->email;
        $updated = $user->save();

        if ($updated) {
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$user]],
                'timestamp' => Carbon::now(),
                'message' => 'Profile Updated Successfully',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'An unexpected error occurred'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Something Went Wrong',
                ],
                500
            );
        }
    }

    public function updateUserProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "full_name" => "required",
                'mobile_number' => [
                    'required',
                    Rule::unique('users')->ignore(Auth::user()->id),
                ],
            ]);
            if ($validator->fails()) {
                return response()->json(['http_status_code' => '422' ,'status' => false, 'context' =>  ['error' => $validator->errors()->first()] ,  'timestamp'=> Carbon::now() , 'message' => 'Validation failed'], 422);
            }
            $user = User::find(Auth::user()->id);
            $user->name = $request->full_name;
            $user->dob = $request->dob;
            $user->details = $request->details;
            $user->mobile_number = $request->mobile_number;
            $user->country_code = $request->country_code;

            if ($request->profile_pic) {
                $image = $request->file('profile_pic');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $destinationPath = public_path('customer/profile');
                $image->move($destinationPath, $image_name);
                $user->profile_pic = $image_name;
            }

            $user->update();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$user]],
                'timestamp' => Carbon::now(),
                'message' => 'Profile Updated Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'An unexpected error occurred'],
                    'timestamp' => Carbon::now(),
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function get_user_profile()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first([
                'name',
                'email',
                'dob',
                'details',
                'country_code',
                'mobile_number',
                'profile_pic',
                'created_at',
            ]);
            $user->profile_url = 
             $user->profile_pic ? 
            asset(
                'public/customer/profile/' . $user->profile_pic
            ) :  null;

            $user->member_since = Carbon::parse( Auth::user()->created_at)->diffForHumans(null, true) . " ago";

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$user]],
                'timestamp' => Carbon::now(),
                'message' => 'User profile fetched Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'An unexpected error occurred'],
                    'timestamp' => Carbon::now(),
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function update_profile_pic(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'profile_pic' => 'mimes:jpeg,jpg,png|required|max:10000',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => '422',
                        'status' => false,
                        'context' => ['error' => $validator->errors()->first()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }
            $user = User::find(Auth::user()->id);
            if ($request->profile_pic) {
                $image = $request->file('profile_pic');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $destinationPath = public_path('customer/profile');
                $image->move($destinationPath, $image_name);
                $user->profile_pic = $image_name;
            }
            $user->save();
            $user->profile_pic = asset('/public/customer/profile/'. $user->profile_pic);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$user]],
                'timestamp' => Carbon::now(),
                'message' => 'User profile fetched Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'An unexpected error occurred'],
                    'timestamp' => Carbon::now(),
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function download_invoice($id)
    {
        try {
            $order_id = $id;
            $order = Order::find($order_id);
            if (!$order) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => true,
                        'context' => ['error' => ['order not found']],
                        'timestamp' => Carbon::now(),
                        'message' => 'order not found',
                    ],
                    404
                );
            }

            $order_item =
                OrderItem::with('product', 'product.categories')
                    ->where('order_id', $order->id)
                    ->get();

            $user = User::find($order->user_id) ?? null;

            $address =
                UserAddress::where(
                    'id',
                    $order->shipping_address_id
                )->first() ?? null;

                if($address){
                    $address->state = State::where('id',$address->state)->orWhere('state_name', $address->state)->value('state_name');
                    $address->country = Country::where('id',$address->country)->orWhere('country_name', $address->country)->value('country_name');
                  }   

                $orderSummary = OrderSummary::where('id',$order->order_summary_id)->first() ?? null;
            $payment = Payment::where('order_summary_id',$orderSummary->id)->first() ?? null;
            // return view(
            //     'pdf.invoice',
            //     compact('order', 'order_item', 'user', 'address', 'payment')
            // );
            
            
            $dompdf = new Dompdf();
            $html = view(
                'pdf.invoice',
                compact('order', 'order_item', 'user', 'address', 'payment','orderSummary')
            )->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfFileName =  'invoice_' . $id . '.pdf'; // Define the PDF file name
            $pdfContent = $dompdf->output();

            // Store the PDF content in a storage path
           $store =  Storage::disk('public')->put('customer/invoice/' . $pdfFileName, $pdfContent);        
            $pdfUrl = asset('public/storage/customer/invoice/' . $pdfFileName); 
            
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$pdfUrl]],
                'timestamp' => Carbon::now(),
                'message' => 'Invoice Downloaded Successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => ['error' => 'An unexpected error occurred'],
                    'timestamp' => Carbon::now(),
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }


    public function resendOtp(Request $request){
        $user = User::where('email',$request->email)->first();
        if ($user) {

            $otp = strval(random_int(1000, 9999));
            $user->otp = $otp;
            $user->otp_created_at = Carbon::now();
            $user->save();
            
            $data['otp'] = $otp;
            $data['email'] = $request->email;
            $data['title'] = 'OTP Verification';
            $data['body'] = 'Your OTP is: ' . $otp;

            Mail::send(
                'email.forgotPasswordMail',
                ['data' => $data],
                function ($message) use ($data) {
                    $message
                        ->from('mail@dilamsys.com', 'ktwis')
                        ->to($data['email'])
                        ->subject($data['body']);
                }
            );

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'OTP send successfully',
            ]);
        }
        else{

            return response()->json([
                'http_status_code' => 404,
                'status' => false,
                'context' => ['error' => 'User Not Found'],
                'timestamp' => Carbon::now(),
                'message' => 'User Not Found',
            ]);

        }
    }
}
