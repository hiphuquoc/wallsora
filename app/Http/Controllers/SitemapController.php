<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Image;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    const MAX_ITEMS = 1000;

    public static function serve($filePath)
    {
        $path = 'sitemaps/' . ltrim($filePath, '/');
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            return abort(404);
        }

        $content = $disk->get($path);
        $response = response($content, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);

        if (request()->header('Accept-Encoding') && str_contains(request()->header('Accept-Encoding'), 'gzip')) {
            $gzipped = gzencode($content, 6);
            if ($gzipped !== false) {
                $response->setContent($gzipped);
                $response->header('Content-Encoding', 'gzip');
                $response->header('Content-Length', strlen($gzipped));
            }
        }

        return $response;
    }

    public static function generateMainContent()
    {
        $types = array_filter(config('tablemysql'), fn($t) => !empty($t['sitemap']));
        $now = now()->toIso8601String();
        $entries = array_map(fn($key) => self::sitemapEntry(
            env('APP_URL') . "/sitemap/{$key}.xml", $now
        ), array_keys($types));

        return self::sitemapIndexXml($entries);
    }

    public static function generateChildContent($type)
    {
        $now = now()->toIso8601String();
        $entries = array_map(fn($lang) => self::sitemapEntry(
            env('APP_URL') . "/sitemap/{$lang['key']}/{$type}.xml", $now
        ), array_filter(config('language'), fn($l) => !empty($l['key'])));

        return self::sitemapIndexXml($entries);
    }

    public static function generateChildForLanguageContent($language, $type, $totalPages, $totalIndexPages)
    {
        $now = now()->toIso8601String();
        $entries = [];

        if ($totalIndexPages > 1) {
            for ($i = 1; $i <= $totalIndexPages; $i++) {
                $entries[] = self::sitemapEntry(
                    env('APP_URL') . "/sitemap/{$language}/{$type}-index-{$i}.xml", $now
                );
            }
        } else {
            for ($i = 1; $i <= $totalPages; $i++) {
                $entries[] = self::sitemapEntry(
                    env('APP_URL') . "/sitemap/{$language}/{$type}-{$i}.xml", $now
                );
            }
        }

        return self::sitemapIndexXml($entries);
    }

    public static function generateChildIndexPageContent($language, $type, $indexPage)
    {
        $config = config("tablemysql.{$type}");
        if (!$config) return '';

        $total = Cache::get("sitemap_count_{$type}_{$language}", 0);
        $totalPages = ceil($total / self::MAX_ITEMS);

        $start = ($indexPage - 1) * self::MAX_ITEMS + 1;
        $end = min($totalPages, $indexPage * self::MAX_ITEMS);
        if ($start > $end) return '';

        $now = now()->toIso8601String();
        $entries = [];

        for ($i = $start; $i <= $end; $i++) {
            $entries[] = self::sitemapEntry(
                env('APP_URL') . "/sitemap/{$language}/{$type}-{$i}.xml", $now
            );
        }

        return self::sitemapIndexXml($entries);
    }

    public static function generateChildForLanguagePageContent($language, $type, $page)
    {
        $page = max(1, (int) $page);
        $config = config("tablemysql.{$type}");
        if (!$config || empty($config['sitemap'])) return '';

        $table = $config['table'];
        $seoRelation = $config['seo_relation'];
        $perPage = self::MAX_ITEMS;
        $offset = ($page - 1) * $perPage;

        $query = DB::table("{$table} as c")
            ->join("{$seoRelation} as r", 'c.id', '=', "r.{$type}_id")
            ->join('seo as s', 's.id', '=', 'r.seo_id')
            ->where('s.language', $language)
            ->orderByDesc('c.id');

        $items = $query->offset($offset)
            ->limit($perPage)
            ->select(['c.id', 's.slug_full', 's.updated_at', 's.seo_title', 's.image'])
            ->get();

        if ($items->isEmpty()) return '';

        $entries = [];
        foreach ($items as $item) {
            $url = env('APP_URL') . '/' . ltrim($item->slug_full, '/');
            $lastmod = date('c', strtotime($item->updated_at));
            $title = $item->seo_title ?? '';
            $image = !empty($item->image)
                ? Image::getUrlImageLargeByUrlImage($item->image)
                : config('image.default');

            $entries[] = self::urlEntry($url, $lastmod, $title, $image);
        }

        return $entries ? self::urlsetXml($entries) : '';
    }

    public static function sitemapEntry($loc, $lastmod)
    {
        $loc = self::xmlEscape($loc);
        return "<sitemap><loc>{$loc}</loc><lastmod>{$lastmod}</lastmod></sitemap>";
    }

    public static function urlEntry($loc, $lastmod, $title, $image)
    {
        $loc = self::xmlEscape($loc);
        $title = self::xmlEscape($title);
        $image = self::xmlEscape($image);

        return <<<XML
<url>
    <loc>{$loc}</loc>
    <lastmod>{$lastmod}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
    <image:image>
        <image:loc>{$image}</image:loc>
        <image:title>{$title}</image:title>
    </image:image>
</url>
XML;
    }

    public static function sitemapIndexXml($entries)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
' . implode("\n", $entries) . '
</sitemapindex>';
    }

    public static function urlsetXml($entries)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
' . implode("\n", $entries) . '
</urlset>';
    }

    /**
     * Escape chuỗi cho XML (chuẩn ENT_XML1)
     */
    private static function xmlEscape($string)
    {
        if ($string === null || $string === '') return '';
        return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8', false);
    }

    public static function fixPermissions($path = null)
    {
        $path = $path ?: app_path('sitemaps');

        if (!is_dir($path)) return;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @chmod($item->getPathname(), 0777);
            } else {
                @chmod($item->getPathname(), 0666);
            }
        }

        @chmod($path, 0777);
    }
}