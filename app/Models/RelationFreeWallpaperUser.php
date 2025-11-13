<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationFreeWallpaperUser extends Model {
    use HasFactory;
    protected $table        = 'relation_free_wallpaper_user';
    protected $fillable     = [
        'free_wallpaper_info_id',
        'user_info_id',
        'type'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationFreeWallpaperUser();
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

    public function infoFreeWallpaper() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'free_wallpaper_info_id');
    }

    public function infoUser() {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_info_id');
    }
}
