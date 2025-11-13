<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\SitemapController;

class GenerateLanguageSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 10;

    protected $language;

    public function __construct($language)
    {
        $this->language = $language;
    }

    public function handle()
    {
        $storage = Storage::disk('local');
        $langPath = "sitemaps/sitemap/{$this->language}";
        $storage->makeDirectory($langPath);

        foreach (config('tablemysql') as $type => $config) {
            $modelName = $config['model_name'] ?? null;
            if (!$modelName || empty($config['sitemap'])) continue;

            $model = resolve("\\App\\Models\\{$modelName}");
            $cacheKey = "sitemap_count_{$type}_{$this->language}";
            $total = Cache::remember($cacheKey, now()->addHours(24), fn() => $model::whereHas('seo', fn($q) => $q->where('language', $this->language))->count());

            if ($total === 0) continue;

            $totalPages = (int) ceil($total / SitemapController::MAX_ITEMS);
            $totalIndexPages = (int) ceil($totalPages / SitemapController::MAX_ITEMS);

            // Index pages
            if ($totalIndexPages > 1) {
                for ($i = 1; $i <= $totalIndexPages; $i++) {
                    $content = SitemapController::generateChildIndexPageContent($this->language, $type, $i);
                    $storage->put("{$langPath}/{$type}-index-{$i}.xml", $content);
                }
            }

            // Page content
            for ($i = 1; $i <= $totalPages; $i++) {
                $content = SitemapController::generateChildForLanguagePageContent($this->language, $type, $i);
                if ($content) {
                    $storage->put("{$langPath}/{$type}-{$i}.xml", $content);
                    $this->info("Page {$i}/{$totalPages} for {$this->language}/{$type}");
                }
            }

            // Entry point
            $content = SitemapController::generateChildForLanguageContent($this->language, $type, $totalPages, $totalIndexPages);
            $storage->put("{$langPath}/{$type}.xml", $content);
        }

        $this->info("Sitemap for {$this->language} completed!");
    }

    protected function info($message)
    {
        if (app()->runningInConsole()) {
            echo "[{$this->language}] {$message}\n";
        }
    }
}