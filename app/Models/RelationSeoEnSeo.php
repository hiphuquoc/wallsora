<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationSeoEnSeo extends Model {
    use HasFactory;
    protected $table        = 'relation_seo_en_seo';
    protected $fillable     = [
        'seo_id',
        'en_seo_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationSeoEnSeo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoSeo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function infoEnSeo() {
        return $this->hasOne(\App\Models\EnSeo::class, 'id', 'en_seo_id');
    }
}
