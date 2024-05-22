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
        Schema::table('disputes', function (Blueprint $table) {
            $table->double('refund_amount')->nullable()->after('status');
            $table->boolean('good_received')->default(false)->after('refund_amount');
            $table->bigInteger('p_id')->unsigned()->nullable()->after('good_received');
            $table->bigInteger('variant_id')->unsigned()->nullable()->after('p_id');
            $table->longText('description')->nullable()->after('variant_id');
        });
    }

    /**
     * Reverse the migrations.
     *  
     * @return void
     */
    public function down()
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropColumn('refund_amount');
            $table->dropColumn('good_received');
            $table->dropColumn('p_id');
            $table->dropColumn('variant_id');
            $table->dropColumn('description');
        });
    }
};
