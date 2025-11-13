<div class="categoryBlogSidebarBox">
    <div class="categoryBlogSidebarBox_head">
        <h2>{{ config('data_language_1.'.$language.'.news_category') }}</h2>
    </div>
    <div class="categoryBlogSidebarBox_box">
        @foreach($categories as $category)
            @foreach($category->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    @php
                        $title = $seo->infoSeo->title ?? '';
                        $urlArticle = env('APP_URL').'/'.$seo->infoSeo->slugFull;
                    @endphp
                    <a href="{{ $urlArticle }}" class="categoryBlogSidebarBox_box_item">
                        <h3>{{ $title }}</h3>
                    </a>
                @endif 
            @endforeach
        @endforeach
    </div>
</div>