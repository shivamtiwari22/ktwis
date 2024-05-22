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
        Schema::create('vendor_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('account_type');
            $table->string('routing_number');
            $table->string('bic_code');
            $table->string('iban_number');
            $table->text('bank_address');
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
        Schema::dropIfExists('vendor_bank_details');
    }
};
