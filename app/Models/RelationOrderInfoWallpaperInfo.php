<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationOrderInfoWallpaperInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_order_info_wallpaper_info';
    protected $fillable     = [
        'order_info_id', 
        'wallpaper_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationOrderInfoWallpaperInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoOrder(){
        return $this->hasOne(\App\Models\Order::class, 'id', 'order_info_id');
    }

    public function infoWallpaper(){
        return $this->hasOne(\App\Models\Wallpaper::class, 'id', 'wallpaper_info_id');
    }
}
