<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\HelperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic;
use App\Models\District;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Blog;
use App\Models\FreeWallpaper;
use App\Models\RegistryEmail;
use App\Models\RelationFreeWallpaperUser;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Services\BuildInsertUpdateModel;
// use SebastianBergmann\Type\FalseType;

class SearchController extends Controller {

    public static function searchAjax(Request $request){
        $searchType = $request->get('search_type');
        $keySearch  = \App\Helpers\Charactor::convertStringSearch($request->get('search'));
        $language   = $request->get('language');
        switch ($searchType) {
            case 'category_info':
                /* tìm kiếm danh mục */
                $xhtmlContent = self::searchCategoryInfo($keySearch, $language);
                break;
            case 'paid_wallpaper':
                /* tìm kiếm hình nền trả phí */
                $xhtmlContent = self::searchPremiumWallpaper($keySearch, $language);
                break;
            case 'free_wallpaper':
                /* tìm kiếm hình nền miễn phí */
                $xhtmlContent = self::searchFreeWallpaper($keySearch, $language);
                break;
            case 'article':
                /* tìm kiếm bài blog */
                $xhtmlContent = self::searchBlogInfo($keySearch, $language);
                break;
            default:
                $xhtmlContent = null;
                break;
        }
        echo $xhtmlContent;
    }

    private static function searchPremiumWallpaper($keySearch, $language){
        // trường họp keySearch rỗng
        if(empty($keySearch)) {
            return view('wallpaper.template.emptySearch', compact('language'))->render();
        }
        // Lấy danh sách ID từ Meilisearch (tìm trong seo.title)
        $ids        = Product::search($keySearch)->get()->pluck('id')->toArray();
        $products   = Product::whereIn('id', $ids)
                        ->whereHas('seos.infoSeo', function($query) use($language){
                            $query->where('language', $language);
                        })
                        ->whereHas('prices.wallpapers', function(){

                        })
                        ->get();
        $count      = $products->count();

        // lấy giao diện
        if($products->isNotEmpty()) {
            return view('wallpaper.search.premiumWallpaper', compact('products', 'language', 'keySearch', 'count'));
        }

        return view('wallpaper.template.emptySearch', compact('language'))->render();
    }

    private static function searchCategoryInfo($keySearch, $language) {
        // Xử lý trường hợp keySearch rỗng
        if (empty($keySearch)) {
            return view('wallpaper.template.emptySearch', compact('language'))->render();
        }

        // Tìm kiếm ID của Category và Tag bằng Meilisearch
        $categoryIds = Category::search($keySearch)->get()->pluck('id')->toArray();
        $tagIds = Tag::search($keySearch)->get()->pluck('id')->toArray();

        // Lấy danh sách categories và tags theo ngôn ngữ
        $categories = Category::whereIn('id', $categoryIds)
            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                $query->where('language', $language);
            })
            ->get();
        $tags = Tag::whereIn('id', $tagIds)
            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                $query->where('language', $language);
            })
            ->get();

        // Gộp kết quả thành một collection
        $categories = $categories->concat($tags);
        $count      = $categories->count();

        // Trả về giao diện phù hợp
        if ($categories->isNotEmpty()) {
            return view('wallpaper.search.categoryInfo', compact('categories', 'language', 'keySearch', 'count'));
        }

        return view('wallpaper.template.emptySearch', compact('language'))->render();
    }

    private static function searchFreeWallpaper($keySearch, $language){
        $response = '<div class="emptySearchBox">Tinh năng này đang được bảo trì, vui lòng sử dụng sau!</div>';
        return $response;
    }

    private static function searchBlogInfo($keySearch, $language){
        // Xử lý trường hợp keySearch rỗng
        if (empty($keySearch)) {
            return view('wallpaper.template.emptySearch', compact('language'))->render();
        }

        // Tìm kiếm ID của Blog bằng Meilisearch
        $blogIds    = Blog::search($keySearch)->get()->pluck('id')->toArray();
        $blogs      = Blog::whereIn('id', $blogIds)
                        ->whereHas('seos.infoSeo', function($query) use($language){
                            $query->where('language', $language);
                        })
                        ->get();
        $count      = $blogs->count();
        
        // Trả về giao diện phù hợp
        if($blogs->isNotEmpty()){
            return view('wallpaper.search.blogInfo', compact('blogs', 'language', 'keySearch', 'count'));
        }
        
        return view('wallpaper.template.emptySearch', compact('language'))->render();
    }
}
