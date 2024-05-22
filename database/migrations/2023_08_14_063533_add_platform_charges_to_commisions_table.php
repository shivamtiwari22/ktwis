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
        Schema::table('commisions', function (Blueprint $table) {
                $table->integer('platform_charges');
                $table->integer('transaction_charges');
                 $table->integer('total_charges');
                 $table->string('countries');
                 $table->dropColumn('comission_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commisions', function (Blueprint $table) {
            //
        });
    }
};
