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
        Schema::create('shipping_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('s_country_id');
            $table->integer('state_id');
            $table->string('state_name');
            $table->foreign('s_country_id')->references('id')->on('shipping_countries');
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
        Schema::dropIfExists('shipping_states');
    }
};
