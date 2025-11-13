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
                <input type="number" min="0" id="ordering" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="ordering" value="{{ old('ordering') ?? $itemSeo->ordering ?? $itemSource->seo->ordering ?? '' }}">
            </div>
            <!-- One Row -->
            <div class="formBox_column2_item_row">
                <span data-toggle="tooltip" data-placement="top" title="
                    Danh sách các Category mà Tag này thuộc
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label" for="categories">Categories cha</label>
                </span>
                <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                    <select class="select2 form-select select2-hidden-accessible" id="categories" name="categories[]" aria-hidden="true" multiple="true">
                        @if(!empty($categories))
                            @foreach($categories as $c)
                                @php
                                    $selected = null;
                                    // Kiểm tra nếu có old input
                                    $oldCategories = old('categories', []);
                                    if(in_array($c->id, $oldCategories)) {
                                        $selected = 'selected';
                                    } else if (!empty($item->categories) && $item->categories->isNotEmpty()) {
                                        // Kiểm tra trong $item->categories
                                        foreach($item->categories as $cCategory) {
                                            if($c->id == $cCategory->infoCategory->id) {
                                                $selected = 'selected';
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                @if(!empty($c->seo))
                                    <option value="{{ $c->id }}" {{ $selected }}>{{ $c->seo->title }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>                    
                </div>
            </div>
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