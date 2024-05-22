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
        Schema::create('custom_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_order_id');
            $table->foreign('custom_order_id')->references('id')->on('custom_orders');
            $table->string('item_name');
            $table->integer('quantity');
            $table->double('price',8,2);
            $table->text('description')->nullable();
            $table->double('amount',8,2);
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
        Schema::dropIfExists('custom_order_items');
    }
};
