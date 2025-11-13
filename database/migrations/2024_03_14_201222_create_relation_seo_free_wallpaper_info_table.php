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
        Schema::create('relation_seo_free_wallpaper_info', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->integer('free_wallpaper_info_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('relation_seo_free_wallpaper_info');
    }
};
