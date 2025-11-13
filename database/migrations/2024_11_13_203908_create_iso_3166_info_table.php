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
    public function up() {
        Schema::create('iso_3166_info', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('alpha_2', 2);
            $table->string('alpha_3', 3)->nullable();
            $table->string('country_code', 3);
            $table->text('region')->nullable();
            $table->text('sub_region')->nullable();
            $table->string('region_code', 3)->nullable();
            $table->string('sub_region_code', 3)->nullable();
            $table->text('percent_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Schema::dropIfExists('iso_3166_info');
    }
};
