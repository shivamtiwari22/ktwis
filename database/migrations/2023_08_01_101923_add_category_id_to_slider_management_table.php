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
        Schema::table('slider_management', function (Blueprint $table) {
                  $table->boolean('has_category_slider')->default(0);
                  $table->unsignedBigInteger('category_id')->nullable();
                  $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slider_management', function (Blueprint $table) {
            //
        });
    }
};
