<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CacheController extends Controller {

    public static function clear(){

        try {
            // Kết nối với nơi lưu trữ cache
            $appName        = env('APP_NAME');
            $disk = Storage::disk(config("main_{$appName}.cache.disk"));
            $folderSave = config('main_' . env('APP_NAME') . '.cache.folderSave');
    
            // Lấy danh sách tất cả file trong thư mục cache
            $files = $disk->files($folderSave);
    
            // Xóa tất cả file trong thư mục cache bằng một lệnh (hiệu quả hơn vòng lặp)
            if (!empty($files)) {
                $disk->delete($files); // Xóa hàng loạt
            }
    
            // Xóa cache Redis
            $redis = Redis::connection();
            $redis->flushall(); // Xóa toàn bộ cache trong Redis
    
            return true;
        } catch (\Exception $e) {
            // Xử lý lỗi nếu cần
            Log::error('Error clearing cache: ' . $e->getMessage());
            return false;
        }

    }

}
