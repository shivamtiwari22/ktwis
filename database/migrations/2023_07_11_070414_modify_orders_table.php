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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['seller_id', 'coupon_id', 'couponcode']);
            $table->unsignedBigInteger('shipping_address_id')->after('user_id');

            $table->foreign('shipping_address_id')->references('id')->on('user_addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address_id');
        });
    }
};
