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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('seller_id');
            $table->integer('invoice_number');
            $table->integer('order_number');
            $table->integer('tax_id')->nullable();
            $table->integer('shipping_address_id')->nullable();
            $table->integer('item_count')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('sub_total',8,2)->nullable();
            $table->decimal('discount_amount',8,2)->nullable();
            $table->decimal('coupon_discount',8,2)->nullable();
            $table->decimal('tax_amount',8,2)->nullable();
            $table->decimal('shipping_amount',8,2)->nullable();
            $table->decimal('total_amount',8,2)->nullable();
            $table->integer('order_summary_id')->nullable();
            $table->string('status')->nullable();
            $table->string('order_notes')->nullable();
            $table->decimal('total_refund_amount',8,2)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
