<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute as ModelsAttribute;
use App\Models\BusinessArea;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Charges;
use App\Models\ContactPage;
use App\Models\Contactus;
use App\Models\Country;
use App\Models\Currency;
use App\Models\FaqAnswer;
use App\Models\FaqTopic;
use App\Models\GlobalSetting;
use App\Models\Pages;
use App\Models\ProductType;
use App\Models\RecentlyViewed;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\RecentItem;
use App\Models\ReturnPolicy;
use App\Models\Review;
use App\Models\SeoPage;
use App\Models\SliderManagement;
use App\Models\SocialMedia;
use App\Models\Specification;
use App\Models\State;
use App\Models\User;
use App\Models\Variant;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Svg\Tag\Rect;

class HomeApiController extends Controller
{
    public function flash_sale(Request $request)
    {
        try {
            $flash = ProductType::with(
                'product.inventory',
                'product.inventoryVariants.variants',
                'product.reviews'
            )->activeVendorProducts()
            ->whereHas('product',function($q){
                $q->where('status','active');
         })
                ->where('type', 'flash_sale')
                ->limit(16)
                ->where(function ($query) {
                    $query->whereHas('product.inventory')
                          ->orWhereHas('product.inventoryVariants');
                })
                ->get();
                $flash_sale = $flash;
            // $flash_sale = $flash->filter(function ($product) {
            //     return $product->product->inventory ||
            //         $product->product->inventoryVariants->count() > 0;
            // });
            $flash_sale->each(function ($product) use ($request) {
                if ($product->product->featured_image) {
                    $product->product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->product->featured_image
                    );
                }

                if ($product->product->inventory) {
                    $discount_percentage =
                        (($product->product->inventory->price -
                            $product->product->inventory->offer_price) /
                            $product->product->inventory->price) *
                        100;
                } else {
                    $discount_percentage =
                        (($product->product->inventoryVariants[0]->variants[0]
                            ->price -
                            $product->product->inventoryVariants[0]->variants[0]
                                ->offer_price) /
                            $product->product->inventoryVariants[0]->variants[0]
                                ->price) *
                        100;
                }
                $product->product->discount_percentage = round(
                    $discount_percentage,
                    2
                );

                if (auth('api')->user()) {
                    $product->product->Wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $product->product->is_wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {

                    if($request->header('fcm-token')){
                        $product->product->Wishlist =   Wishlist::where([
                            'product_id' => $product->product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $product->product->is_wishlist  =   Wishlist::where([
                            'product_id' => $product->product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $product->product->Wishlist = 'No';
                        $product->product->is_wishlist = false;
                    }


                }

                if ($product->product->gallery_images) {
                    $galleryImages = json_decode(
                        $product->product->gallery_images,
                        true
                    );
                    $galleryImageUrls = collect($galleryImages)->map(function (
                        $image
                    ) {
                        return asset('public/vendor/gallery_images/' . $image);
                    });
                    $product->product->gallery_image_urls = $galleryImageUrls;
                }
                $product->product->total_reviews = $product->product->reviews->count();

                $total_rating = $product->product->reviews->sum('rating');
                $product->product->average_reviews =
                    $product->product->total_reviews > 0
                        ? $total_rating / $product->product->total_reviews
                        : 0;
            });
            if ($flash_sale) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $flash_sale,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Flash Sale fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not fetched'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not fetched',
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

    public function deal_of_the_day()
    {
        try {
            $products = ProductType::with(
                'product.inventory',
                'product.inventoryVariants.variants'
            )->activeVendorProducts()
            ->whereHas('product',function($q){
                $q->where('status','active');
         })
                ->where('type', 'deal_of_the_day')
                ->limit(1)
                ->where(function ($query) {
                    $query->whereHas('product.inventory')
                          ->orWhereHas('product.inventoryVariants');
                })
                ->get();

            $deal_of_the_day = $products;

            // $deal_of_the_day = $products->filter(function ($product) {
            //     return $product->product->inventory ||
            //         $product->product->inventoryVariants->count() > 0;
            // });

            $deal_of_the_day->each(function ($product) {
                if ($product->product->featured_image) {
                    $product->product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->product->featured_image
                    );
                }

                if (auth('api')->user()) {
                    $product->product->cart = CartItem::where([
                        'product_id' => $product->product->id,
                        'user_id' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $product->product->is_cart = CartItem::where([
                        'product_id' => $product->product->id,
                        'user_id' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
                    $product->product->cart = 'No';
                    $product->product->is_cart = false;
                }

                if ($product->product->gallery_images) {
                    $galleryImages = json_decode(
                        $product->product->gallery_images,
                        true
                    );
                    $galleryImageUrls = collect($galleryImages)->map(function (
                        $image
                    ) {
                        return asset('public/vendor/gallery_images/' . $image);
                    });
                    $product->product->gallery_image_urls = $galleryImageUrls;
                }

                $product->product->total_reviews = $product->product->reviews->count();
                $total_rating = $product->product->reviews->sum('rating');
                $product->product->average_reviews =
                    $product->product->total_reviews > 0
                        ? $total_rating / $product->product->total_reviews
                        : 0;
            });
            if ($deal_of_the_day) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $deal_of_the_day,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Deal of the Day fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not fetched'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not fetched',
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

    public function featured_item(Request $request)
    {
        try {
            $products = ProductType::with(
                'product.inventory',
                'product.inventoryVariants.variants',
                'product.reviews'
            )->activeVendorProducts()
            ->whereHas('product',function($q){
                   $q->where('status','active');
            })
                ->where('type', 'featured_item')
                ->limit(16)
                ->where(function ($query) {
                    $query->whereHas('product.inventory')
                          ->orWhereHas('product.inventoryVariants');
                });
             $featured_item =   $products ->get();
    
            // $featured_item = $products->filter(function ($product) {
            //     return $product->product->inventory ||
            //         $product->product->inventoryVariants->count() > 0;
            // });

            $featured_item->each(function ($product) use ($request) {
                if ($product->product->featured_image) {
                    $product->product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->product->featured_image
                    );
                }

                if (auth('api')->user()) {
                    $product->product->Wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $product->product->is_wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
                    if($request->header('fcm-token')){
                        $product->product->Wishlist =   Wishlist::where([
                            'product_id' => $product->product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $product->product->is_wishlist  =   Wishlist::where([
                            'product_id' => $product->product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $product->product->Wishlist = 'No';
                        $product->product->is_wishlist = false;
                    }
                }

                if ($product->product->gallery_images) {
                    $galleryImages = json_decode(
                        $product->product->gallery_images,
                        true
                    );
                    $galleryImageUrls = collect($galleryImages)->map(function (
                        $image
                    ) {
                        return asset('public/vendor/gallery_images/' . $image);
                    });
                    $product->product->gallery_image_urls = $galleryImageUrls;
                }

                if ($product->product->inventory) {
                    $discount_percentage =
                        (($product->product->inventory->price -
                            $product->product->inventory->offer_price) /
                            $product->product->inventory->price) *
                        100;
                } else {
                    $discount_percentage =
                        (($product->product->inventoryVariants[0]->variants[0]
                            ->price -
                            $product->product->inventoryVariants[0]->variants[0]
                                ->offer_price) /
                            $product->product->inventoryVariants[0]->variants[0]
                                ->price) *
                        100;
                }
                $product->product->discount_percentage = round(
                    $discount_percentage,
                    2
                );

                $product->product->total_reviews = $product->product->reviews->count();
                $total_rating = $product->product->reviews->sum('rating');
                $product->product->average_reviews =
                    $product->product->total_reviews > 0
                        ? $total_rating / $product->product->total_reviews
                        : 0;
            });
            if ($featured_item) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $featured_item,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Featured Item fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not fetched'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not fetched',
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

    public function trending_item(Request $request)
    {
        try {
            $products = ProductType::with(
                'product.inventory',
                'product.inventoryVariants.variants',
                'product.reviews'
            )->activeVendorProducts()
            ->whereHas('product',function($q){
                $q->where('status','active');
         })
                ->where('type', 'trending_item')
                ->limit(16)
                ->where(function ($query) {
                    $query->whereHas('product.inventory')
                          ->orWhereHas('product.inventoryVariants');
                })
                ->get();

                $trending_item = $products;
            // $trending_item = $products->filter(function ($product) {
            //     return $product->product->inventory ||
            //         $product->product->inventoryVariants->count() > 0;
            // });

            $trending_item->each(function ($product) use ($request) {
                if ($product->product->featured_image) {
                    $product->product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->product->featured_image
                    );
                }

                if (auth('api')->user()) {
                    $product->product->Wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $product->product->is_wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
                    if($request->header('fcm-token')){
                        $product->product->Wishlist =   Wishlist::where([
                            'product_id' => $product->product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $product->product->is_wishlist  =   Wishlist::where([
                            'product_id' => $product->product->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $product->product->Wishlist = 'No';
                        $product->product->is_wishlist = false;
                    }
                }

                if ($product->product->gallery_images) {
                    $galleryImages = json_decode(
                        $product->product->gallery_images,
                        true
                    );
                    $galleryImageUrls = collect($galleryImages)->map(function (
                        $image
                    ) {
                        return asset('public/vendor/gallery_images/' . $image);
                    });
                    $product->product->gallery_image_urls = $galleryImageUrls;
                }

                if ($product->product->inventory) {
                    $discount_percentage =
                        (($product->product->inventory->price -
                            $product->product->inventory->offer_price) /
                            $product->product->inventory->price) *
                        100;
                } else {
                    $discount_percentage =
                        (($product->product->inventoryVariants[0]->variants[0]
                            ->price -
                            $product->product->inventoryVariants[0]->variants[0]
                                ->offer_price) /
                            $product->product->inventoryVariants[0]->variants[0]
                                ->price) *
                        100;
                }
                $product->product->discount_percentage = round(
                    $discount_percentage,
                    2
                );

                $product->product->total_reviews = $product->product->reviews->count();
                $total_rating = $product->product->reviews->sum('rating');
                $product->product->average_reviews =
                    $product->product->total_reviews > 0
                        ? $total_rating / $product->product->total_reviews
                        : 0;
            });
            if ($trending_item) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $trending_item,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Trending Item fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not fetched'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not fetched',
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

    public function add_recently_viewed(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer',
            ]);
            $user_id = Auth::user()->id;
            $recently_viewed = new RecentlyViewed();
            $recently_viewed->product_id = $request->input('product_id');
            $recently_viewed->user_id = $user_id;
            $recently_viewed->save();
            if ($recently_viewed) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $recently_viewed,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' =>
                        'Product added to Recently viewed successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
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

    public function recently_viewed()
    {
        try {
            $id = auth('api')->user()->id ?? null;
            if (!$id) {
                $recently_viewed = [];
            }

            $recently_viewed = RecentlyViewed::with([
                'product',
                'product.inventory',
                'product.inventoryVariants.variants',
                'product.reviews',
            ])
            ->whereHas('product',function($q){
                $q->where('status','active');
         })
                ->where('user_id', $id)
                ->take(16)
                ->get();

            $recently_viewed->each(function ($product) {
                $product->product->featured_image_url = asset(
                    'public/vendor/featured_image/' .
                        $product->product->featured_image
                );

                if (auth('api')->user()) {
                    $product->product->Wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $product->product->is_wishlist = Wishlist::where([
                        'product_id' => $product->product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
                    $product->product->Wishlist = 'No';
                    $product->product->is_wishlist = false;
                }

                $galleryImages = json_decode(
                    $product->product->gallery_images,
                    true
                );
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });

                $product->product->gallery_image_urls = $galleryImageUrls;

                $product->product->total_reviews = $product->product->reviews->count();
                $total_rating = $product->product->reviews->sum('rating');
                $product->product->average_reviews =
                    $product->product->total_reviews > 0
                        ? $total_rating / $product->product->total_reviews
                        : 0;

                if ($product->product->inventory) {
                    $discount_percentage =
                        (($product->product->inventory->price -
                            $product->product->inventory->offer_price) /
                            $product->product->inventory->price) *
                        100;
                } else {
                    $discount_percentage =
                        (($product->product->inventoryVariants[0]->variants[0]
                            ->price -
                            $product->product->inventoryVariants[0]->variants[0]
                                ->offer_price) /
                            $product->product->inventoryVariants[0]->variants[0]
                                ->price) *
                        100;
                }
                $product->product->discount_percentage = round(
                    $discount_percentage,
                    2
                );
            });
            if ($recently_viewed) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $recently_viewed,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Recently viewed fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not fetched'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not fetched',
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

    public function categories()
    {
        try {
            $category = Category::with('children')
                ->withCount('products')
                ->whereNull('parent_category_id')
                ->get()
                ->map(function ($item) {
                    $item->image = asset(
                        'public/admin/category/images/' . $item->image
                    );
                    return $item;
                });
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $category],
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


    public function categoryBySlug($slug){
        try {
           $category = Category::with('children')
                ->withCount('products')
                ->where('slug',$slug)
                ->get()
                ->map(function ($item) {
                    $item->image = asset(
                        'public/admin/category/images/' . $item->image
                    );
                    return $item;
                });

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $category],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data Fetch Successfully ',
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

    public function products_by_category(Request $request, $id)
    {
        try {
            $perPage = 12;
            $page = request()->get('page', 1);
            $category = Category::find($id);
            $firstCategoryName = $category->category_name;

            if ($category) {
                $productsQuery = $category
                    ->products()
                    ->with([
                        'inventory',
                        'inventoryVariants.variants',
                        'reviews',
                    ])->whereHas('vendor.shops', function($q){
                        $q->where('status','active');
                    })
                    ->where('status','active')
                    ->withAvg('reviews', 'rating');
                // ->orderByDesc('reviews_avg_rating');
          
                if ($request->sort_by) {
                    if ($request->sort_by == 'newest') {
                        $productsQuery->orderBy('id', 'desc');
                    } 
                    // elseif ($request->sort_by == 'lowest_price') {
                    //     $productsQuery
                    //         ->withAvg('inventory', 'offer_price')
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
                    //                 ->join(
                    //                     'products',
                    //                     'inventory_with_variants.p_id',
                    //                     '=',
                    //                     'products.id'
                    //                 )
                    //                 ->orderBy('offer_price', 'asc')
                    //                 ->limit(1);
                    //         })
                    //         ->orWhereDoesntHave('inventoryVariants.variants') // Only products without variants
                    //         ->orderBy('inventory_avg_offer_price', 'asc')
                    //     ->orderBy('id', 'asc');
                    // } elseif ($request->sort_by == 'highest_price') {
                    //     $productsQuery
                    //         ->withAvg('inventory', 'offer_price')
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
                    //                 ->join(
                    //                     'products',
                    //                     'inventory_with_variants.p_id',
                    //                     '=',
                    //                     'products.id'
                    //                 )
                    //                 ->limit(1)
                    //                 ->orderBy('offer_price', 'desc');
                    //         }, 'desc')
                    //         ->orWhereDoesntHave('inventoryVariants.variants') // Only products without variants
                    //         ->orderBy('inventory_avg_offer_price', 'desc')
                    //         ->orderBy('id', 'desc');
                    // }
                }

                if ($request->has('is_free_shipping')) {
                    $isFreeShipping = $request->input('is_free_shipping');
                    if ($isFreeShipping == 'true') {
                        $productsQuery->whereHas('user.shipping', function (
                            $query
                        ) {
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
                        $productsQuery
                            ->where('created_at', '>=', $thirtyDaysAgo)
                            ->orderBy('created_at', 'desc');
                    }
                }

                if ($request->has('has_offers')) {
                    $has_offers = $request->input('has_offers');

                    if ($has_offers == 'true') {
                        $productsQuery->where(function ($query) {
                            $query
                                ->whereHas('inventory', function ($offerQuery) {
                                    $offerQuery->whereNotNull('offer_price');
                                })
                                ->orWhereHas(
                                    'inventoryVariants.variants',
                                    function ($offerQuery) {
                                        $offerQuery->whereNotNull(
                                            'offer_price'
                                        );
                                    }
                                );
                        });
                    }
                }

                if ($request->custom_fields_attribute_values) {
                    $custom_array = json_decode(
                        $request->custom_fields_attribute_values,
                        true
                    );
                    $custom = implode(',', $custom_array);

                    if ($custom) {
                        $productsQuery->whereHas(
                            'inventoryVariants.variants',
                            function ($queryBuilder) use ($custom) {
                                $queryBuilder->where(
                                    'attr_value_id',
                                    'like',
                                    '%' . $custom . '%'
                                );

                                // $queryBuilder->where("attr_value_id",$custom);
                            }
                        );
                    }
                }

                if ($request->has('price')) {
                    $price = json_decode($request->price, true);

                    $min_price = $price['min'];
                    $max_price = $price['max'];

                    // $productsQuery->where(function ($query) use (
                    //     $min_price,
                    //     $max_price
                    // ) {
                    //     $query
                    //         ->whereHas('inventory', function ($offerQuery) use (
                    //             $min_price,
                    //             $max_price
                    //         ) {
                    //             $offerQuery->whereBetween('offer_price', [
                    //                 $min_price,
                    //                 $max_price,
                    //             ]);
                    //         })
                    //         ->orWhereHas(
                    //             'inventoryVariants.variants',
                    //             function ($offerQuery) use (
                    //                 $min_price,
                    //                 $max_price
                    //             ) {
                    //                 $offerQuery->whereBetween('offer_price', [
                    //                     $min_price,
                    //                     $max_price,
                    //                 ]);
                    //             }
                    //         );
                    // });
                }


                $productsQuery->whereHas('categories', function ($categoryQuery) use (
                    $id
                ) {
                    $categoryQuery->where('category_id', $id);
                });

                $products = $productsQuery
                    ->where(function ($query) {
                        $query
                            ->whereHas('inventory')
                            ->orWhereHas('inventoryVariants');
                    })
                    ->paginate($perPage, ['*'], 'page', $page);


            
                    if($request->price){
                        $filter = $products->whereBetween('offer_price', [$min_price, $max_price])->values();
                    }
                    else if($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price' && !$request->price ){
                        $filter = $products;
                    }
             
  
             if ($request->sort_by) {
                 if ($request->sort_by == 'lowest_price') {
                     $filter = $filter->sortBy('offer_price')->values()->all();
                  }
                  else if ($request->sort_by == 'highest_price') {
                     $filter = $filter->sortByDesc('offer_price')->values()->all();
                  }
                   
             }
             

                if($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price'  || $request->price){
                    $filteredPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                        $filter,
                    count($filter),
                        $perPage,
                        $page,
                        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                    );
                }
                else {
                     $filteredPaginator  = $products;
                }
               
                $filteredPaginator->each(function ($product) {
                    $product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $product->featured_image
                    );

                    if (auth('api')->user()) {
                        $product->Wishlist = Wishlist::where([
                            'product_id' => $product->id,
                            'created_by' => auth('api')->user()->id,
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $product->is_wishlist = Wishlist::where([
                            'product_id' => $product->id,
                            'created_by' => auth('api')->user()->id,
                        ])->first()
                            ? true
                            : false;
                    } else {
                        $product->Wishlist = 'No';
                        $product->is_wishlist = false;
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

                    if ($product->inventory) {
                        $product->inventory->each(function ($inventory) {
                            $inventory->image_url = asset(
                                'public/vendor/featured_image/inventory/' .
                                    $inventory->image
                            );
                        });
                    }

                    $product->inventoryVariants->each(function (
                        $inventoryVariant
                    ) {
                        $inventoryVariant->variants->each(function ($variant) {
                            $variant->image_variant_url = asset(
                                'public/vendor/featured_image/inventory_with_variant/' .
                                    $variant->image_variant
                            );
                        });
                    });

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
                });

                $category->products = $products;
                $data = [
                    'category' => $firstCategoryName,
                    'product' => $filteredPaginator,
                ];
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $data],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data find Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'data not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not available for this category',
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


    public function products_by_category_slug(Request $request, $slug)
    {
        try {
            $perPage = 12;
            $page = request()->get('page', 1);

            $cat = Category::where('slug',$slug)->first();
            if($cat){
             $id = $cat->id;
            $category = Category::find($id);
            $firstCategoryName = $category->category_name;

            $category_meta_data =new stdClass();
            $category_meta_data->name = $category->category_name;
            $category_meta_data->meta_title = $category->meta_title;
            $category_meta_data->meta_description = $category->meta_description;
            $category_meta_data->keywords = $category->keywords;
            $category_meta_data->og_tags = $category->ogtag;
            $category_meta_data->schema_markup = $category->schema_markup;
  
            if ($category) {
                $productsQuery = $category
                ->products()
                ->with([
                    'inventory',
                    'inventoryVariants.variants',
                    'reviews',
                ])
                ->whereHas('vendor.shops', function($q){
                    $q->where('status','active');
                })->
                  where('status','active')
                ->withAvg('reviews', 'rating');
            // ->orderByDesc('reviews_avg_rating');
      
            if ($request->sort_by) {
                if ($request->sort_by == 'newest') {
                    $productsQuery->orderBy('id', 'desc');
                } 
                // elseif ($request->sort_by == 'lowest_price') {
                //     $productsQuery
                //         ->withAvg('inventory', 'offer_price')
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
                //                 ->join(
                //                     'products',
                //                     'inventory_with_variants.p_id',
                //                     '=',
                //                     'products.id'
                //                 )
                //                 ->orderBy('offer_price', 'asc')
                //                 ->limit(1);
                //         })
                //         ->orWhereDoesntHave('inventoryVariants.variants') // Only products without variants
                //         ->orderBy('inventory_avg_offer_price', 'asc')
                //     ->orderBy('id', 'asc');
                // } elseif ($request->sort_by == 'highest_price') {
                //     $productsQuery
                //         ->withAvg('inventory', 'offer_price')
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
                //                 ->join(
                //                     'products',
                //                     'inventory_with_variants.p_id',
                //                     '=',
                //                     'products.id'
                //                 )
                //                 ->limit(1)
                //                 ->orderBy('offer_price', 'desc');
                //         }, 'desc')
                //         ->orWhereDoesntHave('inventoryVariants.variants') // Only products without variants
                //         ->orderBy('inventory_avg_offer_price', 'desc')
                //         ->orderBy('id', 'desc');
                // }
            }

            if ($request->has('is_free_shipping')) {
                $isFreeShipping = $request->input('is_free_shipping');
                if ($isFreeShipping == 'true') {
                    $productsQuery->whereHas('user.shipping', function (
                        $query
                    ) {
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
                    $productsQuery
                        ->where('created_at', '>=', $thirtyDaysAgo)
                        ->orderBy('created_at', 'desc');
                }
            }

            if ($request->has('has_offers')) {
                $has_offers = $request->input('has_offers');

                if ($has_offers == 'true') {
                    $productsQuery->where(function ($query) {
                        $query
                            ->whereHas('inventory', function ($offerQuery) {
                                $offerQuery->whereNotNull('offer_price');
                            })
                            ->orWhereHas(
                                'inventoryVariants.variants',
                                function ($offerQuery) {
                                    $offerQuery->whereNotNull(
                                        'offer_price'
                                    );
                                }
                            );
                    });
                }
            }

            if ($request->custom_fields_attribute_values) {
                $custom_array = json_decode(
                    $request->custom_fields_attribute_values,
                    true
                );
                $custom = implode(',', $custom_array);

                if ($custom) {
                    $productsQuery->whereHas(
                        'inventoryVariants.variants',
                        function ($queryBuilder) use ($custom) {
                            $queryBuilder->where(
                                'attr_value_id',
                                'like',
                                '%' . $custom . '%'
                            );

                            // $queryBuilder->where("attr_value_id",$custom);
                        }
                    );
                }
            }

            if ($request->has('price')) {
                $price = json_decode($request->price, true);

                $min_price = $price['min'];
                $max_price = $price['max'];

                // $productsQuery->where(function ($query) use (
                //     $min_price,
                //     $max_price
                // ) {
                //     $query
                //         ->whereHas('inventory', function ($offerQuery) use (
                //             $min_price,
                //             $max_price
                //         ) {
                //             $offerQuery->whereBetween('offer_price', [
                //                 $min_price,
                //                 $max_price,
                //             ]);
                //         })
                //         ->orWhereHas(
                //             'inventoryVariants.variants',
                //             function ($offerQuery) use (
                //                 $min_price,
                //                 $max_price
                //             ) {
                //                 $offerQuery->whereBetween('offer_price', [
                //                     $min_price,
                //                     $max_price,
                //                 ]);
                //             }
                //         );
                // });
            }


            $productsQuery->whereHas('categories', function ($categoryQuery) use (
                $id
            ) {
                $categoryQuery->where('category_id', $id);
            });

            $products = $productsQuery
                ->where(function ($query) {
                    $query
                        ->whereHas('inventory')
                        ->orWhereHas('inventoryVariants');
                })
                ->paginate($perPage, ['*'], 'page', $page);


           
                if($request->price){
                    $filter = $products->whereBetween('offer_price', [$min_price, $max_price])->values();
                }
                else if($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price' && !$request->price ){
                    $filter = $products;
                }
         

         if ($request->sort_by) {
             if ($request->sort_by == 'lowest_price') {
                 $filter = $filter->sortBy('offer_price')->values()->all();
              }
              else if ($request->sort_by == 'highest_price') {
                 $filter = $filter->sortByDesc('offer_price')->values()->all();
              }
              
              
         }
         

            if($request->sort_by == 'lowest_price' || $request->sort_by == 'highest_price'  || $request->price){
                $filteredPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    $filter,
                    count($filter),
                        $perPage,
                        $page,
                    ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                );
            }
            else {
                 $filteredPaginator  = $products;
            }
           
            $filteredPaginator->each(function ($product) use ($request) {
                $product->featured_image_url = asset(
                    'public/vendor/featured_image/' .
                        $product->featured_image
                );

                if (auth('api')->user()) {
                    $product->Wishlist = Wishlist::where([
                        'product_id' => $product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $product->is_wishlist = Wishlist::where([
                        'product_id' => $product->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
            
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

                if ($product->inventory) {
                    $product->inventory->each(function ($inventory) {
                        $inventory->image_url = asset(
                            'public/vendor/featured_image/inventory/' .
                                $inventory->image
                        );
                    });
                }

                $product->inventoryVariants->each(function (
                    $inventoryVariant
                ) {
                    $inventoryVariant->variants->each(function ($variant) {
                        $variant->image_variant_url = asset(
                            'public/vendor/featured_image/inventory_with_variant/' .
                                $variant->image_variant
                        );
                    });
                });

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
            });

            $category->products = $products;
            $data = [
                'meta_data' => $category_meta_data,
                'category' => $firstCategoryName,
                'product' => $filteredPaginator,
            ];
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $data],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data find Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'data not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not available for this category',
                    ],
                    404
                );
            }
        }
        else {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'data not found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Category not Found',
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



    public function getproduct($product_id , Request $request)
    {
        try {

            if (auth('api')->user()) {
                $recently_viewed = RecentItem::where([
                    'product_id' => $product_id,
                    'user_id' => auth('api')->user()->id,
                ])->first();
                if ($recently_viewed) {
                    $recently_viewed->delete();
                }
                RecentItem::create([
                    'product_id' => $product_id,
                    'user_id' => auth('api')->user()->id,
                ]);
            }
            else {
                $recently_viewed = RecentItem::where([
                    'product_id' => $product_id,
                    'guest_user' => $request->header('fcm-token'),
                ])->first();
                if ($recently_viewed) {
                    $recently_viewed->delete();
                }
                RecentItem::create([
                    'product_id' => $product_id,
                    'guest_user' => $request->header('fcm-token'),
                ]);

            }

            $products = Product::with([
                'inventory',
                'inventoryVariants.variants',
                'vendor' => function ($query) {
                    $query->select('id', 'name', 'email')->with('shops');
                },
                'reviews' => function ($query) {
                    $query->select(
                        'id',
                        'product_id',
                        'user_id',
                        'rating',
                        'comment',
                        'created_at'
                    );
                },
            ])
                ->where('products.id', $product_id)
                ->first();

            // frequently bought together
            $category = CategoryProduct::where(
                'product_id',
                $product_id
            )->first();
            $frequently_bought = Product::with([
                'inventory',
                'inventoryVariants.variants',
                'reviews',
            ])
                ->select('*')
                ->whereHas('productcat', function ($categoryQuery) use (
                    $category
                ) {
                    $categoryQuery->where(
                        'category_id',
                        $category->category_id  
                    );
                })
                ->where('products.id', '!=', $product_id)
                ->where('products.created_by', $products->created_by)
                ->get()
                ->take(2);

            if ($frequently_bought) {
                foreach ($frequently_bought as $item) {
                    $item->featured_image_url = asset(
                        'public/vendor/featured_image/' . $item->featured_image
                    );

                    if (auth('api')->user()) {
                        $item->Wishlist = Wishlist::where([
                            'product_id' => $item->id,
                            'created_by' => auth('api')->user()->id,
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $item->is_wishlist = Wishlist::where([
                            'product_id' => $item->id,
                            'created_by' => auth('api')->user()->id,
                        ])->first()
                            ? true
                            : false;
                    } else {
                        if($request->header('fcm-token')){
                            $item->Wishlist =   Wishlist::where([
                                'product_id' => $item->id,
                                'guest_user' => $request->header('fcm-token'),
                            ])->first()
                                ? 'Yes'
                                : 'No';
    
                                $item->is_wishlist  =   Wishlist::where([
                                'product_id' => $item->id,
                                'guest_user' => $request->header('fcm-token'),
                            ])->first()
                                ?  true
                                : false;
                        }
                        else {
                            $item->Wishlist = 'No';
                            $item->is_wishlist = false;
    
                        }
                    }

                    $galleryImages = json_decode($item->gallery_images, true);
                    $galleryImageUrls = collect($galleryImages)->map(function (
                        $image
                    ) {
                        return asset('public/vendor/gallery_images/' . $image);
                    });
                    $item->gallery_image_urls = $galleryImageUrls;

                    $item->total_reviews = $item->reviews->count();
                    $total_rating = $item->reviews->sum('rating');
                    $item->average_reviews =
                        $item->total_reviews > 0
                            ? $total_rating / $item->total_reviews
                            : 0;
                }
            }
            $products->frequently_bought = $frequently_bought ?? null;

            if ($products) {
                $products->featured_image_url = asset(
                    'public/vendor/featured_image/' . $products->featured_image
                );

                if ($products->vendor->shops) {
                    $products->vendor->shops->brand_logo_url = asset(
                        'public/vendor/shop/brand/' .
                            $products->vendor->shops->brand_logo
                    );

                    $products->vendor->shops->is_verified =
                        $products->vendor->shops->email_is_verified == 1
                            ? true
                            : false;
                }


                // Vendor reviews 
                $VendorProducts = Product::with(
                    'reviews'
                )->where('status','active')->where('created_by', $products->vendor->id)->pluck('id')->toArray();
                $reviews = Review::whereIn('product_id',$VendorProducts)->get();
                $products->vendor->total_reviews = count($reviews);
                
                $total_rating = $reviews->sum('rating');
                $products->vendor->average_reviews = number_format($products->vendor->total_reviews > 0
                ? $total_rating / $products->vendor->total_reviews
                : 0, 2,'.','');

            
                if (auth('api')->user()) {
                    $products->Wishlist = Wishlist::where([
                        'product_id' => $products->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $products->is_wishlist = Wishlist::where([
                        'product_id' => $products->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {

                    if($request->header('fcm-token')){
                        $products->Wishlist =   Wishlist::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $products->is_wishlist  =   Wishlist::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $products->Wishlist = 'No';
                        $products->is_wishlist = false;
                    }

                }
                if (auth('api')->user()) {                    
                    $products->cart = CartItem::where([
                        'product_id' => $products->id,
                        'user_id' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $products->is_cart = CartItem::where([
                        'product_id' => $products->id,
                        'user_id' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
                    if($request->header('fcm-token')){
                        $products->cart =   CartItem::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $products->is_cart  =   CartItem::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $products->cart = 'No';
                        $products->is_cart = false;
                    }
                }

                $galleryImages = json_decode($products->gallery_images, true);
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $products->gallery_image_urls = $galleryImageUrls;

                $products->total_reviews = $products->reviews->count();
                $total_rating = $products->reviews->sum('rating');
                $products->average_reviews =
                    $products->total_reviews > 0
                        ? $total_rating / $products->total_reviews
                        : 0;

                foreach ($products->inventoryVariants as $inventoryVariant) {
                    foreach ($inventoryVariant->variants as $variant) {
                        $variant->variant_image = asset(
                            'public/vendor/featured_image/inventory_with_variant/' .
                                $variant->image_variant
                        );
                    }
                }

                //  Attributes & their values
                if (!$products->inventory) {
                    $attributeId = explode(
                        ',',
                        $products->inventoryVariants[0]->variants[0]->attr_id
                    );
                    $products->attributes = ModelsAttribute::with(
                        'attributeValues'
                    )
                        ->whereIn('id', $attributeId)
                        ->get();
                }

                $products->product_details = new stdClass();
                $products->product_details->specification =
                    Specification::where('product_id', $products->id)->first([
                        'id',
                        'product_id',
                        'message',
                    ]) ?? null;
                $products->product_details->reviews = Review::select(
                    'id',
                    'product_id',
                    'user_id',
                    'rating',
                    'comment',
                    'created_at'
                )
                    ->with('user:id,name')
                    ->where('product_id', $products->id)
                    ->get();
                $products->product_details->returnPolicy =
                    ReturnPolicy::where(
                        'category_id',
                        $category->category_id
                    )->where('created_by', $products->vendor->id)->first(['id', 'subject', 'message']) ??    ReturnPolicy::where('category_id',0)->first(['id', 'subject', 'message']);

                foreach ($products->product_details->reviews as $review) {
                    $review->creation_time =
                        Carbon::parse($review->created_at)->diffForHumans(
                            null,
                            true
                        ) . ' ago';
                }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $products],
                    'timestamp' => Carbon::now(),
                    'message' => 'Productes fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,

                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
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

    public function getProductBySlug($slug , Request $request)
    {
        try {

            $product = Product::where('slug',$slug)->first();
            if($product){
                $product_id = $product->id;
            if (auth('api')->user()) {
                $recently_viewed = RecentItem::where([
                    'product_id' => $product_id,
                    'user_id' => auth('api')->user()->id,
                ])->first();
                if ($recently_viewed) {
                    $recently_viewed->delete();
                }
                RecentItem::create([
                    'product_id' => $product_id,
                    'user_id' => auth('api')->user()->id,
                ]);
            }
            else {
                $recently_viewed = RecentItem::where([
                    'product_id' => $product_id,
                    'guest_user' => $request->header('fcm-token'),
                ])->first();
                if ($recently_viewed) {
                    $recently_viewed->delete();
                }
                RecentItem::create([
                    'product_id' => $product_id,
                    'guest_user' => $request->header('fcm-token'),
                ]);

            }

            $products = Product::with([
                'inventory',
                'inventoryVariants.variants',
                'vendor' => function ($query) {
                    $query->select('id', 'name', 'email')->with('shops');
                },
                'reviews' => function ($query) {
                    $query->select(
                        'id',
                        'product_id',
                        'user_id',
                        'rating',
                        'comment',
                        'created_at'
                    );
                },
            ])
                ->where('products.id', $product_id)
                ->first();



            // frequently bought together
            $category = CategoryProduct::where(
                'product_id',
                $product_id
            )->first();
            $frequently_bought = Product::with([
                'inventory',
                'inventoryVariants.variants',
                'reviews',
            ])
                ->select('*')
                ->whereHas('productcat', function ($categoryQuery) use (
                    $category
                ) {
                    $categoryQuery->where(
                        'category_id',
                        $category->category_id
                    );
                })
                ->where('products.id', '!=', $product_id)
                ->where(function ($query) {
                    $query->whereHas('inventory')
                          ->orWhereHas('inventoryVariants');
                })
                ->get()
                ->take(2);

            if ($frequently_bought) {
                foreach ($frequently_bought as $item) {
                    $item->featured_image_url = asset(
                        'public/vendor/featured_image/' . $item->featured_image
                    );

                    if (auth('api')->user()) {
                        $item->Wishlist = Wishlist::where([
                            'product_id' => $item->id,
                            'created_by' => auth('api')->user()->id,
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $item->is_wishlist = Wishlist::where([
                            'product_id' => $item->id,
                            'created_by' => auth('api')->user()->id,
                        ])->first()
                            ? true
                            : false;
                    } else {
                        if($request->header('fcm-token')){
                            $item->Wishlist =   Wishlist::where([
                                'product_id' => $item->id,
                                'guest_user' => $request->header('fcm-token'),
                            ])->first()
                                ? 'Yes'
                                : 'No';
    
                                $item->is_wishlist  =   Wishlist::where([
                                'product_id' => $item->id,
                                'guest_user' => $request->header('fcm-token'),
                            ])->first()
                                ?  true
                                : false;
                        }
                        else {
                            $item->Wishlist = 'No';
                            $item->is_wishlist = false;
    
                        }
                    }

                    $galleryImages = json_decode($item->gallery_images, true);
                    $galleryImageUrls = collect($galleryImages)->map(function (
                        $image
                    ) {
                        return asset('public/vendor/gallery_images/' . $image);
                    });
                    $item->gallery_image_urls = $galleryImageUrls;

                    $item->total_reviews = $item->reviews->count();
                    $total_rating = $item->reviews->sum('rating');
                    $item->average_reviews =
                        $item->total_reviews > 0
                            ? $total_rating / $item->total_reviews
                            : 0;
                }
            }
            $products->frequently_bought = $frequently_bought;

            if ($products) {
                $products->featured_image_url = asset(
                    'public/vendor/featured_image/' . $products->featured_image
                );

                if ($products->vendor->shops) {
                    $products->vendor->shops->brand_logo_url = asset(
                        'public/vendor/shop/brand/' .
                            $products->vendor->shops->brand_logo
                    );

                    $products->vendor->shops->is_verified =
                        $products->vendor->shops->email_is_verified == 1
                            ? true
                            : false;
                }

                    // Vendor reviews 
                    $VendorProducts = Product::with(
                        'reviews'
                    )->where('status','active')->where('created_by', $products->vendor->id)->pluck('id')->toArray();
                    $reviews = Review::whereIn('product_id',$VendorProducts)->get();
                    $products->vendor->total_reviews = count($reviews);
                    
                    $total_rating = $reviews->sum('rating');
                    $products->vendor->average_reviews = number_format($products->vendor->total_reviews > 0
                    ? $total_rating / $products->vendor->total_reviews
                    : 0, 2,'.','');

                if (auth('api')->user()) {
                    $products->Wishlist = Wishlist::where([
                        'product_id' => $products->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $products->is_wishlist = Wishlist::where([
                        'product_id' => $products->id,
                        'created_by' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {

                    if($request->header('fcm-token')){
                        $products->Wishlist =   Wishlist::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $products->is_wishlist  =   Wishlist::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $products->Wishlist = 'No';
                        $products->is_wishlist = false;
                    }

                }
                if (auth('api')->user()) {

                    
                    $products->cart = CartItem::where([
                        'product_id' => $products->id,
                        'user_id' => auth('api')->user()->id,
                    ])->first()
                        ? 'Yes'
                        : 'No';

                    $products->is_cart = CartItem::where([
                        'product_id' => $products->id,
                        'user_id' => auth('api')->user()->id,
                    ])->first()
                        ? true
                        : false;
                } else {
                    if($request->header('fcm-token')){
                        $products->cart =   CartItem::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ? 'Yes'
                            : 'No';

                        $products->is_cart  =   CartItem::where([
                            'product_id' => $products->id,
                            'guest_user' => $request->header('fcm-token'),
                        ])->first()
                            ?  true
                            : false;
                    }
                    else {
                        $products->cart = 'No';
                        $products->is_cart = false;
                    }
                }

                $galleryImages = json_decode($products->gallery_images, true);
                $galleryImageUrls = collect($galleryImages)->map(function (
                    $image
                ) {
                    return asset('public/vendor/gallery_images/' . $image);
                });
                $products->gallery_image_urls = $galleryImageUrls;

                $products->total_reviews = $products->reviews->count();
                $total_rating = $products->reviews->sum('rating');
                $products->average_reviews =
                    $products->total_reviews > 0
                        ? $total_rating / $products->total_reviews
                        : 0;

                foreach ($products->inventoryVariants as $inventoryVariant) {
                    foreach ($inventoryVariant->variants as $variant) {
                        $variant->variant_image = asset(
                            'public/vendor/featured_image/inventory_with_variant/' .
                                $variant->image_variant
                        );
                    }
                }

                //  Attributes & their values
                if (!$products->inventory) {
                    $attributeId = explode(
                        ',',
                        $products->inventoryVariants[0]->variants[0]->attr_id
                    );
                    $products->attributes = ModelsAttribute::with(
                        'attributeValues'
                    )
                        ->whereIn('id', $attributeId)
                        ->get();
                }

                $products->product_details = new stdClass();
                $products->product_details->specification =
                    Specification::where('product_id', $products->id)->first([
                        'id',
                        'product_id',
                        'message',
                    ]) ?? null;
                $products->product_details->reviews = Review::select(
                    'id',
                    'product_id',
                    'user_id',
                    'rating',
                    'comment',
                    'created_at'
                )
                    ->with('user:id,name')
                    ->where('product_id', $products->id)
                    ->get();
                $products->product_details->returnPolicy =
                ReturnPolicy::where(
                    'category_id',
                    $category->category_id
                )->where('created_by', $products->vendor->id)->first(['id', 'subject', 'message']) ??    ReturnPolicy::where('category_id',0)->first(['id', 'subject', 'message']);

                foreach ($products->product_details->reviews as $review) {
                    $review->creation_time =
                        Carbon::parse($review->created_at)->diffForHumans(
                            null,
                            true
                        ) . ' ago';
                }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $products],
                    'timestamp' => Carbon::now(),
                    'message' => 'Productes fetched successfully!',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,

                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    404
                );
            }
        }
        else {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,

                    'context' => [
                        'error' =>
                            'Not found',
                    ],
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

    public function product_filter(Request $request)
    {
        // return $request->all();
        $query = Product::query();
        //    return $query;
        if ($request->has('product_id')) {
            $query->whereHas('categories', function ($categoryQuery) use (
                $request
            ) {
                $categoryQuery->where(
                    'product_id',
                    $request->input('product_id')
                );
            });
        }

        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($categoryQuery) use (
                $request
            ) {
                $categoryQuery->where(
                    'category_id',
                    $request->input('category_id')
                );
            });
        }

        if ($request->has('attribute_id')) {
            $query->whereHas('categories', function ($categoryQuery) use (
                $request
            ) {
                $categoryQuery->whereHas('attributes', function (
                    $attributeQuery
                ) use ($request) {
                    $attributeQuery->where(
                        'attribute_id',
                        $request->input('attribute_id')
                    );
                });
            });
        }

        if (
            $request->has('attribute_value') &&
            $request->has('attribute_id') &&
            $request->has('category_id')
        ) {
            $query->whereHas('categories', function ($categoryQuery) use (
                $request
            ) {
                $categoryQuery
                    ->where('category_id', $request->input('category_id'))
                    ->whereHas('attributes', function ($attributeQuery) use (
                        $request
                    ) {
                        $attributeQuery
                            ->where(
                                'attribute_id',
                                $request->input('attribute_id')
                            )
                            ->where(
                                'attribute_value',
                                $request->input('attribute_value')
                            );
                    });
            });
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'Data Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Data not exists!',
                ],
                404
            );
        } else {
            return response()->json(
                [
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $products],
                    'timestamp' => Carbon::now(),
                    'message' => 'Shipping Rate fetched successfully!',
                ],
                200
            );
        }
    }

    public function slider_management(Request $request)
    {
        try {
            $offset = $request->query('offset', 0);
            $limit = $request->query('limit', 10);

            $validatedData = $request->validate([
                'offset' => 'integer|min:0',
                'limit' => 'integer|min:1|max:100',
            ]);

            $offset = $validatedData['offset'] ?? $offset;
            $limit = $validatedData['limit'] ?? $limit;

            $slider = SliderManagement::offset($offset)
                ->where('has_category_slider', 0)
                ->limit($limit)
                ->get();
            $slider->each(function ($item) {
                $item->slider_image_url = asset(
                    'public/admin/site/sliderManagement/slider/' .
                        $item->slider_image
                );
                $item->mobile_image_url = asset(
                    'public/admin/site/sliderManagement/mobile/' .
                        $item->mobile_image
                );
            });
            if ($slider) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $slider,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Slider fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not exits'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not exits',
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

    public function filter_using_cat_id($cat_id)
    {
        try {
            $data = DB::table('categories')
                ->join(
                    'attribute_category',
                    'attribute_category.category_id',
                    '=',
                    'categories.id'
                )
                ->join(
                    'attributes',
                    'attributes.id',
                    '=',
                    'attribute_category.attribute_id'
                )
                ->join(
                    'attribute_values',
                    'attribute_values.attribute_id',
                    '=',
                    'attributes.id'
                )
                ->select(
                    'categories.id as cat_id',
                    'categories.*',
                    'attributes.id as attribute_id',
                    'attributes.attribute_name as attribute_name',
                    'attribute_values.id as attribute_value_id',
                    'attribute_values.attribute_value'
                )
                ->where('categories.id', '=', $cat_id)
                ->get();
            if ($data->count() > 0) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => [$data]],
                    'timestamp' => Carbon::now(),
                    'message' => 'Categories Fetched Successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not exits'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data Not Found',
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

    // get states on the basis of country id 
    public function country_state_id($id)
    {
        try {
            $country_id = $id;
            $data = Country::with('states')
                ->where('countries.id', $country_id)
                ->get();

            if ($data->isEmpty()) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,

                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    404
                );
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $data],
                    'timestamp' => Carbon::now(),
                    'message' => 'Country Get Successfully',
                ]);
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


    // get all countries with states
    public function country_state()
    {
        try {
            $data = Country::with('states')->get();
            if ($data->isEmpty()) {
                return response()->json(
                    [
                        'http_status_code' => 500,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Some error occurred! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'An unexpected error occurred',
                    ],
                    500
                );
            } else {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $data],
                    'timestamp' => Carbon::now(),
                    'message' =>
                        'All Countries with State fetched successfully!',
                ]);
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

    public function attributes_by_category($id)
    {
        try {
            $product = Category::with([
                'attributes',
                'attributes.attributeValues',
            ])
                ->where('categories.id', $id)
                ->orWhere('categories.slug',$id)
                ->get();

            if (!$product) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Category not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Category not found',
                    ],
                    404
                );
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' =>
                    'Category with Attribute and Attribute Values are fetched',
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


    public function attributes_by_category_slug($slug){
        try {
            $product = Category::with([
                'attributes',
                'attributes.attributeValues',
            ])
                ->where('categories.slug', $slug)
                ->get();

            if (!$product) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Category not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Category not found',
                    ],
                    404
                );
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' =>
                    'Category with Attribute and Attribute Values are fetched',
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


    public function attributes_by_categories(Request $request)
    {
        try {
            $product = Category::with([
                'attributes' => function($q){
                      $q->where('created_by',  auth('api')->user()->id);
                },
                'attributes.attributeValues',
            ])
                ->whereIn('categories.id', $request->category_id)
                ->whereHas('attributes', function($query){
                     $query->where('created_by',  auth('api')->user()->id);
                })
                ->get();

            if (!$product) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Category not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Category not found',
                    ],
                    404
                );
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $product],
                'timestamp' => Carbon::now(),
                'message' =>
                    'Category with Attribute and Attribute Values are fetched',
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


    public function category_slider(Request $request, $id)
    {
        try {
            $offset = $request->query('offset', 0);
            $limit = $request->query('limit', 10);

            $validatedData = $request->validate([
                'offset' => 'integer|min:0',
                'limit' => 'integer|min:1|max:100',
            ]);

            $offset = $validatedData['offset'] ?? $offset;
            $limit = $validatedData['limit'] ?? $limit;

            $category = Category::where('slug',$id)->first()->id ?? null; 
            $slider = SliderManagement::offset($offset)
                ->where('has_category_slider', 1)
                ->where('category_id', $category)
                ->limit($limit)
                ->get();
            $slider->each(function ($item) {
                $item->slider_image_url = asset(
                    'public/admin/site/sliderManagement/slider/' .
                        $item->slider_image
                );
                $item->mobile_image_url = asset(
                    'public/admin/site/sliderManagement/mobile/' .
                        $item->mobile_image
                );
            });
            if ($slider) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => [
                        'data' => $slider,
                    ],
                    'timestamp' => Carbon::now(),
                    'message' => 'Slider fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Data not exits'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Data not exits',
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

    public function attributes_by_product(Request $request)
    {
        try {
            $productCategory = CategoryProduct::where(
                'product_id',
                $request->product_id
            )->first();

            $product = Category::with([
                'attributes',
                'attributes.attributeValues',
            ])
                ->where('categories.id', $productCategory->category_id)
                ->get();

            if (!$product) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Category not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Category not found',
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
                    'Category with Attribute and Attribute Values are fetched',
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

    public function get_country_code()
    {
        try {
            $areas = BusinessArea::where('status', 1)->get([
                'id',
                'full_name',
                'iso_code',
                'flag',
                'calling_code',
                'Currency_fk_id',
                'country_id',
            ]);
            foreach ($areas as $area) {
                $area->country =
                    Country::where('id', $area->country_id)->first()
                        ->country_name ?? null;
                $area->currency =
                    Currency::where('id', $area->Currency_fk_id)->first()
                        ->currency_name ?? null;
                $area->flag = asset(
                    'public/admin/setting/business/flag/' . $area->flag
                );
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $areas],
                'timestamp' => Carbon::now(),
                'message' => 'Data Fetched Successfully',
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

    public function guaranteeCharge()
    {
        try {
            $charge = Charges::first();
            if ($charge) {
                $data = Charges::first(['id', 'amount']);
            } else {
                $data = [];
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $data],
                'timestamp' => Carbon::now(),
                'message' => 'Charge Fetched Successfully',
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

    public function about_us()
    {
        try {
            $page = Pages::where('type', 'About Us')
                ->where('status', 'active')
                ->first([
                    'id',
                    'title',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'content',
                    'banner_image',
                    'ogtag',
                    'schema_markup',
                    'keywords'
                ]);
            $page->banner_image = asset(
                'public/admin/appereance/pages/banner_image/' .
                    $page->banner_image
            );
            if (!$page) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Page Not Found',
                    ],
                    500
                );
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $page],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    public function termCondition()
    {
        try {
            $page = Pages::where('type', 'Terms & Conditions For Customers')
                ->where('status', 'active')
                ->first([
                    'id',
                    'title',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'content',
                    'banner_image',
                    'ogtag',
                    'schema_markup',
                    'keywords'
                ]);
            if (!$page) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Page Not Found',
                    ],
                    500
                );
            }

            $page->banner_image = asset(
                'public/admin/appereance/pages/banner_image/' .
                    $page->banner_image
            );

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $page],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    public function shippingPolicy()
    {
        try {
            $page = Pages::where('type', 'Shipping Policy')
                ->where('status', 'active')
                ->first([
                    'id',
                    'title',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'content',
                    'banner_image',
                    'ogtag',
                    'schema_markup',
                    'keywords'
                ]);
            if (!$page) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Page Not Found',
                    ],
                    500
                );
            }

            $page->banner_image = asset(
                'public/admin/appereance/pages/banner_image/' .
                    $page->banner_image
            );

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $page],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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
    public function privacyPolicy()
    {
        try {
            $page = Pages::where('type', 'Privacy Policy')
                ->where('status', 'active')
                ->first([
                    'id',
                    'title',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'content',
                    'banner_image',
                    'ogtag',
                    'schema_markup',
                    'keywords'
                ]);
            if (!$page) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Page Not Found',
                    ],
                    500
                );
            }

            $page->banner_image = asset(
                'public/admin/appereance/pages/banner_image/' .
                    $page->banner_image
            );

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $page],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    public function footerPages($type){
          try{
            //  $value = null;
            // if($type == 'about-us' ){
            //      $value = 'About Us';
            // }
            // else if($type == 'terms-of-use'){
            //     $value = 'Terms & Conditions For Customers';
            // }
            // elseif ($type == 'shipping-policy'){
            //    $value='Shipping Policy';
            // }
            // elseif ($type == 'privacy-policy'){
            //      $value = 'Privacy Policy';
            // }
            // elseif ($type == 'return-&-cancellation'){
            //        $value= 'Return and Refund Policy';
            // }

            $page = Pages::where('slug', $type)
            ->where('status', 'active')
            ->first([
                'id',
                'title',
                'slug',
                'meta_title',
                'meta_description',
                'content',
                'banner_image',
                'ogtag',
                'schema_markup',
                'keywords'
            ]);
        if (!$page) {
            return response()->json(
                [
                    'http_status_code' => 404,
                    'status' => false,
                    'context' => ['error' => 'Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Page Not Found',
                ],
                404
            );
        }

        $page->banner_image = asset(
            'public/admin/appereance/pages/banner_image/' .
                $page->banner_image
        );

        return response()->json([
            'http_status_code' => 200,
            'status' => true,
            'context' => ['data' => $page],
            'timestamp' => Carbon::now(),
            'message' => 'Page Fetched Successfully',
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

    public function returnCancellation()
    {
        try {
            $page = Pages::where('type', 'Return and Refund Policy')
                ->where('status', 'active')
                ->first([
                    'id',
                    'title',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'content',
                    'banner_image',
                    'ogtag',
                    'schema_markup',
                    'keywords'
                ]);
            if (!$page) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Page Not Found',
                    ],
                    500
                );
            }

            $page->banner_image = asset(
                'public/admin/appereance/pages/banner_image/' .
                    $page->banner_image
            );

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $page],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    public function faqs()
    {
        try {
            $faq = FaqTopic::where('status', 'active')
                ->with([
                    'faqAnswer' => function ($query) {
                        $query->select(
                            'id',
                            'question',
                            'answer',
                            'faq_topics_id',
                            'meta_title',
                            'meta_description',
                            'keywords',
                            'ogtag',
                            'schema_markup'
                        );
                    },
                ])
                ->get(['id', 'topic_name', 'faq_for']);
            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $faq],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    public function getGlobalSetting(Request $request){
        try {

            $global = GlobalSetting::get()->map(function($item) use ($request){
                 $item->social_media = SocialMedia::where('global_id',$item->id)->get();
                 $item->help = Pages::where('type','Shipping Policy')->orWhere('type','Return and Refund Policy')->get(['id','title','slug']);
                 $item->consumer_policy = Pages:: where('type','Terms & Conditions For Customers')->orWhere('type','Privacy Policy')->get(['id','title','slug']);
                 $item->about = Pages:: where('type','About Us')->get(['id','title','slug']);
                 
                 $item->logo = asset('public/admin/global/'. $item->logo);
                 $item->qr_code = asset('public/admin/global/'. $item->qr_code);
                 $item->account_status = $request->header('customer_id') ?  User::where('id',$request->header('customer_id'))->first()->customer_status  == 1 ?  true : false : true;
                 return $item;
            });

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $global],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    public function getContactUsData(){
        try {
            $global = ContactPage::where('type','contact')->get()->map(function($item){
                 $item->banner_image = asset('public/admin/contact/'. $item->banner_image);
                 return $item;
            });

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $global],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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

    // get faq content 
    public  function getFaqsData(){
        try {
            $global = ContactPage::where('type','faq')->get()->map(function($item){
                 $item->banner_image = asset('public/admin/contact/'. $item->banner_image);
                 return $item;
            });

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $global],
                'timestamp' => Carbon::now(),
                'message' => 'Page Fetched Successfully',
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


    public function getBusinessArea(){
        try{
            $area = BusinessArea::where('status',1)->get(['name','iso_code','flag','calling_code'])->map(function($items){
                       $items->flag = asset('public/admin/setting/business/flag/'. $items->flag);

                       return $items;
            });

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $area],
                'timestamp' => Carbon::now(),
                'message' => 'Area Fetched Successfully',
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

    public function metaData($type){
           try {      
              $seo = SeoPage::where('type',$type)->first();  
              if($seo){
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $seo],
                    'timestamp' => Carbon::now(),
                    'message' => 'data Fetched Successfully',
                ]);
              } 
              else {
                return response()->json([
                    'http_status_code' => 404,
                    'status' => true,
                    'context' => ['error' => "data not found"],
                    'timestamp' => Carbon::now(),
                    'message' => 'Empty Data',
                ], 404);
              }
         
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
