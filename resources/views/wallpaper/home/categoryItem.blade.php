
@php
    $categorySeo            = new \stdClass;
    foreach($category->seos as $seo){
        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
            $categorySeo    = $seo;
            break;
        }
    }
    $categoryName           = $categorySeo->infoSeo->title ?? null;
    $categoryUrl            = env('APP_URL').'/'.$categorySeo->infoSeo->slug_full;
    /* key id list */
    $idList                 = 'js_openCloseCategoryListForMobile_'.$category->id;
@endphp
<a href="{{ $categoryUrl }}" class="categoryGrid_box_item_image">
    @if(!empty($category->thumnails)&&$category->thumnails->count()>0)
        @foreach($category->thumnails as $thumnail)
            @php
                $categoryThumbMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($thumnail->infoFreewallpaper->file_cloud); 
                $categoryThumbSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($thumnail->infoFreewallpaper->file_cloud); 
                $categoryThumbLarge = \App\Helpers\Image::getUrlImageLargeByUrlImage($thumnail->infoFreewallpaper->file_cloud); 
                $active             = $loop->index == 0 ? ' active' : '';
            @endphp
            <picture>
                <source media="(max-width: 767px)" srcset="{{ $categoryThumbSmall }}">
                <img class="lazyload {{ $active }}" 
                     src="{{ $categoryThumbMini }}" 
                     data-src="{{ $categoryThumbLarge }}" 
                     alt="{{ $categoryName }}" 
                     title="{{ $categoryName }}" 
                     loading="lazy" />
            </picture>
            @break
        @endforeach
    @endif
</a>
<div id="{{ $idList }}" class="categoryGrid_box_item_content">
    <div class="categoryGrid_box_item_content_title">
        <a href="{{ $categoryUrl }}">
            <h2 class="maxLine_1">{{ $categoryName }}</h2>
        </a>
        <div class="categoryGrid_box_item_content_title_action" onclick="openCloseCategoryListForMobile('{{ $idList }}');">
            <!-- dùng after => content -->
        </div>
    </div>
    @if(!empty($category->tags)&&$category->tags->isNotEmpty())
        <div class="categoryGrid_box_item_content_list">
            @foreach($category->tags as $tag)
                @foreach($tag->infoTag->seos as $tagSeo)
                    @if($tagSeo->infoSeo->language==$language)
                        <a href="/{{ $tagSeo->infoSeo->slug_full }}">{{ $tagSeo->infoSeo->title }}</a>
                        @break
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif
</div>