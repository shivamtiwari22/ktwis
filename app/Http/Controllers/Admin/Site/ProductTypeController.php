<?php

namespace App\Http\Controllers\Admin\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductTypeController extends Controller
{
    public function index_type()
    {
        return view('admin.site.product_type.index_type');
    }

    public function create_type()
    {
        $products = Product::where('status', 'active')->get();
        $categories = Category::all();
        return view('admin.site.product_type.create_type', ['products' => $products, 'categories' => $categories]);
    }

    public function list_product_form(Request $request)
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

        $total = Product::
        join('category_product','products.id','category_product.product_id')
        ->join('categories','category_product.category_id', 'categories.id' )
        ->where(function ($query) use ($search) {
            $query->orWhere('products.name', 'like', '%' . $search . '%');
            $query->orWhere('categories.category_name', 'like', '%' . $search . '%');
        })
         ->select('products.*')
        ->count();

        $inventories = Product::join('category_product','products.id','category_product.product_id')
        ->join('categories','category_product.category_id', 'categories.id' )
        ->where(function ($query) use ($search) {
            $query->orWhere('products.name', 'like', '%' . $search . '%');
            $query->orWhere('categories.category_name', 'like', '%' . $search . '%');
        })
         ->select('products.*')
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();


        $data = [];
        foreach ($inventories as $key => $product) {

            $checkbox = '<input type="checkbox" id="product_id[]" name="product_id[]" value="' . $product->id . '">';
            $image = '<img src="' . asset('public/vendor/featured_image/' . $product->featured_image) . '" alt="Banner Image" width="40px">';
            $name = $product->name;

            $categories = [];
            foreach ($product->categories as $category) {
                $categories[] = $category->category_name;
            }



            $data[] = [
                $checkbox,
                $image,
                $name,
                $categories,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function store_product_type(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'page_status' => 'required',
            'product_id' => 'required',
        ], [
            'product_id.required' => 'select at least one product',
        ]);



         if($request->type == "deal_of_the_day"){
            $check_product_type = ProductType::where('type', $request->type)->count();
         }
         else {
              $check_product_type = 0;
         }
        if ($check_product_type == "0" ) {
            foreach ($validatedData['product_id'] as $productID) {
                $user = Auth::user();
                $productType = new ProductType();
                $productType->type = $validatedData['type'];
                $productType->status = $validatedData['page_status'];
                $productType->product_id = $productID;
                $productType->created_by = $user->id;
                $productType->updated_by = $user->id;
                $productType->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Product Types added successfully',
                'location' => route('admin.producttype.index')
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Product cannot be added because the product type has already been added.',
                'location' => route('admin.producttype.index')
            ]);
        }
    }

    public function list_product_type(Request $request)
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

        $total = ProductType::   join('products','product_types.product_id','products.id')->
        where(function ($query) use ($search) {
            $query->orWhere('product_types.type', 'like', '%' . $search . '%');
            $query->orWhere('product_types.created_at', 'like', '%' . $search . '%');
            $query->orWhere('products.name', 'like', '%' . $search . '%');
     
        })->count();

        $products = ProductType::
        join('products','product_types.product_id','products.id')->
        where(function ($query) use ($search) {
            $query->orWhere('product_types.type', 'like', '%' . $search . '%');
            $query->orWhere('product_types.created_at', 'like', '%' . $search . '%');
            $query->orWhere('products.name', 'like', '%' . $search . '%');
     
        })
        ->select('product_types.*')
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        foreach ($products as $key => $product_type) {
        

            $action = '<button class="px-2 btn btn-danger deleteproducttype" id="deleteproducttype" data-id="' . $product_type->id . '" data-name="' . $product_type->type . '"><i class="dripicons-trash"></i></button>';



            $type = $product_type->type;
            if ($type == "flash_sale") {
                $type_name = "Flash Sale";
            } elseif ($type == "featured_item") {
                $type_name = "Featured Item";
            } elseif ($type == "deal_of_the_day") {
                $type_name = "Deal Of The Day";
            } elseif ($type == "trending_item") {
                $type_name = "Trending Item";
            } else {
                $type = "";
            }

            $product = Product::find($product_type->product_id);
            if($product){
              
                $product_name = $product->name;
                $productImage = $product->featured_image;
                $featured_image = '<img src="' . asset('public/vendor/featured_image/' . $productImage) . '" alt="' . $productImage . '" width="40PX">';
    
                $date_times = $product_type->updated_at;
                $dateTime = new \DateTime($date_times);
                $date_time = $dateTime->format('M j, Y H:i:s');
    
                $data[] = [
                    $offset + $key + 1,
                    $type_name,
                    $featured_image,
                    $product_name,
                    $date_time,
                    $action,
                ];
            }
         
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        return response()->json($records);
    }

    public function deleteproducttype(Request $request)
    {
        $delete = ProductType::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Product Type deleted successfully']);
        } else {

            return response()->json(['status' => false, 'message' => "Some error occurred! , Please try again"]);
        }
    }
}
