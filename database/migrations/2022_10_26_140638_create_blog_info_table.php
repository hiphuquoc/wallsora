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
        Schema::create('blog_info', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->boolean('outstanding')->default(0);
            $table->boolean('status')->default(1);
            $table->integer('viewed')->default(0);
            $table->integer('shared')->default(0);
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
        // Schema::dropIfExists('blog_info');
    }
};
