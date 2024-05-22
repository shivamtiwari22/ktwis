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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('system_name');
            $table->string('legal_name');
            $table->string('email_address');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('lang_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('brand_logo');
            $table->string('icon');


            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('business_id')->references('id')->on('business_areas');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('lang_id')->references('id')->on('languages');
            $table->foreign('currency_id')->references('id')->on('currencies');
    
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
        Schema::dropIfExists('system_settings');
    }
};
