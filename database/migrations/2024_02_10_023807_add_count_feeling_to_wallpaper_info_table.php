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
        Schema::table('wallpaper_info', function (Blueprint $table) {
            $table->integer('heart')->default(0);
            $table->integer('ha_ha')->default(0);
            $table->integer('not_like')->default(0);
            $table->integer('vomit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallpaper_info', function (Blueprint $table) {
            //
        });
    }
};
