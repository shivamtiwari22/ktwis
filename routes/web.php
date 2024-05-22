<?php

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserAccountController;
use App\Http\Controllers\Admin\Site\AppearanceController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\User\CustomerController;
use App\Http\Controllers\Vendor\NotificationController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\Shipping\CarrierController;
use App\Http\Controllers\Vendor\Shipping\ZoneController;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

Route::get('/password',  function(){
     dd( app('api_url'));
    return bcrypt('Pass@123');
});


Route::get('/guest-mail', function(){
    $data = [
         "name" => "Abhay",
         "email" => "st@gmail.com",
         "password" => "234234"
    ];


    $arr = [
'email' => "ankitsaraswat9650@gmail.com",
'subject' => "Welcome Mail"
    ];

    Mail::send('email.guestRegistrationMail', ['data' => $data], function ($message) use ($arr) {
        $message->from('deo.officeworks@gmail.com', "Ktwis")->to($arr['email'])->subject($arr['subject']);
    });
    return "Mail Send";
});

Route::prefix('/admin')->group(__DIR__ . '/admin.php');

Route::prefix('/vendor')->group(__DIR__ . '/vendor.php');


Route::get('get-profile', function () {
  
    $apiToken =  'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI3IiwianRpIjoiZDMzNDgwMzVhOTM2NWZhZDEyZDU4ZTRkZjkyZTAwYTk0ZmViMTM0ODg2MThjMGI0YjhkMWFmMmUzN2VhMjY5NGI5NTkwZDY5MTczODg4YTkiLCJpYXQiOjE2OTU3MzI3MTAuNDcwMzIyLCJuYmYiOjE2OTU3MzI3MTAuNDcwMzI2LCJleHAiOjE3MjczNTUxMTAuNDUzNjI0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.hrAFZz1o5Oy6AEAdcRhaLuucEee9DD0hbcY1OLm5XOOEZojB0giso-JZD7UdT90ZLTVTa_mTCQl0mFjsjhLqDaAAwJE-SJgIlPn-qxugvA0jJBxk_rHCxht0TvRPSr-om7R39Mi0_NdX4D-U1tx1_pCXhpgWu5rQ75H7LDWWy7bXH0VLo2yspDP-b2eEgzVXfAz58GwY3yBMLP14ddaJwt1TvX34F2cakCjoCXQN_HY5YKphCzJihOGnMPixls7nxayUPO-1jyM1L3KFvMUn3NtgXv7aRXH8aAEgtMYCYwfddKL1vc5ZNrld7fWEEJ_wCQ-umiAgpqlN-ddA05iJ4KjkGdl0d87nE0fUkaP-W9I9FhBO9vdZ2FfdBuk00iSqDsLLk824xbeBsHejJNO6-5dok5uwm_p6uN5kZCa00Q1OHJB8dd9n-VRVEALiXXcJvWkT5FM8N6kZLpaZ7oyjazlC1g8EPBacPlFuUPA1aZG041V2OqwiVwTUfDjhFVZRyv9-e4zTb-kXFgV-YnTqbAFBhLbyIH3yAc7qpILef1XYbQJJ20dwePboWe4cw04wQQ7oQ4CCnJgJh9ZbtKex6xBXhGh3dsW1TCsDj3I-m4zD65Mni6k43esyIrBHFn2GkwAYIA8sSW85V6ju8xgAR3AckJSqixWThMtN-al7pcg';
    $client_id =  '2';
    $client_secret =  'n3X1XTC4pgkLzKFezVd6aG9ouongV0Ge71icEffX'; 

    $headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMWExYWFmNjVkYzM2MTExMjNjN2FhZGNjNWZlYWZlYzFhMjYwZTdiYjZmMzBmYWU3NzUzOGIwYjNhOTk2YjJjYmVlMzkyM2M2ZTFhNTM3MzUiLCJpYXQiOjE2OTU2MzczNTEuMTM2NjMsIm5iZiI6MTY5NTYzNzM1MS4xMzY2MzQsImV4cCI6MTcyNzI1OTc1MS4xMjAzNTQsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.K0iiIliWhDeWAr5YGKapcjkIabqsL7NfUJ-2ERnjjCALppr9F1qGk4JHDcg3yPs0S0Th2vC05BqW5_JqowepQvz6MPqBIqVree3tFmph3T93Osb7Y21_qsf7FP4DFtLecRnn5YkJ1t0FcNlJzg54oSJry47SchKytVo7Kz06pwsSHbGUI4p8fezG_2SZF-hwPCQmnYB6QY4zmwwwagqFGUEc6yvrcV3YGg5EAI7k_lOuKfw7tyC8wDCWdSN82U01BN3W-AGPK7Yvfh2Nfe0iFFcu1SGYlsmpipDCttWxoNdi4HCMBw2r7l1XLe4_Kp9YsZ257cRYERFcoQW_dVm9qlxQv9iOkQzzqVS7E0kkHA9eTbFc-VP4-VZrTmLwxNuz3jvmbbEWi5Mh0iPaE98AmnnVEr91IRl_lygnXJJKojNHDnfWfJ1hpqW2n6Cu2vkpuRfhOmHkoSg8s4658bhNGFd7Yrb_CxA3AUKDeKROVAEZnJ8SsmmQvg_P52zXOAkCZwjjA5N3T_27El2wnfcez13v5z1e33DMFB44O3JFePKDgDcjkp5TxUn_wl2ovil7uRjW_p4lsL4yuulRyD78MpIoIFVZsySsZErJnb2KmKjIAuEuDjGAASyCZXy_IWf-LlpQbNPReFaXhAYqF0Tb1CgJJ3xZvjtGvixz25f91Do',
        'Accept' => 'application/json',
        'client-id' => $client_id,
        'client-key' => $client_secret
    ];

    $data = [
        'email' => 'st4272333@gmail.com',
        'password' => '12345678',
    ];

   
    
    // Make the API request to get the wallet balance
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiToken,
        'Accept' => 'application/json',
        'client-id' => $client_id,
        'client-key' => $client_secret
    ])->get('http://localhost/credit-pass/api/2fa');


     dd($response->json());
    
    if ($response->successful()) {
        // Handle successful response, e.g., store data in the wallet app's database or display to the user.
        $balance = $response->json();

        // $phpArray = json_decode($balance, true);
          return $balance;
        // ...
    } else {
        // Handle errors, e.g., log the error, show a message to the user, or take appropriate action.
        $errorMessage = $response->json();
        return $response->json();

        // ...
    }

});


Route::get('download-invoice/{id}', [UserAccountController::class, 'download_invoice']);

// testing notification
Route::get('vendor/notification',[NotificationApiController::class,'notification']);


Route::get('reset-password',  [Controller::class, 'reset_password']);
Route::post('user-reset-password',  [Controller::class, 'reset_password_submit'])->name("user.reset.password");


//  Mail Customer Invoice Mail 
Route::get('payment-invoice/{id}/{link}', [OrderController::class, 'invoiceRedirection'])->name('payment-invoice');
Route::get('invoice-payment-method/{pay}/{user}', [OrderController::class, 'invoicePaymentVerify'])->name('invoice-payment.verify');
Route::get('callback/{id}', [OrderController::class, 'callback'])->name('callback');
Route::get('download-invoice/{id}', [OrderController::class, 'downloadInvoice'])->name('download-invoice');


//  Custom Order Invoice Mail 
Route::get('payment-custom-invoice/{id}/{link}', [OrderController::class, 'redirectCustomInvoice'])->name('custom-payment-invoice');
Route::get('custom-invoice-payment-method/{pay}', [OrderController::class, 'customInvoicePaymentVerify'])->name('custom-invoice-payment.verify');
Route::get('custom-callback/{id}', [OrderController::class, 'customCallback'])->name('customCallback');
Route::get('download-custom-invoice/{id}', [OrderController::class, 'customDownloadInvoice'])->name('custom.download-invoice');


// flutter wave call back route 
Route::get('callback', [CheckoutApiController::class, 'callback'])->name('checkout.callback');

// stripe success call back 
Route::get('stripe-success', [CheckoutApiController::class, 'stripe_success'])->name('stripe_success');


// Customer Login to Credit Pass 
Route::post('login', [CustomerController::class, 'CreditPassLogin'])->name('creditPassLogin');
Route::post('credit-Verify-Customer', [CustomerController::class, 'creditVerifyCustomer'])->name('creditVerifyCustomer');
Route::post('customer-wallet', [CustomerController::class, 'customerWallet'])->name('customerWallet');
Route::post('customer-payment', [CustomerController::class, 'customerPayment'])->name('customerPayment');

Route::post('custom-order-payment', [CustomerController::class, 'customCustomerPayment'])->name('customCustomerPayment');




Route::get('admin/order/index',[AdminOrderController::class,'index_order'])->name('admin.order');
Route::post('admin/order/list',[AdminOrderController::class,'list_order'])->name('admin.order.list');
Route::get('admin/order/list/view/{id}',[AdminOrderController::class,'view_order'])->name('admin.carts.view_order');

//wishlist
Route::get('admin/wishlist/index',[AdminOrderController::class,'index_wishlist'])->name('admin.wishlist');
Route::post('admin/wishlist/list',[AdminOrderController::class,'list_wishlist'])->name('admin.wishlist.list');
Route::get('admin/wishlist/list/view/{id}',[AdminOrderController::class,'view_wishlist'])->name('admin.wishlist.view_wishlist');
//carts
Route::get('admin/carts/index',[AdminOrderController::class,'index_cart'])->name('admin.cart');
Route::post('admin/carts/list',[AdminOrderController::class,'list_cart'])->name('admin.carts.list_cart');
Route::get('admin/carts/list/view/{id}',[AdminOrderController::class,'view_carts'])->name('admin.carts.view_carts');
Route::get('admin/cancellation/index',[AdminOrderController::class,'index_cancellation'])->name('admin.cancel');


  
Route::get('push-notification', [NotificationController::class, 'index']);
Route::post('sendNotification', [NotificationController::class, 'sendNotification'])->name('send.notification');



