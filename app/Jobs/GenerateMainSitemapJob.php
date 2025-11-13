<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SitemapController;

class GenerateMainSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 10;

    public function handle()
    {
        $storage = Storage::disk('local');
        $basePath = 'sitemaps';

        // KHÔNG XÓA THƯ MỤC CŨ → tránh lỗi permission
        // if ($storage->exists($basePath)) {
        //     $storage->deleteDirectory($basePath);
        // }
        $storage->makeDirectory($basePath);

        // Tạo sitemap chính
        $content = SitemapController::generateMainContent();
        $storage->put("{$basePath}/sitemap.xml", $content);

        // Tạo thư mục con
        $types = array_keys(array_filter(config('tablemysql'), fn($t) => !empty($t['sitemap'])));
        foreach ($types as $type) {
            $content = SitemapController::generateChildContent($type);
            $storage->makeDirectory("{$basePath}/sitemap");
            $storage->put("{$basePath}/sitemap/{$type}.xml", $content);
        }

        $this->info("Main sitemap generated!");
    }

    protected function info($message)
    {
        if (app()->runningInConsole()) {
            echo "[MainSitemap] {$message}\n";
        }
    }
}