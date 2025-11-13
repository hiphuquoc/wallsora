<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\SitemapController;
use App\Helpers\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GenerateIdRangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $language;
    protected $type;
    protected $minId; // nullable -> used for list types
    protected $maxId; // nullable -> used for list types
    protected $page;
    protected $isList = false;

    public function __construct($language, $type, $minId = null, $maxId = null, $page = 1, $isList = false)
    {
        $this->language = $language;
        $this->type = $type;
        $this->minId = $minId;
        $this->maxId = $maxId;
        $this->page = $page;
        $this->isList = $isList;
    }

    public function handle()
    {
        $config = config("tablemysql.{$this->type}");
        if (!$config) return;

        $storage = Storage::disk('local');
        $langPath = "sitemaps/sitemap/{$this->language}";
        $storage->makeDirectory($langPath);

        $fileName = "{$this->type}-{$this->page}.xml";
        $tmpFile = $storage->path("{$langPath}/.tmp-{$fileName}"); // path on disk
        $finalFile = "{$langPath}/{$fileName}";

        // Open temp stream
        $fp = fopen($tmpFile, 'w');
        if ($fp === false) {
            $this->info("Failed to open temp file for {$finalFile}");
            return;
        }

        // write XML header
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL);

        // If this is a small list (isList true or config list_values provided), list directly
        if ($this->isList || !empty($config['is_list']) || !empty($config['list_values'])) {
            $values = $config['list_values'] ?? [];
            // If no explicit list_values, fallback: read all items via model whereHas seo
            if (empty($values)) {
                $modelName = $config['model_name'] ?? null;
                if ($modelName) {
                    $model = resolve("\\App\\Models\\{$modelName}");
                    $items = $model::whereHas('seo', fn($q) => $q->where('language', $this->language))->get();
                    foreach ($items as $it) {
                        $slug = $it->seo->slug_full ?? null;
                        if ($slug) {
                            $url = env('APP_URL') . '/' . ltrim($slug, '/');
                            $lastmod = $it->seo->updated_at ? date('c', strtotime($it->seo->updated_at)) : now()->toIso8601String();
                            $title = $it->seo->seo_title ?? '';
                            $image = !empty($it->seo->image) ? Image::getUrlImageLargeByUrlImage($it->seo->image) : config('image.default');
                            fwrite($fp, SitemapController::urlEntry($url, $lastmod, $title, $image) . PHP_EOL);
                        }
                    }
                }
            } else {
                // list provided as slugs or urls
                foreach ($values as $val) {
                    $url = Str::startsWith($val, ['http://','https://']) ? $val : env('APP_URL') . '/' . ltrim($val, '/');
                    $lastmod = now()->toIso8601String();
                    fwrite($fp, SitemapController::urlEntry($url, $lastmod, '', config('image.default')) . PHP_EOL);
                }
            }

            // close xml
            fwrite($fp, '</urlset>');
            fclose($fp);

            // atomic move
            $storage->put($finalFile, file_get_contents($tmpFile));
            unlink($tmpFile);

            // Thiết lập quyền tự động
            SitemapController::fixPermissions(); 

            $this->info("Wrote list sitemap {$finalFile}");
            return;
        }

        // Normal big-table path: query relation table for ids between min and max
        $seoRelation = $config['seo_relation'];
        $table = $config['table'];
        $idCol = "{$this->type}_id";

        // Select required fields by joining relation => seo => item
        // We'll fetch items by selecting join where r.{idCol} BETWEEN min and max and s.language filter.
        $query = DB::table("{$seoRelation} as r")
            ->join('seo as s', 's.id', '=', 'r.seo_id')
            ->join("{$table} as c", 'c.id', '=', DB::raw("r.{$idCol}"))
            ->where('s.language', $this->language);

        if ($this->minId !== null && $this->maxId !== null) {
            $query->whereBetween("r.{$idCol}", [$this->minId, $this->maxId]);
        }

        $query->orderByDesc('c.id')
            ->select(['c.id', 's.slug_full', 's.updated_at', 's.seo_title', 's.image'])
            ->chunk(500, function ($items) use ($fp) {
                foreach ($items as $item) {
                    $url = env('APP_URL') . '/' . ltrim($item->slug_full, '/');
                    $lastmod = $item->updated_at ? date('c', strtotime($item->updated_at)) : now()->toIso8601String();
                    $title = $item->seo_title ?? '';
                    $image = !empty($item->image) ? Image::getUrlImageLargeByUrlImage($item->image) : config('image.default');
                    fwrite($fp, SitemapController::urlEntry($url, $lastmod, $title, $image) . PHP_EOL);
                }
            });

        // close xml
        fwrite($fp, '</urlset>');
        fclose($fp);

        // atomic write: move tmp to final
        $storage->put($finalFile, file_get_contents($tmpFile));
        @unlink($tmpFile);

        $this->info("Generated {$finalFile} (page {$this->page})");
    }

    protected function info($message)
    {
        if (app()->runningInConsole()) {
            echo "[IdRangeJob] {$message}\n";
        }
        Log::info("[IdRangeJob] " . $message);
    }
}
