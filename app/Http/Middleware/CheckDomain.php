<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDomain {
    public function handle(Request $request, Closure $next) {
        $host       = $request->getHost();
        $domainName = env('DOMAIN_NAME');
        // Kiểm tra nếu domain không phải 'wallsora.com', chuyển hướng sang đúng domain
        if ($host != $domainName) {
            return redirect()->to('https://' . $domainName . $request->getRequestUri(), 301);
        }
        return $next($request);
    }
}
