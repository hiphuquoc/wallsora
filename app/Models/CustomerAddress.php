<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model {
    use HasFactory;
    protected $table        = 'customer_address';
    protected $fillable     = [
        'customer_info_id',
        'address', 
        'province_info_id',
        'district_info_id'
    ];
    public $timestamps = true;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new CustomerAddress();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) $model->{$key}  = $value;
            $flag       = $model->update();
        }
        return $flag;
    }
}
