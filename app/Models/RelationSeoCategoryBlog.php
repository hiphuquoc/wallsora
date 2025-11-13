<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationSeoCategoryBlog extends Model {
    use HasFactory;
    protected $table        = 'relation_seo_category_blog';
    protected $fillable     = [
        'seo_id',
        'category_blog_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationSeoCategoryBlog();
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
        return $this->hasOne(\App\Models\CategoryBlog::class, 'id', 'category_blog_id');
    }
}
