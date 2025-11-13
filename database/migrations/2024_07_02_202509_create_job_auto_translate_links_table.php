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
        Schema::create('job_auto_translate_links', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->integer('ordering');
            $table->string('language', 3);
            $table->text('link_source');
            $table->text('link_translate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('job_auto_translate_links');
    }
};
