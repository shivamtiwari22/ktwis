<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingRate;
use App\Models\Shop;
use App\Models\User;
use App\Models\Variant;
use App\Models\Wishlist;
use Carbon\Carbon;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistApiController extends Controller
{
    public function add_to_Wishlist(Request $request)
    {
        try {
            $id = auth('api')->user();
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
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

            if($id){
                $wishExists = Wishlist::where('created_by', $id->id)
                ->where('product_id', $request->product_id)
                ->exists();
            }
            else {
                $wishExists = Wishlist::where('guest_user', $request->header('fcm_token'))
                ->where('product_id', $request->product_id)
                ->exists();
            }
         

            if ($wishExists) {
                return response()->json(
                    [
                        'http_status_code' => 409,
                        'status' => false,
                        'context' => [
                            'error' => 'Wishlist Item Already Exists',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Wishlist Item Already Exists',
                    ],
                    409
                );
            }
            $products = Product::where('id', $request->product_id)->first();
            if ($products) {
                // if ($products->has_variant) {
                //     if (!$request->input('variant_id')) {
                //         return response()->json(
                //             [
                //                 'http_status_code' => 422,
                //                 'status' => false,
                //                 'context' => [
                //                     'error' => 'variant_id is required',
                //                 ],
                //                 'timestamp' => Carbon::now(),
                //                 'message' => 'Validation failed',
                //             ],
                //             422
                //         );
                //     }
                // }
                $wishlistItem = new Wishlist();
                $wishlistItem->product_id = $request->product_id;
                $wishlistItem->guest_user = $id ? null : $request->header('fcm_token');
                $wishlistItem->status = 'active';
                $wishlistItem->created_by = $id ? $id->id : null;
                $wishlistItem->updated_by = $id ? $id->id : null;
                if ($products->has_variant == '1') {
                    $wishlistItem->variant_id = $request->input('variant_id');
                }
                $wishlistItem->save();
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $wishlistItem],
                    'timestamp' => Carbon::now(),
                    'message' => 'Item added to wishlist successfully',
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

    public function remove_wishlist($id , Request $request)
    {
        try {
            $product = Product::where('id', $id)->first();
            if ($product) {
                 if(auth('api')->user()){
                    $wishlist = Wishlist::where('product_id', $id)
                    ->where('created_by', auth('api')->user()->id)
                    ->first();
                 }
                 else {
                    $wishlist = Wishlist::where('product_id', $id)
                    ->where('guest_user', $request->header('fcm_token') )
                    ->first();

                 }

                if ($wishlist) {
                    $delete = Wishlist::where('id', $wishlist->id)->delete();
                } else {
                    $delete = false;
                }
            } else {
                return response()->json([
                    'http_status_code' => 404,
                    'status' => true,
                    'context' => ['error' => 'Product Not Found'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Product Not Found',
                ]);
            }

            if ($delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Wishlist removed successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Not Found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Wishlist item not found',
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

    public function remove_all_wishlist(Request $request)
    {
        try {
            if(auth('api')->user()){
                $delete = Wishlist::where('created_by', auth('api')->user()->id)->delete();
            }
            else {
                $delete = Wishlist::where('guest_user', $request->header('fcm_token'))->delete();
            }
            if ($delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Wishlist removed successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Something Went Wrong'],
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

    public function get_wishlist(Request $request)
    {
        try {
            $user_id = auth('api')->user();
            if($user_id){
                $wishlistItems = Wishlist::where('created_by', $user_id->id)->orderBy('id','DESC')->get([
                    'id',
                    'product_id',
                    'variant_id',
                    'status',
                    'created_by',
                    'guest_user'
                ]);
            }
            else {
                $wishlistItems = Wishlist::where('guest_user', $request->header('fcm_token') )->orderBy('id','DESC')->get([
                    'id',
                    'product_id',
                    'variant_id',
                    'status',
                    'created_by',
                    'guest_user'
                ]);
            }
       
         
            if (!$wishlistItems->isEmpty()) {
                foreach ($wishlistItems as $item) {
                    $item->product = Product::where(
                        'id',
                        $item->product_id
                    )->first([
                        'id',
                        'name',
                        'description',
                        'slug',
                        'featured_image',
                        'has_variant',
                        'brand',
                        'model_number',
                        'weight',
                        'created_by',
                    ]);
                    $item->product->is_variant = $item->product->has_variant
                        ? true
                        : false;
                    $item->product->featured_image_url = asset(
                        'public/vendor/featured_image/' .
                            $item->product->featured_image
                    );
                    // $galleryImages = json_decode($item->product->gallery_images, true);
                    // $galleryImageUrls = collect($galleryImages)->map(function ($image) {
                    //     return asset('public/vendor/gallery_images/' . $image);
                    // });
                    // $item->product->gallery_image_urls = $galleryImageUrls;
                    if ($item->variant_id) {
                        $item->product->variant = Variant::where(
                            'id',
                            $item->variant_id
                        )->first(['id', 'price', 'offer_price']);
                    } else {
                        $inventory_without_variants = InventoryWithoutVariant::where(
                            'p_id',
                            $item->product->id
                        )->first(['id', 'price', 'offer_price']);
                        if ($inventory_without_variants) {
                            $item->product->inventory = $inventory_without_variants;
                        }
                    }
                    $item->vendor = User::where(
                        'id',
                        $item->product->created_by
                    )->first(['name', 'email', 'details']);
                    $item->product_rating = Review::where(
                        'product_id',
                        $item->product_id
                    )->get(['rating', 'comment']);
                    $item->total_reviews = $item->product_rating->count();
                    $total_rating = $item->product_rating->sum('rating');
                    $item->average_reviews =
                        $item->total_reviews > 0
                            ? $total_rating / $item->total_reviews
                            : 0;
                    // $item->Shop =  Shop::where(
                    //     'vendor_id',
                    //     $item->product->created_by
                    // )
                    //     ->where('status', 'active')
                    //     ->first([
                    //         'shop_name',
                    //         'legal_name',
                    //         'email',
                    //         'timezone',
                    //         'description',
                    //         'shop_url',
                    //     ]) ?? null ;

                    $item->shipping_details =
                        ShippingRate::where(
                            'created_by',
                            $item->product->created_by
                        )
                            ->where(
                                'minimum_order_weight',
                                '<=',
                                $item->product->weight
                            )
                            ->where(
                                'max_order_weight',
                                '>=',
                                $item->product->weight
                            )
                            ->where('status', 'active')
                            ->first(['delivery_time', 'rate', 'name']) ?? null;
                }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $wishlistItems],
                    'timestamp' => Carbon::now(),
                    'message' => 'Wishlist Data fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Wishlist data not found',
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
}
