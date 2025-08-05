<?php

namespace App\Http\Controllers;

use App\Helpers\Image;
use App\Models\Seo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public static function main()
    {
        $types = Seo::select('type')->distinct()->pluck('type')->toArray();

        $xml = self::generateUrlset(function () use ($types) {
            $entries = '';
            foreach ($types as $type) {
                $lastMod = now()->subSeconds(rand(3600, 259200))->toIso8601String();
                $url = env('APP_URL') . "/sitemap/{$type}.xml";
                $entries .= self::generateUrlEntry($url, $lastMod, 'weekly', '0.8');
            }
            return $entries;
        });

        return self::xmlResponse($xml);
    }

    public static function child($name)
    {
        if (empty($name)) {
            return \App\Http\Controllers\ErrorController::error404();
        }

        $name = \App\Http\Controllers\Admin\HelperController::determinePageType($name);
        $languages = array_column(config('language'), 'key');

        $xml = self::generateUrlset(function () use ($languages, $name) {
            $entries = '';
            foreach ($languages as $lang) {
                if (!$lang) continue;
                $lastMod = now()->subSeconds(rand(3600, 84600))->toIso8601String();
                $url = env('APP_URL') . "/sitemap/{$lang}/{$name}.xml";
                $entries .= self::generateUrlEntry($url, $lastMod, 'weekly', '0.8');
            }
            return $entries;
        });

        return self::xmlResponse($xml);
    }

    public static function childForLanguage($language, $name)
    {
        if (empty($name)) {
            return \App\Http\Controllers\ErrorController::error404();
        }

        $modelName = config("tablemysql.{$name}.model_name");
        if (!$modelName || !class_exists($modelClass = "\App\Models\\{$modelName}")) {
            return \App\Http\Controllers\ErrorController::error404();
        }

        $items = resolve($modelClass)
            ->select('*')
            ->withDefaultSeoForLanguage($language)
            ->orderByDesc('id')
            ->get();

        if ($items->isEmpty()) {
            return \App\Http\Controllers\ErrorController::error404();
        }

        $xml = self::generateUrlset(function () use ($items) {
            $entries = '';
            foreach ($items as $item) {
                $seo = optional($item->seos->first())->infoSeo;
                if (!$seo) continue;

                $url = env('APP_URL') . '/' . self::escapeXml($seo->slug_full);
                $imageUrl = !empty($item->seo->image)
                    ? Image::getUrlImageLargeByUrlImage($item->seo->image)
                    : env('APP_URL') . Storage::url(config('main_' . env('APP_NAME') . '.logo_main'));

                $entries .= "
                    <url>
                        <loc>{$url}</loc>
                        <lastmod>" . self::escapeXml(date('c', strtotime($seo->updated_at))) . "</lastmod>
                        <changefreq>hourly</changefreq>
                        <priority>1</priority>
                        <image:image>
                            <image:loc>" . self::escapeXml($imageUrl) . "</image:loc>
                            <image:title>" . self::escapeXml($seo->seo_title) . "</image:title>
                        </image:image>
                    </url>";
            }
            return $entries;
        });

        return self::xmlResponse($xml);
    }

    private static function generateUrlset(callable $generateEntries): string
    {
        $entries = $generateEntries();
        return <<<XML
            <urlset xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                {$entries}
            </urlset>
            XML;
    }

    private static function generateUrlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return <<<XML
        <url>
            <loc>{$loc}</loc>
            <lastmod>{$lastmod}</lastmod>
            <changefreq>{$changefreq}</changefreq>
            <priority>{$priority}</priority>
        </url>
        XML;
    }

    private static function escapeXml(string $value): string
    {
        return str_replace(
            ['&', '<', '>', '"', "'"],
            ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'],
            $value
        );
    }

    private static function xmlResponse(string $xml)
    {
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
