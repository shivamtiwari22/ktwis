<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\CartItem;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\ProductType;
use App\Models\Variant;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CatalogController extends Controller
{

    public function index_attribute()
    {
        $categories = Category::with('children')->whereNull('parent_category_id')->get();
        $categoryOptions = $this->buildCategoryOptions($categories);
        return view('vendor.catalog.attributes.index', compact('categoryOptions'));
    }

    private function buildCategoryOptions(Collection $categories, $depth = 0)
    {
        $options = [];
        foreach ($categories as $category) {
            $prefix = str_repeat("- - ", $depth);
            $options[$category->id] = $prefix . $category->category_name;
            if ($category->children) {
                $options += $this->buildCategoryOptions($category->children, $depth + 1);
            }
        }
        return $options;
    }
    public function store_attribute(Request $request)
    {
        $validatedData = $request->validate([
            'attribute_type' => 'required',
            'name' => 'required',
            'order' => 'nullable|numeric',
        ]);
        $user = Auth::user();
        $attribute = new Attribute();
        $attribute->attribute_type = $request->attribute_type;
        $attribute->attribute_name = $request->name;
        $attribute->list_order = $request->order;
        $attribute->created_by = $user->id;
        $attribute->updated_by = $user->id;
        $attribute->save();
        $categoryIds = $request->input('categories');
        $attribute->categories()->attach($categoryIds);
        return response()->json(['message' => 'Attribute stored successfully']);
    }

    public function list_attributes(Request $request)
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
        $activeId = Auth::user()->id;
        $total = Attribute::where('status', 'active')->where(function ($query) use ($search) {
            $query->orWhere('attribute_name', 'like', '%' . $search . '%')
                ->orWhere('list_order', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
        })
            ->where('created_by', $activeId)->count();
        $attributes = Attribute::where('status', 'active')->where(function ($query) use ($search) {
            $query->orWhere('attribute_name', 'like', '%' . $search . '%')
                ->orWhere('list_order', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
        })
            ->where('created_by', $activeId)
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        foreach ($attributes as $key => $attribute) {
            $action = '<a href="' . route('vendor.attributes.entities', $attribute->id) . '" class="px-2 btn btn-primary text-white mx-1" data-toggle="tooltip"  title="Add-Attribute-Values" id="showproduct"><i class="uil-plus"></i></a>' .
                '<button class="px-2 btn btn-warning editproduct" id="edit_attribute_button" data-id="' . $attribute->id . '" data-name="' . $attribute->attribute_name . '"  data-toggle="tooltip"  title="Edit-Attribute"><i class="dripicons-document-edit"></i></button> ' .
                '<button class="px-2 btn btn-danger delete_attribute" id="delete_attri" data-id="' . $attribute->id . '" data-name="' . $attribute->attribute_name . '"  data-toggle="tooltip"  title="Delete-Attribute"><i class="dripicons-trash"></i></button>';
            $name = $attribute->attribute_name;
            $type = $attribute->attribute_type;

            // $category 

            $attribute_data = Attribute::find($attribute->id);

            $category = $attribute->categories()->count();
            $categories = $attribute->categories()->pluck('category_name')->toArray();
            $category_name = implode(',',$categories);
            $attributeValues = $attribute_data->attributeValues;
            $count_attr_value = $attributeValues->count();

            $order = $attribute->list_order;

            $data[] = [
                $offset + $key + 1,
                $order,
                $name,
                $type,
                ucwords($category_name),
                $category,
                $count_attr_value,
                $action,
            ];
        }
       

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function show_attributes($id)
    {
        $attribute = Attribute::find($id);
        $attr_id = $attribute->id;
        $categoryOptions = Category::pluck('category_name', 'id');

        $selectedCategories = $attribute->categories->pluck('id');

        return response()->json([
            'attribute' => $attribute,
            'attr_id' => $attr_id,
            'categoryOptions' => $categoryOptions,
            'selectedCategories' => $selectedCategories,
        ]);
    }

    public function update_attributes(Request $request)
    {
        $id = $request->attr_id;
        $user = Auth::user();
        $attribute = Attribute::find($id);
        $attribute->attribute_type = $request->attribute_type_id_edit;
        $attribute->attribute_name = $request->name_edit;
        $attribute->list_order = $request->order_edit;
        $attribute->updated_by = $user->id;
        $attribute->save();


        $categoryIds = $request->input('categories_edit');
        $attribute->categories()->sync($categoryIds);


        return response()->json(['message' => 'Attribute updated successfully']);
    }

    public function delete_attribute(Request $request)
    {
      
        $attribute = Attribute::where('id', $request->id)->delete();
        $attr_value =AttributeValue::where('attribute_id', $request->id)->delete();

        $variants_by_attr = Variant::whereRaw("FIND_IN_SET(?,attr_id)",[$request->id])->get();

          Variant::whereRaw("FIND_IN_SET(?,attr_id)",[$request->id])->delete();
          Attribute::where('id', $request->id)->forceDelete();
         AttributeValue::where('attribute_id', $request->id)->forceDelete();


         foreach($variants_by_attr as $item){
                $inventory = InventoryWithVariant::find($item->inventory_with_variant_id);
                if($inventory){
                       $variantCount = Variant::where('inventory_with_variant_id',$inventory->id)->count();
                       if($variantCount == 0){
                            InventoryWithVariant::where('id', $request->id)->delete();
                            InventoryWithVariant::where('id', $request->id)->forceDelete();
                       }
                }
         }


        if ($attribute) {
            return response()->json(['status' => true, 'location' => route('vendor.attributes.index'), 'msg' => "Attribute Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }


    /////////////////////////////////    entities   ////////////////////////////////
    public function index_entities($id)
    {
        $attr_id = $id;
        $attributes = Attribute::where('id', $attr_id)->where('status', 'active')->first();
        return view('vendor.catalog.attributes.entities.index', ['attributes' => $attributes, 'attr_id' => $attr_id]);
    }

    public function store_entities(Request $request)
    {

        $user = Auth::user();
        $value_attr = new AttributeValue();
        $value_attr->attribute_id = $request->attribute_id;
        $value_attr->attribute_value = $request->attribute_value;
        $value_attr->value_list_order = $request->value_list_order;
        $value_attr->color_attribute = $request->color_attribute  ;
        $value_attr->created_by = $user->id;
        $value_attr->updated_by = $user->id;
        $value_attr->save();

        return response()->json(['message' => 'Attribute stored successfully']);
    }

    public function list_entities(Request $request, $id)
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
        $activeId = Auth::user()->id;
        $total = AttributeValue::where('status', 'active')
            ->where('attribute_id', $id)
            ->where(function ($query) use ($search) {
                $query->orWhere('value_list_order', 'like', '%' . $search . '%')
                    ->orWhere('attribute_value', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            })->where('created_by', $activeId)->count();

        $attr_values = AttributeValue::where('status', 'active')
            ->where('attribute_id', $id)
            ->where(function ($query) use ($search) {
                $query->orWhere('value_list_order', 'like', '%' . $search . '%')
                    ->orWhere('attribute_value', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            })
            ->where('created_by', $activeId)
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        foreach ($attr_values as $key => $attr_value) {
            $action = '<button class="px-2 btn btn-primary view_entities" id="view_entities_button" data-id="' . $attr_value->id . '" data-name="' . $attr_value->attribute_value . '"  data-toggle="tooltip"  title="View-Attribute-Value"><i class="dripicons-preview"></i></button>  ' .
                '<button class="px-2 btn btn-warning editproduct" id="edit_attribute_button" data-id="' . $attr_value->id . '" data-name="' . $attr_value->attribute_value . '" data-toggle="tooltip"  title="Edit-Attribute-Value"><i class="dripicons-document-edit"></i></button>  ' .
                '<button class="px-2 btn btn-danger deleteproduct" id="delete_attri_value" data-id="' . $attr_value->id . '" data-name="' . $attr_value->attribute_value . '" data-toggle="tooltip"  title="Delete-Attribute-Value"><i class="dripicons-trash"></i></button>';


            $order = $attr_value->value_list_order;
            $values = $attr_value->attribute_value;
            if($attr_value->color_attribute){
                $color =  ' <input type="color" id="" value="'.$attr_value->color_attribute   .'"  >';
            }
            else{
                $color =  '';

            }
           
            $pattern = "";

            $data[] = [
                $offset + $key + 1,
                $order,
                $values,
                $color,
                $pattern,
                $action,
            ];
        }

        $records['draw'] = intval($request->input('draw'));
        $records['recordsTotal'] = $total;
        $records['recordsFiltered'] = $total;
        $records['data'] = $data;

        echo json_encode($records);
    }

    public function show_entity($id)
    {
        $attr_value = AttributeValue::findOrFail($id);
        return response()->json($attr_value);
    }

    public function update_entities(Request $request, $id)
    {


        $user = Auth::user();
        $value_attr = AttributeValue::find($id);
        $value_attr->attribute_id = $request->attribute_id_edit;
        $value_attr->attribute_value = $request->attribute_value_edit;
        $value_attr->value_list_order = $request->value_list_order_edit;
        $value_attr->color_attribute = $request->color_attribute;
        $value_attr->updated_by = $user->id;
        $value_attr->save();

        return response()->json(['message' => 'Attribute Value updated successfully',]);
    }

    public function delete_entities(Request $request)
    {

        $attr_value = AttributeValue::where('id', $request->id)->first();
        $variants_by_attr = Variant::whereRaw("FIND_IN_SET(?,attr_id)",[$attr_value->attribute_id])->whereRaw("FIND_IN_SET(?,attr_value_id)",[$request->id])->get();
        Variant::whereRaw("FIND_IN_SET(?,attr_id)",[$attr_value->attribute_id])->whereRaw("FIND_IN_SET(?,attr_value_id)",[$request->id])->delete();
        $attr_value->delete();

        AttributeValue::where('id', $request->id)->forceDelete();

        foreach($variants_by_attr as $item){
            $inventory = InventoryWithVariant::find($item->inventory_with_variant_id);
            if($inventory){
                   $variantCount = Variant::where('inventory_with_variant_id',$inventory->id)->count();
                   if($variantCount == 0){
                        InventoryWithVariant::where('id', $inventory->id)->delete();
                        InventoryWithVariant::where('id', $inventory->id)->forceDelete();
                   }
            }
     }
    
        if ($attr_value) {
            return response()->json(['status' => true, 'msg' => "Attribute Deleted Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }

    public function view_entity($id)
    {
        $attr_value = AttributeValue::findOrFail($id);
        return response()->json($attr_value);
    }

    ////////////////////////////////////////////////////////////////////////products   //////////////////////////////////////////////////////////////////
    public function index_products()
    {
        return view('vendor.catalog.products.index');
    }

    public function list_products(Request $request)
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

        $activeId = Auth::user()->id;
        $total = Product::where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
        })->where('created_by', $activeId)->count();

        $products = Product::where(function ($query) use ($search) {
            $query->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
        })->where('created_by', $activeId)
            ->orderBy('id', $orderRecord)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $data = [];
        foreach ($products as $key => $product) {
            $action = '<a href="' . route('vendor.products.view_products', $product->id) . '" class="px-1 btn btn-primary text-white" id="showproduct"  data-toggle="tooltip"  title="View-Product"><i class="dripicons-preview"></i></a>' .
                '<a href="' . route('vendor.products.edit_product', $product->id) . '" class="px-1 btn btn-warning text-white mx-1" id="showproduct"  data-toggle="tooltip"  title="Edit-Product"><i class="dripicons-document-edit"  ></i></a>' .
                '<button class="px-1 btn btn-danger deleteproduct" id="delete_product" data-id="' . $product->id . '" data-name="' . $product->name . '"  data-toggle="tooltip"  title="Delete-Product"><i class="dripicons-trash"></i></button>';


            $active = $product->status == "active" ? "selected" : "";
            $inactive = $product->status == "inactive" ? "selected" : "";

            $name = $product->name;
            $featured_image = '<img src="' . asset('public/vendor/featured_image/' . $product->featured_image) . '" alt="Featured Image" width="40px">';

            $brand = $product->brand;
            $description = $product->description;

            $status = '<select class="change_status form-select"   data-id="' . $product->id . '">
            <option value="active" ' . $active . '>Active</option>
            <option value="inactive" ' . $inactive . '>Inactive</option>';

            $data[] = [
                $offset + $key + 1,
                $name,
                $featured_image,
                $brand,
                $description,
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

    public function list_products_status(Request $request)
    {
        $rules = [
            'status_value' => 'required'
        ];
        $val = Validator::make($request->all(), $rules);
        if ($val->fails()) {
            return response()->json(['status' => false, 'msg' => $val->errors()->first()]);
            exit;
        } else {
            $products = Product::find($request->application_id);
            $products->status = $request->status_value;
            if ($products->save()) {
                return response()->json(array('status' => true, 'location' =>  route('admin.vendor.applications'), 'msg' => 'Status Updated Successfully!'));
            } else {
                return response()->json(array('status' => false, 'msg' => 'Something Went Wrong!'));
            }
        }
    }

    public function list_products_delete(Request $request)
    {
        
        $delete = Product::where('id', $request->id)->delete();
        $inventoryVariant = InventoryWithVariant::where('p_id',$request->id)->delete();
        $inventory = InventoryWithoutVariant::where('p_id',$request->id)->delete();

        ProductType::where('product_id',$request->id)->delete();
        
        if ($delete) {
            return response()->json(['status' => true, 'location' =>  route('vendor.products.list'), 'msg' => "Product Deleted Successfully"]);
        } else {
            return response()->json(['status' => false, 'msg' => "Error Occurred, Please try again"]);
        }
    }

    public function list_product_view($id)
    {
        $product = Product::find($id);
        $categories = Category::all();
        $gallery_images = json_decode($product->gallery_images, true);
        
        return view('vendor.catalog.products.view_product', ['product' => $product, 'categories' => $categories, 'gallery_images' => $gallery_images]);
    }
    public function product_edit($id)
    {
        $product = Product::find($id);
        $gallery_images = json_decode($product->gallery_images, true);
        $categories = Category::all();
        return view('vendor.catalog.products.edit_product', ['product' => $product, 'categories' => $categories, 'gallery_images' => $gallery_images]);
    }
    public function update_product(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'featured_image' => 'mimes:jpeg,jpg,png|max:2000',
        ]);

        $customMessages = [
            'featured_image.max' => 'The featured image must not be larger than 2 MB.',
        ];
        $validator->setCustomMessages($customMessages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ],
                409
            );
        }

        $user = Auth::user();
        $productId = $request->input('id');
        $product = Product::find($productId);
        $product->name = $request->input('name');
        $product->status = $request->input('status');
        $product->description = $request->input('description');
        $product->requires_shipping = $request->has('requires_shipping');
        $product->brand = $request->input('brand');
        $product->model_number = $request->input('model_number');
        $product->slug = Product::where('slug', $request->slug)->where('id', '!=', $productId)->exists() ?  $request->input('slug') .'-'. Product::where('slug', $request->slug)->where('id', '!=', $productId)->count() : $request->input('slug');
        $product->tags = $request->input('tag-input');
        $product->min_order_qty = $request->input('min_order_qty');
        $product->weight = $request->input('weight');
        $product->key_features = $request->input('key_features');
        $product->linked_items = $request->input('linked_items');
        $product->meta_title = $request->input('meta_title');
        $product->meta_description = $request->input('meta_description');
        $product->ogtag = $request->input('ogtag');
        $product->schema_markup = $request->input('schema_markup');
        $product->updated_by = $user->id;
        if ($request->hasFile('gallery_images')) {
            $images = $request->file('gallery_images');
            $galleryImagePaths = [];
            foreach ($images as $image) {
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/gallery_images');
                $path = $image->move($destinationPath, $image_name);
                $galleryImagePaths[] = $image_name;
            }
            $product->gallery_images =  json_encode($galleryImagePaths);
        }
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('vendor/featured_image');
            $image->move($destinationPath, $image_name);
            $product->featured_image = $image_name;
        }
        $categories = $request->input('categories');
        $dimensions = implode(',', [
            $request->input('length'),
            $request->input('width'),
            $request->input('height')
        ]);
        $product->dimensions = $dimensions;
        $product->save();
        $categories = $request->input('categories');
        $product->categories()->sync($categories);


    //   update cartItem product weight 
    CartItem::where('product_id', $product->id)->update([
        'weight' => $product->weight,
        'total_weight' => DB::raw('quantity * ' . $product->weight), // Use DB::raw for computed values
    ]);

        return response()->json(['success' => true, 'message' => 'Product updated successfully']);
    }

    public function create_products()
    {
        $category = Category::get();
        return view('vendor.catalog.products.create', ['category' => $category]);
    }

    public function store_products(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'gallery_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        //     'gallery_images' => 'max:10',
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        $validator = Validator::make($request->all(), [
            'featured_image' => 'mimes:jpeg,jpg,png|max:2000',
            
        ]);

        $customMessages = [
            'featured_image.max' => 'The featured image must not be larger than 2 MB.',
        ];
        $validator->setCustomMessages($customMessages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ],
                409
            );
        }
    
        // check number of products per vendor 
        $number = Product::where('created_by',Auth::user()->id)->where('status','active')->count();
        if($number > 25){
            return response()->json(
                [
                    'success' => false,
                    'message' => "You can not add more than 25 products",
                ],
                409
            );
        }

        $user = Auth::user();
        $product = new Product;
        $product->name = $request->input('name');
        $product->status = $request->input('status');
        $product->description = $request->input('description');
        $product->requires_shipping = $request->has('requires_shipping');
        $product->has_variant = $request->has('has_variant');
        $product->brand =$request->input('brand');
        $product->model_number = $request->input('model_number');
        $product->slug = Product::where('slug', $request->slug)->exists() ?  $request->input('slug') .'-'. Product::where('slug', $request->slug)->count() : $request->input('slug');
        $product->tags = $request->input('tag-input');
        $product->min_order_qty = $request->input('min_order_qty');
        $product->weight = $request->input('weight');
        $product->key_features = $request->input('key_features');
        $product->linked_items = $request->input('linked_items');
        $product->meta_title = $request->input('meta_title');
        $product->meta_description = $request->input('meta_description');
        $product->ogtag = $request->input('ogtag');
        $product->schema_markup = $request->input('schema_markup');
        $product->created_by = $user->id;
        $product->updated_by = $user->id;
        $product->save();

        if ($request->hasFile('gallery_images')) {
            $images = $request->file('gallery_images');
            $galleryImagePaths = [];

            foreach ($images as $image) {
                $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/gallery_images');
                $path = $image->move($destinationPath, $image_name);
                $galleryImagePaths[] = $image_name;
            }
            $product->gallery_images =  json_encode($galleryImagePaths);
        }

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $image_name = time() . '_image_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('vendor/featured_image');
            $image->move($destinationPath, $image_name);

            $product->featured_image = $image_name;
        }

        $dimensions = implode(',', [
            $request->input('length'),
            $request->input('width'),
            $request->input('height')
        ]);
        $product->dimensions = $dimensions;
        $product->save();
        $categories = $request->input('categories');
        $product->categories()->attach($categories);
        if ($product) {
            return response()->json(['status' => true, 'location' =>route('vendor.products.index'), 'message' => "Product Added Successfully"]);
            exit;
        } else {
            return response()->json(['status' => false, 'message' => "Error Occurred, Please try again"]);
        }
        // return response()->json(['status' => true,  'location' => route('vendor.products.index'),   'message' => 'Product created successfully!!']);
    }
}
