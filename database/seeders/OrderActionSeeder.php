<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\OrderAction;

class OrderActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $source = [
            [
                'name'      => 'Chỉnh sửa',
                'color'     => '#e36414',
                'icon'      => '<i class="fa-solid fa-pen-to-square"></i>',
                'ordering'  => 11
            ],
            [
                'name'      => 'In xác nhận',
                'color'     => '#2874A6',
                'icon'      => '<i class="fa-solid fa-print"></i>',
                'ordering'  => 10
            ],
            [
                'name'      => 'Bàn giao shipper',
                'color'     => '#B7950B',
                'icon'      => '<i class="fa-solid fa-truck"></i>',
                'ordering'  => 9
            ],
            [
                'name'      => 'Đã nhận hàng',
                'color'     => '#239B56',
                'icon'      => '<i class="fa-regular fa-thumbs-up"></i>',
                'ordering'  => 8
            ],
            [
                'name'      => 'Hủy đơn hàng',
                'color'     => '#B03A2E',
                'icon'      => '<i class="fa-solid fa-ban"></i>',
                'ordering'  => 7
            ],
            [
                'name'      => 'Khôi phục đơn hàng',
                'color'     => '#283747',
                'icon'      => '<i class="fa-solid fa-trash-arrow-up"></i>',
                'ordering'  => 6
            ]
        ];
        OrderAction::select('*')->delete();
        foreach($source as $itemInsert) OrderAction::insertItem($itemInsert);
    }
}
