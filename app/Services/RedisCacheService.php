<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RedisCacheService {
    /**
     * Lấy dữ liệu từ cache hoặc truy vấn từ database nếu không có trong cache.
     *
     * @param string $key            Key duy nhất để lưu vào cache.
     * @param callable $queryCallback Hàm callback để thực hiện truy vấn database.
     * @param int $ttl               Thời gian sống của cache (tính bằng giây).
     * @return mixed                 Dữ liệu lấy được từ cache hoặc database.
     */
    public static function getOrSetCache(string $key, callable $queryCallback, int $ttl = null) {
        // Nếu không truyền TTL, sử dụng giá trị mặc định từ config
        $ttl        = $ttl ?? config('app.cache_redis_time', 86400);
        $useCache   = env('APP_CACHE_HTML', true);

        // Lấy dữ liệu từ cache
        if ($useCache && Cache::has($key)) {
            $data   = Cache::get($key);
        }
        
        // Nếu không có dữ liệu trong cache, thực hiện truy vấn từ database
        if (empty($data)) {
            $data   = $queryCallback();

            if (!empty($data)) {
                // Lưu dữ liệu vào cache với TTL
                Cache::put($key, $data, $ttl);
            }
        }

        return $data;
    }

    /**
     * Xóa dữ liệu khỏi cache theo key.
     *
     * @param string $key Key duy nhất của dữ liệu trong cache.
     * @return void
     */
    public static function forgetCache(string $key) {
        Cache::forget($key);
    }
}