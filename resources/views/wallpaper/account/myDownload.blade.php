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
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Title - Description - Social -->
    @php
        $highPrice          = 0;
        $lowPrice           = 0;
    @endphp
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Title - Description - Social -->

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
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')

    <div class="articleBox distanceBetweenBox">
        <div class="pagePageBox distanceBetweenSubbox">
            <!-- breadcrumb -->
            @include('wallpaper.template.breadcrumb')
            <!-- tiêu đề -->
            @php
                $count = 0;
                foreach($infoCustomer->orders as $order) foreach($order->wallpapers as $wallpaper) ++$count;
            @endphp
            <h1 class="titlePage">{{ $itemSeo->title ?? $item->seo->title ?? null }} (<span class="highLight">{{ $count }}</span>)</h1>
            <!-- danh sách tải xuống -->
            @if(!empty($infoCustomer->orders)&&$infoCustomer->orders->count()>0)
                <div class="wallpaperSourceGrid">
                    @foreach($infoCustomer->orders as $order)
                        @foreach($order->wallpapers as $wallpaper)
                            @include('wallpaper.confirm.itemDownload', compact('wallpaper'))
                        @endforeach
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- Header bottom -->
    @include('wallpaper.snippets.headerBottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        
    </script>
@endpush