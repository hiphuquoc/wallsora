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
        Schema::create('wallpaper_info', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('name');
            $table->longText('description')->nullable();
            $table->text('file_name_wallpaper');
            $table->text('file_cloud_wallpaper');
            $table->integer('width_wallpaper');
            $table->integer('height_wallpaper');
            $table->integer('file_size_wallpaper');
            $table->text('extension_wallpaper');
            $table->text('mine_type_wallpaper');
            $table->text('file_name_source');
            $table->text('file_cloud_source');
            $table->integer('width_source');
            $table->integer('height_source');
            $table->integer('file_size_source');
            $table->text('extension_source');
            $table->text('mine_type_source');
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
        // Schema::dropIfExists('wallpaper_info');
    }
};
