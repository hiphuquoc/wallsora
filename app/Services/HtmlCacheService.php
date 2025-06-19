<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use voku\helper\HtmlMin;
use MatthiasMullie\Minify;

class HtmlCacheService
{
    protected $disk;
    protected $cacheFolder;
    protected $fileTtl;
    protected $extension;
    protected $useHtmlCache;
    protected $useHtmlMinify;
    protected $useJsCssMinify;

    public function __construct()
    {
        $appName = env('APP_NAME');

        $this->useHtmlCache     = env('APP_CACHE_HTML', false);
        $this->fileTtl          = config('app.cache_html_time', 2592000);
        $this->cacheFolder      = config("main_{$appName}.cache.folderSave");
        $this->extension        = config("main_{$appName}.cache.extension");
        $this->disk             = Storage::disk(config("main_{$appName}.cache.disk"));
        $this->useHtmlMinify    = env('APP_MINIFY_HTML', env('APP_ENV') === 'production');
        $this->useJsCssMinify   = env('APP_MINIFY_JS_CSS', env('APP_ENV') === 'production');
    }

    public function getOrRender(string $cacheKey, callable $renderCallback): string
    {
        if (!$this->useHtmlCache) {
            return $renderCallback();
        }

        $cachePath = $this->buildCachePath($cacheKey);
        if ($html = $this->getFromGcs($cachePath)) {
            return $html;
        }

        $html = $renderCallback();
        if ($html && $this->useHtmlCache) {
            $this->saveToGcs($cachePath, $html);
        }

        return $html ?? \App\Http\Controllers\ErrorController::error404();
    }

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

    private function saveToGcs(string $path, string $content): void
    {
        // Minify JS và CSS inline nếu bật
        if ($this->useJsCssMinify) {
            $content = $this->minifyJsCssInline($content);
        }

        // Minify HTML nếu bật
        if ($this->useHtmlMinify) {
            $htmlMin = new HtmlMin();
            $content = $htmlMin->minify($content);
        }

        // Nén Gzip (giảm level xuống 6 để tối ưu tốc độ)
        $compressedContent = gzencode($content, 6);

        // Lưu file với đuôi .gz
        $this->disk->put($path . '.gz', $compressedContent);
    }

    private function getFromGcs(string $path): ?string
    {
        $gzPath = $path . '.gz';
        if (!$this->disk->exists($gzPath)) {
            return null;
        }

        $lastModified = $this->disk->lastModified($gzPath);
        if ((time() - $lastModified) > $this->fileTtl) {
            return null;
        }

        $compressed = $this->disk->get($gzPath);
        return gzdecode($compressed);
    }

    private function clearGcs(string $path): void
    {
        $gzPath = $path . '.gz';
        if ($this->disk->exists($gzPath)) {
            $this->disk->delete($gzPath);
        }
    }

    private function minifyJsCssInline(string $html): string
    {
        // Minify CSS inline
        $html = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function ($matches) {
            $cssMinifier = new Minify\CSS($matches[1]);
            return '<style>' . $cssMinifier->minify() . '</style>';
        }, $html);

        // Minify JS inline (chỉ xử lý thẻ <script> không có src)
        $html = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function ($matches) {
            // Bỏ qua nếu script có src hoặc là JSON-LD
            if (preg_match('/\bsrc\s*=/i', $matches[0]) || preg_match('/\btype\s*=\s*[\'"]application\/ld\+json[\'"]/i', $matches[0])) {
                return $matches[0];
            }
            $jsMinifier = new Minify\JS($matches[1]);
            return '<script>' . $jsMinifier->minify() . '</script>';
        }, $html);

        return $html;
    }
}