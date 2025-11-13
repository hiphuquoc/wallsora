<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Jobs\BuildScss;
use App\Helpers\Charactor;

use App\Models\Seo;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Page;
use App\Models\Product;
use App\Models\CategoryBlog;
use App\Models\Blog;
use App\Helpers\Upload;
use App\Models\FreeWallpaper;

class HelperController extends Controller {

    public static function convertStrToSlug(Request $request){
        $string             = $request->get('string') ?? null;
        $language           = $request->get('language');
        $idPageParentVI     = $request->get('id_parent_vi') ?? 0;
        $type               = $request->get('type') ?? null;
        $response           = self::buildSlugFromTitle($string, $type, $language, $idPageParentVI);
        echo $response;
    }

    public static function buildSlugFromTitle($title, $type, $language, $idPageParentVI = 0){
        $response = '';
        if(!empty($title)&&$language){
            $title                  = mb_strtolower($title);
            $type                   = self::determinePageType($type);
            /* trường hợp có gửi vào id của trang cha (bảng vi) */
            if(!empty($idPageParentVI)){
                $infoPageParentVI   = self::getFullInfoPageByIdSeo($idPageParentVI);
                if($type=='category_info'||$type=='tag_info'||$type=='page_info'){ /* trường hợp là category_info hay tag_info hay page_info */
                    /* lấy slug của parent */
                    $slugParent         = null;
                    foreach($infoPageParentVI->seos as $seo){
                        if($seo->infoSeo->language==$language){
                            $slugParent = $seo->infoSeo->slug;
                            break;
                        }
                    }
                    /* lấy chiều ghép và tiến hành ghép */
                    $part       = Charactor::convertStrToUrl($title);
                    $charactor  = config('language.'.$language.'.flag_has_space_in_content')==true ? '-' : '';
                    if(config('language.'.$language.'.flag_join_left_to_right')==true){
                        $response = $slugParent.$charactor.$part;
                    }else {
                        $response = $part.$charactor.$slugParent;
                    }
                } else if($type=='product_info'||$type=='free_wallpaper_info'||$type=='blog_info'){ /* trường hợp là product_info hay free_wallpaper_info */
                    $response   = Charactor::convertStrToUrl($title).'-'.time();
                } else if($type=='category_blog'){ /* trường hợp là category_blog */
                    $response   = Charactor::convertStrToUrl($title);
                }
            } else { /* trường hợp không có trang cha */
                $response   = Charactor::convertStrToUrl($title);
            }
        }
        return $response;
    }

    public static function getFullInfoPageByIdSeo($idSeo){
        $response               = new \stdClass;
        /* kiểm tra xem loại nào */
        $infoSeo                = Seo::select('*')
                                    ->where('id', $idSeo)
                                    ->first();
        if(!empty($infoSeo)){
            $type               = $infoSeo->type;
            switch ($type) {
                case 'tag_info':
                    $response   = Tag::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'product_info':
                    $response   = Product::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'page_info':
                    $response   = Page::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'category_blog':
                    $response   = CategoryBlog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'blog_info':
                    $response   = Blog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'free_wallpaper_info':
                    $response   = FreeWallpaper::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                default:
                    /* vì có thể là category_info, style_info, event_info */
                    $response   = Category::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
            }
        }
        return $response;
    }
    
    public static function removeUnicodeControlCharacters($string) {
        // Regular expression to match zero-width and formatting control characters
        $zeroWidthPattern = '/[\x{200B}-\x{200F}\x{2060}\x{FEFF}]/u';
        return preg_replace($zeroWidthPattern, '', $string);
    }

    public static function determinePageType($type){
        $response = null;
        if(!empty($type)){
            /* các trường hợp còn lại trả về đúng loại */
            $response           = $type;
            /* xử lý trường hợp category */
            $categoryType       = config('main_'.env('APP_NAME').'.category_type');
            foreach($categoryType as $cType){
                if($type==$cType['key']){
                    $response   = $categoryType[0]['key']; /* giá trị mặc định category_info */
                    break;
                }
                
            }
        }
        return $response;
    }

    public static function deleteLanguage(Request $request){
        $idSeoVi    = $request->get('id_seo_vi');
        $languages  = $request->get('languages');
        $infoPage   = self::getFullInfoPageByIdSeo($idSeoVi);
        $count      = 0;
        if(!empty($infoPage)&&!empty($languages)){
            foreach($infoPage->seos as $s){
                if(!empty($s->infoSeo->language)&&in_array($s->infoSeo->language, $languages)){
                    /* xóa ảnh đại diện trên google_clouds */ 
                    Upload::deleteWallpaper($s->infoSeo->image);
                    foreach($s->infoSeo->contents as $c) $c->delete();
                    $s->infoSeo()->delete();
                    $s->delete();
                    ++$count;
                }
            }
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã xóa '.$count.' trang ngôn ngữ!',
            ];
            $request->session()->put('message', $message);
        }
    }

    /* Chuẩn hóa Unicode: Sử dụng Normalizer của PHP (có sẵn trong phần mở rộng intl) để chuẩn hóa chuỗi đầu vào. Điều này đảm bảo các ký tự có dấu được so sánh một cách chính xác. */
    public static function normalizeUnicode($string) {
        return \Normalizer::normalize($string, \Normalizer::FORM_C);
    }
}
