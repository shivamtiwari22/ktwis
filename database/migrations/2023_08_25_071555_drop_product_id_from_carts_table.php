<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'quantity','is_coupon','coupon_id','coupon_amount','actual_price','after_discount_price','variant_id','has_variant']);
            $table->integer('shipping_address_id')->nullable()->after('seller_id');
            $table->integer('item_count')->nullable()->after('shipping_address_id');
            $table->string('coupon_code')->nullable()->after('item_count');
            $table->decimal('sub_total',8,2)->nullable()->after('coupon_code');
            $table->decimal('discount_amount',8,2)->nullable()->after('sub_total');
            $table->decimal('coupon_discount',8,2)->nullable()->after('discount_amount');
            $table->decimal('tax_amount',8,2)->nullable()->after('coupon_discount');
            $table->decimal('shipping_amount',8,2)->nullable()->after('tax_amount');
            $table->decimal('total_amount',8,2)->nullable()->after('shipping_amount');
            $table->integer('cart_summary_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            //
        });
    }
};
