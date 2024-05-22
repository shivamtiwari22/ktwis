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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->string('tx_ref')->nullable();
            $table->string('payment_method')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->double('charged_amount')->nullable();
            $table->double('amount')->nullable();
            $table->string('currency')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('paid_at');
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
        Schema::dropIfExists('payments');
    }
};
