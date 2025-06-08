<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    use HasFactory;
    protected $table        = 'page_info';
    protected $fillable     = [
        'seo_id',
        'type_id',
        'name', 
        'description',
        'show_sidebar',
        'notes',
    ];
    public $timestamps = false;

    public static function getList($params = null){
        $result     = self::select('*')
                        ->whereHas('seo', function($query){
                            $query->where('language', 'vi');
                        })
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->whereHas('seo', function($subQuery) use($params){
                                $subQuery->where('title', 'like', '%'.$params['search_name'].'%');
                            });
                        })
                        ->orderBy('id', 'DESC')
                        ->with(['files' => function($query){
                            $query->where('relation_table', 'page_info');
                        }])
                        ->with('seo')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Page();
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

    // quan trọng dùng để tối ưu vòng lặp khi duyệt tìm ngôn ngữ
    public function scopeWithDefaultSeoForLanguage($query, $language) {
        return $query->whereHas('seos', function ($q) use ($language) {
            $q->join('seo', 'seo.id', '=', 'relation_seo_page_info.seo_id')
            ->where('seo.language', $language);
        })
        ->with(['seos' => function ($q) use ($language) {
            $q->join('seo', 'seo.id', '=', 'relation_seo_page_info.seo_id')
            ->where('seo.language', $language);
        }]);
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoPageInfo::class, 'page_info_id', 'id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id');
    }

    public function type(){
        return $this->hasOne(\App\Models\PageType::class, 'id', 'type_id');
    }

    // public function languages(){
    //     return $this->hasMany(\App\Models\LanguagePageInfo::class, 'id_1', 'id');
    // }

    // public function contents(){
    //     return $this->hasMany(\App\Models\PageContent::class, 'page_info_id', 'id');
    // }
}
