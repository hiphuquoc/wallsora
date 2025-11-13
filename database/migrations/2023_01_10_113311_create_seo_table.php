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
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->integer('level');
            $table->integer('parent')->nullable();
            $table->integer('ordering')->nullable();
            $table->integer('topic')->nullable();
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('slug');
            $table->text('slug_full');
            $table->integer('link_canonical')->nullable();
            $table->string('type', '50');
            $table->string('rating_author_name', 1)->nullable();
            $table->string('rating_author_star', 5);
            $table->integer('rating_aggregate_count')->nullable();
            $table->string('rating_aggregate_star', 5)->nullable();
            $table->integer('created_by');
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
        // Schema::dropIfExists('seo');
    }
};
