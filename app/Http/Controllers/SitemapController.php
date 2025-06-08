<?php

namespace App\Http\Controllers;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Image;

use App\Http\Controllers\Admin\HelperController;
use App\Models\Seo;

class SitemapController extends Controller {

    public static function main(){
        $tmp            = Seo::select('type')
                            ->distinct()
                            ->pluck('type');
        $arrayTable     = $tmp->toArray();
        /* viết dữ liệu */
        $sitemapXhtml       = '<urlset xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach($arrayTable as $item){
            $url            = env('APP_URL').'/sitemap/'.$item.'.xml';
            $mk             = time() - rand(3600, 259200);
            $sitemapXhtml   .= '<url>
                                    <loc>'.$url.'</loc>
                                    <lastmod>'.date('c', $mk).'</lastmod>
                                    <changefreq>weekly</changefreq>
                                    <priority>0.8</priority>
                                </url>';
        }
        $sitemapXhtml       .= '</urlset>';
        /* response */
        return response()->make($sitemapXhtml)->header('Content-Type', 'application/xml');
    }

    public static function child($name){
        if(!empty($name)){
            /* kiểm tra xem có trong loại không */
            $name               = HelperController::determinePageType($name);
            /* viết dữ liệu */
            $sitemapXhtml       = '<urlset xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach(config('language') as $language){
                if(!empty($language['key'])){
                    $language       = $language['key'];
                    $url            = env('APP_URL').'/sitemap/'.$language.'/'.$name.'.xml';
                    $mk             = time() - rand(3600, 84600);
                    $sitemapXhtml   .= '<url>
                                            <loc>'.$url.'</loc>
                                            <lastmod>'.date('c', $mk).'</lastmod>
                                            <changefreq>weekly</changefreq>
                                            <priority>0.8</priority>
                                        </url>';
                }
            }
            $sitemapXhtml       .= '</urlset>';
            /* response */
            return response()->make($sitemapXhtml)->header('Content-Type', 'application/xml');
        }
        /* return 404 */
        return \App\Http\Controllers\ErrorController::error404();
    }

    public static function childForLanguage($language, $name){
        if(!empty($name)){
            $modelName      = config('tablemysql.'.$name.'.model_name');
            $modelInstance  = resolve("\App\Models\\$modelName");
            $items          = $modelInstance::select('*')
                                ->with('seo', 'seos')
                                ->get();
            if(!empty($items)&&$items->isNotEmpty()){
                $sitemapXhtml       = '<urlset xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
                foreach($items as $item){
                    foreach($item->seos as $seo){
                        if(!empty($seo->infoSeo)&&$seo->infoSeo->language==$language){
                            $url            = env('APP_URL').'/'.self::replaceSpecialCharactorXml($seo->infoSeo->slug_full);
                            $urlImage       = env('APP_URL').Storage::url(config('main_'.env('APP_NAME').'.logo_main'));
                            if(!empty($item->seo->image)) $urlImage   = Image::getUrlImageLargeByUrlImage($item->seo->image);
                            $sitemapXhtml   .= '<url>
                                                    <loc>'.$url.'</loc>
                                                    <lastmod>'.date('c', strtotime($seo->infoSeo->updated_at)).'</lastmod>
                                                    <changefreq>hourly</changefreq>
                                                    <priority>1</priority>
                                                    <image:image>
                                                        <image:loc>'.$urlImage.'</image:loc>
                                                        <image:title>'.self::replaceSpecialCharactorXml($seo->infoSeo->seo_title).'</image:title>
                                                    </image:image>
                                                </url>';
                        }
                    }
                }
                $sitemapXhtml       .= '</urlset>';
                return response()->make($sitemapXhtml)->header('Content-Type', 'application/xml');
            }
        }
        /* return 404 */
        return \App\Http\Controllers\ErrorController::error404();
    }

    public static function replaceSpecialCharactorXml($str) {
        if (!empty($str)) {
            $dataEscape = [
                '&' => '&amp;', // Phải thay thế & đầu tiên để tránh các lỗi khác
                '<' => '&lt;',
                '>' => '&gt;',
                '"' => '&quot;',
                "'" => '&apos;'
            ];
            // Sử dụng str_replace để thay thế nhanh và tránh lỗi từ regex
            return str_replace(array_keys($dataEscape), array_values($dataEscape), $str);
        }
        return $str; // Trả về chuỗi nếu nó rỗng
    }
}
