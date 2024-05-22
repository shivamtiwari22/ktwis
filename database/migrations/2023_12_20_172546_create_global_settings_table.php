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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('google analytic')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('keywords')->nullable();
            $table->text('ogtag')->nullable();
            $table->longText('schema_markup')->nullable();
            $table->text('google_tag_manager')->nullable();
            $table->text('search_console')->nullable();
            $table->string('facebook_pixel')->nullable();
            $table->unsignedBigInteger('social_media_id')->nullable();
            $table->text('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('android_link')->nullable();
            $table->string('android_url')->nullable();
            $table->string('iphone_link')->nullable();
            $table->string('iphone_url')->nullable();
            $table->text('copywrite_text')->nullable();
            $table->string('qr_code')->nullable();
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
        Schema::dropIfExists('global_settings');
    }
};
