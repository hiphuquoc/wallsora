<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationOrderStatusOrderAction extends Model {
    use HasFactory;
    protected $table        = 'relation_order_status_order_action';
    protected $fillable     = [
        'order_status_id', 
        'order_action_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationOrderStatusOrderAction();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoStatus(){
        return $this->hasOne(\App\Models\OrderStatus::class, 'id', 'order_status_id');
    }

    public function infoAction(){
        return $this->hasOne(\App\Models\OrderAction::class, 'id', 'order_action_id');
    }
}
