<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminSubdomain {
    public function handle(Request $request, Closure $next) {
        $host       = $request->getHost();
        $domainName = env('DOMAIN_NAME');

        // Kiểm tra nếu không phải subdomain admin và yêu cầu không phải admin, thì chuyển hướng
        if ($host !== 'admin.' . $domainName && $host !== $domainName) {
            return redirect()->to('https://' . $domainName . $request->getRequestUri(), 301);
        }

        return $next($request);
    }
}



