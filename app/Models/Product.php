<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model {
    use HasFactory, Searchable;
    protected $table        = 'product_info';
    protected $fillable     = [
        'seo_id',
        'code',
        'sold',
        'price',
        'notes',
    ];
    public $timestamps = true;

    /* index dữ liệu SearchData */
    public function toSearchableArray() {
        $this->loadMissing(['seo', 'seos.infoSeo', 'tags.infoTag', 'categories.infoCategory']);

        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'title'             => $this->seo->title ?? '',
            'seos'              => $this->seos->pluck('infoSeo.title')->filter()->values()->toArray(),
            'tags'              => $this->tags->pluck('infoTag.seos.infoSeo.title')->filter()->values()->toArray(),
            'categories'        => $this->categories->pluck('infoCategory.seos.infoSeo.title')->filter()->values()->toArray(),
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
                        ->orderBy('created_at', 'DESC')
                        ->with(['files' => function($query){
                            $query->where('relation_table', 'tag_info');
                        }])
                        ->with('seo', 'seos', 'categories')
                        ->paginate($params['paginate']);
    
            return $result;
        }
    
        // Truy vấn mặc định khi không tìm kiếm
        $result = self::select('*')
                    ->when(!empty($params['search_category']), function($query) use($params){
                        $query->whereHas('categories.infoCategory', function($q) use ($params){
                            $q->where('id', $params['search_category']);
                        });
                    })
                    ->orderBy('created_at', 'DESC')
                    ->with(['files' => function($query){
                        $query->where('relation_table', 'tag_info');
                    }])
                    ->with('seo', 'seos', 'categories')
                    ->paginate($params['paginate']);
    
        return $result;
    }

    public static function listLanguageNotExists($params = null){
        $countLanguage  = count(config('language'));
        $result         = self::select('*')
                            /* chỉ lấy sản phẩm gốc */
                            ->whereHas('seo', function($query){
                                $query->whereNull('link_canonical')  // Giá trị NULL
                                    ->orWhere('link_canonical', '') // Chuỗi rỗng
                                    ->orWhere('link_canonical', 'IS NOT DEFINED') // Trường hợp không có giá trị, nếu cần
                                    ->orWhere('link_canonical', '0');
                            })
                            ->with('seo', 'seos')
                            ->withCount('seos') // Đếm số lượng `seos` cho mỗi phần tử
                            ->orderBy('created_at', 'DESC')
                            ->having('seos_count', '<', $countLanguage) // Lọc các phần tử có `seos_count` < tổng ngôn ngữ
                            ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Product();
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
            $q->join('seo', 'seo.id', '=', 'relation_seo_product_info.seo_id')
            ->where('seo.language', $language);
        })
        ->with(['seos' => function ($q) use ($language) {
            $q->join('seo', 'seo.id', '=', 'relation_seo_product_info.seo_id')
            ->where('seo.language', $language);
        }]);
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoProductInfo::class, 'product_info_id', 'id');
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id');
    }

    public function prices() {
        return $this->hasMany(\App\Models\ProductPrice::class, 'product_info_id', 'id');
    }

    public function categories(){
        return $this->hasMany(\App\Models\RelationCategoryProduct::class, 'product_info_id', 'id');
    }

    public function tags(){
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'reference_id', 'id')->where('reference_type', 'product_info');
    }
}
