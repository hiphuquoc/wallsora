<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use App\Models\FreeWallpaper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryMoneyController extends Controller {

    public static function loadMoreWallpaper(Request $request){
        $language                                   = $request->get('language');
        $viewBy                                     = Cookie::get('view_by') ?? 'each_set';
        $response                                   = [
            'content'   => '<div>'.config('data_language_1.'.$language.'.no_suitable_results_found').'</div>',
        ];
        if($request->get('loaded')<$request->get('total')){
            $params                                 = [];
            if(!empty($request->get('id_product'))){
                /* trường hợp tải theo product liên quan */
                $idProduct                          = $request->get('id_product');
                $currentProduct                     = Product::find($idProduct);
                $arrayIdTag                         = $currentProduct->tags->pluck('tag_info_id')->toArray();
                $params['loaded']                   = $request->get('loaded') ?? 0;
                $params['request_load']             = $request->get('request_load');
                $tmp                                = self::getWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params);
                /* đổ theme lấy html - phần này bên ngoài view nằm phía dưới -> không ảnh hưởng khung nhìn đầu tiên nên không cần dùng firstLoad */
                $content                            = self::getXhtmlWallpapers($tmp['wallpapers'], $language, $viewBy, false);
            }else {
                /* trường hợp tải từ category & tag */
                $params['filters']                  = $request->get('filters') ?? [];
                $params['search']                   = $request->get('search') ?? null;
                $params['loaded']                   = $request->get('loaded');
                $params['request_load']             = $request->get('request_load');
                $params['array_category_info_id']   = json_decode($request->get('array_category_info_id'));
                $params['array_tag_info_id']        = json_decode($request->get('array_tag_info_id'));
                $params['sort_by']                  = Cookie::get('sort_by') ?? config('main_'.env('APP_NAME').'.sort_type')[0]['key'];
                $tmp                                = self::getWallpapers($params, $language);
                /* đổ theme lấy html */
                // $firstLoad                          = $params['loaded']==0 ? true : false;
                $firstLoad                          = false;
                $content                            = self::getXhtmlWallpapers($tmp['wallpapers'], $language, $viewBy, $firstLoad);
            }
            
            /* trả kết quả */
            $response['content']                    = $content;
            $response['loaded']                     = $tmp['loaded'];
            $response['total']                      = $tmp['total'];
        }
        return json_encode($response);
    }

    /*
        $firstLoad để kiểm tra xem có phải lần đầu tải ajax /trang không -> dể thay đổi cách hiển thị, cải hiện khung nhìn đầu tiên
    */
    public static function getXhtmlWallpapers($wallpapers, $language, $viewBy = 'each_set', $firstLoad = false){
        $response   = '';
        if(!empty($wallpapers)){
            $i      = 0;
            foreach($wallpapers as $wallpaper){
                if($viewBy=='each_set'){
                    $lazyload   = $firstLoad==true&&$i<4 ? false : true;
                    $response    .= view('wallpaper.template.wallpaperItem', [
                        'product'   => $wallpaper,
                        'language'  => $language,
                        'lazyload'  => $lazyload,
                        'headingTitle'  => 'h2',
                    ])->render();
                    ++$i;
                }else {
                    $wallpaperName      = null;
                    $link               = env('APP_URL').'/'.$wallpaper->seo->slug_full;
                    foreach($wallpaper->seos as $seo){
                        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language) {
                            $wallpaperName = $seo->infoSeo->title;
                            $link = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                            break;
                        }
                    }
                    foreach($wallpaper->prices as $price){
                        foreach($price->wallpapers as $w){
                            $lazyload   = $firstLoad==true&&$i<4 ? false : true;
                            $response .= view('wallpaper.template.perWallpaperItem', [
                                'idProduct'     => $w->id,
                                'idPrice'       => $price->id,
                                'wallpaper'     => $w, 
                                'productName'   => $wallpaperName,
                                'link'          => $link,
                                'language'      => $language,
                                'lazyload'      => $lazyload
                            ]);
                            ++$i;
                        }
                    }
                    
                }
            }
        }
        return $response;
    }

    public static function getWallpapers($params, $language) {
        $cacheKey = 'wallpapers:' . md5(json_encode($params) . $language);
        $cacheTime = config('app.cache_redis_time', 86400);
        $useCache = env('APP_CACHE_HTML', true);
    
        // Kiểm tra xem có sử dụng cache hay không
        if ($useCache) {
            return Cache::remember($cacheKey, now()->addSeconds($cacheTime), function () use ($params, $language) {
                return self::queryWallpapers($params, $language);
            });
        }
        // Nếu không sử dụng cache, truy vấn trực tiếp
        return self::queryWallpapers($params, $language);
    }
    
    /**
     * Hàm thực hiện truy vấn wallpapers.
     *
     * @param array $params
     * @param string $language
     * @return array
     */
    private static function queryWallpapers($params, $language) {
        $keySearch = $params['search'] ?? null;
        $filters = $params['filters'] ?? [];
        $sortBy = $params['sort_by'] ?? null;
        $loaded = $params['loaded'] ?? 0;
        $arrayIdCategory = $params['array_category_info_id'] ?? [];
        $arrayIdTag = $params['array_tag_info_id'] ?? [];
        $requestLoad = $params['request_load'] ?? 10;
    
        // Khởi tạo query
        $query = Product::select('product_info.*')
            ->join('seo', 'seo.id', '=', 'product_info.seo_id')
            ->whereHas('prices.wallpapers', function () {});
        // === THAY ĐỔI TẠI ĐÂY: DÙNG MEILISEARCH ĐỂ LỌC THEO TITLE ===
        if ($keySearch) {
            $ids = Product::search($keySearch)
                ->get()
                ->pluck('id')
                ->toArray();

            if (empty($ids)) {
                $query->whereRaw('0=1'); // Không có kết quả nào
            } else {
                $query->whereIn('product_info.id', $ids);
            }
        }
        // =========================================================
        $query->when(!empty($filters), function ($subQuery) use ($filters) {
                foreach ($filters as $filter) {
                    $subQuery->whereHas('categories.infoCategory', function ($subQueryLv2) use ($filter) {
                        $subQueryLv2->where('id', $filter);
                    });
                }
            })
            ->when(!empty($arrayIdCategory), function ($subQuery) use ($arrayIdCategory) {
                $subQuery->whereHas('categories', function ($subQueryLv2) use ($arrayIdCategory) {
                    $subQueryLv2->whereIn('category_info_id', $arrayIdCategory);
                });
            })
            ->when(!empty($arrayIdTag), function ($subQuery) use ($arrayIdTag) {
                $subQuery->whereHas('tags', function ($subQueryLv2) use ($arrayIdTag) {
                    $subQueryLv2->where('reference_type', 'product_info')
                        ->whereIn('tag_info_id', $arrayIdTag);
                });
            });
    
        // Đếm tổng số sản phẩm theo các tiêu chí lọc
        $total = (clone $query)->count();
    
        // Lấy danh sách sản phẩm với sắp xếp và phân trang
        $wallpapers = $query->when(empty($sortBy), function ($subQuery) {
                $subQuery->orderBy('seo.ordering', 'DESC')
                         ->orderBy('id', 'DESC');
            })
            ->when($sortBy == 'newest' || $sortBy == 'propose', function ($subQuery) {
                $subQuery->orderBy('id', 'DESC');
            })
            ->when($sortBy == 'favourite', function ($subQuery) {
                $subQuery->orderBy('heart', 'DESC')
                         ->orderBy('seo.ordering', 'DESC')
                         ->orderBy('id', 'DESC');
            })
            ->when($sortBy == 'oldest', function ($subQuery) {
                $subQuery->orderBy('id', 'ASC');
            })
            ->with(['seos.infoSeo' => function ($subQuery) use ($language) {
                $subQuery->where('language', $language);
            }, 'seo', 'prices'])
            ->skip($loaded)
            ->take($requestLoad)
            ->get();
    
        return [
            'wallpapers' => $wallpapers,
            'total' => $total,
            'loaded' => $loaded + $requestLoad
        ];
    }

    public static function getWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params) {
        // Tạo khóa cache dựa trên các tham số đầu vào
        $cacheKey = 'wallpapers_related:' . $idProduct
                    . ':' . $language
                    . ':' . md5(json_encode($arrayIdTag))
                    . ':' . $params['loaded']
                    . ':' . $params['request_load'];
        $cacheTime = config('app.cache_redis_time', 86400);
        $useCache = env('APP_CACHE_HTML', true); // Kiểm tra xem có sử dụng cache hay không
    
        // Nếu sử dụng cache
        if ($useCache) {
            return Cache::remember($cacheKey, now()->addSeconds($cacheTime), function () use ($idProduct, $arrayIdTag, $language, $params) {
                return self::queryWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params);
            });
        }
    
        // Nếu không sử dụng cache, truy vấn trực tiếp
        return self::queryWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params);
    }
    
    /**
     * Hàm thực hiện truy vấn wallpapers liên quan.
     *
     * @param int $idProduct
     * @param array $arrayIdTag
     * @param string $language
     * @param array $params
     * @return array
     */
    private static function queryWallpapersByProductRelated($idProduct, $arrayIdTag, $language, $params) {
        $response = [];
        $tmp = Product::select('product_info.*')
            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                $query->where('language', $language);
            })
            ->join('relation_tag_info_orther as rt', 'product_info.id', '=', 'rt.reference_id')
            ->where('rt.reference_type', 'product_info')
            ->whereIn('rt.tag_info_id', $arrayIdTag)
            ->where('product_info.id', '!=', $idProduct)
            ->selectRaw('COUNT(rt.tag_info_id) as common_tags_count')
            ->groupBy(
                'product_info.id',
                'product_info.seo_id',
                'product_info.code',
                'product_info.sold',
                'product_info.heart',
                'product_info.created_at',
                'product_info.updated_at',
                'product_info.price',
                'product_info.notes'
            )
            ->orderByDesc('common_tags_count')
            ->with('tags')
            ->get();
    
        // Phân trang thủ công
        $response['wallpapers'] = $tmp->slice($params['loaded'], $params['request_load'])->values();
        $response['loaded']     = $params['loaded'] + $response['wallpapers']->count();
        $response['total']      = $tmp->count();
    
        return $response;
    }

    public static function buildTocContentMain($contents, $language) {
        // Nếu danh sách nội dung rỗng, trả về dữ liệu mặc định
        if (empty($contents) || count($contents) === 0) {
            return [
                'content' => '',
                'toc_content' => ''
            ];
        }
    
        // Sắp xếp theo `ordering`
        $sortedContents = collect($contents)->sortBy('ordering');
    
        // Ghép các nội dung lại sau khi đã sắp xếp
        $htmlContent = '';
        foreach ($sortedContents as $content) {
            $htmlContent .= $content->content;
        }
    
        // Kiểm tra nếu nội dung rỗng
        if (empty(trim($htmlContent))) {
            return [
                'content' => '',
                'toc_content' => ''
            ];
        }
    
        // Phân tích HTML của $htmlContent để tạo TOC
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
    
        $dataTocContent = [];
        $indexToc = 0;
        foreach ($dom->getElementsByTagName('h2') as $i => $heading) {
            // Tạo id nếu không có
            $dataId = $heading->getAttribute('id');
            if (empty($dataId)) {
                $dataId = 'randomIdTocContent_' . $i;
                $heading->setAttribute('id', $dataId);
                $indexToc++;
            }
    
            $dataTocContent[$i] = [
                'id' => $dataId,
                'name' => $heading->nodeName,
                'title' => $heading->textContent
            ];
        }
    
        // Tạo nội dung TOC với view
        $xhtml = view('wallpaper.template.tocContentMain', [
            'data' => $dataTocContent,
            'language' => $language
        ])->render();
    
        // Trả về cả nội dung đã cập nhật và TOC
        return [
            'content' => $dom->saveHTML(),
            'toc_content' => $xhtml,
        ];
    }
    
}
