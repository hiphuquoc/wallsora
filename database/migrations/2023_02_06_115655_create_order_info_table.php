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
        Schema::create('order_info', function (Blueprint $table) {
            $table->id();
            $table->integer('order_status_id')->default(0);
            $table->string('code', 15);
            $table->integer('customer_info_id')->nullable();
            $table->integer('product_count');
            $table->text('product_cash');
            $table->text('ship_cash');
            $table->text('total');
            $table->integer('payment_method_info_id')->default(0); /* 0 là không xác định => để liên hệ lại */
            $table->text('email')->nullable();
            $table->text('note')->nullable();
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
        // Schema::dropIfExists('order_info');
    }
};
