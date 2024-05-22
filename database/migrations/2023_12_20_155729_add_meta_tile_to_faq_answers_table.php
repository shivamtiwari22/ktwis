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
        Schema::table('faq_answers', function (Blueprint $table) {
               $table->string('meta_title')->nullable();
               $table->text('meta_description')->nullable();
               $table->text('keywords')->nullable();
               $table->text('ogtag')->nullable();
               $table->text('schema_markup')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq_answers', function (Blueprint $table) {
            //
        });
    }
};
