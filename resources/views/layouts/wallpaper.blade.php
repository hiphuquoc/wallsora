<!DOCTYPE html>
<html lang="{{ $language ?? 'vi' }}" dir="{{ config('language.'.$language.'.dir') }}">   
    
{{-- class="{{ request()->cookie('view_mode') ?? config('main_'.env('APP_NAME').'.view_mode')[0]['key'] }}" --}}

<!-- === START:: Head === -->
<head>
    @include('wallpaper.snippets.head')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body class="background">

    <!-- SVG icon inline -->
    @include('wallpaper.snippets.svgSprite')
    
    @include('wallpaper.snippets.headerTop')

    <!-- === START:: Content === -->
    <div id="js_settingCollapsedMenu" class="layoutHeaderSide">
        <!-- giữ chỗ thanh bên để chống nhảy trang -->
        <div class="layoutHeaderSide_placeholder"></div>
        <!-- thanh bên ===== truyền vào service để tạo cacheHtml -->
        <div id="js_toggleMenuMobile" class="layoutHeaderSide_header container">
            {!! $menuHtml ?? null !!}
        </div>
        <div class="backgroundBlurMobileMenu" onClick="toggleMenuMobile('js_toggleMenuMobile');"></div>
        <!-- nội dung chính -->
        <div class="layoutHeaderSide_content container">
            <div id="js_blurBackground">
                @yield('content')
                @include('wallpaper.snippets.footer')
            </div>
        </div>
    </div>
    <!-- === END:: Content === -->

    <!-- === START:: BOTTOM === -->
    <div class="bottom">
        <div id="smoothScrollToTop" class="gotoTop" onclick="javascript:smoothScrollToTop();">
            <svg><use xlink:href="#icon_arrow_up"></use></svg>
        </div>
        @stack('bottom')
    </div>
    <!-- === END:: BOTTOM === -->

    <!-- === START:: LOADING === -->
    <div id="js_toggleFullLoading" class="fullLoading">
        <div class="fullLoading_box">
            <div class="loadingIcon"></div>
            <div id="js_toggleFullLoading_text" class="fullLoading_box_text">{{ config('data_language_3.'.$language.'.the_system_is_processing_your_request') }}</div>
        </div>
    </div>
    <!-- === END:: LOADING === -->
    
    <!-- Modal -->
    @stack('modal')

    <!-- login form modal -->
    <div id="js_checkLoginAndSetShow_modal">
        <!-- tải ajaax checkLoginAndSetShow() -->
    </div>

    <!-- === START:: Scripts Default === -->
    @include('wallpaper.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
    
</body>
<!-- === END:: Body === -->

</html>