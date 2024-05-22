<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderSummary;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Unique;

use function PHPUnit\Framework\returnSelf;

class ReviewController extends Controller
{
    public function index()
    {
        return view('vendor.reviews.index');
    }

    public function list_reviews(Request $request)
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
        $query = Product::with('reviews')
            ->where(function ($query) use ($search) {
                $query
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhereHas('reviews', function ($query) use ($search) {
                        $query->where('rating', 'like', '%' . $search . '%');
                    });
            })
            ->whereHas('reviews')
            ->where('created_by', $currentUserId);

        $total = $query->count();

        $products = $query
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($products as $key => $product) {
            $action =
                '<a href="' .
                route('vendor.reviews.view', $product->id) .
                '" class="px-2 btn btn-primary text-white mx-1" id="showproduct" data-toggle="tooltip" title = "View"><i class="dripicons-preview"></i></a>';

            $product_name = $product->name;
            $featured_image =
                '<img src="' .
                asset(
                    'public/vendor/featured_image/' . $product->featured_image
                ) .
                '" alt="Featured Image" width="40px">';

            $totalRatings = 0;
            $totalReviews = count($product->reviews);
            foreach ($product->reviews as $review) {
                $totalRatings += $review->rating;
            }
            $starRating = '';
            $averageRating =
                $totalReviews > 0 ? $totalRatings / $totalReviews : 0;
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $averageRating) {
                    $starRating .=
                        '<span class="star filled-star">&#9733;</span>';
                } elseif ($i - 0.5 <= $averageRating) {
                    $starRating .=
                        '<span class="star half-star">&#9733;</span>';
                } else {
                    $starRating .= '<span class="star">&#9734;</span>';
                }
            }
            $rate =
                $starRating .
                '<h5 class="font-14 my-1 fw-normal">(' .
                $totalReviews .
                ')</h5>';

            $data[] = [
                $offset + $key + 1,
                $product_name,
                $featured_image,
                $rate,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function reviews_view($id)
    {
        $currentUserId = Auth::user()->id;
        $products = Review::with('user')
            ->where('product_id', $id)
            ->WhereHas('product', function ($query) use ($currentUserId) {
                $query->where('created_by', $currentUserId);
            })
            ->get();

        return view('vendor.reviews.view', ['products' => $products]);
    }

  
    public function testMail()
    {
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => 500,
            'email' => 'st4272333@gmail.com',
            'tx_ref' =>  uniqid(),
            'currency' => "NGN",
            'redirect_url' => route('callback'),
            'customer' => [
                'email' =>'st4272333@gmail.com',
                "phone_number" => '987654321',
                "name" => 'shivam'
            ],

            "customizations" => [
                "title" => 'Complete Order',
                "description" =>  date('d M Y')
            ]
        ];

        $payment = Flutterwave::initializePayment($data);


          
        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return "hello";
        }

        return redirect($payment['data']['link']);
    
        // $data = ['payment_link' => $paymentLink];
        // Mail::send('email.mail', $data, function ($message) use ($arr) {
        //     $message->from('mail@dilamsys.com', "Green Spark System")->to($arr['email'])->subject($arr['subject']);
        // });
      
    }


    public function callback(){
        $status = request()->status;

        //if payment is successful
        if ($status ==  'successful') {

        $transactionID = Flutterwave::getTransactionIDFromCallback();
        $data = Flutterwave::verifyTransaction($transactionID);

        dd($transactionID);
        }
        elseif ($status ==  'cancelled'){
            //Put desired action/code after transaction has been cancelled here
        }
        else{
            //Put desired action/code after transaction has failed here
        }
    }

 
}
