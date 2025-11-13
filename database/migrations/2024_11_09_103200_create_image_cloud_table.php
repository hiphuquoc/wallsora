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
        Schema::create('image_cloud', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(1);
            $table->text('folder_name');
            $table->text('file_name');
            $table->text('extension');
            $table->text('file_cloud');
            $table->integer('width');
            $table->integer('height');
            $table->integer('file_size');
            $table->text('mine_type');
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
        // Schema::dropIfExists('image_cloud');
    }
};
