<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\InventoryWithVariant;
use App\Models\Variant;
use App\Models\AttributeValue;
use App\Models\InventoryWithoutVariant;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class StockController extends Controller
{
    public function index_inventory()
    {
        $attributes = Attribute::with('attributeValues')
            ->where('status', 'active')
            ->get();

        return view('vendor.stocks.inventory.index', [
            'attributes' => $attributes,
        ]);
    }

    public function get_modal_data(Request $request)
    {
        $productId = $request->id;

        $data = DB::table('attributes')
            ->join(
                'attribute_values',
                'attributes.id',
                '=',
                'attribute_values.attribute_id'
            )
            ->join(
                'attribute_category',
                'attributes.id',
                '=',
                'attribute_category.attribute_id'
            )
            ->join(
                'category_product',
                'attribute_category.category_id',
                '=',
                'category_product.category_id'
            )
            ->where('category_product.product_id', '=', $productId)
            ->where('attributes.created_by', Auth::user()->id)
            ->select(
                'attributes.id as attribute_id',
                'attributes.attribute_name as attribute_name',
                'attribute_values.id as attribute_value_id',
                'attribute_values.attribute_value',
                'category_product.product_id',
            )
            ->distinct()
            ->get();

         $variant_exist = InventoryWithVariant::where('p_id',$productId)->first() ? true : false;

       
         
         if($data->count() == 0){
            return response()->json(['status' => false ,'msg' => "No attribute found first create attribute & their values"]) ;
         }

         if($variant_exist){
    
            return response()->json(['status' => false , 'data' => $data,'msg' => "Variant already exists"]) ;
         }

         return $data;
        return response()->json($data);
    }

    public function get_variant_inventory(Request $request)
    {
        $selectedValues = $request->selectedValues;
        $permutations = $request->permutations;
        $combinations = $request->combinations;
        $productID = $request->productID;

        session()->put('selectedValues', $selectedValues);
        session()->put('permutations', $permutations);
        session()->put('combinations', $combinations);
        session()->put('productID', $productID);
        return response()->json(['success' => true]);
    }

    public function get_variant_file()
    {
        $selectedValues = session('selectedValues');
        $permutations = session('permutations');
        $combinations = session('combinations');
        $productID = session('productID');

        $attributeValueIds = Arr::flatten($permutations); // Flatten the array to get all the IDs
        $attributeValues = DB::table('attribute_values')
            ->whereIn('id', $attributeValueIds)
            ->get();

            // dd($attributeValues);

        $attributeValueMap = [];
        foreach ($attributeValues as $attributeValue) {
            $attributeValueMap[$attributeValue->id] =
                $attributeValue->attribute_value;
        }

        $attributeValuePermutations = [];
        foreach ($permutations as $permutation) {
            $attributeValuePermutation = [];
            foreach ($permutation as $attributeValueId) {
                $attributeValuePermutation[] =
                    $attributeValueMap[$attributeValueId];
            }
            $attributeValuePermutations[] = $attributeValuePermutation;
        }

        // dd($permutations);

        return view(
            'vendor.stocks.inventory_with_variant.create',
            compact(
                'selectedValues',
                'attributeValuePermutations',
                'attributeValues',
                'productID',
                'permutations'
            )
        );
    }

    public function store_inventory_with_variant(Request $request)
    {
        $validatedData = $request->validate([
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
            
        ]);

        $user = Auth::user();
        $inventory = new InventoryWithVariant();
        $inventory->p_id = $request->input('p_id');
        $inventory->title = $request->input('title');
        $inventory->status = $request->input('status');
        $inventory->description = $request->input('description');
        $inventory->slug = $request->input('slug');
        $inventory->meta_title = $request->input('meta_title');
        $inventory->meta_description = $request->input('meta_description');
        $inventory->created_by = $user->id;
        $inventory->updated_by = $user->id;

        $inventory->save();

        $combinations = $request->input('combinations');
        $attrIds = $request->input('attr_ids');
        $skus = $request->input('sku');
        $stockQuantities = $request->input('stock_quantity');
        $purchasePrices = $request->input('purchase_price');
        $prices = $request->input('price');
        $offerPrices = $request->input('offer_price');

        for ($i = 0; $i < count($combinations); $i++) {
            $variant = new Variant();
            $variant->inventory_with_variant_id = $inventory->id;
            $variant->attr_id = $attrIds[$i];
            $variant->attr_value_id = $combinations[$i];
            $variant->sku = $skus[$i];
            $variant->stock_quantity = $stockQuantities[$i];
            $variant->purchase_price = $purchasePrices[$i];
            $variant->price = $prices[$i];
            $variant->offer_price = $offerPrices[$i];
            if ($request->hasFile('image.' . $i)) {
                $image = $request->file('image.' . $i);

                $image_name[$i] =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();

                $destinationPath = public_path(
                    'vendor/featured_image/inventory_with_variant'
                );
                $image->move($destinationPath, $image_name[$i]);

                $variant->image_variant = $image_name[$i];
            }
            $variant->save();
        }

        return response()->json([
            'status' => true,
            'location' => route('vendor.inventory.index'),
            'message' => 'Inventory with Variant Stored Successfully',
        ]);
    }

    public function list_inventory_with_variant(Request $request)
    {
        if (isset($request->search['value'])) {
            $search = $request->search['value'];
        } else {
            $search = '';
        }

        if (isset($request->length)) {
            $limit = $request->length;
        } else {
            $limit = 20;
        }

        if (isset($request->start)) {
            $offset = $request->start;
        } else {
            $offset = 0;
        }

        $orderRecord = $request->order[0]['dir'];
        $nameOrder = $request->columns[$request->order[0]['column']]['name'];

        $total = Variant::join('inventory_with_variants','variants.inventory_with_variant_id','=','inventory_with_variants.id')->join('products','inventory_with_variants.p_id','=','products.id')->select('variants.*','products.name')->where(function ($query) use ($search) {
            $query
            ->orWhere('products.name','like', '%' . $search . '%')
            ->orWhere('variants.offer_price', 'like', '%' . $search . '%')
            ->orWhere('variants.sku', 'like', '%' . $search . '%')
            ->orWhere('variants.price', 'like', '%' . $search . '%')
            ->orWhere('variants.stock_quantity', 'like', '%' . $search . '%');
        })  ->whereHas('inventoryWithVariant', function ($query) {
            $query->where('created_by', auth()->user()->id);
        })->get()->groupBy('inventory_with_variant_id')->count();

    
        $inventories = Variant::join('inventory_with_variants','variants.inventory_with_variant_id','=','inventory_with_variants.id')->join('products','inventory_with_variants.p_id','=','products.id')->select('variants.*','products.name')->where(function ($query) use ($search) {
            $query
                ->orWhere('products.name','like', '%' . $search . '%')
                ->orWhere('variants.offer_price', 'like', '%' . $search . '%')
                ->orWhere('variants.sku', 'like', '%' . $search . '%')
                ->orWhere('variants.price', 'like', '%' . $search . '%')
                ->orWhere('variants.stock_quantity', 'like', '%' . $search . '%');
        })
        ->whereHas('inventoryWithVariant', function ($query) {
            $query->where('created_by', auth()->user()->id);
        })
            ->orderBy('id', $orderRecord)
            // ->limit($limit)
            // ->offset($offset)
            ->get()->groupBy('inventory_with_variant_id');

    
        $data = [];
        $count = 0;
        foreach ($inventories as $key => $inventory) {   
           $count++;

            $featured_image =
                '<img src="' .
                asset(
                    'public/vendor/featured_image/inventory_with_variant/' .
                    $inventory[0]->image_variant
                ) .
                '" alt="Featured Image" width="40px">';

            // $sku = $inventory->sku;
            // $price = $inventory->price;
            // $offer_price = $inventory->offer_price;
            // $stock_qty = $inventory->stock_quantity;
            $invent = DB::table('inventory_with_variants')
                ->where('id', $inventory[0]->inventory_with_variant_id)
                ->first();
            $title = $invent->title;
            $product = Product::where('id',$invent->p_id)->first();

            $action =
            '<a href="' .
            route('vendor.inventory.view_variant', $inventory[0]->inventory_with_variant_id) .   
            '" class="px-2 btn btn-primary text-white mx-1" id="showproduct"  data-toggle="tooltip"  title="View" ><i class="dripicons-preview"></i></a>' .
            '<a href="javascript:void(0);"  data-id="'.$product->id.'" data-invt="'.$inventory[0]->inventory_with_variant_id.'"  class="px-2 btn btn-warning text-white mx-1  edit_variants" data-toggle="tooltip"  title="Edit" id="showproduct"><i class="dripicons-document-edit"></i></a>' .
            '<button class="px-2 btn btn-danger deleteinventory_variant" id="deleteinventory_variant" data-toggle="tooltip"  title="Delete" data-id="' .
            $inventory[0]->inventory_with_variant_id .
            '" data-name="' .
            $inventory[0]->sku .
            '"><i class="dripicons-trash"></i></button>';

            $data[] = [
                $offset + $count,
                $product->name,
                $featured_image,
                $title,
                $action,
            ];
        
        }

        //   return  $data;
        $result = array_slice($data, $offset, $limit);
        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $result;

        echo json_encode($records);
    }

    public function edit_variant()
    {

        $selectedValues = session('selectedValue');
        $permutations = session('permutation');
        $combinations = session('combination');
        $productID = session('productId');
        $inventory_variantId = session('inventory_variantId');

        $attributeValueIds = Arr::flatten($permutations); // Flatten the array to get all the IDs
        $attributeValues = DB::table('attribute_values')
            ->whereIn('id', $attributeValueIds)
            ->get();

            // dd($attributeValues);

        $attributeValueMap = [];
        foreach ($attributeValues as $attributeValue) {
            $attributeValueMap[$attributeValue->id] =
                $attributeValue->attribute_value;
        }

        $attributeValuePermutations = [];
        foreach ($permutations as $permutation) {
            $attributeValuePermutation = [];
            foreach ($permutation as $attributeValueId) {
                $attributeValuePermutation[] =
                    $attributeValueMap[$attributeValueId];
            }
            $attributeValuePermutations[] = $attributeValuePermutation;
        }

           
        $inventory = InventoryWithVariant::find($inventory_variantId);
        $variants = Variant::where('inventory_with_variant_id', $inventory_variantId)->get();

        return view(
            'vendor.stocks.inventory_with_variant.edit',
            compact('variants', 'inventory',
            'selectedValues',
            'attributeValuePermutations',
            'attributeValues',
            'productID',
            'permutations')
        );
    }

    public function update_variant(Request $request)
    {

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


        return response()->json(['message' => 'Variant updated successfully']);
    }

    public function view_variant($id)
    {
        $inventory = InventoryWithVariant::find($id);
        $variants = Variant::where('inventory_with_variant_id', $id)->get();

        
        return view(
            'vendor.stocks.inventory_with_variant.view',
            compact('variants', 'inventory')
        );
    }

    public function list_variant_delete(Request $request)
    {
        $delete = Variant::where('inventory_with_variant_id', $request->id)->delete();
         InventoryWithVariant::destroy($request->id);

        if ($delete) {
            return response()->json([
                'status' => true,
                'location' => route('vendor.inventory.index'),
                'msg' => 'Variant Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function search_inventory(Request $request)
    {
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
        })->get();

        foreach( $results as $result ) { 
            $result->already_added = InventoryWithVariant::where('p_id',$result->id)->first() ? true : false;
        }

        return response()->json($results);
    }

    public function add_inventory($id, Request $request)
    {
        $p_id = $id;

        $variant_exist = InventoryWithoutVariant::where('p_id',$id)->first() ;

        if($variant_exist){
            return back()->with('msg','Stock Already Exists');
        }

        return view('vendor.stocks.inventory.add_inventory', ['p_id' => $p_id]);
    }

    public function view_Image(Request $request)
    {
        if ($request->hasFile('featured_image')) {
            $image = Image::make($request->file('featured_image'));
            $imageDataUrl = $image->encode('data-url')->encoded;

            return response()->json([
                'success' => true,
                'imageDataUrl' => $imageDataUrl,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file received.',
        ]);
    }

    public function store_inventory(Request $request)
    {
        $user = Auth::user();
        $p_id = $request->input('p_id');
        $sku = $request->input('sku');
        $stock_qty = $request->input('stock_qty');
        $purchase_price = $request->input('purchase_price');
        $price = $request->input('price');
        $offer_price = $request->input('offer_price');

        $inventory = new InventoryWithoutVariant();
        $inventory->p_id = $p_id;
        $inventory->sku = $sku;
        $inventory->stock_qty = $stock_qty;
        $inventory->purchase_price = $purchase_price;
        $inventory->price = $price;
        $inventory->offer_price = $offer_price;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();

            $destinationPath = public_path('vendor/featured_image/inventory');
            $image->move($destinationPath, $image_name);

            $inventory->image = $image_name;
        }
        $inventory->created_by = $user->id;
        $inventory->updated_by = $user->id;

        $inventory->save();

        return response()->json([
            'status' => true,
            'location' => route('vendor.inventory.index'),
            'message' => 'Inventory Stored Successfully',
        ]);
    }

    public function list_inventory(Request $request)
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

        $total = InventoryWithoutVariant::join('products','inventory_without_variants.p_id','=','products.id')->select('inventory_without_variants.*','products.name')->where(function ($query) use ($search ) {
            $query
            ->Where('products.name', 'like', '%' . $search . '%')
            ->orWhere('inventory_without_variants.offer_price', 'like', '%' . $search . '%')
            ->orWhere('inventory_without_variants.sku', 'like', '%' . $search . '%')
            ->orWhere('inventory_without_variants.price', 'like', '%' . $search . '%')
            ->orWhere('inventory_without_variants.stock_qty', 'like', '%' . $search . '%');
        })
        ->where('inventory_without_variants.created_by',Auth::user()->id)->count();

        $inventories = InventoryWithoutVariant::join('products','inventory_without_variants.p_id','=','products.id')->select('inventory_without_variants.*','products.name')->
        where(function ($query) use (
            $search
        ) {
            $query
            ->where('products.name', 'like', '%' . $search . '%')
                ->orWhere('inventory_without_variants.offer_price', 'like', '%' . $search . '%')
                ->orWhere('inventory_without_variants.sku', 'like', '%' . $search . '%')
                ->orWhere('inventory_without_variants.price', 'like', '%' . $search . '%')
                ->orWhere('inventory_without_variants.stock_qty', 'like', '%' . $search . '%');
        })

            ->orderBy('inventory_without_variants.id', $orderRecord)
            ->where('inventory_without_variants.created_by',Auth::user()->id)
            ->limit($limit)
            ->offset($offset)
            ->get();

        $data = [];
        foreach ($inventories as $key => $inventory) {
            $action =
                '<a href="' .
                route('vendor.inventory.view_inventory', $inventory->id) .
                '" class="px-2 btn btn-primary text-white mx-1" id="showproduct" data-toggle="tooltip"  title="View"><i class="dripicons-preview"></i></a>' .
                '<a href="' .
                route('vendor.inventory.edit', $inventory->id) .
                '" class="px-2 btn btn-warning text-white mx-1" id="showproduct"  data-toggle="tooltip"  title="Edit" ><i class="dripicons-document-edit"></i></a>   ' .
                '<button class="px-2 btn btn-danger deleteinventory" id="delete_inventory"   data-toggle="tooltip"  title="Delete" data-id="' .
                $inventory->id .
                '" data-name="' .
                $inventory->sku .
                '"><i class="dripicons-trash"></i></button>';

            $featured_image =
                '<img src="' .
                asset(
                    'public/vendor/featured_image/inventory/' .
                        $inventory->image
                ) .
                '" alt="Featured Image" width="40px">';

            $name =  Product::where('id', $inventory->p_id)->first()->name ?? null;
            $sku = $inventory->sku;
            $offer_price = $inventory->offer_price;
            $stock_qty = $inventory->stock_qty;

            $data[] = [
                $offset + $key + 1,
                $featured_image,
                $name,
                $sku,
                $offer_price,
                $stock_qty,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function list_inventory_delete(Request $request)
    {
        $delete = InventoryWithoutVariant::where('id', $request->id)->delete();
        if ($delete) {
            return response()->json([
                'status' => true,
                'location' => route('vendor.inventory.index'),
                'msg' => 'Inventory Deleted Successfully',
            ]);
            exit();
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Error Occurred, Please try again',
            ]);
        }
    }

    public function edit_inventory($id)
    {
        $inventory = InventoryWithoutVariant::find($id);
        return view('vendor.stocks.inventory.edit_inventory', [
            'inventory' => $inventory,
        ]);
    }

    public function update_inventory(Request $request)
    {
        $user = Auth::user();
        $id = $request->input('id');
        $p_id = $request->input('p_id');
        $sku = $request->input('sku');
        $stock_qty = $request->input('stock_qty');
        $purchase_price = $request->input('purchase_price');
        $price = $request->input('price');
        $offer_price = $request->input('offer_price');

        $inventory = InventoryWithoutVariant::find($id);
        $inventory->p_id = $p_id;
        $inventory->sku = $sku;
        $inventory->stock_qty = $stock_qty;
        $inventory->purchase_price = $purchase_price;
        $inventory->price = $price;
        $inventory->offer_price = $offer_price;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $image_name =
                time() .
                '_image_' .
                uniqid() .
                '.' .
                $image->getClientOriginalExtension();

            $destinationPath = public_path('vendor/featured_image/inventory');
            $image->move($destinationPath, $image_name);

            $inventory->image = $image_name;
        }
        $inventory->updated_by = $user->id;
        $inventory->save();

        return response()->json([
            'status' => true,
            'location' => route('vendor.inventory.index'),
            'message' => 'Inventory Updated Successfully',
        ]);
    }

    public function view_inventory($id)
    {
        $inventory = InventoryWithoutVariant::find($id);
        return view('vendor.stocks.inventory.view_inventory', [
            'inventory' => $inventory,
        ]);
    }

    public function get_attributes(Request $request){
        $productId = $request->id;

              $attributeValuesId = [];
        $attributeValues = Variant::where('inventory_with_variant_id',$request->inventory_id)->get();
            foreach($attributeValues as $item){
                       $array =   explode(',',$item->attr_value_id);
                       foreach($array as $a){
                           $attributeValuesId[] = $a;
                       }
            } 
          $uniqueAttributeValueId =  $attributeValuesId;



        $data = DB::table('attributes')
            ->join(
                'attribute_values',
                'attributes.id',
                '=',
                'attribute_values.attribute_id'
            )
            ->join(
                'attribute_category',
                'attributes.id',
                '=',
                'attribute_category.attribute_id'
            )
            ->join(
                'category_product',
                'attribute_category.category_id',
                '=',
                'category_product.category_id'
            )
            ->where('category_product.product_id', '=', $productId)
            ->where('attributes.created_by', Auth::user()->id)
            ->select(
                'attributes.id as attribute_id',
                'attributes.attribute_name as attribute_name',
                'attribute_values.id as attribute_value_id',
                'attribute_values.attribute_value',
                'category_product.product_id',
            )
            ->distinct()
            ->get();

     
            if($data->count() == 0){
               return response()->json(['status' => false ,'msg' => "No attribute found first create attribute & their values"]) ;
            }

        return response()->json(['data' => $data , 'attribute_value_id' => $uniqueAttributeValueId , 'invt_id' => $request->inventory_id]);
    }


    public function get_edit_inventory(Request $request){
        $selectedValues = $request->selectedValues;
        $permutations = $request->permutations;
        $combinations = $request->combinations;
        $productID = $request->productID;

        session()->put('selectedValue', $selectedValues);
        session()->put('permutation', $permutations);
        session()->put('combination', $combinations);
        session()->put('productId', $productID);
        session()->put('inventory_variantId', $request->inventory_variantId);
        return response()->json(['success' => true]);
    }


}
