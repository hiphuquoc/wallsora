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
        Schema::create('relation_order_info_wallpaper_info', function (Blueprint $table) {
            $table->id();
            $table->integer('order_info_id');
            $table->integer('wallpaper_info_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('relation_order_info_wallpaper_info');
    }
};
