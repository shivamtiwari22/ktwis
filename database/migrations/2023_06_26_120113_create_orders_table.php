


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
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('shipping_method_id');
            $table->unsignedBigInteger('tax_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->string('couponcode')->nullable();
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->decimal('subtotal', 8, 2)->nullable();
            $table->decimal('tax_amount', 8, 2)->nullable();
            $table->decimal('shipping_amount', 8, 2)->nullable();
            $table->decimal('grand_total', 8, 2);
            $table->string('order_notes')->nullable();
            $table->string('payment_release_status')->default('Pending');
            $table->enum('status', ['pending', 'processing', 'completed', 'canceled', 'dispatched', 'delivered', 'returned'])->default('pending');
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('vendor_coupons')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
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
