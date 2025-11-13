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
        Schema::create('seller_info', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_name')->nullable();
            $table->string('name')->nullable();
            $table->string('phone');
            $table->string('zalo')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->integer('province_info_id')->nullable();
            $table->integer('district_info_id')->nullable();
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
        // Schema::dropIfExists('seller_info');
    }
};
