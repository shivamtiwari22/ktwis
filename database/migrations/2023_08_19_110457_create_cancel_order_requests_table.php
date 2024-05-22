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
        Schema::create('cancel_order_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('p_id');
            $table->string('order_status')->nullable();
            $table->string('reason');
            $table->text('description');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by');

            
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('p_id')->references('id')->on('products');
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('cancel_order_requests');
    }
};
