<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //

    public function getAllProduct()
    {
        try {
            $data = Product::all();
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$data]],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetch Successfully ',
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

    public function show_attribute($product_id)
    {
        try {
            $product = Product::find($product_id);

            if (!$product) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Product not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Product not found',
                    ],
                    404
                );
            }
            if ($product->has_variant == 1) {
                $product->load('categories.attributes.attributeValues');
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => [
                            'error' => 'This product does not have variants',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'This product does not have variants',
                    ],
                    404
                );
            }
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => [$product]],
                'timestamp' => Carbon::now(),
                'message' =>
                    'Product with Attribute and Attribute Values are fetched',
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



    public function  getVendorProducts($id,Request $request){
        try{

            $q = Product::with([
                'inventory',
                'inventoryVariants.variants',
                'reviews',
            ])
                ->where('status','active')
                ->where('created_by', $id);
                // ->take(9);
            
                if ($request->sort_by) {
                    if ($request->sort_by == 'newest') {
                        $q->orderBy('id', 'desc');
                    }
                }
    
                if ($request->has('is_free_shipping')) {
                    $isFreeShipping = $request->input('is_free_shipping');
                    if ($isFreeShipping == 'true') {
                        $q->whereHas('user.shipping', function ($query) {
                            $query
                            ->where('is_free', 1)
                            ->where('status', 'active')
                            ->where(function ($innerQuery) {
                                $innerQuery
                                ->whereColumn('minimum_order_weight', '<=','products.weight')
                                ->whereColumn('products.weight', '<=', 'max_order_weight');
                            });  
                        });
                    }
                }
    
                if ($request->has('has_offers')) {
                    $has_offers = $request->input('has_offers');
    
                    if ($has_offers == 'true') {
                        $q->where(function ($query) {
                            $query
                                ->whereHas('inventory', function ($offerQuery) {
                                    $offerQuery->whereNotNull('offer_price');
                                })
                                ->orWhereHas(
                                    'inventoryVariants.variants',
                                    function ($offerQuery) {
                                        $offerQuery->whereNotNull('offer_price');
                                    }
                                );
                        });
                    }
                }
    
                if ($request->has('new_arrival')) {
                    $has_new_arrival = $request->input('new_arrival');
                    if ($has_new_arrival == 'true') {
                        $currentDate = Carbon::now();
                        $thirtyDaysAgo = $currentDate->subDays(30);
                        $q->where('created_at', '>=', $thirtyDaysAgo)->orderBy('created_at', 'desc');
                    }
                }
    
    
                $filter = $q->where(function ($query) {
                    $query->whereHas('inventory')
                          ->orWhereHas('inventoryVariants');
                })->get();
    
                
                if ($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price') {
                    if ($request->sort_by == 'lowest_price') {
                        $products = $filter->sortBy('offer_price')->values()->all();
                     }
                     else if ($request->sort_by == 'highest_price') {
                        $products = $filter->sortByDesc('offer_price')->values()->all();
                     }
                }
                else {
                    $products  = $filter;
                }
    
               
              foreach($products as $product){
                $product->featured_image_url = asset(
                    'public/vendor/featured_image/' .
                        $product->featured_image
                );

                if(auth('api')->user()){
                    $product->Wishlist = Wishlist::where(['product_id' => $product->id, 'created_by' => auth('api')->user()->id])->first() ? "Yes" : "No" ;
                    $product->is_wishlist = Wishlist::where(['product_id' => $product->id, 'created_by' => auth('api')->user()->id])->first() ? true : false ;
                }
                else{

                    $product->Wishlist = "No"; 
                    $product->is_wishlist = false; 


                    if($request->header('fcm-token')){
                        $product->Wishlist =   Wishlist::where([
                            'product_id' => $product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                            $product->is_wishlist  =   Wishlist::where([
                                'product_id' => $product->id,
                                'guest_user' => $request->header('fcm-token'),
                            ])->first()
                                ?  true
                                : false;
                    }
                    else {
                        $product->Wishlist = 'No';
                       $product->is_wishlist = false; 

                    }
                }

                $galleryImages = json_decode(
                    $product->gallery_images,
                    true
                );
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });

                $product->gallery_image_urls = $galleryImageUrls;

                if($product->inventory){
                    $discount_percentage  = ($product->inventory->price - $product->inventory->offer_price) / $product->inventory->price * 100;
                }
                else{
                    $discount_percentage = ($product->inventoryVariants[0]->variants[0]->price -$product->inventoryVariants[0]->variants[0]->offer_price) / $product->inventoryVariants[0]->variants[0]->price * 100;
                }
                $product->discount_percentage  = round($discount_percentage,2);

                $product->total_reviews = $product->reviews->count();
                $total_rating = $product->reviews->sum('rating');
                $product->average_reviews = ($product->total_reviews > 0) ? ($total_rating / $product->total_reviews) : 0;

            }


            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $products],
                'timestamp' => Carbon::now(),
                'message' => 'product Fetched Successfully',
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


    public function searchVendorProducts(Request $request){

        try{

            $query = $request->input('query');
            $q = Product::query()->with([
                'inventory',
                'inventoryVariants.variants',
                'reviews',
            ])->where('created_by', $request->vendor_id);


            if ($request->has('query')) {
                $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder
                        ->where('name', 'like', '%' . $query . '%');
                        // ->orWhere('model_number', 'like', '%' . $query . '%')
                        // ->orWhere('meta_title', 'like', '%' . $query . '%')
                        // ->orWhere(
                        //     'meta_description',
                        //     'like',
                        //     '%' . $query . '%'
                        // )
                        // ->orWhere('key_features', 'like', '%' . $query . '%')
                        // ->orWhere('description', 'like', '%' . $query . '%')
                        // ->orWhere(function ($queryBuilder) use ($query) {
                        //     $queryBuilder
                        //         ->where('tags', 'like', "%$query%")

                        //         ->orWhere('tags', 'like', "$query,%")
                        //         ->orWhere('tags', 'like', "%,$query,%");
                        // });
                });
            }

            if ($request->sort_by) {
                if ($request->sort_by == 'newest') {
                    $q->orderBy('id', 'desc');
                }
            }

            if ($request->has('is_free_shipping')) {
                $isFreeShipping = $request->input('is_free_shipping');
                if ($isFreeShipping == 'true') {
                    $q->whereHas('user.shipping', function ($query) {
                        $query
                        ->where('is_free', 1)
                        ->where('status', 'active')
                        ->where(function ($innerQuery) {
                            $innerQuery
                            ->whereColumn('minimum_order_weight', '<=','products.weight')
                            ->whereColumn('products.weight', '<=', 'max_order_weight');
                        });  
                    });
                }
            }

            if ($request->has('has_offers')) {
                $has_offers = $request->input('has_offers');

                if ($has_offers == 'true') {
                    $q->where(function ($query) {
                        $query
                            ->whereHas('inventory', function ($offerQuery) {
                                $offerQuery->whereNotNull('offer_price');
                            })
                            ->orWhereHas(
                                'inventoryVariants.variants',
                                function ($offerQuery) {
                                    $offerQuery->whereNotNull('offer_price');
                                }
                            );
                    });
                }
            }

            if ($request->has('new_arrival')) {
                $has_new_arrival = $request->input('new_arrival');
                if ($has_new_arrival == 'true') {
                    $currentDate = Carbon::now();
                    $thirtyDaysAgo = $currentDate->subDays(30);
                    $q->where('created_at', '>=', $thirtyDaysAgo)->orderBy('created_at', 'desc');
                }
            }


            $filter = $q->where(function ($query) {
                $query->whereHas('inventory')
                      ->orWhereHas('inventoryVariants');
            })->get();

            
            if ($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price') {
                if ($request->sort_by == 'lowest_price') {
                    $products = $filter->sortBy('offer_price')->values()->all();
                 }
                 else if ($request->sort_by == 'highest_price') {
                    $products = $filter->sortByDesc('offer_price')->values()->all();
                 }
            }
            else {
                $products  = $filter;
            }

             foreach($products as $product){
                $product->featured_image_url = asset(
                    'public/vendor/featured_image/' . $product->featured_image
                );

                $galleryImages = json_decode($product->gallery_images, true);
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $product->gallery_image_urls = $galleryImageUrls;

                if(auth('api')->user()){
                    $product->Wishlist = Wishlist::where(['product_id' => $product->id, 'created_by' => auth('api')->user()->id])->first() ? "Yes" : "No" ;
                    $product->is_wishlist = Wishlist::where(['product_id' => $product->id, 'created_by' => auth('api')->user()->id])->first() ? true : false ;
                }
                else{
                    $product->Wishlist = "No";
                    $product->is_wishlist = false; 
                }

          
                if($product->inventory){
                    $discount_percentage  = ($product->inventory->price - $product->inventory->offer_price) / $product->inventory->price * 100;
                }
                else{
                    $discount_percentage = ($product->inventoryVariants[0]->variants[0]->price -$product->inventoryVariants[0]->variants[0]->offer_price) / $product->inventoryVariants[0]->variants[0]->price * 100;
                }
                $product->discount_percentage  = round($discount_percentage,2);

                $product->total_reviews = $product->reviews->count();
                $total_rating = $product->reviews->sum('rating');
                $product->average_reviews = ($product->total_reviews > 0) ? ($total_rating / $product->total_reviews) : 0;
            };

          
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $products],
                'timestamp' => Carbon::now(),
                'message' => 'Products fetched successfully!',
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
