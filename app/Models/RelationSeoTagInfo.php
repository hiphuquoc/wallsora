<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationSeoTagInfo extends Model {
    use HasFactory;
    protected $table        = 'relation_seo_tag_info';
    protected $fillable     = [
        'seo_id',
        'tag_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationSeoTagInfo();
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
        return $this->hasOne(\App\Models\Tag::class, 'id', 'tag_info_id');
    }
}
