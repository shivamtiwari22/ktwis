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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_with_variant_id');
            $table->foreign('inventory_with_variant_id')
                ->references('id')->on('inventory_with_variants')
                ->onDelete('cascade');
            $table->string('attr_id');
            $table->string('attr_value_id');
            $table->string('sku');
            $table->integer('stock_quantity');
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('offer_price', 8, 2)->nullable();
            $table->string('image_variant')->nullable();
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
        Schema::dropIfExists('variants');
    }
};
