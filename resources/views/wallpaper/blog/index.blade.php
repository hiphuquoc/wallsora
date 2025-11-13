@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/category-blog-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/category-blog-first-view.scss']['file'];
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
        $lowPrice           = $highPrice;
    @endphp
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

    {{-- <!-- STRAT:: FAQ Schema -->
    @include('wallpaper.schema.itemlist', ['data' => $blogs])
    <!-- END:: FAQ Schema --> --}}

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
        
        <div class="pageCategoryBlog distanceBetweenSubbox">
            <!-- breadcrumb -->
            @include('wallpaper.template.breadcrumb')
            <!-- thân trang -->
            <div class="layoutPageCategoryBlog">
                <div class="layoutPageCategoryBlog_left distanceBetweenSubbox">
                    <!-- tiêu đề -->
                    <h1 class="titlePage">{{ $itemSeo->title }}</h1>
                    <!-- thông tin bài viết -->
                    <div class="blogInfoHeadBox">
                        @foreach($item->seos as $seo)
                            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                <div class="blogInfoHeadBox_item maxLine_1">
                                    <svg><use xlink:href="#icon_user"></use></svg>
                                    Name Admin
                                </div>
                                <div class="blogInfoHeadBox_item maxLine_1">
                                    <svg><use xlink:href="#icon_clock_bold"></use></svg>
                                    {!! date('d \t\h\á\n\g m, Y', strtotime($seo->infoSeo->created_at)) !!}
                                </div>
                                <div class="blogInfoHeadBox_item maxLine_1">
                                    <svg style="transform: scale(1.15)"><use xlink:href="#icon_eye_bold"></use></svg>
                                    {{ $item->viewed }}
                                </div> 
                                <div class="blogInfoHeadBox_item maxLine_1">
                                    <svg style="transform: scale(1.1)"><use xlink:href="#icon_share"></use></svg>
                                    {{ $item->shared }}
                                </div>
                                @break
                            @endif
                        @endforeach
                    </div>
                    <!-- nội dung -->
                    <div id="js_buildTocContentMain_element" class="contentBox">
                        {!! $htmlContent !!}
                    </div>
                </div>
                <!-- sidebar -->
                <div class="layoutPageCategoryBlog_right">
                    <!-- bai viết nổi bật -->
                    @if(!empty($blogFeatured)&&$blogFeatured->count()>0)
                        @include('wallpaper.categoryBlog.blogFeatured', compact('blogFeatured', 'language'))
                    @endif
                    <!-- danh sách category_blog -->
                    @if(!empty($categoriesLv2)&&$categoriesLv2->count()>0)
                        @include('wallpaper.categoryBlog.categoryBlogList', [
                            'categories'    => $categoriesLv2,
                            'language'      => $language,
                        ])
                    @endif
                </div>
            </div>

        </div>
        <!-- bài viết liên quan -->

    </div>

@endsection
@push('modal')
    {{-- <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => null,
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null,
            'language'  => $language
        ])
    </div> --}}
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