<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationFreewallpaperCategory extends Model {
    use HasFactory;
    protected $table        = 'relation_free_wallpaper_category';
    protected $fillable     = [
        'free_wallpaper_info_id',
        'category_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationFreewallpaperCategory();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoFreewallpaper() {
        return $this->hasOne(\App\Models\FreeWallpaper::class, 'id', 'free_wallpaper_info_id');
    }

    public function infoCategory() {
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_info_id');
    }
}
