<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        @php
            $chatgptDataAndEvent = [];
            foreach($prompts as $prompt){
                if($language=='vi'){
                    if($prompt->reference_name=='title'&&$prompt->type=='auto_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'title');
                        break;
                    }
                }else {
                    if($prompt->reference_name=='title'&&$prompt->type=='translate_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'title');
                        break;
                    }
                }
            }
        @endphp
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là Tiêu đề được hiển thị trên website
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="title">Tiêu đề Trang</label>
                    @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                        <i class="fa-solid fa-arrow-rotate-left reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                    @endif
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="title">
                    {{ !empty($itemSeo->title) ? mb_strlen($itemSeo->title) : 0 }}
                </div>
            </div>
            <input type="text" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" id="title" name="title" value="{{ old('title') ?? $itemSeo->title ?? null }}" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} required>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
        @if($language=='vi')
            <!-- One Row -->
            <div class="formBox_full_item">
                <span data-toggle="tooltip" data-placement="top" title="
                    Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label" for="ordering">Thứ tự</label>
                </span>
                <input type="number" min="0" id="ordering" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="ordering" value="{{ old('ordering') ?? $item->seo->ordering ?? null }}">
            </div>
            <!-- One Row -->
            <div class="formBox_full_item">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là mã sản phẩm dùng để tìm sản phẩm trên kho
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="code">Mã sản phẩm</label>
                </span>
                <input type="number" min="0" id="code" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="code" value="{{ old('code') ?? $item->code ?? null }}" required />
            </div>
            <!-- category/style/event -->
            {{-- @foreach(config('main_'.env('APP_NAME').'.category_type') as $categoryType)
                <div class="formBox_full_item">
                    <label class="form-label">{{ $categoryType['name'] }}</label>
                    <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                        <select class="select2 form-select select2-hidden-accessible" name="{{ $categoryType['key'] }}[]" multiple="true">
                            <option value="">- Lựa chọn -</option>
                            @if(!empty($categories))
                                @foreach($categories as $category)
                                    @if(!empty($category->seo->type)&&$category->seo->type==$categoryType['key'])
                                        @php
                                            $selected   = null;
                                            if(!empty($item->categories)){
                                                foreach($item->categories as $c) {
                                                    if(!empty($c->infoCategory->id)&&$c->infoCategory->id==$category->id) {
                                                        $selected = ' selected';
                                                        break;
                                                    }
                                                }
                                            }
                                            /* tất cả tag */
                                        @endphp
                                        <option value="{{ $category->id }}"{{ $selected }}>{{ $category->seo->title }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            @endforeach --}}
            @foreach(config('main_'.env('APP_NAME').'.category_type') as $categoryType)
                <div class="formBox_full_item">
                    <label class="form-label">{{ $categoryType['name'] }}</label>
                    <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                        <select class="select2 form-select select2-hidden-accessible" name="{{ $categoryType['key'] }}[]" multiple="true">
                            <option value="">- Lựa chọn -</option>
                            @if(!empty($categories))
                                @foreach($categories as $category)
                                    @if(!empty($category->seo->type) && $category->seo->type == $categoryType['key'])
                                        @php
                                            $selected = null;
                                            $oldCategories = old($categoryType['key'], []);
                                            if(in_array($category->id, $oldCategories)) {
                                                $selected = 'selected';
                                            } else if(!empty($item->categories)){
                                                foreach($item->categories as $c) {
                                                    if(!empty($c->infoCategory->id) && $c->infoCategory->id == $category->id) {
                                                        $selected = 'selected';
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $category->id }}" {{ $selected }}>{{ $category->seo->title }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            @endforeach
            <!-- One row -->
            {{-- <div class="formBox_full_item">
                @php
                    $arrayTagName           = [];
                    if(!empty($item->tags)){
                        foreach($item->tags as $tag){
                            if(!empty($tag->infoTag->seo->title)) $arrayTagName[] = $tag->infoTag->seo->title;
                        }
                    }
                    $strTagName             = implode(',', $arrayTagName);
                    /* action & prompt */
                    $chatgptDataAndEvent = [];
                    foreach($prompts as $prompt){
                        if($prompt->reference_name=='tag'){
                            if($prompt->type=='auto_content_for_image'){
                                $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'tag');
                                break;
                            }
                        }
                    }
                @endphp
                <label for="tag" class="form-label">
                    Tag name
                    @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                        <i class="fa-solid fa-arrow-rotate-left reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                    @endif
                </label>
                <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                    <input id="tag" name="tag" class="form-control" placeholder="Nhập tag name" value="{{ $strTagName }}" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} />  
                </div>
            </div> --}}
            <div class="formBox_full_item">
                @php
                    $arrayTagName = [];
            
                    // Sử dụng old() để giữ lại các tag đã nhập trước đó khi có lỗi validate
                    $oldTags = old('tag');
                    if($oldTags) {
                        $arrayTagName = explode(',', $oldTags);
                    } else {
                        if(!empty($item->tags)) {
                            foreach($item->tags as $tag) {
                                if(!empty($tag->infoTag->seo->title)) $arrayTagName[] = $tag->infoTag->seo->title;
                            }
                        }
                    }
                    $strTagName = implode(',', $arrayTagName);
            
                    // action & prompt
                    $chatgptDataAndEvent = [];
                    foreach($prompts as $prompt){
                        if($prompt->reference_name == 'tag') {
                            if($prompt->type == 'auto_content_for_image') {
                                $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'tag');
                                break;
                            }
                        }
                    }
                @endphp
                <label for="tag" class="form-label">
                    Tag name
                    @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                        <i class="fa-solid fa-arrow-rotate-left reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                    @endif
                </label>
                <div class="{{ !empty($flagCopySource) && $flagCopySource == true ? 'boxInputSuccess' : '' }}">
                    <input id="tag" name="tag" class="form-control" placeholder="Nhập tag name" value="{{ $strTagName }}" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} />
                </div>
            </div>            
            <!-- One Row -->
            <div class="formBox_full_item">
                <label class="form-label inputRequired" for="price">Giá trọn bộ $</label>
                <input type="text" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" id="price" name="price" value="{{ old('price') ?? $item->price ?? null }}" required />
            </div>
        @endif
    </div>
</div>
@push('scriptCustom')
    <script type="text/javascript">
        var strTag = {!! json_encode($arrayTag) !!};
        new Tagify(document.querySelector("#tag"), {
            whitelist: strTag,
            maxTags: Infinity, // allows to select max items
            dropdown: {
                maxItems: Infinity, // display max items
                classname: "tags-inline", // Custom inline class
                enabled: 0,
                closeOnSelect: false
            }
        });
    </script>
@endpush