<?php

namespace App\Models;

use App\Http\Controllers\Admin\HelperController;
use App\Http\Controllers\Admin\RedirectController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class Seo extends Model {
    use HasFactory;
    protected $table        = 'seo';
    protected $fillable     = [
        'title', 
        'description', 
        'image',
        'image_small',
        'level', 
        'parent', 
        'ordering',
        'topic', 
        'seo_title',
        'seo_description',
        'slug',
        'slug_full',
        'link_canonical',
        'type',
        'rating_author_name', 
        'rating_author_star',
        'rating_aggregate_count', 
        'rating_aggregate_star',
        'created_at',
        'updated_at',
        'language',
    ];

    public static function insertItem($params, $idSeoVI = 0){ /* truyền thêm idSeoVi nếu muốn kiểm tra */
        $id                 = 0;
        if(!empty($params)){
            /* kiểm tra slug_full và language có trùng không trước khi insert */
            $flagNext           = false;
            if(!empty($idSeoVI)){
                $flagCheckLanguage  = self::checkLanguageUnique($idSeoVI, $params['language']);
                $flagCheckSLugFull  = self::checkSlugFullUnique($params['slug_full'], 'insert', 0);
                if($flagCheckLanguage==false&&$flagCheckSLugFull==false) $flagNext = true;
            }else {
                $flagCheckSLugFull  = self::checkSlugFullUnique($params['slug_full'], 'insert', 0);
                if($flagCheckSLugFull==false) $flagNext = true;
            }
            /* tiến hành */
            if($flagNext==true){
                $model      = new Seo();
                foreach($params as $key => $value) $model->{$key}  = $value;
                $model->save();
                $id         = $model->id;
            }
        }
        return $id;
    }

    public static function updateItem($id, $params){
        // Đặt thời gian chờ không giới hạn
        set_time_limit(0);
        $flag               = false;
        if(!empty($id)&&!empty($params)){
            $model          = self::find($id);
            /* kiểm tra slug_full có phải duy nhất không */
            $slugFullNew    = self::buildFullUrl($params['slug'], $model->parent);
            $flagCheckSLugFull = self::checkSlugFullUnique($slugFullNew, 'update', $id);
            if($flagCheckSLugFull==false){
                /* lấy slug_full cũ - mới để so sánh */
                $slugFullOld    = $model->slug_full;
                foreach($params as $key => $value) $model->{$key}  = $value;
                $flag           = $model->update();
                /* mỗi lần cập nhật lại slug thì phải build lại slug_full của toàn bộ children và thay thế internal link trong tất cả content của cả slug hiện tại và slug con */
                if($slugFullOld!=$slugFullNew) {
                    /* tạo bản redirect 301 */
                    $urlOldWithPrefix   = RedirectController::filterUrl($slugFullOld);
                    $urlNewWithPrefix   = RedirectController::filterUrl($slugFullNew);
                    RedirectController::createRedirectAndFix($urlOldWithPrefix, $urlNewWithPrefix);
                    /* thay thế internal link trong tất cả content của slug hiện tại */
                    self::replaceInternalLinksInSeoContents($slugFullOld, $slugFullNew);
                    /* cập nhật lại slug_full của phần tử con */
                    self::updateSlugChilds($model->id);
                }
            }
        }
        return $flag;
    }

    public static function replaceInternalLinksInSeoContents($slugOld, $slugNew){
        $baseUrl        = env('APP_URL');

        $contentsMatch = SeoContent::whereRaw('content REGEXP ?', [
            'href=["\']' . preg_quote($baseUrl . '/' . HelperController::normalizeUnicode($slugOld), '/') . '(\?.*)?["\']'
        ])
        ->orWhereRaw('content REGEXP ?', [
            'href=["\']\.\./\.\./' . preg_quote(HelperController::normalizeUnicode($slugOld), '/') . '(\?.*)?["\']'
        ])
        ->get();

        // Xử lý từng bản ghi
        foreach ($contentsMatch as $content) {
            $content->content = self::replaceInternalLinks($slugOld, $slugNew, $content->content);
            $content->save();
        }
    }

    public static function replaceInternalLinks($slugOld, $slugNew, $content) {
        // Lấy giá trị URL từ biến môi trường
        $baseUrl = env('APP_URL');
        // Sử dụng regex để tìm và thay thế các liên kết nội bộ trong thuộc tính href
        $patterns = [
            // Định dạng URL đầy đủ
            '/href=["\']' . preg_quote($baseUrl, '/') . '\/' . preg_quote($slugOld, '/') . '(\?.*?)?["\']/u',
            // Định dạng URL tương đối
            '/href=["\']\.\.\/\.\.\/' . preg_quote($slugOld, '/') . '(\?.*?)?["\']/u'
        ];
        $replacements = [
            'href="' . $baseUrl . '/' . $slugNew . '$1"',
            'href="../../' . $slugNew . '$1"'
        ];
        // Thay thế các liên kết trong content
        $updatedContent = preg_replace($patterns, $replacements, $content);
        return $updatedContent;
    }

    public static function updateSlugChilds($idParent){
        $childs = self::select('id', 'level', 'parent', 'slug', 'slug_full')
                    ->where('parent', $idParent)
                    ->get();
        foreach($childs as $child){
            $slugFullNew     = self::buildFullUrl($child->slug, $child->parent);
            $slugFullOld     = $child->slug_full;
            if($slugFullNew!=$slugFullOld){
                 /* cập nhật lại slug_full */
                $paramsUpdate   = [
                    'slug'      => $child->slug,
                    'slug_full' => $slugFullNew
                ];
                self::updateItem($child->id, $paramsUpdate);
                /* tạo redirect 301 */
                $urlOldWithPrefix   = RedirectController::filterUrl($slugFullOld);
                $urlNewWithPrefix   = RedirectController::filterUrl($slugFullNew);
                RedirectController::createRedirectAndFix($urlOldWithPrefix, $urlNewWithPrefix);
                /* thay thế internal link trong tất cả content */
                self::replaceInternalLinksInSeoContents($slugFullOld, $slugFullNew);
                /* kiểm tra xem có child cấp thấp hơn không */
                $numberChildsOfChild = self::where('parent', $child->id)->count();
                if($numberChildsOfChild>0) self::updateSlugChilds($child->id);
            }
        }
    }

    public static function getItemBySlug($slug = null){
        $result = null;
        if(!empty($slug)){
            $result = self::select('*')
                        ->where('slug', $slug)
                        ->first();
        }
        return $result;
    }

    public static function buildFullUrl($slug, $parent = 0){
        $url    = $slug;
        if(!empty($parent)){
            $infoSeo    = self::select('slug_full')
                            ->where('id', $parent)
                            ->first();
            if(!empty($infoSeo->slug_full)){
                $url    =  $infoSeo->slug_full.'/'.$slug;
            }
        }
        return $url;
    }

    public static function checkSlugFullUnique($slugFull, $type = 'insert', $idSeo = 0){
        $flag           = true; /* cờ đánh dấu trùng */
        $slugFull       = trim($slugFull, '/');
        /* trường hợp insert */
        if($type=='insert'){
            $infoSeo    = self::select('*')
                            ->whereRaw('slug_full COLLATE utf8mb4_bin = ?', [$slugFull]) /* chỉ định so sánh dấu */
                            ->first();
            if(empty($infoSeo)) $flag = false;
        }
        /* trường hợp update */
        if($type=='update'&&!empty($idSeo)){
            $infoSeo    = self::select('*')
                             ->whereRaw('slug_full COLLATE utf8mb4_bin = ?', [$slugFull])
                            ->where('id', '!=', $idSeo)
                            ->first();
            if(empty($infoSeo)) $flag = false;
        }
        return $flag;
    }

    public static function checkLanguageUnique($idSeoVi, $language){
        $flag   = false;
        $tmp    = HelperController::getFullInfoPageByIdSeo($idSeoVi);
        if(!empty($tmp)&&!empty($language)){
            foreach($tmp->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language) {
                    $flag = true;
                    break;
                }
            }
        }
        return $flag;
    }

    public function user(){
        return $this->hasOne(\App\Models\User::class, 'id', 'rating_author_name');
    }

    public function contents(){
        return $this->hasMany(\App\Models\SeoContent::class, 'seo_id', 'id')->orderBy('ordering')->orderBy('id');
    }

    public function source(){
        return $this->hasOne(\App\Models\Seo::class, 'id', 'link_canonical');
    }

    public function jobAutoTranslate(){
        return $this->hasMany(\App\Models\JobAutoTranslate::class, 'seo_id', 'id')->whereColumn('language', 'language');
    }

    public function jobAutoTranslateLinks() {
        return $this->hasMany(\App\Models\JobAutoTranslateLinks::class, 'seo_id', 'id');
    }
}
