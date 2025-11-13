

<div class="searchViewBeforeOfPremiumWallpaper_boxItem customScrollBar-y">
    @foreach($products as $product)
        @php
            $infoSeoProduct = [];
            foreach($product->seos as $seo){
                if($seo->infoSeo->language==$language) {
                    $infoSeoProduct = $seo->infoSeo;
                    break;
                }
            }
        @endphp
        @if(!empty($infoSeoProduct))
            @php
                $title      = $infoSeoProduct->title;
                $url        = $infoSeoProduct->slug_full;
                /* giá */
                $priceOld   = '<span>'.\App\Helpers\Number::getFormatPriceByLanguage($product->price, $language, false).'</span>';
                $priceSell  = \App\Helpers\Number::getPriceOriginByCountry($product->price);
                $priceSell  = \App\Helpers\Number::getFormatPriceByLanguage($priceSell, $language);
                $image      = Storage::url(config('image.loading_main_gif'));
                if(!empty($product->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)) $image = \App\Helpers\Image::getUrlImageMiniByUrlImage($product->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper);
            @endphp
            <a href="/{{ $url }}" class="searchViewBeforeOfPremiumWallpaper_boxItem_item">
                <div class="searchViewBeforeOfPremiumWallpaper_boxItem_item_image">
                    <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
                </div>
                <div class="searchViewBeforeOfPremiumWallpaper_boxItem_item_content">
                    <div class="searchViewBeforeOfPremiumWallpaper_boxItem_item_content_title maxLine_2">
                        {{ $title }}
                    </div>
                    <div class="searchViewBeforeOfPremiumWallpaper_boxItem_item_content_price">
                        <div>{!! $priceSell !!}</div>
                        {!! $priceOld !!}
                    </div>
                </div>
            </a>
        @endif
    @endforeach
</div>
<!-- button viewAll -->
@php
    $url    = route('routing', ['slug' => config('language.'.$language.'.slug_page_premium_wallpaper')]).'?search='.$keySearch;
@endphp
<a href="{{ $url }}" class="searchViewBeforeOfPremiumWallpaper_viewAll">
    <div>{{ config('data_language_1.'.$language.'.view_all') }} (<span>{{ $count }}</span>)</div>
    <svg><use xlink:href="#icon_double_arrow_right"></use></svg>
</a>