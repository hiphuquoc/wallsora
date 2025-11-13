<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        Schema::table('seo', function (Blueprint $table) {
            // Chạy lệnh SQL để thêm index
            DB::statement('ALTER TABLE seo ADD INDEX seo_slug_index (slug(255))');
            DB::statement('ALTER TABLE seo ADD INDEX seo_slug_full_index (slug_full(255))');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seo', function (Blueprint $table) {
            //
        });
    }
};
