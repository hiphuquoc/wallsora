<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $source = [
            [
                'name'  => 'Chưa xác nhận',
                'color' => '#283747'
            ],
            [
                'name'  => 'Đã xác nhận',
                'color' => '#2874A6'
            ],
            [
                'name'  => 'Đang giao hàng',
                'color' => '#B7950B'
            ],
            [
                'name'  => 'Đã nhận hàng',
                'color' => '#239B56'
            ],
            [
                'name'  => 'Đơn hủy',
                'color' => '#B03A2E'
            ]
        ];
        $status = OrderStatus::all();
        foreach($source as $itemInsert) {
            $flagInsert = true;
            foreach($status as $s){
                if(mb_strtolower($s->name)==mb_strtolower($itemInsert['name'])){
                    $flagInsert = false;
                    break;
                }
            }
            if($flagInsert==true) OrderStatus::insertItem($itemInsert);
        }
    }
}
