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
            $table->dropColumn('coupon_value');
            
            $table->unsignedBigInteger('seller_id')->after('user_id');

            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_amount', 8, 2)->nullable();
            $table->decimal('sub_total', 8, 2)->nullable();
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
            $table->decimal('coupon_value', 8, 2)->nullable();
            $table->dropColumn('seller_id');
            $table->dropColumn('coupon_code');
            $table->dropColumn('coupon_amount');
            $table->dropColumn('sub_total');
        });
    }
};
