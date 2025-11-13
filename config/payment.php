<?php 


return [
    'momo'      => [
        'action'            => 'Thanh toán qua Momo',
        'partner_name'      => 'Name.com.vn',
        'store_id'          => 'Name.com.vn',
        /* môi trường production */
        'endpoint_create'   => 'https://payment.momo.vn/v2/gateway/api/create',
        'partner_code'      => 'MOMOLNCV20230412',
        'access_key'        => 'CnX75vqGxFVp0u9o',
        'secret_key'        => 'eZzLBvX0RZHhzmlAO1VCU0nmB5JBSAvw',
        /* môi trường test */
        // 'endpoint_create'   => 'https://test-payment.momo.vn/v2/gateway/api/create',
        // 'partner_code'      => 'MOMOLNCV20230412_TEST',
        // 'access_key'        => 'p05z68oqDjIh0rF3',
        // 'secret_key'        => 'd2KCebzFK9DPONFVDiAJMAgeY88B7if9',
        'payment_success_code'  => [
            0, 9000
        ]
    ],
    'zalopay'   => [
        /* môi trường production */
        'endpoint'  => 'https://openapi.zalopay.vn/v2/create',
        'app_id'    => '2669',
        'app_user'  => 'Wallpaper',
        "key_1"     => "51mCWr1bU8sg6IThT31DcQCiyHaNMbgw",
        "key_2"     => "ql3W79bv0GThJ4djl8JynroiVkdvbpy6",
        /* môi trường test */
        // 'endpoint'  => 'https://sb-openapi.zalopay.vn/v2/create',
        // 'app_id'    => '2669',
        // 'app_user'  => 'Wallpaper',
        // "key_1"     => "gi1wPy9qc2NA5QakzbDjA1OShlXPiAfG",
        // "key_2"     => "4HZweYiFVY0RQwUTcHozGlTNZy1WyblB",
        "api"       => [
            "getbanklist"       => "https://gateway.zalopay.vn/api/getlistmerchantbanks"
        ],
    ],
    'vnpay'   => [
        // /* môi trường production */
        // 'endpoint'  => 'https://openapi.zalopay.vn/v2/create',
        // 'app_id'    => '2669',
        // 'app_user'  => 'Wallpaper',
        // "key_1"     => "51mCWr1bU8sg6IThT31DcQCiyHaNMbgw",
        // "key_2"     => "ql3W79bv0GThJ4djl8JynroiVkdvbpy6",
        /* môi trường test */
        'endpoint'      => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
        'access_key'    => 'HITOURW1',
        'secret_key'    => 'UVI7LAJKBKYZME0LQZIU262XD86WKUCG',
    ]
];