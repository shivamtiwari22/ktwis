<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Product;
use App\Models\Variant;
use Carbon\Carbon;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StockApiController extends Controller
{
    public function get_stock_without_variant()
    {
        try {
            $inventory = InventoryWithoutVariant::with('product')->where('created_by',Auth::user()->id)->get();
             
             foreach($inventory as $item){
                $item->image_url =  asset(
                    'public/vendor/featured_image/inventory/' .
                        $item->image);
             }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $inventory],
                'timestamp' => Carbon::now(),
                'message' => 'Inventory without Variant fetched Successfully',
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

    public function get_stock_with_variant()
    {
        try {
            $inventory = InventoryWithVariant::with('product.categories')->where('created_by',Auth::user()->id)->get();

            foreach($inventory as $item){
                if( $item->product){
                $item->product->featured_image_url =  asset(
                    'public/vendor/featured_image/' .
                        $item->product->featured_image);
                }
             }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $inventory],
                'timestamp' => Carbon::now(),
                'message' => 'Inventory with Variant fetched Successfully',
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


    public function get_stock_with_variant_groupBy(){
        try {


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


    public function add_to_stock(Request $request)
    {
        try {
            $rules = [
                'product_id' => 'required|integer',
                'sku' => 'required',
                'stock_qty' => 'required|numeric',
                'purchase_price' => 'required',
                'price' => 'required',
                'offer_price' => 'required',
                'featured_image' => 'mimes:jpeg,jpg,png|required|max:20000',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            $product = Product::find($request->product_id);
            $product_inventory = InventoryWithoutVariant::where(
                'p_id',
                $product->id
            )->first();
            if ($product_inventory) {
                return response()->json(
                    [
                        'http_status_code' => '409',
                        'status' => false,
                        'context' => ['error' => 'Inventory already exists'],
                        'timestamp' => Carbon::now(),
                        'message' =>
                            'Inventory already exists for this product',
                    ],
                    409
                );
            }

            if ($product->has_variant == '0') {
                $user = Auth::user();
                $inventory = new InventoryWithoutVariant();
                $inventory->p_id = $request->product_id;
                $inventory->sku = $request->sku;
                $inventory->stock_qty = $request->stock_qty;
                $inventory->purchase_price = $request->purchase_price;
                $inventory->price = $request->price;
                $inventory->offer_price = $request->offer_price;
                if ($request->hasFile('featured_image')) {
                    $image = $request->file('featured_image');
                    $image_name =
                        time() .
                        '_image_' .
                        uniqid() .
                        '.' .
                        $image->getClientOriginalExtension();
                    $destinationPath = public_path(
                        'vendor/featured_image/inventory'
                    );
                    $image->move($destinationPath, $image_name);

                    $inventory->image = $image_name;
                }
                $inventory->created_by = $user->id;
                $inventory->updated_by = $user->id;

                $inventory->save();
                if ($inventory) {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => $inventory],
                        'timestamp' => Carbon::now(),
                        'message' => 'Inventory stored successfully',
                    ]);
                } else {
                    return response()->json(
                        [
                            'http_status_code' => 500,
                            'status' => false,
                            'context' => ['error' => 'Something Went Wrong'],
                            'timestamp' => Carbon::now(),
                            'message' => 'An unexpected error occurred',
                        ],
                        500
                    );
                }
            } else {
                return response()->json(
                    [
                        'http_status_code' => '404',
                        'status' => false,
                        'context' => ['error' => 'Product not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Product not found',
                    ],
                    404
                );
            }
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

    public function add_to_stock_variant(Request $request)
    {
        try {
            $rules = [
                'product_id' => 'required|integer',
                'title' => 'required',
                'status' => 'required',
                'combinations.*' => 'required',
                'attr_ids.*' => 'required',
                'sku.*' => 'required',
                'stock_quantity.*' => 'required',
                'purchase_price.*' => 'nullable',
                'price.*' => 'required',
                'offer_price.*' => 'nullable',
                'description' => 'nullable',
                'slug' => 'nullable',
                'meta_title' => 'nullable',
                'meta_description' => 'nullable',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

          
            $product = Product::find($request->product_id);

            if ($product->has_variant == '1') {
                $user = Auth::user();
                $inventory = new InventoryWithVariant();
                $inventory->p_id = $request->input('product_id');
                $inventory->title = $request->input('title');
                $inventory->status = $request->input('status');
                $inventory->description = $request->input('description');
                $inventory->slug = $request->input('slug');
                $inventory->meta_title = $request->input('meta_title');
                $inventory->meta_description = $request->input(
                    'meta_description'
                );
                $inventory->created_by = $user->id;
                $inventory->updated_by = $user->id;

                $inventory->save();

                $combinations = $request->combinations;
           
                foreach($combinations as $item){
                    $variant = new Variant();
                    $variant->inventory_with_variant_id = $inventory->id;
                    $variant->attr_id = $item['attr_id'];
                    $variant->attr_value_id = $item['attr_value_id'];
                    $variant->sku = $item['sku'];
                    $variant->stock_quantity = $item['stock_quantity'];
                    $variant->purchase_price = $item['purchase_price'];
                    $variant->price = $item['price'];
                    $variant->offer_price = $item['offer_price'];
                    if (isset($item['image']) && $item['image']->isValid()) {
                        $image = $item['image'];
                        $image_name =
                            time() .
                            '_image_' .
                            uniqid() .
                            '.' .
                            $image->getClientOriginalExtension();
                        $destinationPath = public_path(
                            'vendor/featured_image/inventory_with_variant'
                        );
                        $image->move($destinationPath, $image_name);
    
                        $variant->image_variant = $image_name;
                    }
                    $variant->save();
                }

             
                return response()->json(
                    [
                        'http_status_code' => '200',
                        'status' => true,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Inventory with Variant Stored Successfully',
                    ]
                );
            }

             else {
                return response()->json(
                    [
                        'http_status_code' => '404',
                        'status' => false,
                        'context' => ['error' => 'Product not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Product not found',
                    ],
                    404
                );
            }
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

    public function view_stock($id)
    {
        $inventory = InventoryWithoutVariant::find($id);

        $inventory->image_url =  asset(
            'public/vendor/featured_image/inventory/' .
                $inventory->image);

        if ($inventory) {
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $inventory],
                'timestamp' => Carbon::now(),
                'message' => 'Inventory Fetched Successfully',
            ]);
        }

        return response()->json(
            [
                'http_status_code' => '404',
                'status' => false,
                'context' => ['error' => 'Inventory Not Found'],
                'timestamp' => Carbon::now(),
                'message' => 'Not Found',
            ],
            404
        );
    }

    public function update_stock(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|integer',
                'sku' => 'required' ,
                'stock_qty' => 'required|numeric',
                'purchase_price' => 'required',
                'price' => 'required',
                'offer_price' => 'required',
                'featured_image' => 'mimes:jpeg,jpg,png|max:20000|nullable',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            $user = Auth::user();
            $id = $request->input('id');
            $inventory = InventoryWithoutVariant::find($id);
            $inventory->sku = $request->sku;
            $inventory->stock_qty = $request->stock_qty;
            $inventory->purchase_price = $request->purchase_price;
            $inventory->price = $request->price;
            $inventory->offer_price = $request->offer_price;
            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path(
                    'vendor/featured_image/inventory'
                );
                $image->move($destinationPath, $image_name);

                $inventory->image = $image_name;
            }
            $inventory->updated_by = $user->id;
            $inventory->save();

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $inventory],
                'timestamp' => Carbon::now(),
                'message' => 'Inventory stored successfully',
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

    public function delete_stock($id)
    {
        try {
            $delete = InventoryWithoutVariant::where(
                'id',
                $id
            )->delete();
            if ($delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Inventory Deleted Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' => 'Error Occurred, Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
                );
            }
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

    public function view_stock_variant($id)
    {
        try {
            $inventory = InventoryWithVariant::find($id);
            $variants = Variant::where(
                'inventory_with_variant_id',
                $id
            )->get();

            foreach ($variants as $variant) {
                $variant->image_url =  asset(
                    'public/vendor/featured_image/inventory_with_variant/' .
                        $variant->image_variant);

                        $attrValueIds = explode(',', $variant->attr_value_id);
                  $variant->attribute_values = AttributeValue::whereIn('id', $attrValueIds)->get(['id','attribute_value','attribute_id','color_attribute']);
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $variants],
                'timestamp' => Carbon::now(),
                'message' => 'variant fetched Successfully',
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

    public function update_stock_variant(Request $request)
    {

        try {
            $rules = [
                'variant_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

    
            $combinations = $request->input('combinations');

            $combinations = $request->input('combinations');
            $attrIds = $request->input('attr_ids');
            $skus = $request->input('sku');
            $stockQuantities = $request->input('stock_quantity');
            $purchasePrices = $request->input('purchase_price');
            $prices = $request->input('price');
            $offerPrices = $request->input('offer_price');
    
            for($a= 0 ; $a<count($combinations) ; $a++){
    
              if(isset($request->variant_id[$a])){
                $id = $request->variant_id[$a];
                $variant = Variant::findOrFail($id);
                $variant->sku = $request->sku[$a];
                $variant->attr_id = $attrIds[$a];
                $variant->attr_value_id = $combinations[$a];
                $variant->stock_quantity = $request->stock_quantity[$a];
                $variant->purchase_price = $request->purchase_price[$a];
                $variant->price = $request->price[$a];
                $variant->offer_price = $request->offer_price[$a];
                if ($request->hasFile('image')) {
        
                    if(isset($request->image[$a])){
        
                    $image = $request->file('image')[$a];
        
                    $image_name =
                        time() .
                        '_image_' .
                        uniqid() .
                        '.' .
                        $image->getClientOriginalExtension();
        
                    $destinationPath = public_path(
                        'vendor/featured_image/inventory_with_variant'
                    );
                    $image->move($destinationPath, $image_name);
        
                    $variant->image_variant = $image_name;
                }
        
                }
        
                $variant->save();
                $inventory = $variant->inventory_with_variant_id;
              }
              else{
                $variant = new Variant();
                $variant->inventory_with_variant_id = $inventory;
                $variant->attr_id = $attrIds[$a];
                $variant->attr_value_id = $combinations[$a];
                $variant->sku = $skus[$a];
                $variant->stock_quantity = $stockQuantities[$a];
                $variant->purchase_price = $purchasePrices[$a];
                $variant->price = $prices[$a];
                $variant->offer_price = $offerPrices[$a];
                if (isset($request->image[$a])) {
                    $image = $request->file('image')[$a];
    
                    $image_name[$a] =
                        time() .
                        '_image_' .
                        uniqid() .
                        '.' .
                        $image->getClientOriginalExtension();
    
                    $destinationPath = public_path(
                        'vendor/featured_image/inventory_with_variant'
                    );
                    $image->move($destinationPath, $image_name[$a]);
    
                    $variant->image_variant = $image_name[$a];
                }
                $variant->save();
    
              }
            }
           
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $variant],
                'timestamp' => Carbon::now(),
                'message' => 'Variant updated successfully',
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

    public function delete_stock_variant($id)
    {
        try {

            $delete = InventoryWithoutVariant::where('id', $id)->delete();
            if ($delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Inventory With Variant Deleted Successfully',
                ]);
            } else {
                return response()->json([
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' => 'Error Occurred, Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
                );
            }
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

    public function search_product(Request $request){
        try{
        $query = $request->input('query');
      
        $results = Product::where(function ($queryBuilder) {
            $queryBuilder
                ->where('status', 'active')
                ->where('created_by', Auth::user()->id)
                ->where(function ($subQueryBuilder) {
                    $query = '%' . request()->input('query') . '%';
                    $subQueryBuilder
                        ->where('name', 'like', $query)
                        ->orWhere('status', 'like', $query)
                        ->orWhere('description', 'like', $query);
                });
        })->whereHas('user.shops',function ($queryBuilder) {
            $queryBuilder->where('status', 'active');   
        })->with(['categories' => function($query){
                  $query->select('*');
        }])->get();

        foreach( $results as $result ) { 
            $result->featured_image_url = asset(
                'public/vendor/featured_image/' .
                    $result->featured_image
            );
            $result->already_added = InventoryWithVariant::where('p_id',$result->id)->first() ? true : false;
        }

        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $results],
            'timestamp' => Carbon::now(),
            'message' => 'Product Fetched Successfully',
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
