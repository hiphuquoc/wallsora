<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationCategoryInfoTagInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_category_info_tag_info';
    protected $fillable     = [
        'category_info_id', 
        'tag_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationCategoryInfoTagInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoCategory(){
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_info_id');
    }

    public function infoTag(){
        return $this->hasOne(\App\Models\Tag::class, 'id', 'tag_info_id');
    }
}
