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
        Schema::create('cart_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('shipping_address_id')->nullable();
            $table->integer('billing_address_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('total_amount',8,2)->nullable();
            $table->decimal('discount_amount',8,2)->nullable();
            $table->decimal('coupon_discount',8,2)->nullable();
            $table->decimal('tax_amount',8,2)->nullable();
            $table->decimal('shipping_charges',8,2)->nullable();
            $table->decimal('grand_total',8,2)->nullable();
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
        Schema::dropIfExists('cart_summaries');
    }
};
