<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use Illuminate\Support\Facades\View;

class HeaderMainService
{
    protected $menuRepository;
    protected $htmlCacheService;

    public function __construct(MenuRepository $menuRepository, HtmlCacheService $htmlCacheService)
    {
        $this->menuRepository = $menuRepository;
        $this->htmlCacheService = $htmlCacheService;
    }

    /**
     * Lấy menu HTML từ cache hoặc render mới nếu chưa có
     */
    public function getMenuHtml($language)
    {
        $cacheKey   = 'html_header_side_' . $language;
        return $this->htmlCacheService->getOrRender($cacheKey, function () use($language) {
            $menuData = $this->menuRepository->getMenuData($language);
            return View::make('wallpaper.snippets.headerSide', $menuData)->render();
        });
    }

    /**
     * Xóa cache của menu
     */
    public function clearCache($cacheKey)
    {
        $this->htmlCacheService->clear($cacheKey);
    }
}