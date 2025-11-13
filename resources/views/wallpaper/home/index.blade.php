@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/home-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/home-first-view.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(asset('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Organization Schema -->
    @include('wallpaper.schema.organization')
    <!-- END:: Organization Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->
    
    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.itemlist', ['data' => $categories])
    <!-- END:: FAQ Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => 1, 'highPrice' => 5])
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <!-- share social -->
    @include('wallpaper.template.shareSocial')
    <!-- content -->
    <div class="breadcrumbMobileBox"><!-- dùng để chống nhảy padding - margin so với các trang có breadcrumb --></div>
    <!-- Item Category Grid Box -->
    <div class="articleBox">
        @foreach(config('main_'.env('APP_NAME').'.category_type') as $type)
            @include('wallpaper.home.categoryBox', [
                'type'  => $type['key'],
                'data'  => $categories,
            ])
        @endforeach
    </div>
@endsection
@push('modal')
    <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => '',
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null,
            'language'  => $language
        ])
    </div>
@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('wallpaper.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            let interval; // Biến lưu trữ interval cho carousel

            // Sử dụng $(document).on để xử lý sự kiện trên các phần tử động
            $(document).on('mouseenter', '.categoryGrid_box_item_image', function() {
                var $children = $(this).children('img');
                let currentIndex = 0;

                // Đổi ảnh ngay lập tức khi hover lần đầu
                $children.eq(currentIndex).removeClass('active');
                currentIndex = (currentIndex + 1) % $children.length;
                $children.eq(currentIndex).addClass('active');

                // Khởi động carousel để tiếp tục đổi ảnh mỗi 2s
                interval = setInterval(function() {
                    $children.eq(currentIndex).removeClass('active');
                    currentIndex = (currentIndex + 1) % $children.length; // Quay lại từ đầu khi hết phần tử
                    $children.eq(currentIndex).addClass('active');
                }, 2000); // Thay đổi thời gian giữa các vòng lặp nếu cần

            }).on('mouseleave', '.categoryGrid_box_item_image', function() {
                clearInterval(interval); // Dừng carousel khi chuột ra ngoài
                var $children = $(this).children('img');
                $children.removeClass('active'); // Ẩn tất cả hình ảnh khi không hover
                $children.eq(0).addClass('active'); // Hiển thị hình ảnh đầu tiên khi không hover
            });

            // Khởi động carousel với hình ảnh đầu tiên khi trang tải hoặc khi có phần tử mới được thêm vào
            function initializeImages() {
                $('.categoryGrid_box_item_image').each(function() {
                    var $children = $(this).children('img');
                    $children.eq(0).addClass('active'); // Hiển thị hình ảnh đầu tiên
                });
            }

            // Khởi động cho các phần tử ban đầu
            initializeImages();
        });
    </script>
@endpush
