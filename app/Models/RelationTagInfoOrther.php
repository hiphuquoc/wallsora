<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationTagInfoOrther extends Model {
    use HasFactory;
    protected $table        = 'relation_tag_info_orther';
    protected $fillable     = [
        'tag_info_id',
        'reference_id',
        'reference_type'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationTagInfoOrther();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoTag() {
        return $this->hasOne(\App\Models\Tag::class, 'id', 'tag_info_id');
    }

    public function infoProduct() {
        return $this->hasOne(\App\Models\Product::class, 'id', 'reference_id');
    }

    public function infoFreeWallpaper() {
        return $this->hasOne(\App\Models\FreeWallpaper::class, 'id', 'reference_id');
    }
    
}
