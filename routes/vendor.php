<?php

use App\Http\Controllers\Api\UserAccountController;
use App\Http\Controllers\Vendor\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\RegisterController;
use App\Http\Controllers\Vendor\CatalogController;
use App\Http\Controllers\Vendor\InvoiceController;
use App\Http\Controllers\Vendor\NotificationController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ReportsController;
use App\Http\Controllers\Vendor\ReviewController;
use App\Http\Controllers\Vendor\Setting\SettingController;
use App\Http\Controllers\Vendor\Shipping\CarrierController;
use App\Http\Controllers\Vendor\Shipping\WishlistController;
use App\Http\Controllers\Vendor\Shipping\ZoneController;
use App\Http\Controllers\Vendor\StockController;
use App\Http\Controllers\Vendor\SupportController;
use App\Http\Controllers\Vendor\TaxController;
use App\Http\Controllers\Vendor\TwilioController;
use App\Http\Controllers\Vendor\WalletController;
use Illuminate\Support\Facades\Mail;

Route::get('mail', function () {
    $arr = array();
    $arr['email'] = 'st4272333@gmail.com';
    $arr['subject'] = 'Testing Complete Order Mail';

    $data = array('name' => 'hello');
    Mail::send('email.mail', $data, function ($message) use ($arr) {
        $message->from('mail@dilamsys.com', "Ktwis")->to($arr['email'])->subject($arr['subject']);
    });
    return "sent";
});


// Route::get('mail', [ReviewController::class, 'testMail']);
Route::get('verification-confirmation/{id}', [RegisterController::class, 'verification_confirmation'])->name('vendor.verificationConfirmation');

Route::get('otp-verification', [RegisterController::class, 'otp_verification'])->name('vendor.verification');
Route::post('vendor-otp-verify', [RegisterController::class, 'vendor_verify'])->name('vendor.vendor-verify');
Route::post('resend-otp', [RegisterController::class, 'resendOtp'])->name('vendor.resend-otp');

//register
Route::get('register', [RegisterController::class, 'register'])->name('vendor.register');
Route::post('/register_store', [RegisterController::class, 'register_store'])->name('vendor.register.store');
Route::get('/terms-and-conditions', [RegisterController::class, 'terms'])->name('vendor.terms');
//login
Route::get('login', [RegisterController::class, 'login'])->name('vendor.login');
Route::post('login_vendor', [RegisterController::class, 'login_vendor'])->name('vendor.login_vendor');


Route::get('download-invoice/{id}', [UserAccountController::class, 'download_invoice']);


// lang
Route::get('lang/{lang}', [RegisterController::class, 'switchLang']);

//ForgotPassword
Route::post('/send-email', [RegisterController::class, 'send_mail'])->name('vendor.forgot.email'); 

Route::get('/forgot-password', [RegisterController::class, 'forgot_password'])->name('vendor.forgot.password');
Route::get('/otp-verify/{email}', [RegisterController::class, 'otp'])->name('vendor.otp');
Route::post('/otp-verify', [RegisterController::class, 'otp_verify'])->name('vendor.otp_verify');
Route::get('/new-password/{email}', [RegisterController::class, 'new_password'])->name('vendor.new_password');
Route::post('/new-password-store', [RegisterController::class, 'new_password_store'])->name('vendor.new_password.store');


Route::group(['middleware' => 'vendor'], function () {
    Route::get('logout', [RegisterController::class, 'logout'])->name('vendor.logout');
    Route::get('/push-notificaiton', [NotificationController::class, 'index'])->name('vendor.push-notificaiton');
    Route::post('/fcm-token', [NotificationController::class, 'updateToken'])->name('vendor.fcmToken');
    Route::post('/send-notification',[NotificationController::class,'notification'])->name('vendor.notification');
    Route::get('/test-notification',[NotificationController::class,'testNotification'])->name('vendor.test.notification');
    // Route::patch('/updateToken-token', [NotificationController::class, 'updateTokenes'])->name('vendor.updateToken');

    //dashboard
    Route::get('dashboard', [RegisterController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/save-token',[FCMController::class,'index']);
    //profile edit
    Route::get('profile', [RegisterController::class, 'profile'])->name('vendor.profile');
    Route::post('profile/update', [RegisterController::class, 'update_profile'])->name('vendor.profile.update');

    //change password
    Route::get('/change-password', [RegisterController::class, 'change_password'])->name('vendor.change.password');

    
    Route::post('/change-password/store', [RegisterController::class, 'change_password_store'])->name('vendor.change.password.store');
    
    // verifcation mail 

    Route::get('send/verification-mail', [RegisterController::class, 'verification_mail'])->name('vendor.verificationMail');


    //catalog Routes
    // ->  attributes
    Route::get('catalog/attributes', [CatalogController::class, 'index_attribute'])->name('vendor.attributes.index');
    Route::post('catalog/attributes/store', [CatalogController::class, 'store_attribute'])->name('vendor.attributes.store_attribute');
    Route::post('catalog/attributes/list', [CatalogController::class, 'list_attributes'])->name('vendor.attributes.list');
    Route::get('catalog/attributes/show/{id}', [CatalogController::class, 'show_attributes'])->name('vendor.attribute.show');
    Route::post('catalog/attributes/update', [CatalogController::class, 'update_attributes'])->name('vendor.attribute.update');
    Route::post('catalog/attributes/delete', [CatalogController::class, 'delete_attribute'])->name('vendor.attribute.delete');

    //attributes_values
    Route::get('catalog/attributes/{id}/entities', [CatalogController::class, 'index_entities'])->name('vendor.attributes.entities');
    Route::post('catalog/attributes/entities/store', [CatalogController::class, 'store_entities'])->name('vendor.attributes.entities.store_attr_value');
    Route::post('catalog/attributes/entities/{id}/list_entities', [CatalogController::class, 'list_entities'])->name('vendor.attributes.list_entities');
    Route::get('catalog/attributes/show/{id}/entities', [CatalogController::class, 'show_entity'])->name('vendor.attribute.show_entity');
    Route::post('catalog/attributes/update/{id}/entities', [CatalogController::class, 'update_entities'])->name('vendor.attribute.update_entities');
    Route::post('catalog/attributes/delete/entities', [CatalogController::class, 'delete_entities'])->name('vendor.attribute.delete.entities');
    Route::get('catalog/attributes/view/{id}/entities', [CatalogController::class, 'view_entity'])->name('vendor.attribute.view_entity');

    // -> catalog   products
    Route::get('catalog/products', [CatalogController::class, 'index_products'])->name('vendor.products.index');
    Route::post('catalog/products/list', [CatalogController::class, 'list_products'])->name('vendor.products.list');
    Route::get('catalog/products/view_product/{id}', [CatalogController::class, 'list_product_view'])->name('vendor.products.view_products');
    Route::post('catalog/products/list/status/update', [CatalogController::class, 'list_products_status'])->name('vendor.products.list.status.update');
    Route::post('catalog/products/list/delete', [CatalogController::class, 'list_products_delete'])->name('vendor.products.list.delete');
    Route::get('catalog/products/create', [CatalogController::class, 'create_products'])->name('vendor.products.create');
    Route::post('catalog/products/store', [CatalogController::class, 'store_products'])->name('vendor.proucts.store_products');
    Route::get('catalog/products/edit/{id}', [CatalogController::class, 'product_edit'])->name('vendor.products.edit_product');
    Route::post('catalog/products/update', [CatalogController::class, 'update_product'])->name('vendor.products.update_products');

    //stocks routes
    Route::get('stocks/inventory', [StockController::class, 'index_inventory'])->name('vendor.inventory.index');
    Route::post('stocks/inventory/list', [StockController::class, 'list_inventory'])->name('vendor.products.list_inventory');
    Route::get('stocks/inventory/search', [StockController::class, 'search_inventory'])->name('vendor.inventory.search_inventory');
    Route::post('stocks/inventory/view_product_modal', [StockController::class, 'view_product_modal'])->name('vendor.inventory.view_product_modal');
    
    // list
    Route::get('stocks/inventory/add/{id}', [StockController::class, 'add_inventory'])->name('vendor.inventory.add_inventory');
    Route::post('stocks/inventory/add/view_image', [StockController::class, 'view_Image'])->name('vendor.inventory.add.view_image');
    Route::post('stocks/inventory/store', [StockController::class, 'store_inventory'])->name('vendor.inventory.store_inventory');
    // edit
    Route::get('stocks/inventory/edit/{id}', [StockController::class, 'edit_inventory'])->name('vendor.inventory.edit');
    Route::post('stocks/inventory/update', [StockController::class, 'update_inventory'])->name('vendor.inventory.update_inventory');
    // delete
    Route::post('stocks/inventory/list/delete', [StockController::class, 'list_inventory_delete'])->name('vendor.inventory.list.delete');
    // view
    Route::get('stocks/inventory/view_inventory/{id}', [StockController::class, 'view_inventory'])->name('vendor.inventory.view_inventory');

    Route::post('stocks/inventory/get_attributes', [StockController::class, 'get_attributes'])->name('vendor.inventory.get_attributes');


    //get_variant_inventory
    Route::post('stocks/inventory/get_modal_data', [StockController::class, 'get_modal_data'])->name('vendor.inventory.get_modal_data');
    Route::post('stocks/inventory/get_inventory', [StockController::class, 'get_variant_inventory'])->name('vendor.inventory.get_variant_inventory');
    Route::get('stocks/inventory/get_inventory/create', [StockController::class, 'get_variant_file'])->name('vendor.inventory.get_variant_file');
    Route::post('stocks/inventory/get_inventory/store', [StockController::class, 'store_inventory_with_variant'])->name('vendor.inventory.store_inventory_with_variant');
    // Route::get('stocks/inventory/get_inventory/create',[StockController::class,'create_inventory_with_variant'])->name('vendor.inventory.create_inventory_with_variant');
    Route::post('stocks/inventory/list_variant', [StockController::class, 'list_inventory_with_variant'])->name('vendor.inventory.list_inventory_with_variant');
    Route::post('stocks/inventory/get_edit_inventory', [StockController::class, 'get_edit_inventory'])->name('vendor.inventory.get_edit_inventory');
    Route::get('stocks/inventory/list_variant_edit', [StockController::class, 'edit_variant'])->name('vendor.inventory.list_variant_edit');
    Route::post('stocks/inventory/update_variant', [StockController::class, 'update_variant'])->name('vendor.inventory.update_variant');
    Route::get('stocks/inventory/view_variant/{id}', [StockController::class, 'view_variant'])->name('vendor.inventory.view_variant');
    Route::post('stocks/inventory/list_variant/delete', [StockController::class, 'list_variant_delete'])->name('vendor.inventory.list_variant.delete');

    // Vendor Coupons
    Route::get('coupon/add-new', [CouponController::class, 'add_coupon'])->name('vendor.coupon.addnew');
    Route::post('coupon/store', [CouponController::class, 'save_coupon'])->name('vendor.coupon.save');
    Route::get('coupon/list', [CouponController::class, 'coupon_list'])->name('vendor.coupon.list');
    Route::post('coupon/list-render', [CouponController::class, 'coupon_list_render'])->name('vendor.coupon.list.render');
    Route::post('vendor/all-applications-list-status-update', [CouponController::class, 'coupons_list_status_update'])->name('vendor.coupon.list.status.update');
    Route::get('coupon/edit/{id}', [CouponController::class, 'edit_coupon'])->name('vendor.coupon.edit');
    Route::post('coupon/update', [CouponController::class, 'update_coupon'])->name('vendor.coupon.update');
    Route::get('coupon/view/{id}', [CouponController::class, 'view_coupon'])->name('vendor.coupon.view');
    Route::post('coupon/delete', [CouponController::class, 'delete_coupon'])->name('vendor.coupon.delete');
    // Pending Coupons
    Route::get('coupon/pending-list', [CouponController::class, 'pending_coupon_list'])->name('vendor.coupon.pending.list');
    Route::post('coupon/pending-list-render', [CouponController::class, 'pending_coupon_list_render'])->name('vendor.coupon.list.pending.render');
    // Published Coupons
    Route::get('coupon/published-list', [CouponController::class, 'published_coupon_list'])->name('vendor.coupon.published.list');
    Route::post('coupon/published-list-render', [CouponController::class, 'published_coupon_list_render'])->name('vendor.coupon.list.published.render');


    // Carriers
    Route::get('shipping/carrier/add-new',  [CarrierController::class, 'add_new'])->name("vendor.carrier.add_new");
    Route::post('shipping/carrier/save',  [CarrierController::class, 'save'])->name("vendor.carrier.save");
    Route::get('shipping/carrier/list',  [CarrierController::class, 'list'])->name("vendor.carrier.list");
    Route::post('shipping/carrier/list_render',  [CarrierController::class, 'list_render'])->name("vendor.carrier.listrender");
    Route::post('shipping/carrier/status_update',  [CarrierController::class, 'list_status_update'])->name("vendor.carrier.status_update");
    Route::post('shipping/carrier/delete',  [CarrierController::class, 'delete'])->name("vendor.carrier.delete");
    Route::get('shipping/carrier/edit/{id}',  [CarrierController::class, 'edit'])->name("vendor.carrier.edit");
    Route::post('shipping/carrier/update',  [CarrierController::class, 'update'])->name("vendor.carrier.update");
    Route::get('shipping/carrier/view/{id}',  [CarrierController::class, 'view'])->name("vendor.carrier.view");
    Route::get('shipping/zone/status/update',  [CarrierController::class, 'zone_status_update'])->name("vendor.carrier.zone_status_update");
    // Route::post('shipping/carrier/zone/delete',  [CarrierController::class, 'shipping_zone_delete'])->name("vendor.carrier.shipping_zone_delete");

    // Shipping Zones
    Route::get('shipping/zones/add-new',  [ZoneController::class, 'add_new'])->name("vendor.zones.add_new");
    Route::post('shipping/zones/store',  [ZoneController::class, 'store'])->name("vendor.zones.store");
    Route::get('shipping/zones/index',  [ZoneController::class, 'index'])->name("vendor.zones.index");
    Route::post('shipping/zones/list', [ZoneController::class, 'list_zones'])->name('vendor.zones.list_zones');
    Route::get('shipping/zones/edit/{id}',  [ZoneController::class, 'edit'])->name("vendor.zones.edit");
    Route::post('shipping/zones/update',  [ZoneController::class, 'update'])->name("vendor.zones.update");
    Route::get('shipping/zones/view/{id}',  [ZoneController::class, 'view'])->name("vendor.zones.view");
    Route::post('shipping/zones/delete',  [ZoneController::class, 'delete'])->name("vendor.zones.delete");
    Route::post('shipping/zones/update_zone',  [ZoneController::class, 'update_zone'])->name("vendor.zones.update_zone");
    Route::post('shipping/zone/status/update',  [ZoneController::class, 'zone_status_update'])->name("vendor.carrier.zone_status_update_data");
    Route::post('shipping/carrier/zone/delete',  [ZoneController::class, 'shipping_zone_delete'])->name("vendor.carrier.shipping_zone_delete");
    Route::post('shipping/carrier/zone/delete/shipping',  [ZoneController::class, 'delete_shipping'])->name("vendor.carrier.delete_shipping");

    Route::post('shipping/zones/country/delete',  [ZoneController::class, 'delete_zone_country'])->name("vendor.zone-country.delete");
    Route::post('shipping/zones/states/edit',  [ZoneController::class, 'states_edit'])->name("vendor.zone-states.edit");
    Route::get('shipping/zones/states/search',  [ZoneController::class, 'states_search'])->name("vendor.zone.searchCountry");
    Route::post('shipping/zones/states/update',  [ZoneController::class, 'states_update'])->name("vendor.zone.stateUpdate");



    // get all states on basis of selected country
    Route::post('shipping/get-states',  [ZoneController::class, 'getStates'])->name("vendor.getStates");


    //Tax Route
    Route::get('settings/tax/create',  [TaxController::class, 'create'])->name("vendor.settings.tax.create");
    Route::post('settings/tax/store',  [TaxController::class, 'store'])->name("vendor.settings.tax.store");
    Route::get('settings/tax/get-states/{countryId}',  [TaxController::class, 'get_states'])->name("vendor.settings.tax.get_states");
    Route::get('settings/tax/index',  [TaxController::class, 'tax_index'])->name("vendor.settings.tax.index");
    Route::post('settings/tax/list',  [TaxController::class, 'list_tax'])->name("vendor.settings.tax.list");
    Route::get('settings/tax/edit/{id}',  [TaxController::class, 'edit'])->name("vendor.settings.tax.edit");
    Route::post('settings/tax/update',  [TaxController::class, 'update'])->name("vendor.settings.tax.update");
    Route::get('settings/tax/view/{id}',  [TaxController::class, 'view'])->name("vendor.settings.tax.view");
    Route::post('settings/tax/delete',  [TaxController::class, 'delete'])->name("vendor.settings.tax.delete");

    //shipping rates 
    Route::get('/shipping/rates/index', [CarrierController::class, 'index_rate'])->name('vendor.shipping.rates');
    Route::get('/shipping/rates/create', [CarrierController::class, 'create_rate'])->name('vendor.shipping.rates.create');
    Route::post('/shipping/rates/store', [CarrierController::class, 'store_rate'])->name('vendor.shipping.rates.store');
    Route::post('/shipping/rates/list', [CarrierController::class, 'list_rate'])->name('vendor.shipping.list_rate');
    Route::get('/shipping/rates/edit/{id}', [CarrierController::class, 'edit_rate'])->name('vendor.shipping.rates.edit');
    Route::post('/shipping/rates/update', [CarrierController::class, 'update_rate'])->name('vendor.shipping.rates.update');
    Route::get('/shipping/rates/view/{id}', [CarrierController::class, 'view_rate'])->name('vendor.shipping.rates.view');
    Route::post('/shipping/rates/delete', [CarrierController::class, 'delete_rate'])->name('vendor.shipping.rates.delete');

    //shops 
    Route::get('/setting/shops/create', [SettingController::class, 'create_shop'])->name('vendor.settings.shops.create');
    Route::post('/setting/shops/store', [SettingController::class, 'store_shop'])->name('vendor.setting.shops.store');
    Route::post('/setting/shops/address_update', [SettingController::class, 'address_update'])->name('vendor.setting.shops.address_update');
    Route::post('/setting/shops/bank_update', [SettingController::class, 'bank_update'])->name('vendor.setting.shops.bank_update');

    Route::post('/setting/shops/maintenance_mode', [SettingController::class, 'update_maintenance_mode'])->name('vendor.update_maintenance_mode');

    //wishlist
    Route::get('/wishlisted', [WishlistController::class, 'index'])->name('vendor.wishlist.index')->middleware('translate');
    Route::post('/wishlisted/list', [WishlistController::class, 'list_wishlist'])->name('vendor.wishlist.list');
    Route::get('/wishlisted/view/{id}', [WishlistController::class, 'view_wishlist'])->name('vendor.wishlist.view');

    //carts
    Route::get('/customer/cart', [WishlistController::class, 'index_cart'])->name('vendor.carts.index')->middleware('translate');
    Route::post('/customer/cart/list', [WishlistController::class, 'list_cart'])->name('vendor.carts.list');
    Route::get('/customer/cart/{id}', [WishlistController::class, 'view_carts'])->name('vendor.carts.view');
    Route::post('/customer/cart/delete', [WishlistController::class, 'cart_delete'])->name('vendor.cart.delete');

    Route::get('/cancellation/index', [
        OrderController::class,
        'index_cancellation',
    ])->name('vendor.cancel')->middleware('translate');


    Route::post('/cancellation/list', [
        OrderController::class,
        'cancellation_list',
    ])->name('vendor.cancellation.list');

    Route::post('/cancellation/approved', [
        OrderController::class,
        'cancellation_approved',
    ])->name('vendor.cancellation.approved');


    // Route::get('/customer/cart/search', [WishlistController::class, 'search_customer'])->name('vendor.carts.search_customer');
    // Route::post('/customer/cart/customer/', [WishlistController::class, 'get_customer'])->name('vendor.carts.get_customer');
    // Route::get('/customer/cart/customer/carts', [WishlistController::class, 'showCustomerPage'])->name('vendor.carts.get_customer');

    // currency Conversion
    Route::get('/currency-conversion', [WalletController::class, 'currencyConversion'])->name('vendor.currency.index');
    Route::post('/post-currency-conversion', [WalletController::class, 'postCurrencyConversion'])->name('vendor.currency.post');


    // Customer 
    Route::get('/all-customer', [ReportsController::class, 'AllCustomer'])->name('vendor.customer.index')->middleware('translate');
    Route::post('/create-customer', [ReportsController::class, 'createCustomer'])->name('vendor.customer.create');
    Route::post('/edit-customer', [ReportsController::class, 'editCustomer'])->name('vendor.customer.edit');
    Route::post('/delete-customer', [ReportsController::class, 'deleteCustomer'])->name('vendor.customer.delete');
    Route::post('/customer-change-password', [ReportsController::class , 'ChangePassword'])->name('vendor.customer.passwordChange');

    
    // add order 
    Route::any('/add-order', [OrderController::class, 'addOrder'])->name('vendor.order.add');
    Route::post('/add-custom-order', [OrderController::class, 'addCustomOrder'])->name('vendor.customOrder.place');
    Route::post('/update-customer-address', [OrderController::class, 'updateCustomerAddress'])->name('vendor.order.customerAddress');
    Route::post('/get-products', [OrderController::class, 'getProducts'])->name('vendor.order.getProducts');
    Route::post('/order-place', [OrderController::class, 'orderPlace'])->name('vendor.order.place');
    Route::get('/all-custom-orders', [OrderController::class, 'customOrders'])->name('vendor.order.custom');
    Route::post('/all-custom-listing', [OrderController::class, 'postAllCustomOrder'])->name('vendor.custom_order_listing');


    // order 
    Route::get('/all-order', [OrderController::class, 'allOrder'])->name('vendor.order.index');
    Route::post('/all-order-listing', [OrderController::class, 'postAllOrder'])->name('vendor.order_listing');
    
    Route::get('/all-order/show_product_detail/{id}', [OrderController::class, 'show_product_detail'])->name('vendor.order.show_product_detail')->middleware('checkOrder');
    Route::post('/all-order/show_product_detail/payment_status', [OrderController::class, 'payment_status'])->name('vendor.order.payment_status');
    Route::post('/all-order/show_product_detail/order_status', [OrderController::class, 'order_status'])->name('vendor.order.order_status');
    Route::post('/all-order/show_product_detail/send_message', [OrderController::class, 'send_message'])->name('vendor.order.send_message');
    Route::get('/all-order/show_product_detail/customer_invoice/{id}', [OrderController::class, 'customer_invoice'])->name('vendor.order.customer_invoice');
    // Route::post('/send-email', 'MailController@sendEmail');  
    Route::post('/all-order/show_product_detail/full_fill', [OrderController::class, 'full_fill'])->name('vendor.order.full_fill');

    // invoic
    Route::get('/invoice/{id}',[OrderController::class,'invoice'])->name('invoice');

    // Wallet 
    Route::get('/my-Wallet', [WalletController::class, 'myWallet'])->name('vendor.wallet.index');
    Route::get('/deposite-fund',[WalletController::class,'depositeFund'])->name('vendor.wallet.fund');
    Route::Post('/verify-payment',[WalletController::class,'verify'])->name('vendor.payment.verify');
    Route::Post('/tranfer-amount',[WalletController::class,'transferAmount'])->name('vendor.wallet.transfer');
    Route::Post('/amount-withdrawal',[WalletController::class,'moneywithdrawal'])->name('vendor.wallet.withdrawal');
    Route::get('/all-transaction', [WalletController::class, 'alltransaction'])->name('vendor.wallet.transaction');

    
    //  notification
    Route::get('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('vendor.markRead');
    Route::get('/all-clear', [NotificationController::class, 'allMarkAsRead'])->name('vendor.allMarkAsRead');
    Route::get('/view-all', [NotificationController::class, 'viewAll'])->name('vendor.viewAll');



    //twillio
    Route::get('/send-sms', [TwilioController::class, 'get_send_sms'])->name('twilio.get.sendsms');
    Route::post('/send-sms/send', [TwilioController::class, 'send_sms'])->name('twilio.sendsms');

    //disputes
    Route::get('/disputes', [SupportController::class,'index_disputes'])->name('vendor.disputes.index');
    Route::post('/disputes/list', [SupportController::class,'list_disputes'])->name('vendor.disputes.list');
    Route::post('/disputes/list/show', [SupportController::class,'list_disputes_show'])->name('vendor.disputes.list_disputes_show');
    Route::get('/disputes/reply/{id}', [SupportController::class,'reply_disputes'])->name('vendor.disputes.reply');
    Route::post('/disputes/reply/store', [SupportController::class,'reply_store_disputes'])->name('vendor.disputes.reply.store');
    Route::post('/disputes/list/show', [SupportController::class,'list_disputes_show'])->name('vendor.disputes.list_disputes_show');
    Route::get('/disputes/view/{id}', [SupportController::class,'view_disputes'])->name('vendor.disputes.view');
    // message
    Route::get('/message_index',[SupportController::class,'message_index'])->name('vendor.message.message_index');
    Route::post('/message_data',[SupportController::class,'message_data'])->name('vendor.message.message_data');
    Route::get('/sent-message',[SupportController::class,'sent_message'])->name('vendor.message.sent_message');
    Route::post('/message-data-sent',[SupportController::class,'message_data_sent'])->name('vendor.message.message_data_sent');
    Route::get('/draft-message',[SupportController::class,'draft_message'])->name('vendor.message.draft_message');
    Route::post('/message-data-draft',[SupportController::class,'message_data_draft'])->name('vendor.message.message_data_draft');
    Route::post('/message-save-draft',[SupportController::class,'message_saved_as_draft'])->name('vendor.message.save-as-draft');
    Route::get('/spams-message',[SupportController::class,'spams_message'])->name('vendor.message.spams_message');
    Route::post('/message_data_spam',[SupportController::class,'message_data_spam'])->name('vendor.message.message_data_spam');

    // spacification
    Route::get('/specifications',[SupportController::class,'specifications'])->name('vendor.specifications');
    Route::post('/specifications_data',[SupportController::class,'specifications_data'])->name('vendor.specifications.specifications_data');
    Route::get('/spacification_edit',[SupportController::class,'spacification_edit'])->name('vendor.specifications.spacification_edit');
    Route::post('/update_specification',[SupportController::class,'update_specification'])->name('vendor.specifications.update_specification');
    Route::post('/add_specification',[SupportController::class,'add_specification'])->name('vendor.specifications.add_specification');
    Route::post('/delete_specification',[SupportController::class,'delete_specification'])->name('vendor.specifications.delete_specification');

    // return
    Route::get('/return_cancellation',[SupportController::class,'return_cancellation'])->name('vendor.return_cancellation');
    Route::post('/return_cancellation_data',[SupportController::class,'return_cancellation_data'])->name('vendor.return_cancellation_data');
    Route::get('/return_policy_edit',[SupportController::class,'return_policy_edit'])->name('vendor.return_policy_edit');
    Route::post('/update_return_policy',[SupportController::class,'update_return_policy'])->name('vendor.update_return_policy');
    Route::post('/return_policy_add',[SupportController::class,'return_policy_add'])->name('vendor.return_policy_add');
    Route::post('/delete_return_policy',[SupportController::class,'delete_return_policy'])->name('vendor.delete_return_policy');

    Route::get('/trash-message',[SupportController::class,'trash_message'])->name('vendor.message.trash_message');
    Route::post('/trash-trash_message_data_spam',[SupportController::class,'trash_message_data_spam'])->name('vendor.message.trash_message_data_spam');
    Route::post('/trash-spams-data',[SupportController::class,'spams_data'])->name('vendor.message.spams_data');
    Route::post('/composer_data_send_save',[SupportController::class,'composer_data_send_save'])->name('vendor.message.composer_data_send_save');
    Route::post('/email-template',[SupportController::class,'email_template'])->name('vendor.message.email_template');
    Route::post('/email-templatet-draft',[SupportController::class,'email_template_draft'])->name('vendor.message.email_template_draft');

    //   new compose message
     Route::get('/compose_new_message',[SupportController::class,'compose_new_message'])->name('vendor.message.compose_new_message');
    Route::post('/trash_data',[SupportController::class,'trash_data'])->name('vendor.message.trash_data');
    Route::post('/sent_spams_data',[SupportController::class,'sent_spams_data'])->name('vendor.message.sent_spams_data');
    Route::post('/sent_trash_data',[SupportController::class,'sent_trash_data'])->name('vendor.message.sent_trash_data');
    Route::post('/draft_spams_data',[SupportController::class,'draft_spams_data'])->name('vendor.message.draft_spams_data');
    Route::post('/draft_trash_data',[SupportController::class,'draft_trash_data'])->name('vendor.message.draft_trash_data');
    Route::post('/spams_spams_data',[SupportController::class,'spams_spams_data'])->name('vendor.message.spams_spams_data');
    Route::post('/spam_data_delete',[SupportController::class,'spam_data_delete'])->name('vendor.message.spam_data_delete');
    Route::post('/trash_spams_data',[SupportController::class,'trash_spams_data'])->name('vendor.message.trash_spams_data');
    Route::post('/composer_data_send',[SupportController::class,'composer_data_send'])->name('vendor.message.composer_data_send');
    //reports
    Route::get('/reports/performance', [ReportsController::class,'index_performance'])->name('vendor.performance.index');
    
    //reviews
    Route::get('/products/reviews', [ReviewController::class,'index'])->name('vendor.reviews.index');
    Route::post('/products/reviews/list',  [ReviewController::class, 'list_reviews'])->name('vendor.reviews.list');
    Route::get('/products/reviews/view/{id}',  [ReviewController::class, 'reviews_view'])->name('vendor.reviews.view');
    //invoices
    Route::get('/invoices', [InvoiceController::class,'invoice_index'])->name('vendor.invoice.index');
    Route::post('/invoice/list',  [InvoiceController::class, 'list_invoice'])->name('vendor.invoice.list');
    Route::get('/invoices/{id}', [InvoiceController::class,'invoice_view'])->name('vendor.invoice.view');
    // Route::get('/invoices/view/{id}', [InvoicesController::class)


    // Payout Detail Route 
    Route::get('payout-detail', [ReportsController::class, 'payout_detail'])->name('vendor.payoutDetail');
    Route::post('post-payout-detail', [ReportsController::class, 'post_payout_detail'])->name('vendor.postPayoutDetail');
});
