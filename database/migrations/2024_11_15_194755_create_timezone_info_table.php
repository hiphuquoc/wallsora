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
        Schema::create('timezone_info', function (Blueprint $table) {
            $table->id();
            $table->integer('iso_3166_info_id');
            $table->string('country_code', 2);
            $table->text('timezone');
            $table->text('timezone_lower');
            $table->text('gmt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('timezone_info');
    }
};
