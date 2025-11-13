<?php

return [
    'author_name'           => 'wallsora.com',
    'founder_name'          => 'wallsora.com',
    'founder_address'       => '55 Cô Giang, Rạch Giá',
    'founding'              => '2023-03-30',
    'company_name'          => 'wallsora.com',
    'email'                 => 'anhnendienthoai@gmail.com',
    'company_description'   => 'Giới thiệu dịch vụ',
    'contacts'          =>  [
                                [
                                    'type'      => 'customer service',
                                    'phone'     => '0968617168'
                                ],
                                [
                                    'type'      => 'technical support',
                                    'phone'     => '0968617168'
                                ],
                                [
                                    'type'      => 'sales',
                                    'phone'     => '0968617168'
                                ]
                            ],
    'logo_main'             => 'images/upload/logo-type-manager-upload.webp',
    'products'          => [
        [
            'type'      => 'Product',
            'product'   => 'Thương mại điện tử'
        ]
    ],
    'socials'           => [
        'https://facebook.com/name',
        'https://twitter.com/name',
        'https://pinterest.com/name',
        'https://youtube.com/name'
    ],
    'google_cloud_storage' => [
        'default_domain'    => 'https://'.env('GOOGLE_CLOUD_STORAGE_BUCKET').'.storage.googleapis.com/',
        'cdn_domain'        => 'https://cdn.wallsora.com/', /* đường dẫn CDN thiết lập trên google cloud CDN */
        // 'wallpapers'        => 'test/',
        'wallpapers'        => 'wallpapers/',
        'sources'           => 'sources/',
        'freeWallpapers'    => 'freewallpapers/',
        'images'            => 'storage/images/',
    ],
    'filter'    => [
        'price' => [
            [
                'name'  => 'Nhỏ hơn 100,000đ',
                'min'   => '0',
                'max'   => '100000'
            ],
            [
                'name'  => 'Từ 100,000đ - 200,000đ',
                'min'   => '100000',
                'max'   => '200000'
            ],
            [
                'name'  => 'Từ 200,000đ - 350,000đ',
                'min'   => '200000',
                'max'   => '350000'
            ],
            [
                'name'  => 'Từ 350,000đ - 500,000đ',
                'min'   => '350000',
                'max'   => '500000'
            ],
            [
                'name'  => 'Từ 500,000đ - 1,000,000đ',
                'min'   => '500000',
                'max'   => '1000000'
            ],
            [
                'name'  => 'Trên 1,000,000đ',
                'min'   => '1000000',
                'max'   => '9999999999999999999999'
            ]
        ]
    ],
    'view_by' => [
        [
            'icon'      => 'icon_gift',
            'key'       => 'each_set'
        ],
        [
            'icon'      => 'icon_star',
            'key'       => 'each_image'
        ]
    ],
    'cache'     => [
        'extension'     => 'html',
        'folderSave'    => 'public/caches/',
        // 'folderSave'    => 'storage/caches/', /* lưu trên google cloud */
        'disk'          => 'local',
    ],
    'main.password_user_default' => 'hitourVN@mk123',
    'category_type' => [
        [
            'key' => 'category_info', /* mặc định nằm ở trên */
            'key_filter_language'   => 'filter_by_themes',
            'name' => 'Chủ đề'
        ],
        [
            'key' => 'style_info',
            'key_filter_language'   => 'filter_by_styles',
            'name' => 'Phong cách'
        ],
        [
            'key' => 'event_info',
            'key_filter_language'   => 'filter_by_events',
            'name' => 'Sự kiện'
        ]
    ],
    'sort_type' => [
        [
            'icon'      => 'icon_star',
            'key'       => 'propose',
        ],
        [
            'icon'      => 'icon_heart',
            'key'       => 'favourite',
        ],
        [
            'icon'      => 'icon_arrow_down',
            'key'       => 'newest',
        ],
        [
            'icon'      => 'icon_arrow_up',
            'key'       => 'oldest',
        ]
    ],
    'feeling_type'  => [
        [
            'icon'          => 'icon_vomit_2',
            'icon_unactive' => 'icon_vomit_2_unactive',
            'key'           => 'vomit',
            'name'          => 'Ói',
            'en_name'       => 'Vomit'
        ],
        [
            'icon'      => 'icon_not_like_2',
            'icon_unactive' => 'icon_not_like_2_unactive',
            'key'       => 'notlike',
            'name'      => 'Không thích',
            'en_name'   => 'Not like'
        ],
        [
            'icon'      => 'icon_haha_2',
            'icon_unactive' => 'icon_haha_2_unactive',
            'key'       => 'haha',
            'name'      => 'Haha',
            'en_name'   => 'Haha'
        ],
        [
            'icon'      => 'icon_heart_2',
            'icon_unactive' => 'icon_heart_2_unactive',
            'key'       => 'heart',
            'name'      => 'Thả tim',
            'en_name'   => 'Heart'
        ]
    ],
    'auto_fill' => [
        'alt'   => [
            'vi'    => 'Hình nền điện thoại',
            'en'    => 'Phone wallpaper'
        ],
        'slug'  => [
            'vi'    => 'tag-hinh-nen-dien-thoai',
            'en'    => 'tag-phone-wallpaper'
        ]
    ],
    'url_free_wallpaper_category'   => [
        'hinh-nen-dien-thoai-mien-phi', 'free-phone-wallpapers', 'fonds-d-ecran-pour-telephones-gratuit', 'fondos-de-pantalla-para-telefonos-gratuito', '手机壁纸免费', 'обои-для-телефона-бесплатно', '無料携帯電話の壁紙', 'wallpaper-ponsel-gratis', '무료-휴대폰-배경화면', 'फोन-वॉलपेपर-मुफ्त', 'ফোন-ওয়ালপেপার-বিনা-মূল্যে', 'फोन-वॉलपेपर-मोफत', 'தொலைபேசி-வால்பேப்பர்கள்-இலவசம்', 'ఫోన్-వాల్పేపర్లు-నివ్వు', 'مفت-فون-وال-پیپرز', 'ફોન-વોલપેપર્સ-મફત', 'wallpaper-hp-gratis', 'kertas-dinding-telefon-percuma', 'วอลเปเปอร์โทรศัพท์ฟรี', 'مجاني-خلفيات-الهاتف', 'رایگان-تصاویر-زمینه-تلفن', 'handy-hintergründe-kostenlos', 'telefon-duvar-kağıtları-ücretsiz', 'sfondi-per-telefono-gratuito', 'tapety-na-telefon-darmowy', 'шпалери-для-телефону-безкоштовно', 'telefoon-achtergronden-gratis', 'ταπετσαρίες-για-τηλέφωνα-δωρεάν', 'telefon-hatterkepek-ingyenes', 'tapety-na-telefon-zdarma', 'tapete-pentru-telefon-gratuit', 'tapety-na-telefon-zadarmo', 'მობილური-ტელეფონის-ფონები-უფასოდ', 'חינם-טפטים-לטלפונים', 'telefon-fon-rasmlari-bepul', 'papeis-de-parede-para-telefones-gratuito', 'mga-wallpaper-ng-telepono-libre', 'telefon-bakgrunder-gratis', 'telefon-bakgrunner-gratis', 'puhelimen-taustakuvat-ilmainen', 'telefon-baggrunde-gratis', 'ഫോണിന്റെ-വാൾപേപ്പറുകൾ-സൗജന്യം', 'телефонни-тапети-безплатно', 'телефон-обои-акысыз', 'позадине-за-телефон-бесплатно', 'tālruņu-fona-attēli-bezmaksas', 'telefonų-fono-paveikslėliai-nemokamas', 'ozadje-za-telefon-brezplačno', 'утасны-ханын-зураг-үнэгүй',
    ],
    'url_confirm_page'   => [
        'xac-nhan', 'confirm', 'commande', 'confirmacion', '订单确认', 'आदेश-की-पुष्टि', 'অর্ডার-নিশ্চিতকরণ', 'आदेश-पुष्टी', 'ஆணை-உறுதிப்படுத்தல்', 'ఆర్డర్-నిర్ధారణ', 'آرڈر-کی-تصدیق', 'આદેશ-પુષ્ટિ', '注文確認', '주문-확인', 'konfirmasi-pesanan', 'pengesahan-pesanan', 'การยืนยันคำสั่งซื้อ', 'підтвердження-замовлення', 'bestelbevestiging', 'επιβεβαίωση-παραγγελίας', 'rendeles-megerősitese', 'potvrzeni-objednavky', 'confirmarea-comenzii', 'potvrdenie-objednavky', 'წარდგინების-დადასტურება', 'אישור-הזמנה', 'buyurtma-tasdiqlash', 'confirmaçao-de-pedido', 'تأكيد-الطلب', 'تأیید-سفارش', 'подтверждение-заказа', 'bestellbestätigung', 'sipariş-onayı', 'conferma-dell-ordine', 'potwierdzenie-zamowienia', 'konfirmasi-pesanan-id', 'kumpirmasyon-ng-order', 'beställningsbekräftelse', 'ordrebekreftelse', 'tilauksen-vahvistus', 'ordrebekræftelse', 'ഓർഡർ-സ്ഥിരീകരണം', 'потвърждение-на-поръчка', 'заказды-тастыктоо', 'potvrda-narudžbine', 'pasūtījuma-apstiprinājums', 'užsakymo-patvirtinimas', 'potrditev-naročila', 'захиалга-баталгаажуулалт',
    ],
    'url_cart_page'   => [
        'gio-hang', 'shopping-cart', 'panier', 'carrito-de-compras', '购物车', 'शॉपिंग-कार्ट', 'শপিং-কার্ট', 'खरेदी-गाडी', 'ஷாப்பிங்-கார்ட்', 'షాపింగ్-కార్ట్', 'خریداری-کی-ٹوکری', 'ખરીદીટર', 'ショッピングカート', '장바구니', 'keranjang-belanja', 'troli-pembelian', 'ตะกร้าสินค้า', 'кошик', 'winkelwagentje', 'καλάθι', 'bevasarlo-kosar', 'nakupni-košik', 'coș-de-cumparaturi', 'nakupny-košik', 'შოპინგის-კალათ', 'עגלת-קניות', 'savat', 'carrinho-de-compras', 'عربة-التسوق', 'سبد-خرید', 'корзина', 'warenkorb', 'alışveriş-sepeti', 'carrello', 'koszyk', 'keranjang-belanja-id', 'cart-ng-pamimili', 'kundvagn', 'handlekurv', 'ostoskori', 'indkøbskurv', 'ഷോപ്പിംഗ്-കാർട്ട്', 'количка', 'дүкөн-арабасы', 'корпа-за-куповину', 'iepirkumu-grozs', 'pirkinių-krepšelis', 'nakupovalna-košarica', 'сагс',
    ],
    'tool_translate'    => [
        'ai', 'google_translate'
    ],
    'ai_version'    => [ /* vesion đầu tiên được mặc định dùng ở các trường hợp không quy định */
        'qwen-max', 'qwen-plus', 'qwen-turbo', 'gpt-4o', 'gpt-4o-mini', 'o1', 'o1-mini', 'o3-mini', 'deepseek-ai/DeepSeek-R1', 'deepseek-ai/DeepSeek-V3'
    ],
    'percent_discount_default'  => '0.4', /* hệ số giá mặc định khi không lấy được thông tin GPS và IP của khách hàng */
    'status_check_translate_of_page'    => [
        0 => [
            'notes'     => 'Chưa kiểm tra',
            'color'     => '',
        ],
        1 => [
            'notes'     => 'Đã báo cập nhật',
            'color'     => '#D9DFC6',
        ],
        2 => [
            'notes'     => 'Cập nhật thất bại',
            'color'     => '#FFF2C2',
        ],
    ],
    'view_mode' => [
        /* phần tử đầu tiên mặc định */
        [
            'key'  => 'light',
            'icon'  => 'icon_view_mode_light',
        ],
        [
            'key'   => 'gray',
            'icon'  => 'icon_view_mode_gray',
        ],
        // [
        //     'key'   => 'sepia',
        //     'icon'  => 'icon-light',
        // ],
        [
            'key'   => 'dark',
            'icon'  => 'icon_view_mode_dark',
        ]
    ],
];