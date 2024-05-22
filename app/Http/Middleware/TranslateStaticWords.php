<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TranslateStaticWords
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($response->isSuccessful()) {
            $content = $response->getContent();
            $translatedContent = $this->translateStaticWords($content);
            $response->setContent($translatedContent);
        }
        return $response;
    }

    protected function translateStaticWords($content)
    {
        $translations = [
            'Order Date' => __('messages.order_date'),
            'Dashboard' => __('messages.dashboard'),
            'Order' => __('messages.order'),
            'Customer' => __('messages.customer'),
            'Grand Total' => __('messages.grand_total'),
            'Payment' => __('messages.payment'),
            'Payment Link' => __('messages.payment_link'),
            'Status' => __('messages.status'),
            'View' => __('messages.view'),
            'Created at' => __('messages.created_at'),
            'Items' => __('messages.items'),
            'Quantities' => __('messages.quantities'),
            'Action' => __('messages.action'),
            'Carts' => __('messages.Carts'),
            'Home' => __('messages.home'),
            'Email' => __('messages.email'),
            'Member Since' => __('messages.member_since'),
            'Membership Since' => __('messages.member_since'),
            'Products' => __('messages.products'),
            'Product Name' => __('messages.product_name'),
            'Image' => __('messages.image'),
            'Quantity' => __('messages.quantity'),
            'Price' => __('messages.price'),
            'Total' => __('messages.total'),
            'Warning' => __('messages.warning'),
            'Confirm' => __('messages.confirm'),
            'Cancel' => __('messages.cancel'),
            'Cart Details' => __('messages.cart_details'),
            'Are you sure you want to delete ?' => __('messages.are_you_sure'),
            'Wishlist List' => __('messages.wishlist_list'),
            'Last Wishlisted On' => __('messages.wish_details'),
            'Wishlist Details' => __('messages.approve'),
            'Cancellation List' => __('messages.can_list'),
            'APPROVE' => __('messages.approve'),
            'DECLINE' => __('messages.decline'),
            'Requested items' => __('messages.req_item'),
            'Requested at' => __('messages.req_at'),

            //  cutomer section 
            'Avtar' => __('messages.avtar'),
            'Avatar(JPG, JPEG, PNG, 2MB max)' => __('messages.avtar'),
            'Customer Name' => __('messages.customer_name'),
            'Add Customer' => __('messages.add_customer'),
            'Form' => __('messages.form'),
            'Full Name' => __('messages.full_name'),
            'Name' => __('messages.full_name'),
            'DOB' => __('messages.dob'),
            'Password' => __('messages.password'),
            'Confirm Password' => __('messages.confirm_password'),
            'Description' => __('messages.description'),
            'Address Line 1' => __('messages.address_line_1'),
            'Address Line 2' => __('messages.address_line_2'),
            'City' => __('messages.city'),
            'Zip/Postal Code' => __('messages.zip'),
            'Phone' => __('messages.phone'),
            'Country' => __('messages.county'),
            'State' => __('messages.state'),
            'Select Country' => __('messages.select_country'),
            'Select State' => __('messages.select_state'),
            'Save' => __('messages.save'),
            'Profile' => __('messages.profile'),
            'Customer Profile' => __('messages.customer_profile'),
            'Address' =>  __('messages.address')

            // Add more word translations here
        ];

        foreach ($translations as $word => $translation) {
            $content = str_replace($word, $translation, $content);
        }

        return $content;
    }
}
