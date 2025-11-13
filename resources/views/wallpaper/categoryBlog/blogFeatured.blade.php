<div class="blogSiderbarBox">
    <div class="blogSiderbarBox_head">
        <h2>{{ config('data_language_1.'.$language.'.featured_articles') }}</h2>
    </div>
    <div class="blogSiderbarBox_box">
        @foreach($blogFeatured as $blog)
            @foreach($blog->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    @php
                        $title = $seo->infoSeo->title ?? '';
                        $urlArticle = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                    @endphp
                    <div class="blogSiderbarBox_box_item">
                        <a href="{{ $urlArticle }}" class="blogSiderbarBox_box_item_image">
                            @php
                                $imageMini  = \App\Helpers\Image::getUrlImageMiniByUrlImage($blog->seo->image);
                                $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($blog->seo->image);
                            @endphp
                            @if(!empty($blog->seo->image))
                                <img 
                                    class="lazyload" 
                                    src="{{ $imageMini }}" 
                                    data-src="{{ $imageSmall }}" 
                                    alt="{{ $title }}" 
                                    title="{{ $title }}" 
                                    loading="lazy"
                                />
                            @endif
                        </a>
                        <div class="blogSiderbarBox_box_item_content">
                            <a href="{{ $urlArticle }}" class="blogSiderbarBox_box_item_content_title">
                                <h3 class="maxLine_2">{{ $title }}</h3>
                            </a>
                            <div class="blogSiderbarBox_box_item_content_info">
                                <div>Admin Name</div> 
                                <div>{{ date('d \t\h\á\n\g m, Y', strtotime($seo->infoSeo->created_at)) }}</div>
                            </div>
                        </div>
                    </div>
                    @php
                        break;
                    @endphp
                @endif
            @endforeach
        @endforeach
    </div>
</div>