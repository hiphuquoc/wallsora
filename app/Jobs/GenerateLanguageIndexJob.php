<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SitemapController;

class GenerateLanguageIndexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $language;
    protected $type;
    protected $totalPages;

    public $tries = 3;
    public $timeout = 120;

    public function __construct($language, $type, $totalPages)
    {
        $this->language = $language;
        $this->type = $type;
        $this->totalPages = $totalPages;
    }

    public function handle()
    {
        $storage = Storage::disk('local');
        $folderPath = "sitemaps/sitemap/{$this->language}";
        $storage->makeDirectory($folderPath);

        $fileName = "{$this->type}.xml";
        $tmpFile = $storage->path("{$folderPath}/.tmp-{$fileName}");
        $finalFile = "{$folderPath}/{$fileName}";

        $baseUrl = url("sitemap/{$this->language}");
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        for ($i = 1; $i <= $this->totalPages; $i++) {
            $loc = "{$baseUrl}/{$this->type}-{$i}.xml";
            $lastmod = now()->toIso8601String();
            $xml[] = "  <sitemap>";
            $xml[] = "    <loc>{$loc}</loc>";
            $xml[] = "    <lastmod>{$lastmod}</lastmod>";
            $xml[] = "  </sitemap>";
        }

        $xml[] = '</sitemapindex>';

        // Ghi temp file trước
        file_put_contents($tmpFile, implode("\n", $xml));
        // Chuyển temp -> final
        $storage->put($finalFile, file_get_contents($tmpFile));
        @unlink($tmpFile);

        // Fix permissions đồng bộ với IdRangeJob
        SitemapController::fixPermissions($folderPath);

        $this->info("Created index sitemap for {$this->language}/{$this->type} ({$this->totalPages} pages)");
    }

    protected function info($message)
    {
        if (app()->runningInConsole()) {
            echo "[LangIndex] {$message}\n";
        }
        Log::info("[LangIndex] " . $message);
    }
}
