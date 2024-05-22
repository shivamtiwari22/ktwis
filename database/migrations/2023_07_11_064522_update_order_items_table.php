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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price_with_tax', 8, 2)->nullable()->change();
            $table->decimal('price_without_tax', 8, 2)->nullable()->change();
            $table->decimal('price_with_discount', 8, 2)->nullable()->change();
            $table->decimal('price_without_discount', 8, 2)->nullable()->change();
            $table->decimal('refund_amount', 8, 2)->nullable()->change();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_amount', 8, 2)->default(0);


            $table->foreign('seller_id')->references('id')->on('vendors');
            $table->foreign('coupon_id')->references('id')->on('vendor_coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price_with_tax', 8, 2)->change();
            $table->decimal('price_without_tax', 8, 2)->change();
            $table->decimal('price_with_discount', 8, 2)->change();
            $table->decimal('price_without_discount', 8, 2)->change();
            $table->decimal('refund_amount', 8, 2)->change();
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['seller_id', 'coupon_id', 'coupon_code', 'coupon_amount']);
        });
    }
};
