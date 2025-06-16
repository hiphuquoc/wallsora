<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
// use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Url;
use App\Http\Controllers\CategoryMoneyController;
use App\Models\Blog;
use App\Models\Category;
// use App\Models\Tag;
// use App\Models\Style;
use App\Models\Customer;
use App\Models\Page;
use App\Models\CategoryBlog;
use App\Models\FreeWallpaper;
// use App\Models\Seo;
use App\Helpers\GeoIP;
use App\Models\ISO3166;
use Illuminate\Support\Facades\Auth;

use App\Services\HtmlCacheService;
use App\Services\HeaderMainService;

class RoutingController extends Controller{

    // public function routing(Request $request) {
    //     // 1. Xử lý đường dẫn và giải mã URL
    //     $slug = $request->path();
    //     $decodedSlug = urldecode($slug);
    //     $tmpSlug = explode('/', $decodedSlug);
    
    //     // Loại bỏ phần tử rỗng và các phần không cần thiết (ví dụ: 'public')
    //     $arraySlug = array_filter($tmpSlug, function ($part) {
    //         return !empty($part) && $part !== 'public';
    //     });
    
    //     // Loại bỏ hashtag và query string từ phần cuối cùng của đường dẫn
    //     $arraySlug[count($arraySlug) - 1] = preg_replace('#([\?|\#]+).*$#imsU', '', end($arraySlug));
    //     $urlRequest = implode('/', $arraySlug);
    
    //     // 2. Kiểm tra xem URL có tồn tại trong cơ sở dữ liệu không
    //     $itemSeo = Url::checkUrlExists(end($arraySlug));
    
    //     // Nếu URL không khớp, redirect về URL chính xác
    //     if (!empty($itemSeo->slug_full) && $itemSeo->slug_full !== $urlRequest) {
    //         return Redirect::to($itemSeo->slug_full, 301);
    //     }
    
    //     // 3. Nếu URL hợp lệ, xử lý dữ liệu
    //     if (!empty($itemSeo->type)) {
    //         // Thiết lập ngôn ngữ và cấu hình theo IP
    //         $language = $itemSeo->language;
    //         SettingController::settingLanguage($language);
    //         if (empty(session()->get('info_ip'))) {
    //             SettingController::settingIpVisitor();
    //         }
    
    //         // Xử lý tham số tìm kiếm
    //         $search = request('search') ?? null;
    //         $paramsSlug = [];
    //         if (!empty($search)) $paramsSlug['search'] = $search;
            
    //         // Tạo key và đường dẫn cache
    //         $appName        = env('APP_NAME');
    //         $cacheKey   = self::buildNameCache($itemSeo->slug_full, $paramsSlug);
    //         $cacheName = $cacheKey . '.' . config("main_" . $appName . ".cache.extension");
    //         $cacheFolder = config("main_" . $appName . ".cache.folderSave");
    //         $cachePath = $cacheFolder . $cacheName;

    //         $disk       = Storage::disk(config("main_{$appName}.cache.disk"));
    //         $useCache   = env('APP_CACHE_HTML', true);
    //         $redisTtl   = config('app.cache_redis_time', 86400);     // Redis: 1 ngày
    //         $fileTtl    = config('app.cache_html_time', 2592000);     // GCS: 30 ngày
    
    //         $htmlContent = null;
    
    //         // 5. Nếu không có Redis → thử từ GCS (qua CDN)
    //         if ($useCache && !$htmlContent && $disk->exists($cachePath)) {
    //             $lastModified = $disk->lastModified($cachePath);
    //             if ((time() - $lastModified) < $fileTtl) {
    //                 $htmlContent = Storage::get($cachePath);
    //                 if ($htmlContent) {
    //                     Cache::put($cacheKey, $htmlContent, $redisTtl);
    //                 }
    //             }
    //         }
    
    //         // 6. Nếu không có cache → Render
    //         if (!$htmlContent) {
    //             // Lấy dữ liệu thông qua hàm fetchDataForRouting
    //             $htmlContent = $this->fetchDataForRouting($itemSeo, $language);
    
    //             if (!$htmlContent) {
    //                 return \App\Http\Controllers\ErrorController::error404();
    //             }
    
    //             // Lưu cache lại nếu bật
    //             if ($useCache) {
    //                 $disk->put($cachePath, $htmlContent);
    //             }
    //         }
    
    //         echo $htmlContent;
    //     } else {
    //         return \App\Http\Controllers\ErrorController::error404();
    //     }
    // }

    public function routing(Request $request, HtmlCacheService $htmlCacheService) {
        // 1. Xử lý đường dẫn và giải mã URL
        $slug = $request->path();
        $decodedSlug = urldecode($slug);
        $tmpSlug = explode('/', $decodedSlug);
    
        // Loại bỏ phần tử rỗng và các phần không cần thiết (ví dụ: 'public')
        $arraySlug = array_filter($tmpSlug, function ($part) {
            return !empty($part) && $part !== 'public';
        });
    
        // Loại bỏ hashtag và query string từ phần cuối cùng của đường dẫn
        $arraySlug[count($arraySlug) - 1] = preg_replace('#([\?|\#]+).*$#imsU', '', end($arraySlug));
        $urlRequest = implode('/', $arraySlug);
    
        // 2. Kiểm tra xem URL có tồn tại trong cơ sở dữ liệu không
        $itemSeo = Url::checkUrlExists(end($arraySlug));
    
        // Nếu URL không khớp, redirect về URL chính xác
        if (!empty($itemSeo->slug_full) && $itemSeo->slug_full !== $urlRequest) {
            return Redirect::to($itemSeo->slug_full, 301);
        }
    
        // 3. Nếu URL hợp lệ, xử lý dữ liệu
        if (!empty($itemSeo->type)) {
            // Thiết lập ngôn ngữ và cấu hình theo IP
            $language = $itemSeo->language;
            SettingController::settingLanguage($language);
            if (empty(session()->get('info_ip'))) {
                SettingController::settingIpVisitor();
            }
    
            // Tạo key cache
            $paramsSlug = request()->only('search');
            $cacheKey = self::buildNameCache($itemSeo->slug_full, $paramsSlug);
            
            // Dùng HtmlCacheService để lấy hoặc render
            $htmlContent = $htmlCacheService->getOrRender($cacheKey, function () use ($itemSeo, $language) {
                return $this->fetchDataForRouting($itemSeo, $language);
            });

            echo $htmlContent;
        } else {
            return \App\Http\Controllers\ErrorController::error404();
        }
    }

    // Hàm hỗ trợ để lấy dữ liệu cho routing
    private function fetchDataForRouting($itemSeo, $language) {
        // Breadcrumb
        $breadcrumb = Url::buildBreadcrumb($itemSeo->slug_full);
    
        // Thông tin cơ bản
        $modelName = config('tablemysql.' . $itemSeo->type . '.model_name');
        $modelInstance = resolve("\App\Models\\$modelName");
        $idSeo = $itemSeo->id;

        // lấy html header main menu
        $menuHtml = app(HeaderMainService::class)->getMenuHtml();
    
        // Lấy dữ liệu chính
        $item = $modelInstance::select('*')
            ->whereHas('seos', function ($query) use ($idSeo) {
                $query->where('seo_id', $idSeo);
            })
            ->with('seo', 'seos')
            ->first();
    
        if (!$item) return null; 

        // Thêm menuHtml và breadcrumb vào dữ liệu chung
        $sharedData = compact('menuHtml', 'breadcrumb');
    
        // Xử lý theo từng loại type
        switch ($itemSeo->type) {
            case 'free_wallpaper_info':
                return $this->handleFreeWallpaperInfo($item, $itemSeo, $language, $sharedData);
    
            case 'tag_info':
                return $this->handleTagInfo($item, $itemSeo, $language, $sharedData);
    
            case 'product_info':
                return $this->handleProductInfo($item, $itemSeo, $language, $sharedData);
    
            case 'page_info':
                return $this->handlePageInfo($item, $itemSeo, $language, $sharedData);
    
            case 'category_blog':
                return $this->handleCategoryBlog($item, $itemSeo, $language, $sharedData);
    
            case 'blog_info':
                return $this->handleBlogInfo($item, $itemSeo, $language, $sharedData);
    
            default:
                foreach (config('main_' . env('APP_NAME') . '.category_type') as $type) {
                    if ($itemSeo->type === $type['key']) {
                        return $this->handleCategoryType($item, $itemSeo, $language, $sharedData);
                    }
                }
                break;
        }
    
        return null; // Trường hợp không khớp type nào
    }

    private function handleFreeWallpaperInfo($item, $itemSeo, $language, array $sharedData) {
        $idNot = $item->id;
        $arrayIdCategory = [];
        foreach ($item->categories as $category) {
            if (!empty($category->infoCategory)) {
                $arrayIdCategory[] = $category->infoCategory->id;
            }
        }
    
        $total = FreeWallpaper::select('*')
            ->where('id', '!=', $item->id)
            ->whereHas('categories.infoCategory', function ($query) use ($arrayIdCategory) {
                $query->whereIn('id', $arrayIdCategory);
            })
            ->count();
        $loaded         = 0;
        $related = FreeWallpaper::select('*')
            ->where('id', '!=', $item->id)
            ->whereHas('categories.infoCategory', function ($query) use ($arrayIdCategory) {
                $query->whereIn('id', $arrayIdCategory);
            })
            ->orderBy('id', 'DESC')
            ->skip(0)
            ->take($loaded)
            ->get();

        return view('wallpaper.freeWallpaper.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'idNot'    => $idNot,
            'total'     => $total,
            'loaded'   => $loaded,
            'related'   => $related,
            'arrayIdCategory' => $arrayIdCategory,
        ], $sharedData))->render();
    }

    private function handleTagInfo($item, $itemSeo, $language, array $sharedData) {
        /* tìm theo category */
        $arrayIdCategory    = []; /* rỗng do đang tìm theo tags */
        /* chế độ xem */
        $viewBy             = request()->cookie('view_by') ?? 'each_set';
        /* tìm theo tag */
        $arrayIdTag         = [$item->id];
        $params = [
            'key_search' => request()->get('search') ?? null,
            'array_category_info_id' => [],
            'array_tag_info_id' => $arrayIdTag,
            'filters' => request()->get('filters') ?? [],
            'loaded' => 0,
            'request_load' => 10,
            'sort_by' => Cookie::get('sort_by') ?? null,
            'view_by'   => $viewBy,
        ];
        $response       = CategoryMoneyController::getWallpapers($params, $language);
        $wallpapers     = $response['wallpapers'];
        $total          = $response['total'];
        $loaded         = $response['loaded'];
        $dataContent    = CategoryMoneyController::buildTocContentMain($itemSeo->contents, $language);

        return view('wallpaper.categoryMoney.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'wallpapers'    => $wallpapers,
            'total'     => $total,
            'loaded'   => $loaded,
            'viewBy'     => $viewBy,
            'dataContent'   => $dataContent,
            'arrayIdTag'   => $arrayIdTag,
            'arrayIdCategory' => $arrayIdCategory,
        ], $sharedData))->render();
    }

    private function handleProductInfo($item, $itemSeo, $language, array $sharedData) {
        $arrayIdTag = $item->tags->pluck('tag_info_id')->toArray();
        $total = CategoryMoneyController::getWallpapersByProductRelated($item->id, $arrayIdTag, $language, [
            'loaded' => 0,
            'request_load' => 0,
        ])['total'];

        return view('wallpaper.product.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'total'     => $total,
        ], $sharedData))->render();
    }

    private function handlePageInfo($item, $itemSeo, $language, array $sharedData) {
        // Trang tải xuống của tôi
        if (!empty($item->type->code) && $item->type->code === 'my_download' && !empty(Auth::user()->email)) {

            $emailCustomer = Auth::user()->email;
            $infoCustomer = Customer::select('*')
                ->where('email', $emailCustomer)
                ->with('orders')
                ->first();

            return view('wallpaper.account.myDownload', array_merge([
                'item' => $item,
                'itemSeo' => $itemSeo,
                'language' => $language,
                'infoCustomer'     => $infoCustomer,
            ], $sharedData))->render();
        }
    
        // Trang mặc định
        return view('wallpaper.page.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
        ], $sharedData))->render();
    }

    private function handleCategoryBlog($item, $itemSeo, $language, array $sharedData) {
        $params = [
            'sort_by' => Cookie::get('sort_by') ?? null,
            'array_category_blog_id' => CategoryBlog::getTreeCategoryByInfoCategory($item, [])->pluck('id')->prepend($item->id)->toArray(),
        ];
    
        $blogs = \App\Http\Controllers\CategoryBlogController::getBlogs($params, $language)['blogs'];
        $blogFeatured = BlogController::getBlogFeatured($language);

        return view('wallpaper.categoryBlog.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'blogs'     => $blogs,
            'blogFeatured'     => $blogFeatured,
        ], $sharedData))->render();
    }

    private function handleBlogInfo($item, $itemSeo, $language, array $sharedData) {
        $blogFeatured = BlogController::getBlogFeatured($language);
        $dataContent = CategoryMoneyController::buildTocContentMain($itemSeo->contents, $language);
        $htmlContent = str_replace('<div id="tocContentMain"></div>', '<div id="tocContentMain">' . $dataContent['toc_content'] . '</div>', $dataContent['content']);

        return view('wallpaper.blog.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'blogFeatured'     => $blogFeatured,
            'htmlContent'     => $htmlContent,
        ], $sharedData))->render();
    }

    private function handleCategoryType($item, $itemSeo, $language, array $sharedData) {
        $flagFree = in_array($itemSeo->slug, config('main_' . env('APP_NAME') . '.url_free_wallpaper_category'));
        if ($flagFree) {
            return $this->handleFreeCategory($item, $itemSeo, $language, $sharedData);
        }
    
        return $this->handlePaidCategory($item, $itemSeo, $language, $sharedData);
    }
    
    private function handleFreeCategory($item, $itemSeo, $language, array $sharedData) {
        // Khởi tạo các tham số tìm kiếm
        $tmp                                = Category::getTreeCategoryByInfoCategory($item, []);
        $arrayIdCategory                    = [$item->id];
        foreach($tmp as $t) $arrayIdCategory[] = $t->id;
        $params = [
            'array_category_info_id' => $arrayIdCategory,
            'loaded' => 0,
            'request_load' => 20, /* lấy 20 để khai báo schema */
            'sort_by' => Cookie::get('sort_by') ?? null,
            'filters' => request()->get('filters') ?? [],
            'search' => request('search') ?? null,
        ];
    
        // Lấy wallpapers từ controller
        $response = CategoryController::getFreeWallpapers($params, $language);
    
        // Đảm bảo biến wallpapers luôn tồn tại
        $wallpapers = $response['wallpapers'] ?? [];
        $total = $response['total'] ?? 0;
        $loaded = $response['loaded'] ?? 0;
    
        // Xử lý search_feeling (nếu có)
        $searchFeeling = request('search_feeling') ?? [];
        foreach ($searchFeeling as $feeling) {
            if ($feeling === 'all') { /* Nếu có 'all', clear toàn bộ */
                $searchFeeling = [];
                break;
            }
        }
    
        // Xây dựng toc_content
        $dataContent = CategoryMoneyController::buildTocContentMain($itemSeo->contents, $language);

        return view('wallpaper.category.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'wallpapers'     => $wallpapers,
            'arrayIdCategory'     => $arrayIdCategory,
            'total'     => $total,
            'loaded'     => $loaded,
            'searchFeeling'     => $searchFeeling,
            'dataContent'     => $dataContent,

        ], $sharedData))->render();
    }
    
    private function handlePaidCategory($item, $itemSeo, $language, array $sharedData) {
        // Khởi tạo các tham số tìm kiếm
        $arrayIdCategory    = Category::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
        $viewBy             = request()->cookie('view_by') ?? 'each_set';
        $search             = request('search') ?? null;
        $params = [
            'array_category_info_id' => $arrayIdCategory,
            'view_by' => $viewBy,
            'filters' => request()->get('filters') ?? [],
            'loaded' => 0,
            'request_load' => 10,
            'sort_by' => Cookie::get('sort_by') ?? null,
            'search' => $search,
        ];
    
        // Lấy wallpapers từ controller
        $response = CategoryMoneyController::getWallpapers($params, $language);
    
        // Đảm bảo biến wallpapers luôn tồn tại
        $wallpapers = $response['wallpapers'] ?? [];
        $total = $response['total'] ?? 0;
        $loaded = $response['loaded'] ?? 0;
    
        // Xây dựng toc_content
        $dataContent = CategoryMoneyController::buildTocContentMain($itemSeo->contents, $language);

        return view('wallpaper.categoryMoney.index', array_merge([
            'item' => $item,
            'itemSeo' => $itemSeo,
            'language' => $language,
            'wallpapers'     => $wallpapers,
            'arrayIdCategory'     => $arrayIdCategory,
            'total'     => $total,
            'loaded'     => $loaded,
            'viewBy'     => $viewBy,
            'search'     => $search,
            'dataContent'     => $dataContent,

        ], $sharedData))->render();
    }
    
    public static function buildNameCache($slugFull, $params = []){
        $response     = '';
        if(!empty($slugFull)){
             /* xây dựng  slug */
             $tmp    = explode('/', $slugFull);
             $result = [];
             foreach($tmp as $t) if(!empty($t)) $result[] = $t;
             $response = implode('-', $result);
            /* duyệt params để lấy prefix hay # */
            if(!empty($params)){
                $part   = '';
                foreach($params as $key => $param) $part .= $key.'-'.$param;
                if(!empty($part)) $response = $response.'-'.$part;
            }
        }
        return $response;
    }
    
}
