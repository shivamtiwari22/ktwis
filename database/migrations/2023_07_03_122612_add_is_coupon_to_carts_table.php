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
            $table->boolean('is_coupon')->default(0);
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('coupon_value')->nullable();
            $table->decimal('actual_price', 8, 2)->nullable();
            $table->decimal('after_discount_price', 8, 3)->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->boolean('has_variant')->default(0);
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('vendor_coupons')->onDelete('cascade');
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
