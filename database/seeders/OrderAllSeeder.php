<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\OrderStatusSeeder;
use Database\Seeders\OrderActionSeeder;
use Database\Seeders\RelationOrderStatusOrderActionSeeder;

class OrderAllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $this->call([
            OrderStatusSeeder::class,
            OrderActionSeeder::class,
            RelationOrderStatusOrderActionSeeder::class
        ]);
    }
}
