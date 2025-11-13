<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use App\Models\Blog;

class BlogController extends Controller{

    public static function getBlogFeatured($language) {
        $cacheKey = 'blog_featured_' . $language;
        $cacheSeconds = config('app.cache_redis_time', 86400);
        $useCache = env('APP_CACHE_HTML', true); // Kiểm tra xem có sử dụng cache hay không
    
        // Nếu sử dụng cache
        if ($useCache) {
            return Cache::remember($cacheKey, now()->addSeconds($cacheSeconds), function () use ($language) {
                return self::queryBlogFeatured($language);
            });
        }
    
        // Nếu không sử dụng cache, truy vấn trực tiếp
        return self::queryBlogFeatured($language);
    }
    
    /**
     * Hàm thực hiện truy vấn blog nổi bật.
     *
     * @param string $language
     * @return \Illuminate\Support\Collection
     */
    protected static function queryBlogFeatured($language) {
        return Blog::select('blog_info.*')
            ->join('seo', 'seo.id', '=', 'blog_info.seo_id')
            ->whereHas('seos.infoSeo', function ($query) use ($language) {
                $query->where('language', $language);
            })
            ->where('outstanding', 1)
            ->where('status', 1)
            ->with([
                'seos.infoSeo' => function ($query) use ($language) {
                    $query->where('language', $language);
                },
                'seo',
                'seos'
            ])
            ->orderBy('seo.ordering', 'DESC')
            ->orderBy('id', 'DESC')
            ->skip(0)
            ->take(7)
            ->get();
    }
    
}
