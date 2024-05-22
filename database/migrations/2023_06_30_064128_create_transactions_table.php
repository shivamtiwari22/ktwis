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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_wallet_id');
            $table->unsignedBigInteger('receiver_wallet_id');
            $table->decimal('amount',2);
            $table->unsignedBigInteger('currency_id');
            $table->timestamp('transaction_time');
            $table->enum('status',['Pending','Escrowed','Completed','Cancelled']);

            $table->foreign('sender_wallet_id')->references('id')->on('user_wallets')->onDelete('cascade');
            $table->foreign('receiver_wallet_id')->references('id')->on('user_wallets')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
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
        Schema::dropIfExists('transactions');
    }
};
