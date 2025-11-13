<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationOrderInfoProductInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_order_info_product_info';
    protected $fillable     = [
        'order_info_id', 
        'product_info_id',
        'product_price_id',
        'quantity',
        'price',
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationOrderInfoProductInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoOrder(){
        return $this->hasOne(\App\Models\Order::class, 'id', 'order_info_id');
    }

    public function infoProduct(){
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_info_id');
    }
}
