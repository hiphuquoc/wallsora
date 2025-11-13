<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class HtmlCacheService
{
    protected $disk;
    protected $cacheFolder;
    protected $fileTtl;
    protected $extension;

    protected $useHtmlCache;

    public function __construct()
    {
        $appName = env('APP_NAME');

        $this->useHtmlCache   = env('APP_CACHE_HTML', false); // Mặc định tắt
        $this->fileTtl        = config('app.cache_html_time', 2592000);
        $this->cacheFolder    = config("main_{$appName}.cache.folderSave");
        $this->extension      = config("main_{$appName}.cache.extension");
        $this->disk           = Storage::disk(config("main_{$appName}.cache.disk"));
    }

    public function getOrRender(string $cacheKey, callable $renderCallback): string
    {
        // Bước 1: Nếu tắt cache → render trực tiếp
        if (!$this->useHtmlCache) {
            return $renderCallback();
        }

        // Bước 2: Kiểm tra GCS (file cache)
        $cachePath = $this->buildCachePath($cacheKey);
        if ($html = $this->getFromGcs($cachePath)) {
            return $html;
        }

        // Bước 3: Render mới nếu chưa có cache
        $html = $renderCallback();

        if ($html && $this->useHtmlCache) {
            $this->saveToGcs($cachePath, $html);
        }

        return $html ?? \App\Http\Controllers\ErrorController::error404();
    }

    /**
     * Xóa cache
     */
    public function clear(string $cacheKey): void
    {
        $cachePath = $this->buildCachePath($cacheKey);
        $this->clearGcs($cachePath);
    }

    // ------------------------------ PRIVATE METHODS ------------------------------

    private function buildCachePath(string $cacheKey): string
    {
        return $this->cacheFolder . '/' . ltrim($cacheKey, '/') . '.' . $this->extension;
    }

    private function getFromGcs(string $path): ?string
    {
        if (!$this->disk->exists($path)) {
            return null;
        }

        $lastModified = $this->disk->lastModified($path);
        if ((time() - $lastModified) > $this->fileTtl) {
            return null;
        }

        return $this->disk->get($path);
    }

    private function saveToGcs(string $path, string $content): void
    {
        $this->disk->put($path, $content);
    }

    private function clearGcs(string $path): void
    {
        if ($this->disk->exists($path)) {
            $this->disk->delete($path);
        }
    }
}