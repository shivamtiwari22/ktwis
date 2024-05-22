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
        Schema::table('business_areas', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('status');
            $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_areas', function (Blueprint $table) {
            $table->dropColumn('state_id');
            $table->dropColumn('country_id');
        });
    }
};
