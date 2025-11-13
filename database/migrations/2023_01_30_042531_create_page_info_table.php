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
        Schema::create('page_info', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->integer('type_id');
            $table->boolean('show_sidebar')->default(0);
            $table->longText('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('page_info');
    }
};
