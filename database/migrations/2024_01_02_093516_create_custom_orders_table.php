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
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('seller_to_customer')->nullable();
            $table->string('terms_condition')->nullable();
            $table->string('reference')->nullable();
            $table->string('attachments')->nullable();
            $table->string('invoice_number');
            $table->date('date');
            $table->double('sub_total',8,2);
            $table->double('discount',8,2);
            $table->double('shipping',8,2);
            $table->double('total_amount',8,2);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->string('status');
            $table->string('payment_status');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
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
        Schema::dropIfExists('custom_orders');
    }
};
