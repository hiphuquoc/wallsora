<?php
return [
    'left-menu-admin'   => [
        [
            'name'      => 'Đơn hàng',
            'route'     => 'admin.order.list',
            'icon'      => '<i class="fa-regular fa-file-lines"></i>'
        ],
        [
            'name'  => 'Chủ đề',
            'route' => '',
            'icon'  => '<i class="fa-solid fa-layer-group"></i>',
            'child'     => [
                [
                    'name'  => '1. Danh sách',
                    'route' => 'admin.category.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Ngôn ngữ',
                    'route' => 'admin.category.listLanguageNotExists',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'  => 'Tag',
            'route' => '',
            'icon'  => '<i class="fa-solid fa-tags"></i>',
            'child'     => [
                [
                    'name'  => '1. Danh sách',
                    'route' => 'admin.tag.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Ngôn ngữ',
                    'route' => 'admin.tag.listLanguageNotExists',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'      => 'Nền trả phí',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-cloud"></i>',
            'child'     => [
                [
                    'name'  => '1. Kho trả phí',
                    'route' => 'admin.wallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>',
                ],
                [
                    'name'  => '2. Sản phẩm',
                    'route' => 'admin.product.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '3. Ngôn ngữ',
                    'route' => 'admin.product.listLanguageNotExists',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'      => 'Nền miễn phí',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-cloud-arrow-down"></i>',
            'child'     => [
                [
                    'name'  => '1. Kho miễn phí',
                    'route' => 'admin.freeWallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Seo từng ảnh',
                    'route' => 'admin.seoFreeWallpaper.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '3. Ngôn ngữ',
                    'route' => '',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'      => 'Trang',
            'route'     => 'admin.page.list',
            'icon'      => '<i class="fa-regular fa-file-lines"></i>',
            // 'child'     => [
            //     [
            //         'name'  => '1. Danh sách',
            //         'route' => '',
            //         'icon'  => '<i data-feather=\'circle\'></i>'
            //     ]
            // ]
        ],
        [
            'name'      => 'Blog',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-blog"></i>',
            'child'     => [
                [
                    'name'  => '1. Chuyên mục',
                    'route' => 'admin.categoryBlog.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Bài viết',
                    'route' => 'admin.blog.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                
            ]
        ],
        [
            'name'      => 'Ảnh',
            'route'     => 'admin.image.list',
            'icon'      => '<i class="fa-regular fa-images"></i>',
        ],
        [
            'name'      => 'Cài đặt',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-gear"></i>',
            'child'     => [
                [
                    'name'  => '1. Giao diện',
                    'route' => 'admin.theme.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Slider home',
                    'route' => 'admin.setting.slider',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ]
            ]
        ],
        [
            'name'      => 'Công cụ SEO',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-screwdriver-wrench"></i>',
            'child'     => [
                [
                    'name'  => '1. Redirect 301',
                    'route' => 'admin.redirect.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'      => 'Công nghệ AI',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-robot"></i>',
            'child'     => [
                [
                    'name'  => '1. Prompt',
                    'route' => 'admin.prompt.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. API AI',
                    'route' => 'admin.apiai.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
        [
            'name'      => 'Báo cáo',
            'route'     => '',
            'icon'      => '<i class="fa-solid fa-flag-checkered"></i>',
            'child'     => [
                [
                    'name'  => '1. Auto dịch',
                    'route' => 'admin.translate.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
                [
                    'name'  => '2. Check translate page',
                    'route' => 'admin.checkTranslateOfPage.list',
                    'icon'  => '<i data-feather=\'circle\'></i>'
                ],
            ]
        ],
    ]
];