<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Blog extends Model {
    use HasFactory, Searchable;
    protected $table        = 'blog_info';
    protected $fillable     = [
        'seo_id', 
        'outstanding',
        'status',
        'viewed',
        'shared',
        'notes',
    ];
    public $timestamps      = false;

    /* index dữ liệu SearchData */
    public function toSearchableArray() {
        $this->loadMissing(['seo', 'seos.infoSeo', 'categories.infoCategory']);

        return [
            'id'                => $this->id,
            'title'             => $this->seo->title ?? '',
            'seos'              => $this->seos->pluck('infoSeo.title')->filter()->values()->toArray() ?? [],
            'categories'        => $this->categories->pluck('infoCategory.seos.infoSeo.title')->filter()->values()->toArray() ?? [],
        ];
    }

    public static function getList($params = null){
        if (!empty($params['search_name'])) {
            $searchName = $params['search_name'];
    
            // Lấy danh sách ID từ Meilisearch (tìm trong seo.title)
            $ids = self::search($searchName)->get()->pluck('id')->toArray();
    
            // Truy vấn tiếp tục trong database với điều kiện khác
            $result = self::whereIn('id', $ids)
                        ->when(!empty($params['search_category']), function($query) use($params){
                            $query->whereHas('categories.infoCategory', function($q) use ($params){
                                $q->where('id', $params['search_category']);
                            });
                        })
                        ->with('seo')
                        ->orderBy('id', 'DESC')
                        ->paginate($params['paginate']);
    
            return $result;
        }

        // Truy vấn mặc định khi không tìm kiếm
        $result     = self::select('*')
                        ->when(!empty($params['search_category']), function($query) use($params){
                            $query->whereHas('categories.infoCategory', function($q) use ($params){
                                $q->where('id', $params['search_category']);
                            });
                        })
                        ->with('seo')
                        ->orderBy('id', 'DESC')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Blog();
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
            $q->join('seo', 'seo.id', '=', 'relation_seo_blog_info.seo_id')
            ->where('seo.language', $language);
        })
        ->with(['seos' => function ($q) use ($language) {
            $q->join('seo', 'seo.id', '=', 'relation_seo_blog_info.seo_id')
            ->where('seo.language', $language);
        }]);
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoBlogInfo::class, 'blog_info_id', 'id');
    }

    public function categories(){
        return $this->hasMany(\App\Models\RelationCategoryBlogBlogInfo::class, 'blog_info_id', 'id');
    }
}
