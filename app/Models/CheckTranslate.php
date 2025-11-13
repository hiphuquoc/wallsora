<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckTranslate extends Model {
    use HasFactory;
    protected $table        = 'check_translate';
    protected $fillable     = [
        'seo_id',
        'language', 
        'type',

        'title_vi',
        'seo_title_vi',
        'seo_description_vi',

        'title_en',
        'seo_title_en',
        'seo_description_en',

        'title',
        'title_google_translate_vi',
        'title_google_translate_en',
        'seo_title',
        'seo_description',

        'new_title',
        'new_title_google_translate_vi',
        'new_title_google_translate_en',
        'new_seo_title',
        'new_seo_description',
        
        'status',
    ];
    public $timestamps = true;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new CheckTranslate();
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

    public function infoSeo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

}
