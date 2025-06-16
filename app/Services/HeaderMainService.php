<?php

namespace App\Services;

use App\Repositories\MenuRepository;
use Illuminate\Support\Facades\View;

class HeaderMainService
{
    protected $menuRepository;
    protected $language;
    protected $cacheKey;
    protected $htmlCacheService;

    public function __construct(MenuRepository $menuRepository, HtmlCacheService $htmlCacheService)
    {
        $this->menuRepository = $menuRepository;
        $this->htmlCacheService = $htmlCacheService;
        $this->language = \App\Http\Controllers\SettingController::getLanguage();
        $this->cacheKey = 'html_header_side_' . $this->language;
    }

    /**
     * Lấy menu HTML từ cache hoặc render mới nếu chưa có
     */
    public function getMenuHtml()
    {
        return $this->htmlCacheService->getOrRender($this->cacheKey, function () {
            $menuData = $this->menuRepository->getMenuData($this->language);
            return View::make('wallpaper.snippets.headerSide', $menuData)->render();
        });
    }

    /**
     * Xóa cache của menu
     */
    public function clearCache()
    {
        $this->htmlCacheService->clear($this->cacheKey);
    }
}