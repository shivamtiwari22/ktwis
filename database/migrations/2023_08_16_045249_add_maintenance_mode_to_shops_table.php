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
        Schema::table('shops', function (Blueprint $table) {
           
            $table->string('shop_url')->nullable()->after('shop_name');
            $table->string('legal_name')->nullable()->after('shop_name');
            $table->integer('created_by')->nullable()->after('status');
            $table->integer('updated_by')->nullable()->after('created_by');
            $table->boolean('maintenance_mode')->default(1)->after('vendor_id');
            $table->boolean('email_is_verified')->default(0)->after('maintenance_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
         

        });
    }
};
