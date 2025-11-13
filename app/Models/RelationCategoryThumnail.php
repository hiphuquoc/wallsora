<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationCategoryThumnail extends Model {
    use HasFactory;
    protected $table        = 'relation_category_thumnail';
    protected $fillable     = [
        'category_info_id', 
        'free_wallpaper_info_id',
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationCategoryThumnail();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoCategory(){
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_info_id');
    }

    public function infoFreewallpaper(){
        return $this->hasOne(\App\Models\FreeWallpaper::class, 'id', 'free_wallpaper_info_id');
    }
}
