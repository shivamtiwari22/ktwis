<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\SaleBanner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews.index');
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
        
   
        $query = Product::with('reviews')
           ->join('users','products.created_by','users.id')
           ->select('products.*')
            ->where(function ($query) use ($search) {
                $query->orWhere('products.name', 'like', '%' . $search . '%')
                    ->orWhereHas('reviews', function ($query) use ($search) {
                        $query->where('rating', 'like', '%' . $search . '%');
                    })
                    ->orWhere('users.name','like', '%' . $search . '%');
            })
            ->whereHas('reviews');

        $total = $query->count();

        $products = $query->WhereHas('reviews', function ($query) use ($orderRecord) {
            $query->orderBy('id', $orderRecord);
        })
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($products as $key => $product) {
            $action = '<a href="' . route('admin.reviews.view', $product->id) . '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>';
            $vendor_name =  User::where('id', $product->created_by)->first()->name;
            $product_name = $product->name;

            $totalRatings = 0;
            $totalReviews = count($product->reviews);
            foreach ($product->reviews as $review) {
                $totalRatings += $review->rating;
            }
            $starRating = '';
            $averageRating = ($totalReviews > 0) ? ($totalRatings / $totalReviews) : 0;
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $averageRating) {
                    $starRating .= '<span class="star filled-star">&#9733;</span>';
                } else if ($i - 0.5 <= $averageRating) {
                    $starRating .= '<span class="star half-star">&#9733;</span>';
                } else {
                    $starRating .= '<span class="star">&#9734;</span>';
                }
            }
            $rate = $starRating.'<h5 class="font-14 my-1 fw-normal">('.$averageRating.')</h5>';

            $data[] = [
                $offset + $key + 1,
                $vendor_name,
                $product_name,
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
            // return $id;
        // $products = Product::find($id)->with('reviews.user')->whereHas('reviews')->get();
        // return  $products;
        // $product= 
        // $currentUserId = Auth::user()->id;
        // $products = Review::with('user')->where('product_id',$id)->WhereHas('product', function ($query) use ($currentUserId) {
        //     $query->where('created_by',$currentUserId);
        // })->get();   
        $products = Review::with('user')->where('product_id',$id)->get();   
        return view('admin.reviews.view', ['products' => $products]);
    }


    public function sale_banner()
    {
        $sale_banner =   SaleBanner::all();
        return view('admin.sale_banner.index',compact('sale_banner'));
    }


    public function add_sale_banner(Request $request)
    {
        $validatedData = $request->validate([
            'file_data' => 'required',
            'link'=> 'required',

        ]);

        if (!$validatedData) {
            return response()->json(['status' => false, 'message' => 'data not found.',], 404);
        }
        $user = Auth::user();

        $myvariable =  new SaleBanner();
        $myvariable->link = $request->link;
        $myvariable->created_by = $user->id;
        if ($request->hasFile('file_data')) {
            $image = $request->file('file_data');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('admin/salebanner');
            $image->move($destinationPath, $image_name);
            $myvariable->image = $image_name;
        }
        $myvariable->save();

        if ($myvariable) {
            return response()->json(['status' => true, 'message' => 'Sale Banner Data Saved Successfully.', 'location' => route('admin.setting.sale_banner'),], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to submit dispute reply.',], 500);
        }

    }
    public function sale_banner_data(Request $request)
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


        $total =  SaleBanner::count();

        $messages =
        SaleBanner::
        orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($messages as $key => $dispute) {
            $action = '
            <p class="btn btn-warning text-white openModalButton" data-bs-toggle="modal" data-bs-target="#exampleModal" data-dispute="' . $dispute->id . '">
                <i class="dripicons-document-edit"></i>
            </p>'. '<p class="btn btn-danger rounded   deleteType" style="    margin-left: 3%;" data-bs-toggle="modal" data-bs-target="#exampleModal" id="DeleteClient" data-id="' . $dispute->id .  '">
            <i class="dripicons-trash"></i>
            </p>';

            $Active = $dispute->status == "0" ? "selected" : "";
            $Inactive = $dispute->status == "1" ? "selected" : "";
            $sale_banner_image = '<img src="' . asset('public/admin/salebanner/' . $dispute->image) . '" alt="Featured Image" width="40px">';
            $status = '<select class="change_status form-select"   data-id="' . $dispute->id . '">
            <option value="0" ' . $Active . '>Active</option>

            <option value="1" ' . $Inactive . '>Inactive</option>
            ';
            $data[] = [
           $key+1,
                $sale_banner_image,
                $status,
                $action,
            ];
        }


        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }


    public function sale_banner_edit(Request $request)
    {

        $disputeId = $request->input('disputeId');
        $data = SaleBanner::find($disputeId);
        return response()->json(compact('data'));
    }

    public function sale_banner_status_update(Request $request)
    {
        $rules = [
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $coupon = SaleBanner::find($request->sale_id);
            $coupon->status = $request->status_value;
            if ($coupon->save()) {
                return response()->json(array('status' => true, 'location' =>  route('admin.setting.sale_banner'), 'msg' => 'Sale Banner Data Updated Successfully'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }

    public function sale_banner_update(Request $request)
    {
       
        $validatedData = $request->validate([
            // 'file_data' => 'required',
            'link' => 'required'
        ]);
        if (!$validatedData) {
            return response()->json(['status' => false, 'message' => 'data not found.',], 404);
        }
        $myvariable = SaleBanner::find($request->id);
        $myvariable->link = $request->link;
        if ($request->hasFile('file_data')) {
            $image = $request->file('file_data');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('admin/salebanner');
            $image->move($destinationPath, $image_name);
            $myvariable->image = $image_name;
        }
        if ($myvariable->save()) {
            return response()->json(array('status' => true, 'location' =>  route('admin.setting.sale_banner'), 'message' => 'Sale banner data update successfully'));
        } else {
            return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
        }
        
    }

    public function sale_banner_delete(Request $request)
    {
        $category = SaleBanner::find($request->id);
        if ($category->delete()) {
            return response()->json(['status' => true, 'location' => route('admin.setting.sale_banner'), 'msg' => "Sale Banner Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }    }
    
}
