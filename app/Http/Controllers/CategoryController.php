<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Category;
use App\Models\FreeWallpaper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller {

    public static function loadmoreFreeWallpapers(Request $request){
        /* tìm kiếm bằng feeling */
        $searchFeeling = $request->get('search_feeling') ?? [];
        // foreach($searchFeeling as $feeling){
        //     if($feeling=='all'){ /* trường hợp tìm kiếm có all thì clear */
        //         $searchFeeling = [];
        //         break;
        //     }
        // }
        $response           = [];
        $content            = '';
        $params             = [];
        if(!empty($request->get('total'))){
            $language                           = $request->get('language') ?? session()->get('language');
            $params['loaded']                   = $request->get('loaded');
            $params['request_load']             = $request->get('request_load');
            $params['array_category_info_id']   = json_decode($request->get('array_category_info_id'));
            $params['sort_by']                  = Cookie::get('sort_by') ?? null;
            $params['filters']                  = $request->get('filters') ?? [];
            $params['id_not']                   = $request->get('idNot') ?? 0;
            $tmp                                = self::getFreeWallpapers($params, $language);
            $user           = Auth::user();
            $language       = $request->get('language');
            foreach($tmp['wallpapers'] as $wallpaper){
                $content    .= view('wallpaper.category.item', compact('wallpaper', 'language', 'user'))->render();
            }
        }
        $response['content']    = $content;
        $response['loaded']     = $tmp['loaded'] ?? $request->get('loaded') ?? 0;
        $response['total']      = $tmp['total'] ?? $request->get('total') ?? 0;
        return json_encode($response);
    }

    public static function getFreeWallpapers($params, $language) {
        $idNot           = $params['id_not'] ?? 0;
        $filters         = $params['filters'] ?? [];
        $sortBy          = $params['sort_by'] ?? null;
        $loaded          = $params['loaded'] ?? 0;
        $arrayIdCategory = $params['array_category_info_id'] ?? [];
        $requestLoad     = $params['request_load'] ?? 10;
    
        // Tạo khóa cache dựa trên các tham số đầu vào
        $cacheKey = 'free_wallpapers_' . md5(json_encode([
            'id_not'              => $idNot,
            'filters'             => $filters,
            'sort_by'             => $sortBy,
            'loaded'              => $loaded,
            'category_info_ids'   => $arrayIdCategory,
            'request_load'        => $requestLoad,
            'language'            => $language
        ]));
    
        $cacheSeconds = config('app.cache_redis_time', 3600);
        $useCache = env('APP_CACHE_HTML', true); // Kiểm tra xem có sử dụng cache hay không
    
        // Nếu sử dụng cache
        if ($useCache) {
            return Cache::remember($cacheKey, now()->addSeconds($cacheSeconds), function () use (
                $idNot, $filters, $sortBy, $loaded, $arrayIdCategory, $requestLoad, $language
            ) {
                return self::queryFreeWallpapers(
                    $idNot, $filters, $sortBy, $loaded, $arrayIdCategory, $requestLoad, $language
                );
            });
        }
    
        // Nếu không sử dụng cache, truy vấn trực tiếp
        return self::queryFreeWallpapers(
            $idNot, $filters, $sortBy, $loaded, $arrayIdCategory, $requestLoad, $language
        );
    }
    
    /**
     * Hàm thực hiện truy vấn wallpapers miễn phí.
     *
     * @param int $idNot
     * @param array $filters
     * @param string|null $sortBy
     * @param int $loaded
     * @param array $arrayIdCategory
     * @param int $requestLoad
     * @param string $language
     * @return array
     */
    private static function queryFreeWallpapers($idNot, $filters, $sortBy, $loaded, $arrayIdCategory, $requestLoad, $language) {
        $query = FreeWallpaper::select('*')
            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                $query->where('language', $language);
            })
            ->when(!empty($idNot), function ($query) use ($idNot) {
                $query->where('id', '!=', $idNot);
            })
            ->when(!empty($arrayIdCategory), function ($query) use ($arrayIdCategory) {
                $query->whereHas('categories', function ($query) use ($arrayIdCategory) {
                    $query->whereIn('category_info_id', $arrayIdCategory);
                });
            })
            ->when(!empty($filters), function ($query) use ($filters) {
                foreach ($filters as $filter) {
                    $query->whereHas('categories.infoCategory', function ($query) use ($filter) {
                        $query->where('id', $filter);
                    });
                }
            });
    
        // Đếm tổng số wallpapers
        $total = (clone $query)->count();
    
        // Lấy danh sách wallpapers với sắp xếp và phân trang
        $wallpapers = $query
            ->when(empty($sortBy), function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->when($sortBy == 'newest' || $sortBy == 'propose', function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->when($sortBy == 'favourite', function ($query) {
                $query->orderBy('heart', 'DESC')->orderBy('id', 'DESC');
            })
            ->when($sortBy == 'oldest', function ($query) {
                $query->orderBy('id', 'ASC');
            })
            ->skip($loaded)
            ->take($requestLoad)
            ->get();
    
        return [
            'wallpapers' => $wallpapers,
            'total'      => $total,
            'loaded'     => $loaded + $requestLoad
        ];
    }

    public static function loadInfoCategory(Request $request){ /* hàm này dùng load thông tin của category bao gồm các tag con (dùng cho trang chủ) */
        // hàm này trả rả html nên dùng cache html 

        $idCategory         = $request->get('category_info_id') ?? 0;
        $language           = $request->get('language') ?? session()->get('language');
        $nameCache          = 'load_info_category_' . md5(json_encode([
            'category_info_id'    => $idCategory,
            'language'            => $language
        ])).'.'.config('main_'.env('APP_NAME').'.cache.extension');
        $cachePath          = config('main_'.env('APP_NAME').'.cache.folderSave').$nameCache;
        $cacheTime          = config('app.cache_html_time', 86400);

        $disk               = Storage::disk('gcs');
        $useCache           = env('APP_CACHE_HTML') == true;

        // Chỉ kiểm tra và sử dụng cache khi APP_CACHE_HTML = true
        if ($useCache && $disk->exists($cachePath) && $cacheTime > (time() - $disk->lastModified($cachePath))) {
            $xhtml          = $disk->get($cachePath);
        } else {
            $infoCategory   = Category::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', $idCategory)
                                ->with('seo', 'seos', 'thumnails')
                                ->first();
            $xhtml          = view('wallpaper.home.categoryItem', [
                                'category'  => $infoCategory,
                                'language'  => $language,
                                ])->render();
            // Chỉ ghi cache khi APP_CACHE_HTML = true
            if ($useCache) {
                $disk->put($cachePath, $xhtml);
            }
        }
        echo $xhtml;
    }
}
