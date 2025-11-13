<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationProductPriceWallpaperInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_product_price_wallpaper_info';
    protected $fillable     = [
        'product_price_id', 
        'wallpaper_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationProductPriceWallpaperInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoWallpaper(){
        return $this->hasOne(\App\Models\Wallpaper::class, 'id', 'wallpaper_info_id');
    }
}
