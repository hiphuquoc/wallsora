
@php
    /* kiểm tra selected với link truy cập hiện tại */
    $flagSelected   = false;
    $nameSelect     = config('data_language_1.'.$language.'.all');
    if(!empty($categoryChoose->seo)&&$categoryChoose->seo->type==$type['key']){
        if(!empty($categories)&&$categories->isNotEmpty()){
            foreach($categories as $category){
                if($category->id==$categoryChoose->id&&$category->flag_show==true) {
                    /* chạy vòng lặp để lấy đúng ngôn ngữ */
                    foreach($category->seos as $s){
                        if(!empty($s->infoSeo)&&$language==$s->infoSeo->language){
                            $nameSelect = $s->infoSeo->title;
                            break;
                        }
                    }
                    $flagSelected = true;
                    break;
                }
            }
        }
    }
    /* kiểm tra selected với filter */
    if($flagSelected==false){
        if(!empty($filters[$type['key']])){
            if(!empty($categories)&&$categories->isNotEmpty()){
                foreach($categories as $category){
                    if($filters[$type['key']]==$category->id&&$category->flag_show==true) {
                        /* chạy vòng lặp để lấy đúng ngôn ngữ */
                        foreach($category->seos as $s){
                            if(!empty($s->infoSeo)&&$language==$s->infoSeo->language){
                                $nameSelect = $s->infoSeo->title;
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }
    }
@endphp
<div class="selectCustom_text maxLine_1">
    {!! config('data_language_1.'.$language.'.'.$type['key_filter_language']) !!}
</div>
<div class="selectCustom_input maxLine_1">
    <div class="maxLine_1">{{ $nameSelect }}</div>
</div>
<div class="selectCustom_box">
    @php
        $urlCategoryChoose              = '';
        if(!empty($categoryChoose->seos)){
            foreach($categoryChoose->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                    $urlCategoryChoose  = $seo->infoSeo->slug_full;
                    break; 
                }
            }
        }
        if(!empty($urlPhoneWallpaper)) $urlCategoryChoose = $urlPhoneWallpaper;
    @endphp
    <a href="/{{ $urlCategoryChoose }}" class="selectCustom_box_item">
        {{ config('data_language_1.'.$language.'.all') }}
    </a>
    @if(!empty($categories)&&$categories->isNotEmpty())
        <!-- ===== Thuật toán của filter box 

            -> khi truy cập cùng loại thì selectbox loại đó sẽ là thẻ a -> chuyển link
            -> khi filter thêm loại khác thì mới filter

            Ví dụ: sự kiện gồm Tết và Giáng sinh
            - nếu truy cập vào trang tết thì selectbox sự kiện sẽ là thẻ a
            - các selectbox với loại khác như Chủ đề, Phong cách sẽ là filter

        -->
        @foreach($categories as $category)
            @if(!empty($category->seo->type)&&$category->seo->type==$type['key']&&$category->flag_show==true)
                @php
                    $urlCategory    = '';
                    $nameCategory   = '';
                    foreach($category->seos as $seo){
                        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                            $urlCategory = $seo->infoSeo->slug_full;
                            $nameCategory = $seo->infoSeo->title;
                            break;
                        }
                    }
                @endphp
                @if(!empty($categoryChoose->seo)&&$categoryChoose->seo->type==$type['key'])
                    @php
                        $selected = '';
                        if($categoryChoose->id==$category->id) $selected = 'selected';
                    @endphp
                    <a href="/{{ $urlCategory ?? null }}" class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                        {{ $nameCategory }}
                        <!-- check box -->
                        <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                    </a>
                @else 
                    @php
                        $selected = '';
                        if(!empty($filters[$type['key']])&&$filters[$type['key']]==$category->id) $selected = 'selected';
                    @endphp
                    <div class="selectCustom_box_item {{ $selected }}" onclick="setFilter(this);">
                        {{ $nameCategory }}
                        <!-- check box -->
                        <input type="radio" name="filters[{{ $type['key'] }}]" value="{{ $category->id }}" />
                    </div>
                @endif
            @endif
        @endforeach
    @endif
</div>