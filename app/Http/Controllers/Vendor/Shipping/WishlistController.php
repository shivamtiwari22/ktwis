<?php

namespace App\Http\Controllers\Vendor\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class WishlistController extends Controller
{
    public function index()
    {
        return view('vendor.wishlist.index');
    }

    public function list_wishlist(Request $request)
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

        $loginVendorProducts = Product::where('created_by',Auth::user()->id)->pluck('id')->toArray();


        $total = User::with(['wishlists' => function ($query) use ($loginVendorProducts) {


            $query->whereIn('product_id',$loginVendorProducts)->with(['product' => function ($subQuery)  {
                // Apply additional where clause for the product relationship
                $subQuery->where('created_by', Auth::user()->id);
            }]);
        }])
        ->whereHas('wishlists', function ($query) use ($search) {
            $query->whereNull('deleted_at');
            
        })
        ->whereIn('id', function ($query) {
            $query->select('created_by')
                ->from('wishlists');
        })
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->count();



        $users = User::with(['wishlists' => function ($query) use ($loginVendorProducts) {
            $query->whereIn('product_id',$loginVendorProducts)->with(['product' => function ($subQuery)  {
                // Apply additional where clause for the product relationship
                $subQuery->where('created_by', Auth::user()->id);
            }]);
        }])->whereHas('wishlists', function ($query) use ($search) {
            $query->whereNull('deleted_at');
        })
            ->whereIn('id', function ($query) {
                $query->select('created_by')
                    ->from('wishlists');
            })
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();
        
            // return $users;

        $data = [];
        foreach ($users as $key => $user) {
            if($user->wishlists->isNotEmpty()){

                $action =
                '<button class="px-2 btn btn-primary view_wishlist" id="view_wishlist" data-id="' . $user->id . '" data-name="' . $user->id . '"   data-toggle="tooltip"  title="View Wishlist" ><i class="dripicons-preview"></i></button>';
            $name = $user->name;

            $wishlisted_on = Wishlist::where('created_by', $user->id)->orderByDesc('created_at')->first();

            $last_on = null;
            if ($wishlisted_on) {
                $last_on = $wishlisted_on->created_at;
                $dateTime = new \DateTime($last_on);
                $date_time = $dateTime->format('M j, Y');
            }
            else{
                $date_time = null;
            }

        
            $quantity = Wishlist::where('created_by', $user->id)
              ->whereHas('product', function ($query) use ($search) {
                $query->where('created_by',Auth::user()->id);
            })
            ->count();
        
            $data[] = [
                $offset + $key,
                $name,
                $date_time,
                $quantity,
                $action,
            ];
            }
           
        }

        $total_count = Count($data);
    
        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total_count;
        $records['recordsFiltered'] = $total_count;
        $records['data'] = $data;


        echo json_encode($records);
    }

    public function view_wishlist($id)
    {
        $user = User::where('id', $id)->with(['wishlists' => function ($query) {
            $query->whereHas('product', function ($subQuery) {
                $subQuery->where('created_by', auth()->id());
            })
            ->with('product.inventory', 'variant');
        }])
                ->get();


        return response()->json($user);
    }

    public  function index_cart()
    {
        return view('vendor.carts.index');
    }

    public function list_cart(Request $request)
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


            $total = Cart::join('users','carts.user_id','=','users.id')
            ->where('carts.seller_id', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%');
            })->count();


        $users = Cart::
            join('users','carts.user_id','=','users.id')
            ->where('carts.seller_id', Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%');
            })->select('carts.*')
            ->orderBy('carts.id', $orderRecord)
            ->skip($offset)
            ->take($limit)
            ->get();
          
        $data = [];
        foreach ($users as $key => $user) {
            $customer = user::where('id',$user->user_id)->first();
            $action =
                '<button class="px-2 btn btn-primary view_wishlist" id="view_wishlist" data-id="' . $user->id . '" data-name="' . $user->id . '"   data-toggle="tooltip" data-placement="top" title="View Cart"    ><i class="dripicons-preview"></i></button>
                <a class=" px-2 btn btn-danger deleteTypes"  id="DeleteClient" data-id="'.$user->id.'"   data-toggle="tooltip" data-placement="top" title="Delete Cart">
                <i class="dripicons-trash"></i>
                </a>';
            $name = $customer->name;
            $created_at =  Carbon::parse($user->created_at)->diffForHumans(null, true) . " ago";
            $quantity = CartItem::where('cart_id',$user->id)->get()->sum('quantity');
            $data[] = [
                $offset + $key + 1,
                $created_at,
                $name,
                $user->item_count,
                $quantity,
                $user->total_amount,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function cart_delete(Request $request){
        $cart = Cart::find($request->id)->delete();
        $cart_items = CartItem::where('cart_id', $request->id)->delete();
        return response()->json(['status'=> true , 'msg'=> "Customer Cart Deleted Successfully"]);
    }

    public function view_carts($id)
    {
        $cart = Cart::find($id);
        $cart->cart_details = CartItem::where('cart_id',$cart->id)->get();

        foreach( $cart->cart_details as $detail){
             $detail->product = Product::where('id',$detail->product_id)->first();
        } 
        $cart->customer = User::where('id',$cart->user_id)->first();
        $cart->customer->member_since = date("M d,Y", strtotime($cart->customer->created_at));
                    return response()->json($cart);
    }
}
