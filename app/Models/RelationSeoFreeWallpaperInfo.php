<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationSeoFreeWallpaperInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_seo_free_wallpaper_info';
    protected $fillable     = [
        'seo_id',
        'free_wallpaper_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationSeoFreeWallpaperInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoSeo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function infoTable() {
        return $this->hasOne(\App\Models\FreeWallpaper::class, 'id', 'free_wallpaper_info_id');
    }
}
