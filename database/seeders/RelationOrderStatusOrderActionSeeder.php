<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\RelationOrderStatusOrderAction;
use \App\Models\OrderStatus;
use \App\Models\OrderAction;

class RelationOrderStatusOrderActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $source = [
            [
                'status'    => 'Chưa xác nhận',
                'action'    => [
                    'Chỉnh sửa', 'In xác nhận', 'Hủy đơn hàng'
                ]
            ],
            [
                'status'    => 'Đã xác nhận',
                'action'    => [
                    'Chỉnh sửa', 'In xác nhận', 'Bàn giao shipper', 'Hủy đơn hàng'
                ]
            ],
            [
                'status'    => 'Đang giao hàng',
                'action'    => [
                    'In xác nhận', 'Đã nhận hàng'
                ]
            ],
            [
                'status'    => 'Đã nhận hàng',
                'action'    => [
                    'In xác nhận'
                ]
            ],
            [
                'status'    => 'Đơn hủy',
                'action'    => [
                    'Khôi phục đơn hàng'
                ]
            ]
        ];
        RelationOrderStatusOrderAction::select('*')->delete();
        $status     = OrderStatus::all();
        $actions    = OrderAction::all();
        foreach($source as $insert){
            /* lấy id status */
            $idStatus = 0;
            foreach($status as $itemStatus){
                if(mb_strtolower($itemStatus->name)==mb_strtolower($insert['status'])) $idStatus = $itemStatus->id;
            }
            if(!empty($idStatus)){
                foreach($insert['action'] as $nameActionInsert){
                    foreach($actions as $itemAction){
                        if(mb_strtolower($itemAction->name)==mb_strtolower($nameActionInsert)){
                            RelationOrderStatusOrderAction::insertItem([
                                'order_status_id'   => $idStatus,
                                'order_action_id'   => $itemAction->id
                            ]);
                            break;
                        }
                    }
                }
            }
        }
    }
}
