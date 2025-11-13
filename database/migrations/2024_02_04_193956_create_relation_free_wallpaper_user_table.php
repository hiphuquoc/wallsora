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
        Schema::create('relation_free_wallpaper_user', function (Blueprint $table) {
            $table->id();
            $table->integer('free_wallpaper_info_id');
            $table->integer('user_info_id');
            $table->text('type');
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
        // Schema::dropIfExists('relation_free_wallpaper_user');
    }
};
