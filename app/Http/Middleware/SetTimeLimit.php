<?php

namespace App\Http\Middleware;

use Closure;

class SetTimeLimit
{
    public function handle($request, Closure $next)
    {
        // Thiết lập giá trị thời gian chờ cho trang web
        set_time_limit(3600);
        ini_set('max_execution_time', 3600);
        ini_set('max_input_time', 3600);

        return $next($request);
    }
}