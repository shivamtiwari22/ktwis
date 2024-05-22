<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryWithVariant;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Wishlist;
use Carbon\Carbon;

class SearchSortingApiController extends Controller
{
    public function search_products(Request $request)
    {
        $query = $request->input('query');
        $perPage = 12;
        $page = request()->get('page', 1);
        try {
            $q = Product::query()->with([
                'inventory',
                'inventoryVariants.variants',
                'reviews',
            ])->where('status','active')
            ->whereHas('vendor.shops', function($q){
                $q->where('status','active');
            }) ;

            
            if ($request->has('category_id')) {
                $category_id = $request->input('category_id');
                $q->whereHas('categories', function ($categoryQuery) use (
                    $category_id
                ) {
                    $categoryQuery->where('category_id', $category_id);
                });
            }

            if ($request->sort_by) {
                if ($request->sort_by == 'newest') {
                    $q->orderBy('id', 'desc');
                } 
                
                // elseif ($request->sort_by == 'lowest_price') {
                //     $q
                //         ->withAvg('inventory', 'offer_price')
                //         ->orderBy('inventory_avg_offer_price');

                //     $q
                //         ->orderBy(function ($query) {
                //             $query
                //                 ->select('offer_price')
                //                 ->from('variants')
                //                 ->join(
                //                     'inventory_with_variants',
                //                     'variants.inventory_with_variant_id',
                //                     '=',
                //                     'inventory_with_variants.id'
                //                 )
                //                 ->where('variants.id', 1)
                //                 ->whereColumn(
                //                     'inventory_with_variants.p_id',
                //                     'products.id'
                //                 )
                //                 ->orderBy('offer_price', 'asc')
                //                 ->limit(1);
                //         }, 'asc')
                //         ->orderBy('id', 'asc');
                // } elseif ($request->sort_by == 'highest_price') {
                //     $q
                //         ->withAvg('inventory', 'offer_price')
                //         ->orderByDesc('inventory_avg_offer_price');

                //     $q
                //         ->orderBy(function ($query) {
                //             $query
                //                 ->select('offer_price')
                //                 ->from('variants')
                //                 ->join(
                //                     'inventory_with_variants',
                //                     'variants.inventory_with_variant_id',
                //                     '=',
                //                     'inventory_with_variants.id'
                //                 )
                //                 ->where('variants.id', 1)
                //                 ->whereColumn(
                //                     'inventory_with_variants.p_id',
                //                     'products.id'
                //                 )
                //                 ->orderBy('offer_price', 'desc')
                //                 ->limit(1);
                //         }, 'desc')
                //         ->orderBy('id', 'desc');
                // }
            }

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

            if ($request->has('is_free_shipping')) {
                $isFreeShipping = $request->input('is_free_shipping');
                if ($isFreeShipping == 'true') {
                    $q->whereHas('user.shipping', function ($query) {
                        $query
                            ->where('is_free', 1)
                            ->where('status', 'active')
                            ->where(function ($innerQuery) {
                                $innerQuery
                                    ->whereColumn(
                                        'minimum_order_weight',
                                        '<=',
                                        'products.weight'
                                    )
                                    ->whereColumn(
                                        'products.weight',
                                        '<=',
                                        'max_order_weight'
                                    );
                            });
                    });
                }
            }

            if ($request->has('new_arrival')) {
                $has_new_arrival = $request->input('new_arrival');
                if ($has_new_arrival == 'true') {
                    $currentDate = Carbon::now();
                    $thirtyDaysAgo = $currentDate->subDays(30);
                    $q
                        ->where('created_at', '>=', $thirtyDaysAgo)
                        ->orderBy('created_at', 'desc');
                }
            }

            // return $q->get();
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

            if ($request->custom_fields_attribute_values) {
                // return $request->custom_fields_attribute_values;
                // $custom_value = [];
                // foreach ($request->custom_fields as $field) {
                //     foreach ($field['values'] as $item) {
                //         $custom_value[] = $item;
                //     }
                // }

                $custom = implode(
                    ',',
                    $request->custom_fields_attribute_values
                );

                if ($custom) {
                    $q->whereHas('inventoryVariants.variants', function (
                        $queryBuilder
                    ) use ($custom) {
                        $queryBuilder->where(
                            'attr_value_id',
                            'like',
                            '%' . $custom . '%'
                        );

                        // $queryBuilder->where("attr_value_id",$custom);
                    });
                }
            }

        
            $filter = $q->where(function ($query) {
                $query->whereHas('inventory')
                      ->orWhereHas('inventoryVariants');
            }) ->paginate($perPage, ['*'], 'page', $page);


            if($request->price){
                $min_price = $request->price['min'];
                $max_price = $request->price['max'];
                $products = $filter->whereBetween('offer_price', [$min_price, $max_price])->values();

            }
            else if($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price' && !$request->price ){
                $products = $filter;
            }

            if ($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price') {
                if ($request->sort_by == 'lowest_price') {
                    $products = $products->sortBy('offer_price')->values()->all();
                }
                else if ($request->sort_by == 'highest_price') {
                    $products = $products->sortByDesc('offer_price')->values()->all();
                }
            }
            
            
            if($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price' || $request->price){
                $filteredPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    $products,
                    count($products),
                    $perPage,
                    $page,
                    ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                );
            }
            else {
                 $filteredPaginator  = $filter;
            }


            foreach ($filteredPaginator as $product) {
                $product->featured_image_url = asset(
                    'public/vendor/featured_image/' . $product->featured_image
                );

                if (auth('api')->user()) {
                    $product->Wishlist = Wishlist::where([
                        'product_id' => $product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';
                } else {
                 
                    if($request->header('fcm-token')){
                        $product->Wishlist =   Wishlist::where([
                            'product_id' => $product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';
                    }
                    else {
                        $product->Wishlist = 'No';
                    }


                }

                $galleryImages = json_decode($product->gallery_images, true);
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $product->gallery_image_urls = $galleryImageUrls;

                
                $product->total_reviews = $product->reviews->count();
                $total_rating = $product->reviews->sum('rating');
                $product->average_reviews =
                    $product->total_reviews > 0
                        ? $total_rating / $product->total_reviews
                        : 0;

                if ($product->inventory) {
                    $discount_percentage =
                        (($product->inventory->price -
                            $product->inventory->offer_price) /
                            $product->inventory->price) *
                        100;
                    $offer_price = $product->inventory->offer_price;
                } else {
                    $discount_percentage =
                        (($product->inventoryVariants[0]->variants[0]
                            ->price -
                            $product->inventoryVariants[0]->variants[0]
                                ->offer_price) /
                            $product->inventoryVariants[0]->variants[0]
                                ->price) *
                        100;
                    $offer_price =
                        $product->inventoryVariants[0]->variants[0]
                            ->offer_price;
                }
                $product->discount_percentage = round(
                    $discount_percentage,
                    2
                );

                
            };


            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $filteredPaginator],
                'timestamp' => Carbon::now(),
                'message' => 'Products fetched successfully!',
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

    public function sorting_product(Request $request)
    {
        try {
            $sort = $request->input('sort');

            $products = Product::join(
                'inventory_without_variants',
                'products.id',
                '=',
                'inventory_without_variants.p_id'
            )
                ->join(
                    'inventory_with_variants',
                    'products.id',
                    '=',
                    'inventory_with_variants.p_id'
                )
                ->join(
                    'variants',
                    'inventory_with_variants.id',
                    '=',
                    'variants.inventory_with_variant_id'
                );

            switch ($sort) {
                case 'price_low_to_high':
                    $products->orderByRaw(
                        'COALESCE(inventory_without_variants.offer_price, variants.offer_price) ASC'
                    );
                    $products->orderBy('variants.offer_price', 'asc');
                    break;
                case 'price_high_to_low':
                    $products->orderByRaw(
                        'COALESCE(inventory_without_variants.offer_price, variants.offer_price) DESC'
                    );
                    $products->orderBy('variants.offer_price', 'desc');
                    break;
                case 'rating_high_to_low':
                    $products->orderBy('products.rating', 'desc');
                    break;
                case 'rating_low_to_high':
                    $products->orderBy('products.rating', 'asc');
                    break;
                default:
                    $products->orderByRaw(
                        'COALESCE(inventory_without_variants.offer_price, variants.offer_price) ASC'
                    );
                    $products->orderBy('variants.offer_price', 'asc');
            }

            $sortedProducts = $products->get();

            $sortedProducts->each(function ($product) {
                $product->featured_image_url = asset(
                    'public/vendor/featured_image/' . $product->featured_image
                );
                $product->inventory_without_variant_url = asset(
                    'public/vendor/featured_image/inventory' . $product->image
                );
            });

            $sortedProducts->each(function ($product) {
                $galleryImages = json_decode($product->gallery_images, true);
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $product->gallery_image_urls = $galleryImageUrls;
            });

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $sortedProducts],
                'timestamp' => Carbon::now(),
                'message' => 'Products fetched successfully!',
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

    public function variant_by_attribute(Request $request)
    {
        try {
            $attrValues = implode(',', $request->attribute_value_id);
            $inventoryVariant = InventoryWithVariant::where(
                'p_id',
                $request->product_id
            )->first();

            if (!$inventoryVariant) {
                return response()->json(
                    [
                        'http_status_code' => 400,
                        'status' => false,
                        'context' => [
                            'error' => 'Product does not have variants',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    400
                );
            }

            $attr_value =
                Variant::where(
                    'inventory_with_variant_id',
                    $inventoryVariant->id
                )->first()->attr_value_id ?? null;
            $attr_value_count = count(explode(',', $attr_value));

            if ($attr_value_count == count($request->attribute_value_id)) {
                $variant = Variant::where(
                    'inventory_with_variant_id',
                    $inventoryVariant->id
                )
                    ->where('attr_value_id', $attrValues)
                    ->first();

                if ($variant) {
                    $variant->in_stock = $variant
                        ? ($variant->stock_quantity > 0
                            ? 'Yes'
                            : 'NO')
                        : null;
                }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $variant],
                    'timestamp' => Carbon::now(),
                    'message' => 'Variant fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 400,
                        'status' => true,
                        'context' => [
                            'error' => 'Please Provide All Attribute Value id',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Something Went Wrong',
                    ],
                    400
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
}
