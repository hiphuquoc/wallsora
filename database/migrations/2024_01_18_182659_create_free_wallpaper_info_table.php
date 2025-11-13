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
        Schema::create('free_wallpaper_info', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id')->nullable();
            $table->integer('user_id');
            $table->text('file_name');
            $table->text('file_cloud');
            $table->integer('width');
            $table->integer('height');
            $table->integer('file_size');
            $table->text('extension');
            $table->text('mine_type');
            $table->integer('heart')->default(0);
            $table->integer('ha_ha')->default(0);
            $table->integer('not_like')->default(0);
            $table->integer('vomit')->default(0);
            $table->boolean('flag_thumnail_category')->default(0);
            $table->longText('notes')->nullable();
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
        // Schema::dropIfExists('free_wallpaper_info');
    }
};
