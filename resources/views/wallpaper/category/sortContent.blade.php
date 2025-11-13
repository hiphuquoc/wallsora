<div class="sortBox">
    <div class="sortBox_left">
        <!-- sort by -->
        @php
            $dataSort       = config('main_'.env('APP_NAME').'.sort_type');
            $sortBy         = Cookie::get('sort_by') ?? $dataSort[0]['key'];
            $inputSortBy    = null;
            foreach($dataSort as $sortItem){
                if($sortBy==$sortItem['key']) {
                    $inputSortBy    = '<svg><use xlink:href="#'.$sortItem['icon'].'"></use></svg>'.config('data_language_1.'.$language.'.'.$sortItem['key']);
                }
            }
        @endphp
        <div class="selectCustom">
            <div class="selectCustom_text maxLine_1">
                {!! config('data_language_1.'.$language.'.sort_by') !!}
            </div>
            <div class="selectCustom_input maxLine_1">
                {!! $inputSortBy !!}
            </div>
            <div class="selectCustom_box">
                @foreach($dataSort as $sortItem)
                    @php
                        $selected = null;
                        if($sortBy==$sortItem['key']) $selected = 'selected';
                    @endphp
                    <div class="selectCustom_box_item {{ $selected }}" onClick="setSortBy('{{ $sortItem['key'] }}')">
                        <svg><use xlink:href="#{{ $sortItem['icon'] }}"></use></svg>
                        {!! config('data_language_1.'.$language.'.'.$sortItem['key']) !!}
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Chủ đề/phong cách/sự kiện -->
        @foreach(config('main_'.env('APP_NAME').'.category_type') as $type)
            <div class="selectCustom hide-991">
                @include('wallpaper.category.selectCustom', compact('type'))
            </div>
        @endforeach
        <!-- icon filter nâng cao -->
        @php
            $titleAdvancedFilter = config('data_language_1.'.$language.'.advanced_filter');
        @endphp
        <div class="filterAdvanced show-991">
            <div id="js_toggleFilterAdvanced_element" class="filterAdvanced_icon" onclick="toggleFilterAdvanced('js_toggleFilterAdvanced_element');">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" height="18" class="sm:mr-2 sm:text-slate-400"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
            <div class="filterAdvanced_text">
                {{ $titleAdvancedFilter }}
            </div>
            <div class="filterAdvanced_box">
                <div class="filterAdvanced_box_title">
                    <span>{{ $titleAdvancedFilter}}</span>
                    <div class="filterAdvanced_box_title_close" onclick="toggleFilterAdvanced('js_toggleFilterAdvanced_element');">
                        <svg><use xlink:href="#icon_close"></use></svg>
                    </div>
                </div>
                
                <div class="filterAdvanced_box_content">
                    @foreach(config('main_'.env('APP_NAME').'.category_type') as $type)
                        <div class="filterAdvanced_box_content_item">
                            <div class="selectCustom">
                                @include('wallpaper.category.selectCustom')
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="sortBox_right">
        <div class="sortBox_right_item">
            <!-- số lượng -->
            <span class="quantity">
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.photo_set') }} {{ $total }}</div>
            </span> 
        </div>
    </div>
</div>