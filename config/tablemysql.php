<?php

return [
    'free_wallpaper_info' => [
        'model_name'    => 'FreeWallpaper',
        'table'         => 'free_wallpaper_info',
        'seo_relation'  => 'relation_seo_free_wallpaper_info',
        'sitemap'       => true,
    ],
    'tag_info' => [
        'model_name'    => 'Tag',
        'table'         => 'tag_info',
        'seo_relation'  => 'relation_seo_tag_info',
        'sitemap'       => true,
    ],
    'category_info' => [
        'model_name'    => 'Category',
        'table'         => 'category_info',
        'seo_relation'  => 'relation_seo_category_info',
        'sitemap'       => true,
    ],
    'style_info' => [
        'model_name'    => 'Category',
        'table'         => 'style_info',
        'seo_relation'  => 'relation_seo_category_info',
        'sitemap'       => false,
    ],
    'event_info' => [
        'model_name'    => 'Category',
        'table'         => 'event_info',
        'seo_relation'  => 'relation_seo_category_info',
        'sitemap'       => false,
    ],
    'page_info' => [
        'model_name'    => 'Page',
        'table'         => 'page_info',
        'seo_relation'  => 'relation_seo_page_info',
        'sitemap'       => true,
    ],
    'product_info' => [
        'model_name'    => 'Product',
        'table'         => 'product_info',
        'seo_relation'  => 'relation_seo_product_info',
        'sitemap'       => true,
        
    ],
    'category_blog' => [
        'model_name'    => 'CategoryBlog',
        'table'         => 'category_blog',
        'seo_relation'  => 'relation_seo_category_blog',
        'sitemap'       => true,
    ],
    'blog_info' => [
        'model_name'    => 'Blog',
        'table'         => 'blog_info',
        'seo_relation'  => 'relation_seo_blog_info',
        'sitemap'       => true,
    ],
];
