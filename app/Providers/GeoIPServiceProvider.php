<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GeoIp2\Database\Reader;

class GeoIPServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register() {
        $this->app->singleton('geoip', function ($app) {
            $dbPath = storage_path('app/public/mindmax/GeoLite2-City.mmdb'); // Đường dẫn đến file GeoIP
            return new Reader($dbPath);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot() {
        //
    }
}
