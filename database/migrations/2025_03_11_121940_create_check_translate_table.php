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
        Schema::create('check_translate', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->string('language', 3);
            $table->text('type');
            /* bản tiếng việt */
            $table->text('title_vi');
            $table->text('seo_title_vi');
            $table->text('seo_description_vi');
            /* bản tiếng anh */
            $table->text('title_en');
            $table->text('seo_title_en');
            $table->text('seo_description_en');
            /* bản dịch hiện tại */
            $table->text('title');
            $table->text('title_google_translate_vi')->nullable();
            $table->text('title_google_translate_en')->nullable();
            $table->text('seo_title');
            $table->text('seo_description');
            /* bản dịch mới */
            $table->text('new_title');
            $table->text('new_title_google_translate_vi')->nullable();
            $table->text('new_title_google_translate_en')->nullable();
            $table->text('new_seo_title');
            $table->text('new_seo_description');
            $table->integer('status')->default(0);
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
        // Schema::dropIfExists('check_translate');
    }
};
