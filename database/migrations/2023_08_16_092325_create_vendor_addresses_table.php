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
        Schema::create('vendor_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('address_line1');
            $table->string('address_line2');
            $table->string('city');
            $table->string('postal_code');
            $table->string('phone');
            $table->integer('country');
            $table->integer('state');
            $table->unsignedBigInteger('vendor_id');


            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('vendor_id')->references('id')->on('users');
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
        Schema::dropIfExists('vendor_addresses');
    }
};
