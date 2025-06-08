<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FreeWallpaper extends Model {
    use HasFactory;
    protected $table        = 'free_wallpaper_info';
    protected $fillable     = [
        'user_id',
        'name', 
        'description',
        'file_name',
        'extension',
        'file_cloud',
        'width',
        'height',
        'file_size',
        'mine_type',
        'heart',
        'ha_ha',
        'not_like',
        'vomit',
        'notes',
    ];
    public $timestamps = true;

    public static function getList($params = null){
        $arrayLanguageAccept = [];
        foreach(config('language') as $language) if($language['key']!='vi') $arrayLanguageAccept[] = $language['key'];
        $result     = self::select('*')
                        ->whereDoesntHave('seo', function ($query) use($arrayLanguageAccept){
                            $query->whereIn('language', $arrayLanguageAccept);
                        })
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $query->whereHas('seo', function($subQuery) use($params){
                                $subQuery->where('title', 'like', '%'.$params['search_name'].'%');
                            });
                        })
                        ->orderBy('created_at', 'DESC')
                        ->with('categories')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new FreeWallpaper();
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
            $q->join('seo', 'seo.id', '=', 'relation_seo_free_wallpaper_info.seo_id')
            ->where('seo.language', $language);
        })
        ->with(['seos' => function ($q) use ($language) {
            $q->join('seo', 'seo.id', '=', 'relation_seo_free_wallpaper_info.seo_id')
            ->where('seo.language', $language);
        }]);
    }

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoFreeWallpaperInfo::class, 'free_wallpaper_info_id', 'id');
    }
    
    public function categories(){
        return $this->hasMany(\App\Models\RelationFreewallpaperCategory::class, 'free_wallpaper_info_id', 'id');
    }

    public function tags(){
        return $this->hasMany(\App\Models\RelationTagInfoOrther::class, 'reference_id', 'id')->where('reference_type', 'free_wallpaper_info');
    }

    public function feeling(){
        return $this->hasOne(\App\Models\RelationFreeWallpaperUser::class, 'free_wallpaper_info_id', 'id')->where('user_info_id', Auth::user()->id ?? null);
    }

    public function thumnailsOf(){
        return $this->hasMany(\App\Models\RelationCategoryThumnail::class, 'free_wallpaper_info_id', 'id');
    }
}
