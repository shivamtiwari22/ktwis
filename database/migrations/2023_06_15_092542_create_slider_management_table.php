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
        Schema::create('slider_management', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('title_color')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('subtitle_color')->nullable();
            $table->string('description')->nullable();
            $table->string('description_color')->nullable();
            $table->string('link')->nullable();
            $table->integer('order')->nullable();
            $table->enum('text_position', ['left', 'right'])->nullable();
            $table->string('slider_image');
            $table->string('mobile_image')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slider_management');
    }
};
