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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['inactive', 'active'])->default('inactive');
            $table->longtext('description')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('requires_shipping')->default(false);
            $table->string('brand')->nullable();
            $table->string('model_number')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('tags')->nullable();
            $table->integer('min_order_qty')->nullable();
            $table->float('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->text('key_features')->nullable();
            $table->text('linked_items')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->softDeletes();
            $table->timestamps();

            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
