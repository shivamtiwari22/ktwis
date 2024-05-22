<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Controller;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductApiController extends Controller
{
    public function product_all_data($id)
    {
        try {
            $data = Product::where('id', $id)->first();
            $data->featured_image_url = asset(
                'public/vendor/featured_image/' . $data->featured_image
            );
            $galleryImages = json_decode($data->gallery_images, true);
            $galleryImageUrls = collect($galleryImages)->map(function ($image) {
                return asset('public/vendor/gallery_images/' . $image);
            });
            $data->gallery_image_urls = $galleryImageUrls;

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Product data show successfully',
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

    public function all_product()
    {
        try {
            $data = Product::with('categories')
                ->where('created_by', Auth::user()->id)
                ->get();

            foreach ($data as $item) {
                $item->featured_image_url = asset(
                    'public/vendor/featured_image/' . $item->featured_image
                );
                $galleryImages = json_decode($item->gallery_images, true);
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $item->gallery_image_urls = $galleryImageUrls;
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Product data show successfully',
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

    public function Product_add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'status' => 'required',
                'slug' => 'required',
                'description' => 'required',
                'requires_shipping' => 'required',
                'brand' => 'required',
                'min_order_qty' => 'required',
                'meta_description' => 'required',
                'weight' => 'required',
                'featured_image' => 'required',
                'key_features' => 'required',
                'meta_title' => 'required',
                'length' => 'required',
                'height' => 'required',
                'categories' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => 'Validation failed'],
                        'timestamp' => Carbon::now(),
                        'message' => $validator->errors()->first(),
                    ],
                    422
                );
            }


            $number = Product::where('created_by',Auth::user()->id)->where('status','active')->count();
            if($number > 25){
                return response()->json(
                    [
                        'http_status_code' => 409,
                        'status' => false,
                        'context' => ['error' => 'You can not add more than 25 products'],
                        'timestamp' => Carbon::now(),
                        'message' => "You can not add more than 25 products",
                    ],
                    409
                );
            }

            $user = Auth::user();
            $product = new Product();
            $product->name = $request->input('name');
            $product->status = $request->input('status');
            $product->description = $request->input('description');
            $product->requires_shipping = $request->input('requires_shipping');
            $product->has_variant = $request->input('has_variant');
            $product->brand = $request->input('brand');
            $product->model_number = $request->input('model_number');
            $product->slug = Product::where('slug', $request->slug)->exists() ?  $request->input('slug') .'-'. Product::where('slug', $request->slug)->count() : $request->input('slug');
            $product->tags = $request->input('tag-input');
            $product->min_order_qty = $request->input('min_order_qty');
            $product->weight = $request->input('weight');
            $product->key_features = $request->input('key_features');
            $product->linked_items = $request->input('linked_items');
            $product->meta_title = $request->input('meta_title');
            $product->meta_description = $request->input('meta_description');
            $product->created_by = $user->id;
            $product->updated_by = $user->id;
            $product->save();
            if ($request->hasFile('gallery_images')) {
                $images = $request->file('gallery_images');
                $galleryImagePaths = [];
                foreach ($images as $image) {
                    if ($image->isValid()) {
                        $image_name =
                            time() .
                            '_image_' .
                            uniqid() .
                            '.' .
                            $image->getClientOriginalExtension();
                        $destinationPath = public_path('vendor/gallery_images');
                        $path = $image->move($destinationPath, $image_name);
                        $galleryImagePaths[] = $image_name;
                    }
                }
                $product->gallery_images = json_encode($galleryImagePaths);
            }

            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $image_name =
                    time() .
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/featured_image');
                $image->move($destinationPath, $image_name);
                $product->featured_image = $image_name;
            }
            $dimensions = implode(',', [
                $request->input('length'),
                $request->input('width'),
                $request->input('height'),
            ]);
            $product->dimensions = $dimensions;
            $product->save();
            $categories = explode(',', $request->categories);
            $product->categories()->sync($categories);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Product created successfully',
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
    public function product_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'status' => 'required',
                'slug' => 'required',
                'description' => 'required',
                'requires_shipping' => 'required',
                'brand' => 'required',
                'min_order_qty' => 'required',
                'weight' => 'required',
                'featured_image' => 'mimes:jpeg,jpg,png|max:2000',
                'key_features' => 'required',
                'length' => 'required',
                'height' => 'required',
                'product_id' => 'required',
                'categories' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => 'Validation failed'],
                        'timestamp' => Carbon::now(),
                        'message' => $validator->errors()->first(),
                    ],
                    422
                );
            }

            $user = Auth::user();
            $productId = $request->input('product_id');
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
            $product->updated_by = $user->id;

            $galleryImagePaths = [];
            if ($request->hasFile('gallery_images')) {
                $images = $request->file('gallery_images');
               
                foreach ($images as $image) {
                    $image_name =
                        time() .
                        '_image_' .
                        uniqid() .
                        '.' .
                        $image->getClientOriginalExtension();
                    $destinationPath = public_path('vendor/gallery_images');
                    $path = $image->move($destinationPath, $image_name);
                    $galleryImagePaths[] = $image_name;
                }
                // $product->gallery_images = json_encode($galleryImagePaths);
            }

            // update multiple images 
                $getDeleteImg = $request->deleted_images;
               $deletedImages =   $getDeleteImg ?? [];
            // Get the current images from the JSON column
              $currentImages = json_decode($product->gallery_images, true);
            // Remove images that are in the deleted array
            $filtered = array_diff($currentImages, $deletedImages);

            // Add new images from the requested array
                $filteredImages = array_merge($filtered, $galleryImagePaths);
                $product->gallery_images = json_encode($filteredImages);
           

            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $image_name =
                    time() .    
                    '_image_' .
                    uniqid() .
                    '.' .
                    $image->getClientOriginalExtension();
                $destinationPath = public_path('vendor/featured_image');
                $image->move($destinationPath, $image_name);
                $product->featured_image = $image_name;
            }

            $dimensions = implode(',', [
                $request->input('length'),
                $request->input('width'),
                $request->input('height'),
            ]);
            $product->dimensions = $dimensions;
            $product->save();
            $categories = explode(',', $request->categories);
            $product->categories()->sync($categories);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' => 'Product updated  successfully',
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

    public function product_delete($id)
    {
        $delete = Product::where('id', $id)->delete();
        $inventoryVariant = InventoryWithVariant::where('p_id',$id)->delete();
        $inventory = InventoryWithoutVariant::where('p_id',$id)->delete();

        if ($delete) {
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Product Deleted Successfully',
            ]);
            exit();
        } else {
            return response()->json(
                [
                    'http_status_code' => 500,
                    'status' => false,
                    'context' => [
                        'error' => 'Some error occurred! , Please try again',
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'An unexpected error occurred',
                ],
                500
            );
        }
    }
}
