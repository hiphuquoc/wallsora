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
        Schema::create('prompt_info', function (Blueprint $table) {
            $table->id();
            $table->text('type'); /* auto_content */
            $table->text('reference_table'); /* tên bảng */
            $table->text('reference_name'); /* tên input name */
            $table->text('reference_prompt'); /* prompt */
            $table->text('tool');
            $table->text('version');
            $table->integer('ordering')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('prompt_info');
    }
};
