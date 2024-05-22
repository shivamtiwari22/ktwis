<?php

use App\Http\Controllers\Api\AddressApiController;
use App\Http\Controllers\Api\Vendor\AttributeApiController;
use App\Http\Controllers\Api\CartsApiController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Api\CustomerInvoiceApiController;
use App\Http\Controllers\Api\CustomerWalletApiController;
use App\Http\Controllers\Api\DisputeApiController;
use App\Http\Controllers\Api\FCMApiController;
use App\Http\Controllers\Api\FCMController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RecentItemApiController;
use App\Http\Controllers\Api\SaleBannerApiController;
use App\Http\Controllers\Api\SearchSortingApiController;
use App\Http\Controllers\Api\ShippingApiController;
use App\Http\Controllers\Api\SliderApiController;
use App\Http\Controllers\Api\SocialApiController;
use App\Http\Controllers\Api\SupportTicketApiController;
use App\Http\Controllers\Api\TestimonialApiController;
use App\Http\Controllers\Api\UserAccountController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserContactUsController;
use App\Http\Controllers\Api\UserForgotPassword;
use App\Http\Controllers\Api\Vendor\AuthApiController;
use App\Http\Controllers\Api\Vendor\CouponController;
use App\Http\Controllers\Api\Vendor\CustomerPanelApiController;
use App\Http\Controllers\Api\Vendor\MessageApiController;
use App\Http\Controllers\Api\Vendor\ProductApiController;
use App\Http\Controllers\Api\Vendor\ReviewApiController;
use App\Http\Controllers\Api\Vendor\SettingApiController;
use App\Http\Controllers\Api\Vendor\SpecificationApiController;
use App\Http\Controllers\Api\Vendor\StockApiController;
use App\Http\Controllers\Api\Vendor\VendorDashboardAPIController;
use App\Http\Controllers\Api\Vendor\VendorDisputeApiController;
use App\Http\Controllers\Api\Vendor\WalletApiController;
use App\Http\Controllers\Api\WishlistApiController;
use App\Http\Controllers\Vendor\NotificationController;
use App\Models\Vendor;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


    
//Config 
Route::get('country_state_id/{id}', [HomeApiController::class, 'country_state_id']);
Route::get('country_state', [HomeApiController::class, 'country_state']);
// global data 
Route::get('get-global-settings', [HomeApiController::class, 'getGlobalSetting']);
Route::get('get-business-area', [HomeApiController::class, 'getBusinessArea']);

// Open Dispute text 
Route::get('get-dispute-text', [DisputeApiController::class, 'get_dispute_text']);
Route::get('get-country-code', [HomeApiController::class, 'get_country_code']);
Route::get('get-guarantee-charge', [HomeApiController::class, 'guaranteeCharge']);

// pages 
Route::get('/about-us', [HomeApiController::class, 'about_us']);
Route::get('terms-of-use', [HomeApiController::class, 'termCondition']);
Route::get('shipping-policy', [HomeApiController::class, 'shippingPolicy']);
Route::get('privacy-policy', [HomeApiController::class, 'privacyPolicy']);
Route::get('return-cancellation', [HomeApiController::class, 'returnCancellation']);
Route::get('faqs', [HomeApiController::class, 'faqs']);

Route::get('page/{type}', [HomeApiController::class, 'footerPages']);
Route::get('meta-data/{type}', [HomeApiController::class, 'metaData']);


//  contact us 
Route::get('contact-us', [HomeApiController::class, 'getContactUsData']);

// faq content 
Route::get('faq-contact', [HomeApiController::class, 'getFaqsData']);

// update vendor release payment status 
Route::post('update-payment-Status' , [OrderApiController::class, 'updatePaymentStatus']);

//  get customer address
 Route::get('get-all-customer-address/{id}', [AddressApiController::class, 'get_all_customer_address']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// mail test 
Route::get('mail', function () {
    $arr = array();
    $arr['email'] = 'himanshu32301@gmail.com';
    $arr['subject'] = 'Hello request for quotation from Ktwis';

    $data = array('name' => 'hello');
    Mail::send('email.mail', $data, function ($message) use ($arr) {
        $message->from('mail@dilamsys.com', "Ktwis")->to($arr['email'])->subject($arr['subject']);
    });
    return "sent1";
});



// open Home apis 
Route::post('/slider', [SliderApiController::class, 'index_slider']);
Route::get('/flash_sale', [HomeApiController::class, 'flash_sale']);
Route::get('/deal_of_the_day', [HomeApiController::class, 'deal_of_the_day']);
Route::get('/featured_item', [HomeApiController::class, 'featured_item']);
Route::get('/trending_item', [HomeApiController::class, 'trending_item']);
Route::get('/categories', [HomeApiController::class, 'categories']);
Route::get('/category-by-slug/{slug}', [HomeApiController::class, 'categoryBySlug']);
Route::get('products', [ProductController::class, 'getAllProduct']);
Route::get('/products_by_category/{id}', [HomeApiController::class, 'products_by_category']);
Route::get('/products_by_category_slug/{slug}', [HomeApiController::class, 'products_by_category_slug']);
Route::get('/getproduct/{product_id}', [HomeApiController::class, 'getproduct']);
Route::get('/getProductBySlug/{slug}', [HomeApiController::class, 'getProductBySlug']);
Route::get('/slider_management', [HomeApiController::class, 'slider_management']);
Route::get('/filter_using_cat_id/{cat_id}', [HomeApiController::class, 'filter_using_cat_id']);
Route::post('/search_products', [SearchSortingApiController::class, 'search_products']);
Route::post('/sorting_product', [SearchSortingApiController::class, 'sorting_product']);
Route::get('/product_filter', [HomeApiController::class, 'product_filter']);
Route::post('/recently_viewed', [HomeApiController::class, 'recently_viewed']);
Route::get('/get_all_sale_banner',[SaleBannerApiController::class,'get_all_sale_banner']);
Route::post('add-contact-details', [ContactUsController::class, 'store']);
   // Recent Item 
Route::post('/recent/item-post',[RecentItemApiController::class,'item_post']);
Route::get('/recent/getRecentItems-by-category/{id}',[RecentItemApiController::class,'getRecentItemsbyCategory']);



 //    coustom_searching
 Route::post('/search_customs', [SearchSortingApiController::class, 'search_custom']);
 Route::post('/test-monial',[TestimonialApiController::class,'gettestimonial']);
 Route::post('/add-testimonial',[TestimonialApiController::class,'add_testimonial']);
 Route::post('/edit-testimonial',[TestimonialApiController::class,'edit_testimonial']);
 Route::post('/delete-testimonial',[TestimonialApiController::class,'delete_testimonial']);
 Route::post('/social-media-verification',[SocialApiController::class,'social_media_email']);
 Route::post('/notification',[NotificationApiController::class,'notification']);
Route::get('/search_customs', [SearchSortingApiController::class, 'search_custom']);

//attribute_using_product_id
Route::get('/show-attribute/{product_id}', [ProductController::class, 'show_attribute']);
Route::get('/attributes-by-category/{id}', [HomeApiController::class, 'attributes_by_category']);
Route::get('/attributes-by-category-slug/{slug}', [HomeApiController::class, 'attributes_by_category_slug']);
Route::post('/attributes-by-product', [HomeApiController::class, 'attributes_by_product']);
Route::post('/attributes-by-categories', [HomeApiController::class, 'attributes_by_categories']);
//   get variant id  by  attribute value ids 
Route::post('/variant-by-attribute', [SearchSortingApiController::class, 'variant_by_attribute']);




Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', [UserAuthController::class, 'logout']);


    Route::group(['middleware' => 'checkUserStatus'], function () {
    Route::get('get-my-cart', [CartsApiController::class, 'get_my_cart']);
    Route::get('get-user-address/{type}', [AddressApiController::class, 'get_address_by_type']);
    Route::post('update-user-profile', [UserAccountController::class, 'updateUserProfile']);
    Route::get('get-user-profile', [UserAccountController::class, 'get_user_profile']);
    Route::post('update-profile-pic', [UserAccountController::class, 'update_profile_pic']);
    Route::post('change-password', [UserForgotPassword::class, 'change_password']);

    //  customer notification
   Route::get('/get-all-notification',[NotificationApiController::class,'allNotification']);
   Route::get('/mark-as-read/{id}', [NotificationApiController::class, 'markAsRead']);
   Route::get('/all-clear', [NotificationApiController::class, 'allMarkAsRead']);

        // customer---invoice
   Route::post('/customer-invoice',[CustomerInvoiceApiController::class,'customer_invoice']);
   Route::post('/contact-to-seller',[CustomerInvoiceApiController::class,'contact_to_seller']);


    //add recently viewed
    Route::post('/add-recently-viewed', [HomeApiController::class, 'add_recently_viewed']);
    
      // customer Dashboard 
      Route::get('/customer/dashboard', [CustomerWalletApiController::class, 'dashboard']);
      Route::get('/customer/messages', [CustomerWalletApiController::class, 'messages']);
      //   mark as read messages 
      Route::get('/customer/message-mark-as-read/{id}', [CustomerWalletApiController::class, 'messageMarkAsRead']);

      // download pdf  
      Route::get('download-invoice/{id}', [UserAccountController::class, 'download_invoice']);


      // customer order
      Route::get('customer-orders', [OrderApiController::class, 'getAllCusomerOrder']);
      Route::post('search-orders', [OrderApiController::class, 'search_order_by_orderId']);
      Route::get('order-details/{id}', [OrderApiController::class, 'order_details'])->where('id', '[0-9]+')->middleware('orderDetail');
      Route::post('cancel-order-req', [OrderApiController::class, 'cancel_order_request']);   

     // Disputes 
      Route::get('get-my-disputes', [DisputeApiController::class, 'get_my_disputes']);
      Route::get('disputes-products/{id}', [DisputeApiController::class, 'get_dispute_products']);
      Route::post('open-dispute', [DisputeApiController::class, 'openDispute']);
      Route::get('dispute-details/{id}', [DisputeApiController::class, 'customerDisputeDetails'])->middleware('disputeDetail');
      Route::post('dispute-response', [DisputeApiController::class, 'disputeResponse']);
      Route::get('dispute-resolved/{id}', [DisputeApiController::class, 'disputeResolved']);

    });

    

// \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

        
    // review
    Route::post('/review/add',[ReviewApiController::class,'add_review']);
    Route::get('/review/getReviews-by-Pid/{id}',[ReviewApiController::class,'get_review_by_productId']);

    Route::get('/reviews',[ReviewApiController::class,'reviews']);
    Route::get('/view-reviews/{id}',[ReviewApiController::class,'viewReviews']);


     // Cart data
     Route::get('/get-customer-cart',[CustomerPanelApiController::class,'cart_data_show']);
     Route::get('/get-customer-wishlist',[CustomerPanelApiController::class,'wishlist']);
     Route::get('/order',[CustomerPanelApiController::class,'order']);
     Route::get('/cancellation-req',[CustomerPanelApiController::class,'cancellation']);
     Route::post('/cancellation-req-approval',[CustomerPanelApiController::class,'cancellation_approval']);
     Route::post('/cancellation-search',[CustomerPanelApiController::class,'cancellation_search']);
     

    Route::post('/invoices',[ReviewApiController::class,'invoices']);
    Route::get('/report',[ReviewApiController::class,'report']);


    // vendor Apis 

    Route::post('/message_data',[MessageApiController::class,'message_add']);
    Route::post('/compose_new',[MessageApiController::class,'compose_new']);
    Route::post('/inbox-message',[MessageApiController::class,'inbox_message']);
    Route::post('/sent-message',[MessageApiController::class,'sent_message']);
    Route::post('/draft-message',[MessageApiController::class,'draft_message']);

        //  products
    Route::get('/product_details/{id}',[ProductApiController::class,'product_all_data']);
    Route::post('/Product_add',[ProductApiController::class,'Product_add']);
    Route::post('/product_update',[ProductApiController::class,'product_update']);
    Route::get('/delete-product/{id}',[ProductApiController::class,'product_delete']);
    Route::get('get-all-product',[ProductApiController::class,'all_product']);

    Route::post('add-coupon', [CouponController::class, 'add_coupon']);
    Route::post('view-coupon/{id}', [CouponController::class, 'view_coupon']);
    Route::post('edit-coupon/{id}', [CouponController::class, 'edit_coupon']);
    Route::post('delete-coupon/{id}', [CouponController::class, 'delete_coupon']);
    
    Route::post('/spams-message',[MessageApiController::class,'spams_message']);
    Route::post('/trash-message',[MessageApiController::class,'trash_message']);
    Route::post('/email-template',[MessageApiController::class,'email_template']);
    Route::post('/spam-message',[MessageApiController::class,'spam_message']);
    Route::post('/trash-data',[MessageApiController::class,'trash_data']);
    Route::post('/move-to-inbox',[MessageApiController::class,'move_to_inbox']);
    Route::post('/message-delete-permanent',[MessageApiController::class,'message_delete_permanent']);
    Route::post('/message-move-inbox',[MessageApiController::class,'message_move_inbox']);

       //  send message in vendor order details
       Route::post('/order-send-message',[CustomerPanelApiController::class,'sendMessage']);

    // Get All Email templates 
    Route::get('/get-email-templates',[MessageApiController::class,'getEmailTemplates']);

    // show all message data
    Route::get('/inbox-message-data',[MessageApiController::class,'inbox_message_data']);
    Route::get('/sent-message-data',[MessageApiController::class,'sent_message_data']);
    Route::get('/draft-message-data',[MessageApiController::class,'draft_message_data']);
    Route::get('/spam-message-data',[MessageApiController::class,'spam_message_data']);
    Route::get('/trash-message-data',[MessageApiController::class,'trash_message_data']);


    //  specification
    Route::post('/specification-add',[SpecificationApiController::class,'specification_add']);
    Route::get('/specification-delete/{id}',[SpecificationApiController::class,'specification_delete']);
    Route::post('/specification-edit',[SpecificationApiController::class,'specification_edit']);
    Route::get('/specification-show',[SpecificationApiController::class,'specification_show']);

    // return policy
    Route::post('/return-policy-add',[SpecificationApiController::class,'return_policy_add']);
    Route::post('/return-policy-update',[SpecificationApiController::class,'return_policy_update']);
    Route::get('/return-policy-data',[SpecificationApiController::class,'return_policy_data']);
    Route::get('/return-policy-delete/{id}',[SpecificationApiController::class,'return_policy_delete']);
    //Shipping for vendor
    //Carriers
    Route::post('add-carrier', [ShippingApiController::class, 'add_carriers']);
    Route::post('update-carrier/{carrier_id}', [ShippingApiController::class, 'update_carriers']);
    Route::post('get-carrier/{carrier_id}', [ShippingApiController::class, 'get_carrier_by_id']);
    Route::post('get-all-carrier', [ShippingApiController::class, 'get_all_carrier']);
    Route::post('delete-carrier/{carrier_id}', [ShippingApiController::class, 'delete_carrier']);
    Route::get('update-carrier-status/{carrier_id}', [ShippingApiController::class, 'update_status']);

    //shipping rates
    Route::post('add-shipping-rates', [ShippingApiController::class, 'add_shipping_rates']);
    Route::post('update-shipping-rates', [ShippingApiController::class, 'update_shipping_rates']);
    Route::post('get-shipping-rates/{rate_id}', [ShippingApiController::class, 'get_rate_by_id']);
    Route::post('get-shipping-rates-vendor/{vendor_id}', [ShippingApiController::class, 'get_rate_by_vendor_id']);
    Route::post('get-all-shipping-rates', [ShippingApiController::class, 'get_all_rate']);
    Route::get('delete-rates/{rate_id}', [ShippingApiController::class, 'delete_rate']);
    Route::get('get-rates-byzone/{id}', [ShippingApiController::class, 'getRates']);

    //zones
    Route::post('add-zone', [ShippingApiController::class, 'add_zone']);
    Route::post('update-zone', [ShippingApiController::class, 'update_zone']);
    Route::post('get-zone-by-id/{zone_id}', [ShippingApiController::class, 'get_zone_by_id']);
    Route::post('get-zone-by-vendor_id/{vendor_id}', [ShippingApiController::class, 'get_zone_by_vendor_id']);
    Route::get('delete-zone/{zone_id}', [ShippingApiController::class, 'delete_zone']);
    Route::get('get-all-zone', [ShippingApiController::class, 'get_all_zone']);

    // vendor customers 
    Route::get('get-all-customer', [CustomerPanelApiController::class, 'get_all_customer']);
    Route::get('view-customer/{id}', [CustomerPanelApiController::class, 'view_customer']);
    Route::get('delete-customer/{id}', [CustomerPanelApiController::class, 'delete_customer']);
    Route::post('customer-change-password', [CustomerPanelApiController::class, 'change_password']);
    Route::post('add-customer', [CustomerPanelApiController::class, 'add_customer']);
    Route::post('edit-customer', [CustomerPanelApiController::class, 'edit_customer']);


    //attributes
    Route::get('get-attributes', [AttributeApiController::class, 'get_attributes']);
    Route::get('get-attributes-by-id/{id}', [AttributeApiController::class, 'get_attributes_by_id']);
    Route::post('store-attributes', [AttributeApiController::class, 'store_attributes']);
    Route::post('update-attributes', [AttributeApiController::class, 'update_attributes']);
    Route::get('delete-attribute/{id}', [AttributeApiController::class, 'delete_attribute']);


    //  Vendor attributes
    Route::get('get-vendor-attributes', [AttributeApiController::class, 'get_vendor_attributes']);

    //attributes values
    Route::get('get-attributes-value', [AttributeApiController::class, 'get_attributes_values']);
    Route::get('get-attributes-value-by-id/{id}', [AttributeApiController::class, 'get_attributes_values_by_id']);
    Route::get('get-attributes-value-by-attr-id/{attr_id}', [AttributeApiController::class, 'get_attributes_values_by_attr_id']);
    Route::post('add-attributes-value', [AttributeApiController::class, 'add_attribute_value']);
    Route::post('update-attributes-value', [AttributeApiController::class, 'update_attribute_value']);
    Route::get('delete-attribute-value/{id}', [AttributeApiController::class, 'delete_attribute_value']);


    //Stocks
    Route::get('stocks/without_variant', [StockApiController::class, 'get_stock_without_variant']);
    Route::get('stocks/with_variant', [StockApiController::class, 'get_stock_with_variant']);
    Route::post('stocks/add-stock', [StockApiController::class, 'add_to_stock']);
    Route::get('stocks/view-stock/{id}', [StockApiController::class, 'view_stock']);
    Route::post('stocks/update-stock', [StockApiController::class, 'update_stock']);
    Route::get('stocks/delete-stock/{id}', [StockApiController::class, 'delete_stock']);
    Route::post('stocks/add-stock-variants', [StockApiController::class, 'add_to_stock_variant']);
    Route::get('stocks/view-stock-variant/{id}', [StockApiController::class, 'view_stock_variant']);
    Route::post('stocks/update-stock-variant', [StockApiController::class, 'update_stock_variant']);
    Route::get('stocks/delete-stock-variant/{id}', [StockApiController::class, 'delete_stock_variant']);

   
    Route::post('payment', [CheckoutApiController::class, 'payment']);

  

    Route::post('all-payment', [OrderApiController::class, 'getAllPayment']);

    Route::post('update-order-status', [OrderApiController::class, 'order_status']);
    Route::post('full-fill-order', [OrderApiController::class, 'full_fill']);
    Route::post('cancel-order', [OrderApiController::class, 'cancel_order']);


    // download invoice 

    
    // Customer Wallet 
    Route::Post('/my-wallet', [CustomerWalletApiController::class, 'myWallet']);
    Route::Post('/verify-payment', [CustomerWalletApiController::class, 'verify']);
    Route::Post('/tranfer-amount', [CustomerWalletApiController::class, 'transferAmount']);
    Route::Post('/amount-withdrawal', [CustomerWalletApiController::class, 'moneywithdrawal']);
    Route::Post('/user-transactions', [CustomerWalletApiController::class, 'alltransaction']);

  


    // shipping zone
    Route::post('/shipping-data',[ShippingApiController::class,'shipping_data']);
    Route::post('/shipping-data-add',[ShippingApiController::class,'shipping_data_add']);
    Route::post('/shipping-data-update',[ShippingApiController::class,'shipping_data_update']);
    Route::post('/shipping-data-delete',[ShippingApiController::class,'shipping_data_delete']);
   
    // shipping rate
    Route::post('/shipping-rate-data-add',[ShippingApiController::class,'shipping_rate_data_add']);
    Route::post('/shipping-rate-update',[ShippingApiController::class,'shipping_rate_update']);
    Route::post('/shipping-zone-delete',[ShippingApiController::class,'shipping_zone_delete']);

    // Support Ticket 
    Route::post('post-support-ticket', [SupportTicketApiController::class, 'PostSupportTicket']);

    //dashboard
    Route::post('/vendor/dashboard', [VendorDashboardAPIController::class, 'dashboard']);
    Route::post('/vendor/support-desk/dispute', [VendorDashboardAPIController::class, 'support_desk_dispute']);
    Route::post('/vendor/support-desk/dispute-id', [VendorDashboardAPIController::class, 'support_desk_dispute_id']);
    Route::post('/vendor/support-desk/dispute-id/reply', [VendorDashboardAPIController::class, 'support_desk_reply_store_disputes']);
    //vendor-performance
    Route::post('/vendor/performance', [VendorDashboardAPIController::class, 'index_performance']);
    //tax
    Route::post('/tax', [OrderApiController::class, 'get_tax']);

    Route::get('vendor/use-coupon/{couponCode}', [CouponController::class, 'use_coupon']);

    // vendor coupons 
    Route::get('get-all-vendor-coupon', [CouponController::class, 'allCoupon']);


    // sale banner
Route::post('/sale_banner_add',[SaleBannerApiController::class,'sale_banner_add']);
Route::post('/sale_banner_edit',[SaleBannerApiController::class,'sale_banner_edit']);
Route::post('/sale_banner_delete',[SaleBannerApiController::class,'sale_banner_delete']);
Route::post('/sale_banner_status',[SaleBannerApiController::class,'sale_banner_status']);

  // search stock product 
  Route::get('get-stock-product', [StockApiController::class, 'search_product']);


//   Create Offline order route 
Route::post('create-order', [CheckoutApiController::class, 'createOrder']);

// create custom order 
Route::post('create-custom-order', [CheckoutApiController::class, 'createCustomOrder']);

Route::get('get-custom-orders', [CheckoutApiController::class, 'getCustomOrders']);



// update customer address 
Route::post('update-customer-address', [AddressApiController::class, 'updateCustomerAddress']);


// update vendor Profile 
Route::post('update-profile', [AuthApiController::class, 'updateProfile']);
Route::get('get-profile', [AuthApiController::class, 'getProfile']);

// get payout detail of vendor
Route::get('get-payout-details', [VendorDashboardAPIController::class, 'getPayoutDetail']);


    // vendor create order api 
  Route::get('get-all-product-without-variant', [CustomerPanelApiController::class, 'getWithoutVariantProduct']);


});



Route::get('/users', function () {
    return Auth::user();
});

Route::middleware('verify.api.client')->group(function () {

    Route::post('Forgot-password',  [UserForgotPassword::class, 'forgot_password']);
    Route::post('verify_otp',  [UserForgotPassword::class, 'verify_otp']);
    Route::post('new_password',  [UserForgotPassword::class, 'new_password']);
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('email-token', [UserAuthController::class, 'email_token']);
    Route::post('account-verify', [UserAuthController::class, 'verify_user_otp']);
    Route::post('update-account', [UserAccountController::class, 'update_account']);
    Route::post('resend-otp', [UserAccountController::class, 'resendOtp']);

    //category
    // Route::post('categories', [CategoryController::class, 'getAllCategory']);


    //vendor orders
    Route::get('/all-vendor-order', [OrderApiController::class, 'vendorAllOrder']);

    // Vendor Wallet 
    Route::Post('/my-Wallet', [WalletApiController::class, 'myWallet']);
    Route::Post('/verify-payment', [WalletApiController::class, 'verify']);
    Route::Post('/tranfer-amount', [WalletApiController::class, 'transferAmount']);
    Route::Post('/amount-withdrawal', [WalletApiController::class, 'moneywithdrawal']);
    Route::Post('/all-transaction', [WalletApiController::class, 'alltransaction']);


    //  vendor setting 
    Route::post('tax/create',  [SettingApiController::class, 'createTax']);
    Route::get('tax/all-tax',  [SettingApiController::class, 'allTax']);
    Route::post('tax/update',  [SettingApiController::class, 'updateTax']);
    Route::post('tax/view',  [SettingApiController::class, 'viewTax']);
    Route::post('tax/delete',  [SettingApiController::class, 'deleteTax']);
    Route::post('shop/update', [SettingApiController::class, 'updateShop']);
    Route::get('shop/shop-details', [SettingApiController::class, 'getShopDetails']);
    Route::post('update-bank-details', [SettingApiController::class, 'updateBankDetail']);
    Route::post('update-shop-address', [SettingApiController::class, 'updateShopAddress']);
    Route::get('resend-verification-link', [SettingApiController::class, 'verification_email']);


    // vendor authentication 
    Route::post('vendor-register', [AuthApiController::class, 'registration']);
    Route::post('vendor-account-verify', [AuthApiController::class, 'account_verify']);
    Route::post('send-otp', [AuthApiController::class, 'sendCode']);
    Route::post('forgot-password',  [AuthApiController::class, 'forgot_password']);


    // Vendor Disputes 
    Route::post('open-dispute', [DisputeApiController::class, 'openDispute']);
   Route::post('vendor-Dispute', [VendorDisputeApiController::class, 'vendorDispute']);


});


// customer 
Route::post('login', [UserAuthController::class, 'login']);

// google login
Route::post('social-login', [UserAuthController::class, 'socialLogin']);


// Vendor
Route::post('Login', [AuthApiController::class, 'login']);





// ============================   Open Routes ===========================================

Route::group(['middleware' => 'checkUserStatus'], function () {
// Wishlist Routes
Route::post('add-to-wishlist', [WishlistApiController::class, 'add_to_Wishlist']);
Route::get('get-wishlist', [WishlistApiController::class, 'get_wishlist']);
Route::delete('remove-wishlist/{id}', [WishlistApiController::class, 'remove_wishlist']);
Route::delete('remove-all-wishlist', [WishlistApiController::class, 'remove_all_wishlist']);
Route::get('/recent/getRecentItems',[RecentItemApiController::class,'getRecentItems']);


// open cart routes 
Route::post('add-to-cart', [CartsApiController::class, 'add_to_cart']);
Route::get('remove-cart/{id}', [CartsApiController::class, 'remove_cart']);
Route::get('get-cart', [CartsApiController::class, 'get_cart']);
Route::post('update-cart-quantity', [CartsApiController::class, 'quantity_update']);
Route::post('update-cart-summary', [CartsApiController::class, 'summary_update']);
Route::post('update-cart', [CartsApiController::class, 'update_cart']);


// get customer address 
Route::get('get-all-address', [AddressApiController::class, 'get_all_address']);
Route::get('get-user-address', [AddressApiController::class, 'get_address_by_user_id']);
Route::get('get-address/{address_id}', [AddressApiController::class, 'get_address']);
Route::post('update-current-address/{address_id}', [AddressApiController::class, 'updateCurrentAddress']);
Route::post('update-address/{address_id}', [AddressApiController::class, 'updateaddress']);
Route::post('add-address', [AddressApiController::class, 'add_address']);
Route::get('remove-address/{id}', [AddressApiController::class, 'remove_address']);


 //  checkout 
 Route::post('store-order', [CheckoutApiController::class, 'store_order']);
 
 //   flutter Wave Payment Gateway Route 
Route::get('stripe-checkout/{amount}', [CheckoutApiController::class, 'flutterPayment']);

// Stripe Payment Gateway 
Route::get('flutter-checkout/{amount}', [CheckoutApiController::class, 'stipe_checkout']);
 

Route::post('guest-checkout', [CartsApiController::class, 'guest_checkout']);
Route::post('verify-guest-otp', [CartsApiController::class, 'verify_guest_otp']);

Route::get('get-all-vendor-coupon/{id}', [CouponController::class, 'allVenderCoupon']);
Route::post('applyCoupon', [CouponController::class, 'applyCoupon']);
Route::post('use-coupon/{id}', [CouponController::class, 'use_coupon']);


//   vendor detail $ products 
Route::get('/get-vendor-detail/{id}', [VendorDashboardAPIController::class, 'getVendorDetail']);
Route::get('/get-vendor-products/{id}', [ProductController::class, 'getVendorProducts']);
Route::post('/search-vendor-products', [ProductController::class, 'searchVendorProducts']);

// home 
Route::get('/category-slider/{id}', [HomeApiController::class, 'category_slider']);


});








