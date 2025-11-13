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
        Schema::create('order_action', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('color', 10)->nullable();
            $table->text('icon');
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
        // Schema::dropIfExists('order_action');
    }
};
