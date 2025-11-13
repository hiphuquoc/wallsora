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
        {{-- <!-- One Row -->
        @php
            $lock = $language=='vi' ? '' : 'disabled';
        @endphp
        <div class="formBox_full_item">
            <label class="form-label inputRequired">Loại danh mục</label>
            <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                <select class="select2 form-select select2-hidden-accessible" name="category_type" {{ $lock }}>
                    @foreach(config('main_'.env('APP_NAME').'.category_type') as $categoryType)
                        @php
                            $selected   = null;
                            if(old('category_type') == $categoryType['key'] || (!empty($item->seo->type) && $item->seo->type == $categoryType['key'])) {
                                $selected = 'selected';
                            }
                        @endphp
                        <option value="{{ $categoryType['key'] }}" {{ $selected }}>{{ $categoryType['name'] }}</option>
                    @endforeach
                </select>                
                @if($lock=='disabled')
                    @foreach(config('main_'.env('APP_NAME').'.category_type') as $categoryType)
                        @php
                            if(!empty($item->seo->type)&&$item->seo->type==$categoryType['key']) {
                                $valueKey = $categoryType['key'];
                                break;
                            }
                        @endphp
                    @endforeach
                    <input type="hidden" name="category_type" value="{{ $valueKey }}">
                @endif
            </div>
        </div> --}}
        @if($language=='vi')
            <!-- One Row -->
            <div class="formBox_full_item">
                <span data-toggle="tooltip" data-placement="top" title="
                    Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label" for="ordering">Thứ tự</label>
                </span>
                <input type="number" min="0" id="ordering" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="ordering" value="{{ old('ordering') ?? $itemSeo->ordering ?? $itemSource->seo->ordering ?? '' }}">
            </div>
            {{-- <!-- One Row -->
            <div class="formBox_column2_item_row">
                <span data-toggle="tooltip" data-placement="top" title="
                    Danh sách các Tags thuộc Category này
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label" for="tags">Tags con</label>
                </span>
                <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                    <select class="select2 form-select select2-hidden-accessible" id="tags" name="tags[]" aria-hidden="true" multiple="true">
                        @if(!empty($tags))
                            @foreach($tags as $t)
                                @php
                                    $selected = null;
                                    // Kiểm tra nếu có old input
                                    $oldTags = old('tags', []);
                                    if(in_array($t->id, $oldTags)) {
                                        $selected = 'selected';
                                    } else if (!empty($item->tags) && $item->tags->isNotEmpty()) {
                                        // Kiểm tra trong $item->tags
                                        foreach($item->tags as $tTag) {
                                            if($t->id == $tTag->infoTag->id) {
                                                $selected = 'selected';
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                @if(!empty($t->seo))
                                    <option value="{{ $t->id }}" {{ $selected }}>{{ $t->seo->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>                    
                </div>
            </div> --}}
            {{-- <!-- One Row -->
            <div class="formBox_column2_item_row">
                <span data-toggle="tooltip" data-placement="top" title="
                    Category này kết nối Blog tin tức nào?
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label" for="category_blog_info_id">Kết nối Chuyên mục blog</label>
                </span>
                <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                    <select class="select2 form-select select2-hidden-accessible" id="category_blog_info_id" name="category_blog_info_id[]" aria-hidden="true" multiple="true">
                        @if(!empty($categoryBlogs))
                            @foreach($categoryBlogs as $c)
                                @php
                                    $selected       = null;
                                    if(!empty($item->categoryBlogs)&&$item->categoryBlogs->isNotEmpty()){
                                        foreach($item->categoryBlogs as $cBlog){
                                            if($c->id==$cBlog->infoCategoryBlog->id) {
                                                $selected = 'selected';
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <option value="{{ $c->id }}" {{ $selected }}>{{ $c->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div> --}}
            <!-- One Row -->
            <div class="formBox_full_item">
                <div class="form-check form-check-success">
                    @php
                        if(empty($item)){
                            $flagCheck = !empty($itemSource->flag_show)&&($itemSource->flag_show==1) ? 'checked' : null;
                        }else {
                            $flagCheck = !empty($item->flag_show)&&($item->flag_show==1) ? 'checked' : null;
                        }
                    @endphp
                    <input type="checkbox" class="form-check-input" name="flag_show" {{ $flagCheck }}>
                    <label class="form-check-label" for="flag_show">Cho phép hiển thị trong danh sách</label>
                </div>
            </div>
        @endif
        
    </div>
</div>