<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\cartSummary;
use App\Models\Country;
use App\Models\currencyBalance;
use App\Models\InventoryWithoutVariant;
use App\Models\InventoryWithVariant;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\ShippingCountry;
use App\Models\ShippingRate;
use App\Models\ShippingState;
use App\Models\ShippingZone;
use App\Models\Shop;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\userWallet;
use App\Models\Variant;
use App\Models\VendorCoupon;
use App\Models\Wishlist;
use Carbon\Carbon;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class CartsApiController extends Controller
{
    public function add_to_cart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
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

            $user = auth('api')->user() ?   auth('api')->user()->id :  $request->user_id ;

            $product = Product::where(
                'id',
                $request->input('product_id')
            )->first();

            if ($user) {
                $cartExists = CartItem::where('user_id', $user)
                    ->where('product_id', $request->product_id)
                    ->exists();
            } else {
                $cartExists = CartItem::Where('guest_user', $request->fcm_token)
                    ->where('product_id', $request->product_id)
                    ->exists();
            }

            if ($cartExists) {
                return response()->json(
                    [
                        'http_status_code' => 409,
                        'status' => false,
                        'context' => ['error' => 'Item already added to cart'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Item already added to cart',
                    ],
                    409
                );
            }

            if ($product) {
                $has_variant = $product->has_variant;
                if ($product->has_variant) {
                    if (!$request->input('variant_id')) {
                        return response()->json(
                            [
                                'http_status_code' => 422,
                                'status' => false,
                                'context' => [
                                    'error' => 'variant_id is required',
                                ],
                                'timestamp' => Carbon::now(),
                                'message' => 'Validation failed',
                            ],
                            422
                        );
                    }
                    $variant = Variant::where(
                        'id',
                        $request->input('variant_id')
                    )->first();

                    $variant_stock = $variant ? $variant->stock_quantity : null ;
                } else {
                    $inventory_without_variants = InventoryWithoutVariant::where(
                        'p_id',
                        $product->id
                    )->first();
                    if ($inventory_without_variants) {
                        $variant = $inventory_without_variants;
                        $variant_stock = $variant->stock_qty;
                    }

                    
                }

                if($variant_stock < $request->quantity){
                    return response()->json(
                        [
                            'http_status_code' => 422,
                            'status' => false,
                            'context' => [
                                'error' => 'Product is out of stock',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'Product is out of stock',
                        ],
                        422
                    );
                }

                $avlCart = Cart::where(function ($query) use ($user, $request) {
                    if ($user) {
                        $query->where('user_id', $user);
                    } else {
                        $query->Where('guest_user', $request->fcm_token);
                    }
                })
                    ->where('seller_id', $product->created_by)
                    ->first();

                if ($avlCart) {
                    $cart = $avlCart;
                } else {
                    $cart = new Cart();
                    $cart->user_id = $user ? $user : null;
                    $cart->guest_user = auth('api')->user() ? null : $request->fcm_token;
                    $cart->seller_id = $product->created_by;
                    $cart->sub_total = $variant->price * $request->quantity;
                    $cart->item_count = 1;
                    $cart->discount_amount =
                        $variant->price * $request->quantity -
                        $variant->offer_price * $request->quantity;
                    $cart->total_amount =
                        $variant->offer_price * $request->quantity;
                    $cart->save();
                }

                $cartItem = new CartItem();
                $cartItem->cart_id = $cart->id;
                $cartItem->product_id = $request->product_id;
                if ($product->has_variant) {
                    $cartItem->variant_id = $request->variant_id;
                }
                $cartItem->quantity = $request->quantity;
                $cartItem->name = $product->name;
                $cartItem->weight = $product->weight;
                $cartItem->total_weight = $request->quantity * $product->weight;
                $cartItem->price = $variant->price;
                $cartItem->offer_price =
                    $variant->price * $request->quantity -
                    $variant->offer_price * $request->quantity;
                $cartItem->purchase_price =
                    $variant->price * $request->quantity;
                $cartItem->base_total =
                    $variant->offer_price * $request->quantity;
                $cartItem->user_id = $user ? $user : null;
                $cartItem->guest_user = auth('api')->user() ? null : $request->fcm_token;
                $cartItem->save();

                if ($avlCart) {
                    $vendorCartItems = CartItem::where(
                        'cart_id',
                        $cart->id
                    )->get();
                    $avlCart->item_count = $vendorCartItems->count();
                    $avlCart->sub_total = $vendorCartItems->sum(
                        'purchase_price'
                    );
                    $avlCart->discount_amount = $vendorCartItems->sum(
                        'offer_price'
                    );
                    $avlCart->total_amount = $vendorCartItems->sum(
                        'base_total'
                    );
                    $avlCart->save();
                }

                if ($user) {
                    $wishlist = Wishlist::where(
                        'product_id',
                        $request->product_id
                    )
                        ->where('created_by', $user)
                        ->first();
                    if ($wishlist) {
                        Wishlist::destroy($wishlist->id);
                    }
                }
                else {

                    $wishlist = Wishlist::where(
                        'product_id',
                        $request->product_id
                    )
                        ->where('guest_user', $request->fcm_token)
                        ->first();
                    if ($wishlist) {
                        Wishlist::destroy($wishlist->id);
                    }
                }
                if ($cartItem) {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Item added to cart successfully',
                    ]);
                } else {
                    return response()->json(
                        [
                            'http_status_code' => 500,
                            'status' => false,
                            'context' => [
                                'error' => 'Item cannot be added to cart',
                            ],
                            'timestamp' => Carbon::now(),
                            'message' => 'Something Went Wrong',
                        ],
                        500
                    );
                }
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

    public function remove_cart($id)
    {
        try {
            // $user = Auth::user()->id;
            $delete = CartItem::where('id', $id)->first();

            if (!$delete) {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => [
                            'error' =>
                                'Cart Item Not Found! , Please try again',
                        ],
                        'timestamp' => Carbon::now(),
                        'message' => 'Item Not Found',
                    ],
                    404
                );
            }

            $cart = Cart::find($delete->cart_id);
            if ($cart) {
                $cart->sub_total = $cart->sub_total - $delete->purchase_price;
                $cart->item_count = $cart->item_count - 1;
                $cart->discount_amount =
                    $cart->discount_amount - $delete->offer_price;
                $cart->total_amount = $cart->total_amount - $delete->base_total;
                $cart->save();
            }
            $delete->delete();

            $cartItem = CartItem::where('cart_id', $cart->id)->get();
            if (count($cartItem) == 0) {
                $cart->delete();
                $cart->forceDelete();
            }

            if ($delete) {
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => []],
                    'timestamp' => Carbon::now(),
                    'message' => 'Cart Item deleted successfully',
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

    public function get_cart(Request $request)
    {
        try {
            $user = auth('api')->user();
            $user_id = $user ? $user->id : null;

            if ($user_id) {
                $wishlistItems = Cart::where('user_id', $user_id)
                    ->orderBy('id', 'DESC')
                    ->get();
            } else {
                $wishlistItems = Cart::where(
                    'guest_user',
                    $request->header('fcm-token')
                )
                    ->orderBy('id', 'DESC')
                    ->get();
            }

            if (!$wishlistItems->isEmpty()) {
                foreach ($wishlistItems as $wishlist) {
                    $wishlist->vendor = User::where(
                        'id',
                        $wishlist->seller_id
                    )->first(['name', 'email', 'details']);

                    $wishlist->shop =
                        Shop::where('vendor_id', $wishlist->seller_id)
                            ->where('status', 'active')
                            ->first([
                                'shop_name',
                                'legal_name',
                                'email',
                                'timezone',
                                'description',
                                'shop_url',
                            ]) ?? null;

                    $productWeight = 0;
                    $wishlist->cart_item = CartItem::with('product')
                        ->where('cart_id', $wishlist->id)
                        ->orderBy('id', 'DESC')
                        ->get();

                    $wishlist->is_guest = $user_id ? false : true;
                    $wishlist->is_guest_register = $request->header('user_id') ? true : false ;

                    // map(function($item) use ($productWeight){
                    //     $productWeight += $item->total_weight;
                    //     $item->product->featured_image_url = asset(
                    //         'public/vendor/featured_image/' .
                    //             $item->product->featured_image
                    //     );

                    //     if ($item->variant_id) {
                    //         $item->product->variant = Variant::where(
                    //             'id',
                    //             $item->variant_id
                    //         )->first();
                    //     } else {
                    //         $inventory_without_variants = InventoryWithoutVariant::where(
                    //             'p_id',
                    //             $item->product->id
                    //         )->first();
                    //         if ($inventory_without_variants) {
                    //             $item->product->product_detail = $inventory_without_variants;
                    //         }
                    //     }

                    //     $item->product->shipping_detail =
                    //     UserAddress::where('user_id', $item->user_id)
                    //         ->where('address_type', 'shipping')
                    //         ->where('is_current',1)
                    //         ->first([
                    //             'id',
                    //             'address_type',
                    //             'contact_person',
                    //             'contact_no',
                    //             'floor_apartment',
                    //             'address',
                    //             'state',
                    //             'country',
                    //             'zip_code',
                    //             'is_current'
                    //         ]) ?? null;

                    //         return $item ;
                    // });

                    foreach ($wishlist->cart_item as $item) {
                        $productWeight += $item->total_weight;
                        $item->product->featured_image_url = asset(
                            'public/vendor/featured_image/' .
                                $item->product->featured_image
                        );

                        if ($item->variant_id) {
                            $item->product->variant = Variant::where(
                                'id',
                                $item->variant_id
                            )->first();
                        } else {
                            $inventory_without_variants = InventoryWithoutVariant::where(
                                'p_id',
                                $item->product->id
                            )->first();
                            if ($inventory_without_variants) {
                                $item->product->product_detail = $inventory_without_variants;
                            }
                        }

                        $item->product->shipping_detail =
                            UserAddress::where('user_id', $item->user_id)
                                ->where('address_type', 'shipping')
                                ->where('is_current', 1)
                                ->first([
                                    'id',
                                    'address_type',
                                    'contact_person',
                                    'contact_no',
                                    'floor_apartment',
                                    'address',
                                    'state',
                                    'country',
                                    'zip_code',
                                    'is_current',
                                ]) ?? null;
                    }

                    // $wishlist->product_weight =  $wishlist->cartItem->sum('total_weight');
                    $location =
                        UserAddress::where('user_id', $wishlist->user_id)
                            ->where('address_type', 'shipping')
                            ->where('is_current', 1)
                            ->first([
                                'id',
                                'address_type',
                                'contact_person',
                                'contact_no',
                                'floor_apartment',
                                'address',
                                'state',
                                'country',
                                'zip_code',
                                'is_current',
                                'is_default',
                            ]) ?? null;

                    if ($location) {
                        $countryId =
                            Country::where(
                                'country_name',
                                $location->country
                            )->first()->id ?? $location->country;

                        $stateId =
                            State::where(
                                'state_name',
                                $location->state
                            )->first()->id ?? $location->state;

                        $shippingCountry = ShippingCountry::where(
                            'country_id',
                            $countryId
                        )
                            ->where('created_by', $wishlist->seller_id)
                            ->first();

                        if ($shippingCountry) {
                            $shippingState = ShippingState::where(
                                'state_id',
                                $stateId
                            )
                                ->where('s_country_id', $shippingCountry->id)
                                ->first();
                        }

                        if ($shippingCountry && $shippingState) {
                            $shipping_zone = ShippingZone::where(
                                'id',
                                $shippingCountry->zone_id
                            )
                                ->where('status', 1)
                                ->where('created_by', $wishlist->seller_id)
                                ->first();

                            // return $shipping_zone;
                            if (isset($shipping_zone)) {
                                $shipping_rates = ShippingRate::where(
                                    'zone_id',
                                    $shipping_zone->id
                                )
                                    ->where(
                                        'minimum_order_weight',
                                        '<=',
                                        $productWeight
                                    )
                                    ->where(
                                        'max_order_weight',
                                        '>=',
                                        $productWeight
                                    )
                                    ->get([
                                        'id',
                                        'delivery_time',
                                        'rate',
                                        'name',
                                    ]);
                                // return $shipping_rates ;

                                if (
                                    !empty($shipping_rates) &&
                                    count($shipping_rates) > 0
                                ) {
                                    $wishlist->shipping_charges = $shipping_rates;
                                    $wishlist->is_shipping_avl = true;
                                } else {
                                    $wishlist->shipping_charges =
                                        'This product cannot be delivered to selected location';
                                    $wishlist->is_shipping_avl = false;
                                }
                            } else {
                                $wishlist->shipping_charges =
                                    'This product cannot be delivered to selected location';
                                $wishlist->is_shipping_avl = false;
                            }
                        } else {
                            $wishlist->shipping_charges =
                                'This product cannot be delivered to selected location';
                            $wishlist->is_shipping_avl = false;
                        }
                    } else {
                        $item->shipping_charges = 'shipping address not found';
                        $wishlist->is_shipping_avl = false;
                    }
                }

                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $wishlistItems],
                    'timestamp' => Carbon::now(),
                    'message' => 'Cart fetched successfully',
                ]);
            } else {
                return response()->json(
                    [
                        'http_status_code' => 200,
                        'status' => false,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Cart is empty',
                    ],
                    200
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

    public function update_cart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart' => 'required',
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

            foreach ($request->cart as $cart) {
                if ($cart['coupon_code']) {
                    $coupon = VendorCoupon::where([
                        'code' => $cart['coupon_code'],
                        'status' => 'published',
                    ])->first();
                    if (!$coupon) {
                        return response()->json(
                            [
                                'http_status_code' => 400,
                                'status' => false,
                                'context' => [
                                    'error' =>
                                        'Invalid coupon code. Please enter a valid coupon',
                                ],
                                'timestamp' => Carbon::now(),
                                'message' =>
                                    'Invalid coupon code' .
                                    ' ' .
                                    $cart['coupon_code'],
                            ],
                            400
                        );
                    } else {
                        if ($coupon->expiry_date < date('Y-m-d')) {
                            return response()->json(
                                [
                                    'http_status_code' => 400,
                                    'status' => false,
                                    'context' => [
                                        'error' => 'Coupon Expired',
                                    ],
                                    'timestamp' => Carbon::now(),
                                    'message' =>
                                        'The coupon you provided has expired and is no longer valid. ' .
                                        ' ' .
                                        'Coupon:' .
                                        $cart['coupon_code'],
                                ],
                                400
                            );
                        } elseif (
                            $coupon->no_of_coupons <= $coupon->used_coupons
                        ) {
                            return response()->json(
                                [
                                    'http_status_code' => 400,
                                    'status' => false,
                                    'context' => [
                                        'error' =>
                                            'Coupon can no longer be used',
                                    ],
                                    'timestamp' => Carbon::now(),
                                    'message' =>
                                        'The coupon you provided has reached its maximum usage limit and can no longer be used. ' .
                                        ' ' .
                                        'Coupon:' .
                                        $cart['coupon_code'],
                                ],
                                400
                            );
                        }
                    }
                }

                //  check available quantity
                $cartItems = CartItem::where('cart_id', $cart['id'])->get();
                foreach ($cartItems as $item) {
                    if ($item->variant_id) {
                        $inventory = InventoryWithVariant::where(
                            'p_id',
                            $item->product_id
                        )->first();
                        $variant = Variant::where(
                            'inventory_with_variant_id',
                            $inventory->id
                        )
                            ->where('id', $item->variant_id)
                            ->first();

                        if ($variant->stock_quantity < $item->quantity) {
                            return response()->json(
                                [
                                    'http_status_code' => 400,
                                    'status' => false,
                                    'context' => [
                                        'error' => 'Product is out of stock',
                                    ],
                                    'timestamp' => Carbon::now(),
                                    'message' =>
                                        $item->name . ' is out of stock',
                                ],
                                400
                            );
                        }
                    } else {
                        $inventory = InventoryWithoutVariant::where(
                            'p_id',
                            $item->product_id
                        )->first();
                        if ($inventory->stock_qty < $item->quantity) {
                            return response()->json(
                                [
                                    'http_status_code' => 400,
                                    'status' => false,
                                    'context' => [
                                        'error' => 'Product is out of stock',
                                    ],
                                    'timestamp' => Carbon::now(),
                                    'message' =>
                                        $item->name . ' is out of stock',
                                ],
                                400
                            );
                        }
                    }
                }
            }

             $user_id = $request->header('user_id') ?? auth('api')->user()->id;

            if ($request->cart_summary) {
                $cartSummary = new cartSummary();
                $cartSummary->user_id =   $user_id;
                $cartSummary->total_amount =
                    $request['cart_summary']['total_amount'];
                $cartSummary->discount_amount =
                    $request['cart_summary']['discount_amount'];
                $cartSummary->coupon_discount =
                    $request['cart_summary']['coupon_discount'];
                $cartSummary->tax_amount =
                    $request['cart_summary']['tax_amount'];
                $cartSummary->shipping_charges =
                    $request['cart_summary']['shipping_charges'];
                $cartSummary->grand_total =
                    $request['cart_summary']['grand_total'];
                $cartSummary->guarantee_charge =
                    $request['cart_summary']['guarantee_charge'] ?? 0;
                $request['cart_summary']['grand_total'];
                $cartSummary->save();
            }

            foreach ($request->cart as $item) {
                $cart = Cart::where('id', $item['id'])->update([
                    'coupon_code' => $item['coupon_code'],
                    'sub_total' => $item['sub_total'],
                    'discount_amount' => $item['discount_amount'],
                    'coupon_discount' => $item['coupon_discount'],
                    'tax_amount' => $item['tax_amount'],
                    'coupon_type' => $item['coupon_type'],
                    'shipping_amount' => $item['shipping_amount'],
                    'shipping_id' => $item['shipping_id'],
                    'total_amount' => $item['total_amount'],
                    'cart_summary_id' => $cartSummary->id,
                ]);
            }

            $data = User::where('id',  $user_id)
                ->with(['roles'])
                ->first();
            $is_vendor = 'No';
            foreach ($data['roles'] as $role) {
                if ($role->role == 'vendor') {
                    $is_vendor = 'yes';
                }
            }

            if ($is_vendor == 'yes') {
                $headers = [
                    'Accept' => 'application/json',
                    'client-key' => 'JewI9xRMfhEtLXXMWUPn9JDCZiPMpiufXKbyhzPU',
                ];
                $data = ['email' => auth('api')->user()->email];
                $url = app('api_url');
                $response = Http::withHeaders($headers)->post(
                    $url . 'get-usd-balance',
                    $data
                );

                if ($response->successful()) {
                    if ($response['context']['data']['wallet_show']) {
                        $cartSummary->pay_by_wallet = 'Yes';
                        $cartSummary->wallet_balance =
                            $response['context']['data']['totalAmountUsd'];
                    } else {
                        $cartSummary->pay_by_wallet = 'No';
                    }
                } else {
                    $cartSummary->pay_by_wallet = 'No';
                }
            } else {
                $cartSummary->pay_by_wallet = 'No';
            }

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $cartSummary],
                'timestamp' => Carbon::now(),
                'message' => 'Cart Updated Successfully',
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

    public function quantity_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cartItem_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
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

            $user = auth('api')->user() ? auth('api')->user()->id : null;

            if ($user) {
                $cart_data = CartItem::where('id', $request->cartItem_id)
                    ->where('user_id', $user)
                    ->first();
            } else {
                $cart_data = CartItem::where('id', $request->cartItem_id)
                    ->where('guest_user', $request->fcm_token)
                    ->first();
            }

            if ($cart_data->variant_id) {
                $variant = Variant::where(
                    'id',
                    $cart_data->variant_id
                )->first();
            } else {
                $inventory_without_variants = InventoryWithoutVariant::where(
                    'p_id',
                    $cart_data->product_id
                )->first();
                if ($inventory_without_variants) {
                    $variant = $inventory_without_variants;
                }
            }

            if ($cart_data) {
                $cart_data->quantity = $request->quantity;
                $cart_data->total_weight =
                    $cart_data->weight * $request->quantity;
                $cart_data->offer_price =
                    $variant->price * $request->quantity -
                    $variant->offer_price * $request->quantity;
                $cart_data->purchase_price =
                    $variant->price * $request->quantity;
                $cart_data->base_total =
                    $variant->offer_price * $request->quantity;
                $cart_data->save();

                // $cart = Cart::where([
                //     'user_id' => $user,
                //     'id' => $cart_data->cart_id,
                // ])->first();

                $cart = Cart::where('id', $cart_data->cart_id)->first();

                if ($cart) {
                    $vendorCartItems = CartItem::where(
                        'cart_id',
                        $cart->id
                    )->get();
                    $cart->item_count = $vendorCartItems->count();
                    $cart->sub_total = $vendorCartItems->sum('purchase_price');
                    $cart->discount_amount = $vendorCartItems->sum(
                        'offer_price'
                    );
                    $cart->total_amount = $vendorCartItems->sum('base_total');
                    $cart->save();
                }

                if ($cart_data) {
                    return response()->json([
                        'http_status_code' => 200,
                        'status' => true,
                        'context' => ['data' => []],
                        'timestamp' => Carbon::now(),
                        'message' => 'Quantity Updated successfully',
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
            } else {
                return response()->json(
                    [
                        'http_status_code' => 404,
                        'status' => false,
                        'context' => ['error' => 'Record not found'],
                        'timestamp' => Carbon::now(),
                        'message' => 'Record not found',
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

    public function get_my_cart()
    {
        try {
            $user_id = Auth::user()->id;
            $wishlistItems = Cart::where('user_id', $user_id)->get();
            if (!$wishlistItems->isEmpty()) {
                foreach ($wishlistItems as $wishlist) {
                    $wishlist->vendor = User::where(
                        'id',
                        $wishlist->seller_id
                    )->first(['name', 'email', 'details']);

                    $wishlist->shop =
                        Shop::where('vendor_id', $wishlist->seller_id)
                            ->where('status', 'active')
                            ->first([
                                'shop_name',
                                'legal_name',
                                'email',
                                'timezone',
                                'description',
                                'shop_url',
                            ]) ?? null;

                    $wishlist->cart_item = CartItem::with('product')
                        ->where('cart_id', $wishlist->id)
                        ->get();

                    $productWeight = 0;
                    foreach ($wishlist->cartItem as $item) {
                        $productWeight += $item->total_weight;
                        //  $wishlist->productWeight = $productWeight;
                        $item->product->featured_image_url = asset(
                            'public/vendor/featured_image/' .
                                $item->product->featured_image
                        );

                        if ($item->variant_id) {
                            $item->product->variant = Variant::where(
                                'id',
                                $item->variant_id
                            )->first();
                        } else {
                            $inventory_without_variants = InventoryWithoutVariant::where(
                                'p_id',
                                $item->product->id
                            )->first();
                            if ($inventory_without_variants) {
                                $item->product->product_detail = $inventory_without_variants;
                            }
                        }

                        $item->product->reviews = Review::where(
                            'product_id',
                            $item->product->id
                        )->get(['rating', 'comment', 'created_at']);

                        $item->product->shipping_detail =
                            UserAddress::where('user_id', $item->user_id)
                                ->where('address_type', 'shipping')
                                ->first([
                                    'id',
                                    'address_type',
                                    'contact_person',
                                    'contact_no',
                                    'floor_apartment',
                                    'address',
                                    'state',
                                    'country',
                                    'zip_code',
                                ]) ?? null;
                    }
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

    public function summary_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'shipping_address_id' => 'required|integer',
                'billing_address_id' => 'required|integer',
                'payment_method' => 'required',
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

            $summary = cartSummary::where('id', $request->id)->update([
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
                'payment_method' => $request->payment_method,
            ]);

            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => []],
                'timestamp' => Carbon::now(),
                'message' => 'Summary Updated Successfully',
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

    // guest checkout process
    public function guest_checkout(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'name' => 'required',
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

            $customer = User::where('email', $request->email)
                ->whereHas('roles', function ($query) {
                    $query->where('role', 'user');
                })
                ->first(['id', 'name', 'email','country_code','mobile_number']);

            if ($customer) {

                //    get customer access token
                // $token = $customer->createToken('MyApp')->accessToken;

                //    destroy customer existing cart
                // $userCart = Cart::where(['user_id' => $customer->id])->get();

                // foreach ($userCart as $cart) {
                //     $guestCart = Cart::where('guest_user', $request->device_id)
                //         ->where('seller_id', $cart->seller_id)
                //         ->first();

                //     if ($guestCart) {
                //         $addCart = Cart::where('seller_id', $cart->seller_id)
                //             ->where('user_id', $customer->id)
                //             ->first();
                //         if ($addCart) {
                //             $addCart->item_count =
                //                 $guestCart->item_count + $addCart->item_count;
                //             $addCart->sub_total =
                //                 $guestCart->sub_total + $addCart->sub_total;
                //             $addCart->discount_amount =
                //                 $guestCart->discount_amount +
                //                 $addCart->discount_amount;
                //             $addCart->coupon_discount =
                //                 $guestCart->coupon_discount +
                //                 $addCart->coupon_discount;
                //             $addCart->coupon_discount =
                //                 $guestCart->coupon_discount +
                //                 $addCart->coupon_discount;
                //             $addCart->total_amount =
                //                 $guestCart->total_amount +
                //                 $addCart->total_amount;
                //             $addCart->save();

                //             $cartItem = CartItem::where(
                //                 'cart_id',
                //                 $guestCart->id
                //             )->update([
                //                 'user_id' => $customer->id,
                //                 'cart_id' => $addCart->id,
                //             ]);
                //         }

                //         //   destroy guest Cart
                //         Cart::where('guest_user', $request->device_id)
                //             ->where('seller_id', $cart->seller_id)
                //             ->forceDelete();
                //     }
                // }

                // assign guest cart
                // Cart::where('guest_user', $request->device_id)->update([
                //     'user_id' => $customer->id,
                // ]);

                // $cart = Cart::where('guest_user', $request->device_id)
                //     ->pluck('id')
                //     ->toArray();
                // $cartItem = CartItem::whereIn('cart_id', $cart)->update([
                //     'user_id' => $customer->id,
                // ]);


                // sending otp to mail 

                $otp = strval(random_int(1000, 9999));
                $customer->otp = $otp;
                $customer->save();

                $data['otp'] = $otp;
                $data['email'] = $request->email;
                $data['title'] = 'OTP Verification';
                $data['body'] = 'Your OTP is: ' . $otp;

                Mail::send(
                    'email.forgotPasswordMail',
                    ['data' => $data],
                    function ($message) use ($data) {
                        $message
                            ->from('mail@dilamsys.com', 'Ktwis')
                            ->to($data['email'])
                            ->subject($data['body']);
                    }
                );

                // // sending Otp to Number 
                // if($customer->country_code){
                //     $message = "Your One Time Password for verification is: ". $otp .". Please enter this code to verify your identity. Do not share this code with anyone";
                //     $number = $customer->country_code.$customer->mobile_number ;
                //     sms($number, $message);
                // }
              

                $address = UserAddress::create([
                    'user_id' => $customer->id,
                    'contact_person' => $request->name,
                    'contact_no' => $request->phone_number,
                    'is_current' => 1,
                    'is_default' => 1,
                    'address_type' => 'shipping',
                    'address' => $request->address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'city' => $request->city,
                    'country_code' => $request->country_code,
                ]);

                UserAddress::where('user_id',$customer->id)->where('id', '!=',$address->id )->update([
                    'is_current' => 0 ,
                    'is_default'  => 0
                ]);


                $user['id'] = $customer->id;
                $user['email'] = $customer->email;
                $user['name'] = $customer->name;
                return response()->json([
                    'http_status_code' => 400,
                    'status' => true,
                    'context' => ['data' => $user , 'user' => "Already Exists"],
                    'timestamp' => Carbon::now(),
                    'message' => 'One Time Password Send Successfully',
                ],400);
            } else {

                $exist = User::where('email', $request->email)
                ->first(['id', 'name', 'email']);
                if($exist){
                    return response()->json([
                        'http_status_code' => 400,
                        'status' => false,
                        'context' => ['error' => "Vendor with this Email is already Exist"],
                        'timestamp' => Carbon::now(),
                        'message' => 'Vendor with this Email is already Exist',
                    ],400);
                }

                //  Create new user
                $password = Str::random(8);
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'country_code' => $request->country_code,
                    'fcm_token' => $request->fcm_token,
                    'mobile_number' => $request->phone_number,
                    'password' => bcrypt($password),
                ]);

                // assign role
                $role = Role::where('role', 'user')->first();
                $user->roles()->attach($role->id, ['user_id' => $user->id]);

                //   store shipping address
                $address = UserAddress::create([
                    'user_id' => $user->id,
                    'contact_person' => $request->name,
                    'contact_no' => $request->phone_number,
                    'is_current' => 1,
                    'is_default' => 1,
                    'address_type' => 'shipping',
                    'address' => $request->address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'zip_code' => $request->zip_code,
                    'city' => $request->city,
                    'country_code' => $request->country_code,
                ]);

                // assign guest cart
                Cart::where('guest_user', $request->device_id)->update([
                    'user_id' => $user->id,
                ]);
                $cart = Cart::where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
                $cartItem = CartItem::whereIn('cart_id', $cart)->update([
                    'user_id' => $user->id,
                ]);


// mail credentials to user

                $data = [
                    "name" =>  $user->name,
                    "email" => $user->email,
                    "password" => $password
               ];
               
                  
                $address->country = Country::where('id',$request->country)->value('country_name');
                $address->state = State::where('id',$request->state)->value('state_name');

                 $user['user_address']  = $address;

               Mail::send('email.guestRegistrationMail', ['data' => $data], function ($message) use ($user) {
                   $message->from('mail@dilamsys.com', "Ktwis")->to($user['email'])->subject("Welcome Mail");
               });
                return response()->json([
                    'http_status_code' => 200,
                    'status' => true,
                    'context' => ['data' => $user],
                    'timestamp' => Carbon::now(),
                    'message' => 'Address Save Successfully',
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


    public function verify_guest_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
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

        $user = User::where('email', $request->user_id)
            ->orWhere('mobile_number', $request->user_id)
            ->first();

        if ($user && $user->otp == $request->otp) {
            $user->otp = null;
            $user->save();

                $userCart = Cart::where(['user_id' => $user->id])->get();

                foreach ($userCart as $cart) {
                    $guestCart = Cart::where('guest_user', $request->device_id)
                        ->where('seller_id', $cart->seller_id)
                        ->first();

                    if ($guestCart) {
                        $addCart = Cart::where('seller_id', $cart->seller_id)
                            ->where('user_id', $user->id)
                            ->first();
                        if ($addCart) {
                          
                            $addCart->sub_total =
                                $guestCart->sub_total + $addCart->sub_total;
                            $addCart->discount_amount =
                                $guestCart->discount_amount +
                                $addCart->discount_amount;
                            $addCart->coupon_discount =
                                $guestCart->coupon_discount +
                                $addCart->coupon_discount;

                            $addCart->coupon_discount =
                                $guestCart->coupon_discount +
                                $addCart->coupon_discount;

                            $addCart->total_amount =
                                $guestCart->total_amount +
                                $addCart->total_amount;
                            $addCart->save();



                            $cartItem = CartItem::where(
                                'cart_id',
                                $guestCart->id
                            )->get();

                            foreach($cartItem as $item){
                         $cart_items = CartItem::where('user_id', $user->id)
                            ->where('product_id', $item->product_id)
                            ->first();

                              if($cart_items){
                                $cart_items->quantity = $item->quantity +  $cart_items->quantity;
                                $cart_items->total_weight = $item->total_weight +    $cart_items->total_weight;
                                $cart_items->offer_price = $item->offer_price +    $cart_items->offer_price;
                                $cart_items->purchase_price = $item->purchase_price +    $cart_items->purchase_price;
                                $cart_items->base_total = $item->base_total +    $cart_items->base_total;
                                $cart_items->save();
                              }

                              CartItem::where('guest_user' , $request->device_id 
                                )->where('product_id', $item->product_id)->delete();

                            }

                            // $cartItem = CartItem::where(
                            //     'cart_id',
                            //     $guestCart->id
                            // )->update([
                            //     'user_id' => $user->id,
                            //     'cart_id' => $addCart->id,
                            //     'guest_user' => null
                            // ]);
                        }

                        //   destroy guest Cart
                        Cart::where('guest_user', $request->device_id)
                            ->where('seller_id', $cart->seller_id)
                            ->forceDelete();
                    }
                }

                // assign guest cart
                Cart::where('guest_user', $request->device_id)->update([
                    'user_id' => $user->id,
                ]);

                $cart = Cart::where('guest_user', $request->device_id)
                    ->pluck('id')
                    ->toArray();

                    
                $cartItem = CartItem::whereIn('cart_id', $cart)->update([
                    'user_id' => $user->id,
                ]);

                $existWishlist = Wishlist::where('created_by', $user->id)->pluck('product_id')->toArray();

                    //   update wishlist cart 
                    Wishlist::where('guest_user', $request->device_id)
                    ->whereNotIn('product_id', $existWishlist)
                    ->update(["created_by" => $user->id]);

                      // update cart item count 
                      foreach(Cart::where('user_id', $user->id)->get() as $item){
                        $cart_count = CartItem :: where('cart_id', $item->id)->count();
                        Cart::where('id',$item->id)->update([
                              'item_count' => $cart_count
                        ]);
               }  
        
                $token =     $user->createToken(
                        'user_application_token'
                    )->accessToken;


            return response()->json([
                'http_status_code' => 200,
                'status' => true,
                'context' => ['data' => $token],
                'timestamp' => Carbon::now(),
                'message' => 'Code Verified',
            ]);
        } else {
            return response()->json(
                [
                    'http_status_code' => 403,
                    'status' => false,
                    'context' => ['error' => 'Code Does Not Match'],
                    'timestamp' => Carbon::now(),
                    'message' => 'Code Does Not Match',
                ],
                403
            );
        }

    }
}
