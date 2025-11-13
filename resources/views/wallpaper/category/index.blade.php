@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/category-free-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/category-free-first-view.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(asset('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        $highPrice          = 0;
        $lowPrice           = 0;
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.breadcrumb', compact('breadcrumb'))
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
    @include('wallpaper.schema.itemlist', ['data' => $wallpapers])
    <!-- END:: FAQ Schema -->

    <!-- STRAT:: ImageObject Schema -->
    @include('wallpaper.schema.imageObject', ['data' => $wallpapers])
    <!-- END:: ImageObject Schema -->

    <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <!-- share social -->
    @include('wallpaper.template.shareSocial')
    <!-- content -->
    <div class="articleBox distanceBetweenBox">
       <div class="distanceBetweenSubbox">
            <!-- breadcrumb -->
            @include('wallpaper.template.breadcrumb')
            <!-- tiêu đề -->
            @php
                $titlePage = $itemSeo->title;
                if($item->seo->level>1){
                    $titlePage  = \App\Helpers\Charactor::concatenateWords([
                        config('data_language_2.'.$language.'.phone_wallpaper'),
                        $itemSeo->title,
                    ], $language);
                }
            @endphp
            <h1 class="titlePage">{{ $titlePage }}</h1>
            <!-- Sort Box -->
            @include('wallpaper.category.sort', [
                'language'          => $language ?? 'vi',
                'total'             => $total
            ])
            <!-- Box 
                vừa vào tải 0 phần tử -> tất cả tải bằng ajax
            -->
            @include('wallpaper.category.box', [
                'wallpapers'        => new \Illuminate\Database\Eloquent\Collection,
                'total'             => $total,
                'loaded'            => 0,
                'arrayIdCategory'   => $arrayIdCategory,
                'language'          => $language
            ])
       </div>
        <!-- Nội dung -->
        @if(!empty($dataContent['content']))
            <div id="js_buildTocContentMain_element" class="distanceBetweenBox contentElement maxContent-1200">
                <div class="contentBox">
                    <div id="tocContentMain">{!! $dataContent['toc_content'] !!}</div>
                    {!! $dataContent['content'] !!}
                </div>
            </div>
        @endif
    </div>
    
@endsection
@push('modal')
    <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => null,
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
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            /* build tocContent khi scroll gần tới */
            const elementBuildTocContent = $('#js_buildTocContentMain_element');
            /* build toc content */
            if(elementBuildTocContent.length){
                if (!elementBuildTocContent.hasClass('loaded')) {
                    buildTocContentMain('js_buildTocContentMain_element');
                }
            }     
        });
    </script>
@endpush