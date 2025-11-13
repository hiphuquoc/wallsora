@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/confirm-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/confirm-first-view.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(asset('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@push('headCustom')
    <!-- Event snippet for Lượt xem trang conversion page -->
    @php
        $tmp = \App\Helpers\Number::getPriceByLanguage($order->total, 'vi');
        $priceConfirm = $tmp['number'];
    @endphp
    <script>
        gtag('event', 'conversion', {
            'send_to': 'AW-16558810206/GKHLCKSGg64ZEN7I7dc9',
            'value': '{{ $priceConfirm }}',
            'currency': 'VND'
        });
    </script>
@endpush
@section('content')
    <div style="overflow:hidden;">
        <div class="articleBox distanceBetweenBox">
            <div class="distanceBetweenSubbox">
                <!-- breadcrumb -->
                @include('wallpaper.template.breadcrumb')
                <!-- tiêu đề -->
                <h1 class="titlePage">{{ $itemSeo->title }}</h1>
                <!-- xác nhận thanh toán -->
                @php
                    $xhtmlTotal = null;
                @endphp
                <div class="confirmMessageBox">
                    <div class="confirmMessageBox_left">
                        <div class="confirmMessageBox_left_icon">
                            <img class="lazyload" src="{{ Storage::url('images/icon-confirm-success.png') }}" loading="lazy" />
                        </div>
                        <div class="confirmMessageBox_left_title">
                            {{ config('data_language_1.'.$language.'.payment_successful') }}
                        </div>
                        <div class="confirmMessageBox_left_desc">
                            {{ config('data_language_1.'.$language.'.thank_you_message') }}
                        </div>
                    </div>
                    <div class="confirmMessageBox_right">
                        <div class="confirmMessageBox_right_item">
                            {{ config('data_language_1.'.$language.'.order_id') }} : <span class="highLight">{{ $order->code }}</span>
                        </div>
                        @if(!empty($order->email))
                            <div class="confirmMessageBox_right_item">
                                {{ config('data_language_1.'.$language.'.email') }} : {{ $order->email }}
                            </div>
                        @endif
                        <div class="confirmMessageBox_right_item">
                            @if($language=='vi')
                                {{ $order->paymentMethod->name }} lúc {{ date('H:i\, d/m/Y', strtotime($order->created_at)) }}
                            @else 
                                {{ $order->paymentMethod->en_name }} at {{ date('H:i\, d/m/Y', strtotime($order->created_at)) }}
                            @endif
                        </div>
                        <div class="confirmMessageBox_right_item">
                            {{ config('data_language_1.'.$language.'.total') }} : <span class="price">{!! \App\Helpers\Number::getFormatPriceByLanguage($order->total, $language) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- danh sách tải xuống -->
            @if(!empty($order->wallpapers))
                <div class="wallpaperSourceGrid">
                    @foreach($order->wallpapers as $wallpaper)
                        @include('wallpaper.confirm.itemDownload', compact('wallpaper'))
                    @endforeach
                </div>
            @endif

        </div>
    </div>
@endsection
@push('modal')
    {{-- <div id="js_toggleWaiting_box" class="waitingBox">
        <div class="waitingBox_box">

            <div id="loading_1">
                <img class="loading_1" src="{{ config('image.loading_main_css') }}" alt="loading cart" title="loading cart" />
            </div>
            <div class="waitingBox_box_text">Đang tải ảnh...</div>

        </div>
        <div class="waitingBox_bg"></div>
    </div> --}}
@endpush
@push('bottom')
    <!-- Header bottom -->
    {{-- @include('wallpaper.snippets.headerBottom') --}}
@endpush