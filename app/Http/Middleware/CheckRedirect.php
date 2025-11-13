<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RedirectInfo;
use Illuminate\Support\Facades\Redirect;

class CheckRedirect {
    public function handle($request, Closure $next) {
        // Lấy đường dẫn và chuẩn hóa ký tự Unicode
        $path = '/' . rawurldecode($request->path());

        // Tìm đường dẫn cũ trong cơ sở dữ liệu
        $redirectInfo = RedirectInfo::select('*')
                            ->whereRaw('old_url COLLATE utf8mb4_bin = ?', [$path]) /* chỉ định so sánh dấu */
                            ->first();

        if ($redirectInfo) {
            // Nếu tìm thấy, thực hiện redirect 301
            return Redirect::to($redirectInfo->new_url, 301);
        }

        // Nếu không tìm thấy, chuyển request đến hệ thống xử lý route thông thường
        return $next($request);
    }
}
