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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('variant_id')->nullable();
            $table->integer('quantity');
            $table->string('name')->nullable();
            $table->decimal('weight',12,4)->default(0);
            $table->decimal('total_weight',12,4)->default(0);

            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('offer_price',8,2)->nullable();
            $table->decimal('purchase_price',8,2)->nullable();
            $table->decimal('base_total',8,2)->nullable();
            $table->unsignedBigInteger('user_id');


            $table->foreign('cart_id')->references('id')->on('carts');
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('cart_items');
    }
};
