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
        Schema::table('redirect_info', function (Blueprint $table) {
            // Chạy lệnh SQL để thêm index
            DB::statement('ALTER TABLE redirect_info ADD INDEX redirect_info_old_url_index (old_url(255))');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redirect_info', function (Blueprint $table) {
            //
        });
    }
};
