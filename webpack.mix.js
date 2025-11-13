const mix = require('laravel-mix');

mix.sass('resources/sources/main/style.scss', 'public/css/main/style.css')
    .sass('resources/sources/admin/style.scss', 'public/css/admin/style.css').options({
    processCssUrls: false
});
//    .options({
//        hmr: true, // Kích hoạt chức năng HMR
//        hmrOptions: {
//            host: 'superdong.dev', // Địa chỉ host cho HMR
//            port: '3000' // Cổng cho HMR
//        }
//    });