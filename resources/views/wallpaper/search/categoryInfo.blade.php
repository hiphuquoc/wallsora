

<div class="searchViewBeforeOfCategory_boxItem customScrollBar-y">
    <div class="">
        @foreach($categories as $category)
            @foreach($category->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    <a href="/{{ $seo->infoSeo->slug_full }}" class="searchViewBeforeOfCategory_boxItem_item">
                        <div>{{ $seo->infoSeo->title }}</div> 
                        <div>{{ $category->products->count() }} {{ config('data_language_1.'.$language.'.photo_set') }}</div>
                    </a>
                    @break
                @endif
            @endforeach
        @endforeach
    </div>
</div>