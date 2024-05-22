<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\RecentItem;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentItemApiController extends Controller
{
    public function item_post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'user_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'http_status_code' => 422,
                        'status' => false,
                        'context' => ['error' => $validator->errors()->first()],
                        'timestamp' => Carbon::now(),
                        'message' => 'Validation failed',
                    ],
                    422
                );
            }

            $recent = RecentItem::create($request->all());

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $recent],
                'timestamp' => Carbon::now(),
                'message' => 'Recent Item Store Successfully',
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

    public function getRecentItems(Request $request)
    {
        try {
            $user = auth('api')->user() ?? null;
            if (!$user) {
                // $recent = [];

                $recent_item = RecentItem::with([
                    'product' => function ($query) {
                        // Include soft-deleted products and their variants
                        $query->withTrashed();
                    },
                    'product.inventory',
                    'product.inventoryVariants.variants',
                    'product.reviews',
                ])
                    ->whereHas('product.vendor.shops', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->whereHas('product', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where('guest_user', $request->header('fcm-token'))
                    ->where(function ($query) {
                        $query
                            ->whereHas('product.inventory')
                            ->orWhereHas('product.inventoryVariants');
                    })
                    ->orderBy('id', 'DESC')
                    ->get();


            } else {
                $recent_item = RecentItem::with([
                    'product' => function ($query) {
                        // Include soft-deleted products and their variants
                        $query->withTrashed();
                    },
                    'product.inventory',
                    'product.inventoryVariants.variants',
                    'product.reviews',
                ])
                    ->whereHas('product.vendor.shops', function ($query) {
                        $query->where('status', 'active');
                    })
                    ->whereHas('product', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where('user_id', auth('api')->user()->id)
                    ->where(function ($query) {
                        $query
                            ->whereHas('product.inventory')
                            ->orWhereHas('product.inventoryVariants');
                    })
                    ->orderBy('id', 'DESC')
                    ->get();
            }

            $recent = $recent_item->unique('product_id')->values();

            $recent->each(function ($product) use ($request) {
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
                        (($product->product->inventoryVariants[0]
                            ->variants[0]->price -
                            $product->product->inventoryVariants[0]
                                ->variants[0]->offer_price) /
                            $product->product->inventoryVariants[0]
                                ->variants[0]->price) *
                        100;
                }
                $product->product->discount_percentage = round(
                    $discount_percentage,
                    2
                );
            });


            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $recent],
                'timestamp' => Carbon::now(),
                'message' => 'Recent Item Fetched Successfully',
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

    public function getRecentItemsbyCategory($id)
    {
        try {
            $user = auth('api')->user() ?? null;
            if (!$user) {
                $recent = [];
            } else {
                $category_id = $id;
                $recent = RecentItem::with([
                    'product',
                    'product.inventory',
                    'product.inventoryVariants.variants',
                    'product.reviews',
                ])
                    ->where('user_id', auth('api')->user()->id)
                    ->whereHas('product.productcat', function (
                        $categoryQuery
                    ) use ($category_id) {
                        $categoryQuery->where('category_id', $category_id);
                    })
                    ->take(10)
                    ->orderBy('id', 'DESC')
                    ->get();

                $recent->each(function ($product) {
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
                            (($product->product->inventoryVariants[0]
                                ->variants[0]->price -
                                $product->product->inventoryVariants[0]
                                    ->variants[0]->offer_price) /
                                $product->product->inventoryVariants[0]
                                    ->variants[0]->price) *
                            100;
                    }
                    $product->product->discount_percentage = round(
                        $discount_percentage,
                        2
                    );
                });
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $recent],
                'timestamp' => Carbon::now(),
                'message' => 'Recent Item Fetched Successfully',
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
}
