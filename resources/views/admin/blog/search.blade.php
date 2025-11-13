<form id="formSearch" method="get" action="#">
    <div class="searchBox">
        <div class="searchBox_item">
            <div class="input-group">
                <input type="text" class="form-control" id="search_name" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}" onkeyup="loadProductWithDelay();">
                {{-- <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button> --}}
            </div>
        </div>
        @if(!empty($categories)&&$categories->isNotEmpty())
            <div class="searchBox_item">
                <div class="position-relative">
                    <select class="form-select select2 select2-hidden-accessible" id="search_category" name="search_category" onchange="loadProduct();" aria-hidden="true">
                        <option value="0">- Tìm theo Category -</option>
                        @foreach($categories as $category)
                            @if($category->products->count()>0)
                                @php
                                    $selected = null;
                                    if(!empty($params['search_category'])&&$params['search_category']==$category->id) $selected = ' selected';
                                @endphp
                                <option value="{{ $category->id }}" {{ $selected }}>{{ $category->seo->title }} ({{ $category->products->count() }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        @if(!empty($tags)&&$tags->isNotEmpty())
            <div class="searchBox_item">
                <div class="position-relative">
                    <select class="form-select select2 select2-hidden-accessible" id="search_tag" name="search_tag" onchange="loadProduct();" aria-hidden="true">
                        <option value="0">- Tìm theo Tag -</option>
                        @foreach($tags as $tag)
                            @if($tag->products->count()>0)
                                @php
                                    $selected = null;
                                    if(!empty($params['search_tag'])&&$params['search_tag']==$tag->id) $selected = ' selected';
                                @endphp
                                <option value="{{ $tag->id }}" {{ $selected }}>{{ $tag->seo->title }} ({{ $tag->products->count() }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>  
        @endif
        {{-- <div class="searchBox_item" style="margin-left:auto;text-align:right;">
            @php
                $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewProductInfo', config('setting.admin_array_number_view'), $viewPerPage, $list->total());
                echo $xhtmlSettingView;
            @endphp
        </div> --}}
    </div>
</form>