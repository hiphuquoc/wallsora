

<div class="searchViewBeforeOfBlog_boxItem customScrollBar-y">
    @foreach($blogs as $blog)
        @foreach($blog->seos as $seo)
            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                @php
                    $title      = $seo->infoSeo->title;
                    $url        = $seo->infoSeo->slug_full;
                    /* giá */
                    $image      = Storage::url(config('image.loading_main_gif'));
                    if(!empty($seo->infoSeo->image)) $image = \App\Helpers\Image::getUrlImageLargeByUrlImage($seo->infoSeo->image);
                @endphp
                <a href="/{{ $url }}" class="searchViewBeforeOfBlog_boxItem_item">
                    <div class="searchViewBeforeOfBlog_boxItem_item_image">
                        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
                    </div>
                    <div class="searchViewBeforeOfBlog_boxItem_item_content">
                        <div class="searchViewBeforeOfBlog_boxItem_item_content_title maxLine_2">
                            {{ $title }}
                        </div>
                        <div class="searchViewBeforeOfBlog_boxItem_item_content_info">
                            <div class="searchViewBeforeOfBlog_boxItem_item_content_info_item maxLine_1">
                                <svg><use xlink:href="#icon_user"></use></svg>
                                Name Admin
                            </div>
                            <div class="searchViewBeforeOfBlog_boxItem_item_content_info_item maxLine_1">
                                <svg><use xlink:href="#icon_clock_bold"></use></svg>
                                {!! date('d \t\h\á\n\g m, Y', strtotime($seo->infoSeo->created_at)) !!}
                            </div>
                        </div>
                    </div>
                </a>
                @break;
            @endif
        @endforeach
    @endforeach
</div>
<!-- button viewAll -->
@php
    $url    = route('routing', ['slug' => config('language.'.$language.'.slug_page_premium_wallpaper')]).'?search='.$keySearch;
@endphp
<a href="{{ $url }}" class="searchViewBeforeOfBlog_viewAll">
    <div>{{ config('data_language_1.'.$language.'.view_all') }} (<span>{{ $count }}</span>)</div>
    <svg><use xlink:href="#icon_double_arrow_right"></use></svg>
</a>