@php
    /* tên được active */
    $nameSelect         = config('data_language_1.'.$language.'.all');
    if(!empty($categoryChoose->seos)){
        foreach($categoryChoose->seos as $seo){
            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                $nameSelect = $seo->infoSeo->title;
                break;
            }
        }
    }
    $urlCategoryLv1     = '';
    if(!empty($categoryLv1)){
        foreach($categoryLv1->seos as $seo){
            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                $urlCategoryLv1 = $seo->infoSeo->slug_full;
                break;
            }
        }
    }
@endphp
<div class="selectCustom_text maxLine_1">
    {!! config('data_language_1.'.$language.'.filter_by_themes') !!}
</div>
<div class="selectCustom_input maxLine_1">
    <div class="maxLine_1">{{ $nameSelect }}</div>
</div>
<div class="selectCustom_box">
    <!-- tất cả -->
    @php
        /* trường hợp không có cateogryBlog được chọn -> selected Tất Cả */
        $selected = !empty($categoryChoose->id) ? '' : 'selected';
    @endphp
    <a href="/{{ $urlCategoryLv1 }}" class="selectCustom_box_item {{ $selected }}">
        {{ config('data_language_1.'.$language.'.all') }}
    </a>
    <!-- option khác -->
    @if(!empty($categories))
        @foreach($categories as $categoryBlog)
            @foreach($categoryBlog->seos as $seo)
                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                    @php
                        $selected = '';
                        if(!empty($categoryChoose->id)&&$categoryBlog->id==$categoryChoose->id) $selected = 'selected';
                    @endphp
                    <a href="/{{ $seo->infoSeo->slug_full }}" class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                        {{ $seo->infoSeo->title }}
                        <!-- check box -->
                        <input type="radio" name="filters[category_blog]" value="{{ $categoryBlog->id }}">
                    </a>
                @endif
            @endforeach
        @endforeach
    @endif

</div>