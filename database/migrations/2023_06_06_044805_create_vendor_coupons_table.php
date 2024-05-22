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
        Schema::create('vendor_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->enum('coupon_type', ['fixed', 'Percentage'])->default('percentage');
            $table->string('amount');
            $table->date('expiry_date');
            $table->string('no_of_coupons');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'published'])->default('published');
            $table->string('used_coupons')->nullable();
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
        Schema::dropIfExists('vendor_coupons');
    }
};
