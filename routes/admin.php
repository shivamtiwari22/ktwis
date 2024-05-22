<?php

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactUsers;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\Settings\BusinessAreaController;
use App\Http\Controllers\Admin\Settings\ChargesController;
use App\Http\Controllers\Admin\Settings\CommisionController;
use App\Http\Controllers\Admin\Settings\CurrencyController;
use App\Http\Controllers\Admin\Settings\LanguageController;
use App\Http\Controllers\Admin\Site\SliderManagementController;
use App\Http\Controllers\Admin\Site\AppearanceController;
use App\Http\Controllers\Admin\Site\NotificationController ;
use App\Http\Controllers\Admin\Site\ProductTypeController;
use App\Http\Controllers\Admin\Site\WalletController;
use App\Http\Controllers\Admin\Site\TestimonialController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\Vendor\VendorController;
use App\Http\Controllers\SupportTicketReplyController;
use App\Http\Controllers\Vendor\CouponController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('login_submit', [AuthController::class, 'login_submit'])->name(
    'admin.login_submit'
);
//Forgot Password
Route::get('/forgot-password', [
    AuthController::class,
    'forgot_password',
])->name('admin.forgot.password');
Route::post('/send_email', [AuthController::class, 'send_mail'])->name(
    'admin.email'
);

  // get all states on basis of selected country
  Route::post('get-states',  [VendorController::class, 'getStates'])->name("admin.getStates");

Route::middleware(['admin_middleware'])->group(function () {
    // Dashboard Route
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name(
        'dashboard'
    );

    // system setting 
    Route::get('system_setting', [
        HomeController::class,
        'system_setting',
    ])->name('admin.system_setting');
    Route::post('update_system_setting', [
        HomeController::class,
        'update_system_setting',
    ])->name('admin.update_system_setting');


    // Global Settings 
    Route::get('global_setting', [HomeController::class,  'global_setting',])->name('admin.global_setting');
    Route::post('update_global_setting', [HomeController::class,'update_global_setting',])->name('admin.update_global_setting');


    // dispute text 
    Route::get('dispute_text', [
        HomeController::class,
        'dispute_text',
    ])->name('admin.dispute_text');

    Route::post('update_dispute_text', [
        HomeController::class,
        'update_dispute_text',
    ])->name('admin.update_dispute_text');


    Route::post('/home', 'HomeController@createChat')->name('home.createChat');
    //profile
    Route::get('/profile', [HomeController::class, 'profile_edit'])->name(
        'admin.profile.edit'
    );
    Route::post('/profile/store', [
        HomeController::class,
        'profile_store',
    ])->name('admin.profile.update');
    //logout
    Route::get('/logout', [AuthController::class, 'logout'])->name(
        'admin.logout'
    );

    //change password
    Route::get('/change-password', [
        AuthController::class,
        'change_password',
    ])->name('admin.change.password');
    Route::post('/change-password/store', [
        AuthController::class,
        'change_password_store',
    ])->name('admin.change.password.store');

    // Add category Page Routes
    Route::get('/categories/create', [
        CategoryController::class,
        'create',
    ])->name('admin.categories.create');
    Route::post('/categories/store', [
        CategoryController::class,
        'store',
    ])->name('admin.categories.store');
    Route::post('/categories/get-parent', [
        CategoryController::class,
        'get_parent_category',
    ])->name('admin.categories.get_parent_category');
    Route::post('/categories/get-child', [
        CategoryController::class,
        'get_child_category',
    ])->name('admin.categories.get_child_category');
    Route::post('/categories/search-category', [
        CategoryController::class,
        'searchCategory',
    ])->name('admin.categories.searchCategory');

    // Category List Page Routes
    Route::get('/categories/list', [CategoryController::class, 'list'])->name(
        'admin.categories.list'
    );
    Route::post('/categories/list/render', [
        CategoryController::class,
        'list_render',
    ])->name('admin.categories.list_render');
    Route::post('categories/list/delete', [
        CategoryController::class,
        'delete_list',
    ])->name('admin.categories.list_delete');

    // Category Edit Page Routes
    Route::get('categories/edit/{id}', [
        CategoryController::class,
        'edit',
    ])->name('admin.categories.edit');
    Route::post('categories/update/', [
        CategoryController::class,
        'update',
    ])->name('admin.categories.update');

    // Category View Page Routes
    Route::get('categories/view/{id}', [
        CategoryController::class,
        'view',
    ])->name('admin.categories.view');

    // Vendor Application Routes

    Route::post('vendor/add-vendor', [
        VendorController::class,
        'add_vendor',
    ])->name('admin.vendor.applications.add.vendor');

    Route::get('vendor/all-applications', [
        VendorController::class,
        'vendor_applications',
    ])->name('admin.vendor.applications');
    Route::post('vendor/all-applications-list', [
        VendorController::class,
        'vendor_applications_list',
    ])->name('admin.vendor.applications.list');
    Route::post('vendor/status-update', [
        VendorController::class,
        'vendor_applications_list_update_status',
    ])->name('admin.vendor.applications.list.status.update');

    Route::post('vendor/all-applications-list-delete', [
        VendorController::class,
        'vendor_applications_list_delete',
    ])->name('admin.vendor.applications.list.delete');
    Route::get('vendor/view-applications/{id}', [
        VendorController::class,
        'vendor_view_applications',
    ])->name('admin.vendor.applications.view.application');
    Route::get('vendor/edit-applications/{id}', [
        VendorController::class,
        'vendor_edit_applications',
    ])->name('admin.vendor.applications.edit.application');


    Route::post('vendor/update-applications', [
        VendorController::class,
        'vendor_update_applications',
    ])->name('admin.vendor.applications.update.application');

    Route::get('vendor/edit-address/{id}', [
        VendorController::class,
        'vendor_edit_address',
    ])->name('admin.vendor.applications.edit.address');

    Route::post('vendor/update-address', [
        VendorController::class,
        'vendor_update_address',
    ])->name('admin.vendor.applications.update.address');


    // Pending vender Application Routes
    Route::get('vendor/rejected-applications', [
        VendorController::class,
        'vendor_rejected_applications',
    ])->name('admin.vendor.rejected.applications');
    Route::post('vendor/rejected-applications-list', [
        VendorController::class,
        'vendor_rejected_applications_list',
    ])->name('admin.vendor.rejected.applications.list');

    // Pending vender Application Routes
    Route::get('vendor/pending-applications', [
        VendorController::class,
        'vendor_pending_applications',
    ])->name('admin.vendor.pending.applications');
    Route::post('vendor/pending-applications-list', [
        VendorController::class,
        'vendor_pending_applications_list',
    ])->name('admin.vendor.pending.applications.list');

    // publish vender Application Routes
    Route::get('vendor/publish-applications', [
        VendorController::class,
        'vendor_publish_applications',
    ])->name('admin.vendor.publish.applications');
    Route::post('vendor/publish-applications-list', [
        VendorController::class,
        'vendor_publish_applications_list',
    ])->name('admin.vendor.publish.applications.list');

    // Contact Table Routes
    Route::get('contact/user/list', [
        ContactUsers::class,
        'contect_user_list',
    ])->name('contact.user.list');
    Route::post('contact/user/list/render', [
        ContactUsers::class,
        'contect_user_list_render',
    ])->name('contact.user.list.render');

    // Currencies
    Route::get('currencies/add-new', [
        CurrencyController::class,
        'add_currency',
    ])->name('currencies.addnew');
    Route::post('currencies/save', [
        CurrencyController::class,
        'save_currency',
    ])->name('currencies.save');
    Route::get('currencies/list', [
        CurrencyController::class,
        'currency_list',
    ])->name('currencies.list');
    Route::post('currencies/list-render', [
        CurrencyController::class,
        'list_render',
    ])->name('currencies.list.render');
    Route::get('currencies/edit/{id}', [
        CurrencyController::class,
        'edit',
    ])->name('currencies.edit');
    Route::post('currencies/update', [
        CurrencyController::class,
        'update',
    ])->name('currencies.update');
    Route::get('currencies/view/{id}', [
        CurrencyController::class,
        'view',
    ])->name('currencies.view');
    Route::post('currencies/delete', [
        CurrencyController::class,
        'delete',
    ])->name('currencies.delete');

    Route::post('currencies/status/update', [
        CurrencyController::class,
        'currency_status_update',
    ])->name('admin.currencies.status_update_data');

    // Business Area
    Route::get('business/add-new', [
        BusinessAreaController::class,
        'add_business_area',
    ])->name('business_area.addnew');
    Route::post('business/save', [
        BusinessAreaController::class,
        'save_business_area',
    ])->name('business_area.save');
    Route::get('business/list', [BusinessAreaController::class, 'list'])->name(
        'business.list'
    );
    Route::post('business/list-render', [
        BusinessAreaController::class,
        'list_render',
    ])->name('business.list.render');
    Route::post('business/status-change', [
        BusinessAreaController::class,
        'status_change',
    ])->name('business.stausChange');
    Route::post('business/delete', [
        BusinessAreaController::class,
        'delete',
    ])->name('business.delete');
    Route::get('business/view/{id}', [
        BusinessAreaController::class,
        'view_business_area',
    ])->name('business_area.view');
    Route::get('business/edit/{id}', [
        BusinessAreaController::class,
        'edit_business_area',
    ])->name('business_area.edit');
    Route::post('business/update', [
        BusinessAreaController::class,
        'update',
    ])->name('business.update');
    Route::post('get-state', [BusinessAreaController::class, 'getState'])->name(
        'vendor.getstate'
    );

    // Commisions
    Route::get('commision/add-new', [
        CommisionController::class,
        'create',
    ])->name('commision.create');
    Route::post('commision/store', [CommisionController::class, 'store'])->name(
        'commision.store'
    );
    Route::get('commision/list', [CommisionController::class, 'list'])->name(
        'commision.list'
    );
    Route::post('commision/list-render', [
        CommisionController::class,
        'list_render',
    ])->name('commision.list.render');
    Route::post('commision/status-change', [
        CommisionController::class,
        'status_change',
    ])->name('commision.stausChange');
    Route::get('commision/view/{id}', [
        CommisionController::class,
        'view',
    ])->name('commision.view');
    Route::get('commision/edit/{id}', [
        CommisionController::class,
        'edit',
    ])->name('commision.edit');
    Route::post('commision/update', [
        CommisionController::class,
        'update',
    ])->name('commision.update');
    Route::post('commision/delete', [
        CommisionController::class,
        'delete',
    ])->name('commisiom.delete');

    // Languages
    Route::get('languages/add-new', [
        LanguageController::class,
        'create',
    ])->name('language.create');
    Route::post('languages/store', [LanguageController::class, 'store'])->name(
        'language.store'
    );
    Route::get('languages/list', [LanguageController::class, 'list'])->name(
        'languages.list'
    );
    Route::post('languages/list-render', [
        LanguageController::class,
        'list_render',
    ])->name('languages.list.render');
    Route::post('languages/status-change', [
        LanguageController::class,
        'status_change',
    ])->name('languages.stausChange');
    Route::post('languages/delete', [
        LanguageController::class,
        'delete',
    ])->name('languages.delete');
    Route::get('languages/view/{id}', [
        LanguageController::class,
        'view',
    ])->name('languages.view');
    Route::get('languages/edit/{id}', [
        LanguageController::class,
        'edit',
    ])->name('languages.edit');
    Route::post('languages/update', [
        LanguageController::class,
        'update',
    ])->name('languages.update');

    //pages
    Route::get('/appereance/pages/index', [
        AppearanceController::class,
        'index_pages',
    ])->name('admin.appereance.pages');
    Route::get('/appereance/create_pages', [
        AppearanceController::class,
        'create_pages',
    ])->name('admin.appereance.create_pages');
    Route::post('/appereance/store_pages', [
        AppearanceController::class,
        'store_pages',
    ])->name('admin.appereance.store_pages');
    Route::post('/appereance/list_pages', [
        AppearanceController::class,
        'list_pages',
    ])->name('admin.appereance.list_pages');
    Route::get('/appereance/edit_pages/{id}', [
        AppearanceController::class,
        'edit_pages',
    ])->name('admin.appereance.edit_pages');
    Route::post('/appereance/update_pages/', [
        AppearanceController::class,
        'update_pages',
    ])->name('admin.appereance.update_pages');
    Route::get('/appereance/view_pages/{id}', [
        AppearanceController::class,
        'view_pages',
    ])->name('admin.appereance.view_pages');
    Route::post('/appereance/page/delete', [
        AppearanceController::class,
        'delete_page',
    ])->name('admin.appereance.delete_page');
    //blogs
    Route::get('/appereance/blogs/index', [
        AppearanceController::class,
        'index_blogs',
    ])->name('admin.appereance.blogs');
    Route::get('/appereance/create_blogs', [
        AppearanceController::class,
        'create_blogs',
    ])->name('admin.appereance.create_blogs');
    Route::post('/appereance/store_blogs', [
        AppearanceController::class,
        'store_blogs',
    ])->name('admin.appereance.store_blogs');
    Route::post('/appereance/list_blogs', [
        AppearanceController::class,
        'list_blogs',
    ])->name('admin.appereance.list_blogs');
    Route::get('/appereance/edit_blogs/{id}', [
        AppearanceController::class,
        'edit_blogs',
    ])->name('admin.appereance.edit_blogs');
    Route::post('/appereance/update_blogs/', [
        AppearanceController::class,
        'update_blogs',
    ])->name('admin.appereance.update_blogs');
    Route::get('/appereance/view_blogs/{id}', [
        AppearanceController::class,
        'view_blogs',
    ])->name('admin.appereance.view_blogs');
    Route::post('/appereance/blogs/delete', [
        AppearanceController::class,
        'delete_blogs',
    ])->name('admin.appereance.delete_blogs');

    // Slider Management
    Route::get('/slider/create', [
        SliderManagementController::class,
        'create',
    ])->name('slider.create');
    Route::post('/slider/store', [
        SliderManagementController::class,
        'store',
    ])->name('slider.store');
    Route::get('/slider/list', [
        SliderManagementController::class,
        'list',
    ])->name('slider.list');
    Route::post('/slider/list-rendor', [
        SliderManagementController::class,
        'list_render',
    ])->name('slider.list.rendor');
    Route::post('slider/delete', [
        SliderManagementController::class,
        'delete',
    ])->name('slider.delete');
    Route::get('/slider/edit/{id}', [
        SliderManagementController::class,
        'edit',
    ])->name('slider.edit');
    Route::post('/slider/update', [
        SliderManagementController::class,
        'update',
    ])->name('slider.update');
    // email templates
    Route::get('/appereance/templates', [
        AppearanceController::class,
        'index_templates',
    ])->name('admin.appereance.templates');
    Route::get('/appereance/create_templates', [
        AppearanceController::class,
        'create_templates',
    ])->name('admin.appereance.create_templates');
    Route::post('/appereance/store_templates', [
        AppearanceController::class,
        'store_templates',
    ])->name('admin.appereance.store_templates');
    Route::post('/appereance/list_templates', [
        AppearanceController::class,
        'list_templates',
    ])->name('admin.appereance.list_templates');
    Route::get('/appereance/edit_templates/{id}', [
        AppearanceController::class,
        'edit_templates',
    ])->name('admin.appereance.edit_templates');
    Route::post('/appereance/update_templates/', [
        AppearanceController::class,
        'update_templates',
    ])->name('admin.appereance.update_templates');
    Route::get('/appereance/view_templates/{id}', [
        AppearanceController::class,
        'view_templates',
    ])->name('admin.appereance.view_templates');
    Route::post('/appereance/templates/delete', [
        AppearanceController::class,
        'delete_templates',
    ])->name('admin.appereance.delete_templates');
    //faqs_topics
    Route::get('/appereance/faq', [
        AppearanceController::class,
        'index_faq',
    ])->name('admin.appereance.faq');
    Route::post('/appereance/store_faq_topic', [
        AppearanceController::class,
        'store_faq_topic',
    ])->name('admin.appereance.store_faq_topic');
    Route::post('/appereance/list_faq_topic', [
        AppearanceController::class,
        'list_faq_topic',
    ])->name('admin.appereance.list_faq_topic');
    Route::get('/appereance/edit_faq_topic/{id}', [
        AppearanceController::class,
        'edit_faq_topic',
    ])->name('admin.appereance.edit_faq_topic');
    Route::post('/appereance/update_faq_topic', [
        AppearanceController::class,
        'update_faq_topic',
    ])->name('admin.appereance.update_faq_topic');
    Route::post('/appereance/faq_topic/delete', [
        AppearanceController::class,
        'delete_topics',
    ])->name('admin.appereance.delete_topics');
    //faq
    Route::post('/appereance/faq/store_faq', [
        AppearanceController::class,
        'store_faq',
    ])->name('admin.appereance.store_faq');
    Route::post('/appereance/faq/list_faq', [
        AppearanceController::class,
        'list_faq',
    ])->name('admin.appereance.list_faq');
    Route::get('/appereance/faq/edit_faq/{id}', [
        AppearanceController::class,
        'edit_faq',
    ])->name('admin.appereance.edit_faq');
    Route::post('/appereance/faq/update_faq', [
        AppearanceController::class,
        'update_faq',
    ])->name('admin.appereance.update_faq');
    Route::post('/appereance/faq/delete_faq', [
        AppearanceController::class,
        'delete_faq',
    ])->name('admin.appereance.delete_faq');

    //Special products
    Route::get('/producttype/index', [
        ProductTypeController::class,
        'index_type',
    ])->name('admin.producttype.index');
    Route::get('/producttype/create', [
        ProductTypeController::class,
        'create_type',
    ])->name('admin.producttype.create');
    Route::post('/producttype/list_product_form', [
        ProductTypeController::class,
        'list_product_form',
    ])->name('admin.producttype.list_product_form');
    Route::post('/producttype/store_product_type', [
        ProductTypeController::class,
        'store_product_type',
    ])->name('admin.producttype.store_product_type');
    Route::post('/producttype/list_product_type', [
        ProductTypeController::class,
        'list_product_type',
    ])->name('admin.producttype.list_product_type');
    Route::get('/producttype/edit_product_type/{id}', [
        ProductTypeController::class,
        'edit_product_type',
    ])->name('admin.producttype.edit_product_type');
    Route::post('/producttype/delete_product_type', [
        ProductTypeController::class,
        'deleteproducttype',
    ])->name('admin.producttype.deleteproducttype');

    //  Wallet
    Route::get('/my-Wallet', [WalletController::class, 'myWallet'])->name(
        'admin.wallet.index'
    );
    Route::get('/all-transaction', [
        WalletController::class,
        'alltransaction',
    ])->name('admin.wallet.transaction');
    Route::post('/change-status', [
        WalletController::class,
        'changeStatus',
    ])->name('admin.wallet.status');

    // charges
    Route::get('/charges', [ChargesController::class, 'charges'])->name(
        'admin.setting.charges'
    );
    Route::post('/set-charges', [ChargesController::class, 'setCharges'])->name(
        'admin.setting.setCharges'
    );

    //orders
    Route::get('/order/index', [
        AdminOrderController::class,
        'index_order',
    ])->name('admin.order');
    Route::post('/order/list', [
        AdminOrderController::class,
        'list_order',
    ])->name('admin.order.list');

    // customer invoice 
    Route::get('/order/customer_invoice/{id}', [AdminOrderController::class, 'customer_invoice'])->name('admin.order.customer_invoice');


    Route::get('/order/list/view/{id}', [
        AdminOrderController::class,
        'view_order',
    ])->name('admin.carts.view_order');
    Route::post('/order/list/payment_status', [
        AdminOrderController::class,
        'payment_status',
    ])->name('admin.carts.payment_status');
    Route::post('/order/list/update_status', [
        AdminOrderController::class,
        'update_status',
    ])->name('admin.carts.update_status');

    //wishlist
    Route::get('/wishlist/index', [
        AdminOrderController::class,
        'index_wishlist',
    ])->name('admin.wishlist');
    Route::post('/wishlist/list', [
        AdminOrderController::class,
        'list_wishlist',
    ])->name('admin.wishlist.list');
    Route::get('/wishlist/list/view/{id}', [
        AdminOrderController::class,
        'view_wishlist',
    ])->name('admin.wishlist.view_wishlist');
    Route::get('/all-order/show_product_detail_admin/{id}', [
        AdminOrderController::class,
        'show_product_detail_admin',
    ])->name('admin.order.show_product_detail_admin');

    //carts
    Route::get('/carts/index', [
        AdminOrderController::class,
        'index_cart',
    ])->name('admin.cart');
    Route::post('/carts/list', [
        AdminOrderController::class,
        'list_cart',
    ])->name('admin.carts.list_cart');
    Route::get('/carts/list/view/{id}', [
        AdminOrderController::class,
        'view_carts',
    ])->name('admin.carts.view_carts');


    //  Contact 
    Route::get('/contact_us', [AppearanceController::class, 'contact_us'])->name(
        'admin.appereance.contact_us'
    );

    // Dynamic content 
    Route::get('/dynamic-Content', [AppearanceController::class, 'dynamicContent'])->name(
        'admin.appereance.content'
    );
    Route::get('/add-Content', [AppearanceController::class, 'addContent'])->name(
        'admin.appereance.addContent'
    );

    Route::post('/post-Content', [AppearanceController::class, 'PostContent'])->name(
        'admin.appereance.postContent'
    );

    Route::get('/edit-Content/{id}', [AppearanceController::class, 'editContent'])->name(
        'admin.appereance.editContent'
    );
    Route::post('/update-Content', [AppearanceController::class, 'updateContent'])->name(
        'admin.appereance.updateContent'
    );

    Route::post('/delete-Content', [AppearanceController::class, 'deleteContent'])->name(
        'admin.appereance.deleteContent'
    );

    Route::post('/update_contact_us', [AppearanceController::class, 'update_contact_us'])->name(
        'admin.appereance.update_contact_us'
    );

    //disputes
    Route::get('/disputes', [SupportController::class, 'index_disputes'])->name(
        'admin.disputes.index'
    );
    Route::post('/disputes/list', [
        SupportController::class,
        'list_disputes',
    ])->name('admin.disputes.list');
    Route::get('/disputes/reply/{id}', [
        SupportController::class,
        'reply_disputes',
    ])->name('admin.disputes.reply');
    Route::post('/disputes/reply/store', [
        SupportController::class,
        'reply_store_disputes',
    ])->name('admin.disputes.reply.store');
    Route::get('/disputes/view/{id}', [
        SupportController::class,
        'view_disputes',
    ])->name('admin.disputes.view');
    Route::post('/disputes/close', [
        SupportController::class,
        'list_disputes_close',
    ])->name('admin.disputes.close');
    Route::get('/disputes/order_details/{id}', [
        SupportController::class,
        'order_details',
    ])->name('admin.disputes.order_details');
    Route::post('/disputes/add_amin_note', [
        SupportController::class,
        'add_amin_note',
    ])->name('admin.disputes.add_amin_note');
    Route::post('/disputes/initiate_refund', [
        SupportController::class,
        'initiate_refund',
    ])->name('admin.disputes.initiate_refund');
    // Route::post('/disputes/initiate_refund', [SupportController::class,'initiate_refund'])->name('admin.disputes.initiate_refund');
    Route::get('/disputes/refund_data/{id}', [
        SupportController::class,
        'refund_data',
    ])->name('admin.disputes.refund_datas');
    Route::post('/disputes/update_refund', [
        SupportController::class,
        'update_refund',
    ])->name('admin.disputes.update_refundes');

    // Tickets
    Route::get('/tickets', [SupportController::class, 'Ticket'])->name(
        'admin.disputes.Ticket'
    );
    Route::post('/tickets/list_ticket', [
        SupportController::class,
        'list_tickets',
    ])->name('admin.disputes.list_ticket_as');
    Route::get('/tickets/update_ticket/{id}', [
        SupportController::class,
        'update_ticket',
    ])->name('admin.disputes.update_ticket');
    Route::post('/tickets/up_ticket_data', [
        SupportController::class,
        'up_ticket_data',
    ])->name('admin.disputes.up_ticket_data');
    Route::get('/tickets/assign_ticker/{id}', [
        SupportController::class,
        'assign_ticker',
    ])->name('admin.disputes.assign_ticker');
    Route::post('/tickets/update_assign_ticket', [
        SupportController::class,
        'update_assign_ticket',
    ])->name('admin.disputes.update_assign_ticket');
    Route::get('/tickets/ticket_reply/{id}', [
        SupportController::class,
        'ticket_reply',
    ])->name('admin.disputes.ticket_reply');
    Route::post('/tickets/update_reply', [
        SupportController::class,
        'update_reply',
    ])->name('admin.disputes.update_reply');
    Route::post('/tickets/Close_list_tickets', [
        SupportController::class,
        'Close_list_tickets',
    ])->name('admin.disputes.Close_list_tickets');
    Route::post('/tickets/ticket_replys', [
        SupportController::class,
        'restore',
    ])->name('admin.disputes.restore');
    // Testimonial
    Route::get('/testimonial/test', [
        SupportController::class,
        'test_monial',
    ])->name('admin.test.test_monial');
    Route::get('/testimonial/test_monial_add', [
        SupportController::class,
        'test_monial_add',
    ])->name('admin.test.test_monial_add');
    Route::post('/testimonial/test_show', [
        SupportController::class,
        'test_show',
    ])->name('admin.test.test_show');
    Route::post('/testimonial/add_testimonial', [
        SupportController::class,
        'add_testimonial',
    ])->name('admin.test.add_testimonial');
    Route::get('/testimonial/edit_testimonial/{id}', [
        SupportController::class,
        'edit_testimonial',
    ])->name('admin.test.edit_testimonial');
    Route::post('/testimonial/update_testimonial', [
        SupportController::class,
        'update_testimonial',
    ])->name('admin.test.update_testimonial');
    Route::post('/testimonial/delete_testimonial', [
        SupportController::class,
        'delete_testimonial',
    ])->name('admin.test.delete_testimonial');
    // end testimonial
    //    refunds
    Route::get('/refund_datas', [
        SupportController::class,
        'refund_datas',
    ])->name('admin.refund.refund_datas');
    Route::post('/order-list', [SupportController::class, 'order_list'])->name(
        'admin.refund.order_list'
    );
    Route::get('/order-refund-customer-data/{id}', [
        SupportController::class,
        'refund_customer_data',
    ])->name('admin.refund.refund_customer_data');
    Route::get('/order-customer-data/{id}', [
        SupportController::class,
        'customer_data',
    ])->name('admin.refund.customer_data');
    Route::post('/order-payment-approve}', [
        SupportController::class,
        'payment_approve',
    ])->name('admin.refund.payment_approve');
    //sale-banner
    Route::get('/sale_banner', [ReviewController::class, 'sale_banner'])->name(
        'admin.setting.sale_banner'
    );
    Route::get('/sale_banner_edit', [
        ReviewController::class,
        'sale_banner_edit',
    ])->name('admin.setting.sale_banner_edit');
    Route::post('/add_sale_banner', [
        ReviewController::class,
        'add_sale_banner',
    ])->name('admin.setting.add_sale_banner');
    Route::post('/sale_banner_data', [
        ReviewController::class,
        'sale_banner_data',
    ])->name('admin.setting.sale_banner_data');
    Route::post('/sale_banner_status_update', [
        ReviewController::class,
        'sale_banner_status_update',
    ])->name('admin.setting.sale_banner_status_update');
    Route::post('/sale_banner_update', [
        ReviewController::class,
        'sale_banner_update',
    ])->name('admin.setting.sale_banner_update');
    Route::post('/sale_banner_delete', [
        ReviewController::class,
        'sale_banner_delete',
    ])->name('admin.setting.sale_banner_delete');
    //Invoicing
    Route::get('/Invoicing/{id}', [
        SupportController::class,
        'Invoicing',
    ])->name('admin.test.Invoicing');

    // end invoicing
    Route::get('/carts/index', [
        AdminOrderController::class,
        'index_cart',
    ])->name('admin.cart');
    Route::post('/carts/list', [
        AdminOrderController::class,
        'list_cart',
    ])->name('admin.carts.list_cart');
    
    Route::get('/carts/list/view/{id}', [
        AdminOrderController::class,
        'view_carts',
    ])->name('admin.carts.view_carts');


    Route::get('/cancellation/index', [
        AdminOrderController::class,
        'index_cancellation',
    ])->name('admin.cancel');

    Route::post('/cancellation/list', [
        AdminOrderController::class,
        'cancellation_list',
    ])->name('admin.cancellation.list');

    Route::post('/cancellation/approved', [
        AdminOrderController::class,
        'cancellation_approved',
    ])->name('admin.cancellation.approved');


    Route::get('/notify', [NotificationController::class, 'index'])->name('admin.notify');
    Route::post('/send-notification',[NotificationController::class,'notification'])->name('admin.send-notification');


    // admin notification 
    Route::get('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('admin.markRead');
    Route::get('/all-clear', [NotificationController::class, 'allMarkAsRead'])->name('admin.allMarkAsRead');
    Route::get('/view-all', [NotificationController::class, 'viewAll'])->name('admin.viewAll');




      //clients
    Route::get('/client-lists', [HomeController::class, 'client_lists'])->name(
        'admin.client.lists'
    );
    Route::post('/add-client', [HomeController::class, 'addClient'])->name(
        'admin.client.add'
    );
    Route::post('/statusChange-client', [HomeController::class, 'client_statusChange'])->name(
        'admin.client.statusChange'
    );

    Route::post('/delete-client', [HomeController::class, 'client_delete'])->name(
        'admin.client.delete'
    );

    //disputes
    Route::get('/disputes', [SupportController::class, 'index_disputes'])->name(
        'admin.disputes.index'
    );
    Route::post('/disputes/list', [
        SupportController::class,
        'list_disputes',
    ])->name('admin.disputes.list');
    Route::get('/disputes/reply/{id}', [
        SupportController::class,
        'reply_disputes',
    ])->name('admin.disputes.reply');
    Route::post('/disputes/reply/store', [
        SupportController::class,
        'reply_store_disputes',
    ])->name('admin.disputes.reply.store');
    Route::get('/disputes/view/{id}', [
        SupportController::class,
        'view_disputes',
    ])->name('admin.disputes.view');
    Route::post('/disputes/view/oreder_sataus', [
        SupportController::class,
        'oreder_sataus',
    ])->name('admin.disputes.oreder_sataus');

    //reports
    //payouts
    Route::get('/reports/payouts', [
        ReportController::class,
        'index_payouts',
    ])->name('admin.payouts.index');
    Route::post('/reports/payouts/list', [
        ReportController::class,
        'list_payout',
    ])->name('admin.payouts.list');
    Route::get('/reports/performance', [
        ReportController::class,
        'index_performance',
    ])->name('admin.performance.index');

    //reviews
    Route::get('/products/reviews', [ReviewController::class, 'index'])->name(
        'admin.reviews.index'
    );
    Route::post('/products/reviews/list', [
        ReviewController::class,
        'list_reviews',
    ])->name('admin.reviews.list');
    Route::get('/products/reviews/view/{id}', [
        ReviewController::class,
        'reviews_view',
    ])->name('admin.reviews.view');


    Route::post('/all-order/send_message', [AdminOrderController::class, 'send_message'])->name('admin.order.send_message');

    // payout details 
    Route::get('/payout-details', [SupportController::class, 'payoutDetails'])->name('admin.payoutDetails');
    Route::post('/payout-details-post', [SupportController::class, 'postPayoutDetails'])->name('admin.payouts.list');



      // Customer 
      Route::get('/all-customer', [HomeController::class, 'AllCustomer'])->name('admin.customer.index');
      Route::post('/get-customer-detail', [HomeController::class, 'getCustomer'])->name('admin.customer.getData');
      Route::post('/customer-status', [HomeController::class, 'customerStatus'])->name('admin.customer.status');

      Route::post('/create-customer', [HomeController::class, 'createCustomer'])->name('admin.customer.create');
      Route::post('/edit-customer', [HomeController::class, 'editCustomer'])->name('admin.customer.edit');
      Route::post('/delete-customer', [HomeController::class, 'deleteCustomer'])->name('admin.customer.delete');
      Route::post('/customer-change-password', [HomeController::class , 'ChangePassword'])->name('admin.customer.passwordChange');

      Route::post('get-states',  [HomeController::class, 'getStates'])->name("admin.getStates");


    //   Seo Pages 
    Route::get('/appereance/seo/index', [  AppearanceController::class,  'seo_index_pages', ])->name('admin.appereance.seo.pages');
    Route::get('/appereance/seo/create_pages', [  AppearanceController::class,  'seo_create_pages',  ])->name('admin.appereance.seo.create_pages');
    Route::post('/appereance/seo/store_pages', [ AppearanceController::class,  'seo_store_pages', ])->name('admin.appereance.seo.store_pages');
    Route::get('/appereance/seo/edit_pages/{id}', [ AppearanceController::class, 'seo_edit_pages', ])->name('admin.appereance.seo.edit_pages');
    Route::post('/appereance/seo/update_pages/', [ AppearanceController::class, 'seo_update_pages', ])->name('admin.appereance.seo.update_pages');
    Route::get('/appereance/seo/view_pages/{id}', [ AppearanceController::class, 'seo_view_pages', ])->name('admin.appereance.seo.view_pages');
    Route::post('/appereance/seo/page/delete', [AppearanceController::class, 'seo_delete_page',   ])->name('admin.appereance.seo.delete_page');

});
