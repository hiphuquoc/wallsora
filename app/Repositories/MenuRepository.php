<?php

namespace App\Repositories;

use App\Models\Page;
use App\Models\Category;
use App\Models\CategoryBlog;

class MenuRepository
{
    public function getMenuData($language)
    {
        return [
            'pageAboutUs'       => $this->getPageAboutUs(),
            'wallpaperMobile'   => $this->getWallpaperMobile(),
            'policies'          => $this->getPolicies(),
            'categoriesBlog'    => $this->getCategoriesBlog(),
            'urlPath'           => urldecode(request()->path()),
            'language'          => $language,
        ];
    }

    protected function getPageAboutUs()
    {
        return Page::whereHas('seo', function ($query) {
                $query->where('slug', 've-chung-toi');
            })
            ->with(['seo', 'seos'])
            ->first();
    }

    protected function getWallpaperMobile()
    {
        $categories = Category::getTreeCategory();

        foreach ($categories as $category) {
            if (!empty($category->seo->level) && $category->seo->level == 1) {
                return $category;
            }
        }

        return null;
    }

    protected function getPolicies()
    {
        return Page::select('page_info.*')
            ->join('seo', 'seo.id', '=', 'page_info.seo_id')
            ->whereHas('type', function ($query) {
                $query->where('code', 'policy');
            })
            ->orderBy('seo.ordering', 'DESC')
            ->orderBy('seo.id', 'ASC')
            ->with(['seo', 'seos'])
            ->get();
    }

    protected function getCategoriesBlog()
    {
        return CategoryBlog::getTreeCategory();
    }
}