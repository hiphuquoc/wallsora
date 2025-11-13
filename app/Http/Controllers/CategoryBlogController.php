<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\CategoryBlog;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class CategoryBlogController extends Controller {

    public static function showSortBoxInCategoryTag(Request $request){
        $xhtml              = '';
        $id                 = $request->get('id');
        $language           = $request->get('language');
        /* select của filter */
        $tmp                = CategoryBlog::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('flag_show', true)
                                ->with('seos.infoSeo', function($query) use ($language) {
                                    $query->where('language', $language);
                                })
                                ->get();
        $categoryLv1        = new \Illuminate\Database\Eloquent\Collection;
        $categories         = new \Illuminate\Database\Eloquent\Collection;
        foreach($tmp as $t){
            if(!empty($t->seo->level==1)){
                $categoryLv1 = $t; /* trang cấp 1 */
            }else { /* trang cấp con */
                $categories->push($t);
            }
        }
        /* giá trị selectBox */
        $categoryChoose     = new \Illuminate\Database\Eloquent\Collection;
        foreach($categories as $tmp){
            if($tmp->id==$id) {
                $categoryChoose = $tmp;
                break;
            }
        }
        /* total */
        if(!empty($categoryChoose->id)){
            $total = $categoryChoose->blogs->count();
        }else {
            $total = Blog::select('id')->count();
        }
        $xhtml              = view('wallpaper.categoryBlog.sortContent', [
            'language'          => $language,
            'total'             => $total,
            'categories'        => $categories,
            'categoryChoose'    => $categoryChoose,
            'categoryLv1'       => $categoryLv1,
        ])->render();
        return $xhtml;
    }

    public static function getBlogs($params, $language){
        $keySearch          = $params['search'] ?? null;
        $sortBy             = $params['sort_by'] ?? null;
        $loaded             = $params['loaded'] ?? 0;
        $requestLoad        = $params['request_load'] ?? 10;
        $arrayIdCategory    = $params['array_category_blog_id'] ?? [];
        $response           = [];
        $wallpapers         = Blog::select('blog_info.*')
                                ->join('seo', 'seo.id', '=', 'blog_info.seo_id')
                                ->where('status', 1)
                                ->whereHas('seos.infoSeo', function ($query) use ($language, $keySearch) {
                                    $query->where('language', $language)
                                        ->where('title', 'like', '%' . $keySearch . '%');
                                })
                                ->when(!empty($arrayIdCategory), function($query) use ($arrayIdCategory) {
                                    $query->whereHas('categories', function($query) use ($arrayIdCategory) {
                                        $query->whereIn('category_blog_id', $arrayIdCategory);
                                    });
                                })
                                ->when(empty($sortBy), function($query) {
                                    $query->orderBy('id', 'DESC');
                                })
                                ->when($sortBy == 'newest' || $sortBy == 'propose', function($query) {
                                    $query->orderBy('id', 'DESC');
                                })
                                // ->when($sortBy == 'favourite', function($query) {
                                //     $query->orderBy('heart', 'DESC')
                                //         ->orderBy('id', 'DESC');
                                // })
                                ->when($sortBy == 'oldest', function($query) {
                                    $query->orderBy('id', 'ASC');
                                })
                                ->with(['seos.infoSeo' => function($query) use ($language) {
                                    $query->where('language', $language);
                                }, 'seo', 'seos'])
                                ->orderBy('seo.ordering', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->skip($loaded)
                                ->take($requestLoad)
                                ->get();
        $total              = Blog::select('blog_info.*')
                                ->join('seo', 'seo.id', '=', 'blog_info.seo_id')
                                ->whereHas('seos.infoSeo', function ($query) use ($language, $keySearch) {
                                    $query->where('language', $language)
                                        ->where('title', 'like', '%' . $keySearch . '%');
                                })
                                ->when(!empty($arrayIdCategory), function($query) use($arrayIdCategory){
                                    $query->whereHas('categories', function($query) use($arrayIdCategory) {
                                        $query->whereIn('category_blog_id', $arrayIdCategory);
                                    });
                                })
                                ->count();
        $response['blogs']      = $wallpapers;
        $response['total']      = $total;
        $response['loaded']     = $loaded + $requestLoad;
        return $response;
    }
}
