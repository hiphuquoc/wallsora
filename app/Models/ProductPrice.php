<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model {
    use HasFactory;
    protected $table        = 'product_price';
    protected $fillable     = [
        'code_name',
        'product_info_id',
        'price',
        'instock'
    ];
    public $timestamps = false;
    private static $columnFilter = [
        'code_name',
        'product_info_id',
        'price',
        'instock'
    ];

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new ProductPrice();
            foreach($params as $key => $value) {
                if(in_array($key, self::$columnFilter)) $model->{$key}  = $value;
            }
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) {
                if(in_array($key, self::$columnFilter)) $model->{$key}  = $value;
            }
            $flag       = $model->update();
        }
        return $flag;
    }

    public function wallpapers(){
        return $this->hasMany(\App\Models\RelationProductPriceWallpaperInfo::class, 'product_price_id', 'id');
    }
}
