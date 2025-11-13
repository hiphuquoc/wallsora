{{-- <input type="hidden" name="seo_id" value="{{ $itemSeo->id ?? null }}" /> --}}
<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        @php
            $chatgptDataAndEvent = [];
            foreach($prompts as $prompt){
                if($language=='vi'){
                    if($prompt->reference_name=='seo_title'){
                        if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                            $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_title');
                            break;
                        }
                    }
                }else {
                    if($prompt->reference_name=='seo_title'&&$prompt->type=='translate_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_title');
                        break;
                    }
                }
            }
        @endphp
        <div class="formBox_full_item">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Đây là Tiêu đề được hiển thị ngoài Google... Tốt nhất nên từ 55- 60 ký tự, có chứa từ khóa chính tranh top và thu hút người truy cập click
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="seo_title">Tiêu đề SEO</label>
                    @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                        <i class="fa-solid fa-pencil reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                    @endif
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="seo_title">
                    {{ !empty($itemSeo->seo_title) ? mb_strlen($itemSeo->seo_title) : 0 }}
                </div>
            </div>
            <input type="text" id="seo_title" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="seo_title" value="{{ old('seo_title') ?? $itemSeo['seo_title'] ?? '' }}" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} required>
            <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
        </div>
        <!-- One Row -->
        @php
            $chatgptDataAndEvent = [];
            foreach($prompts as $prompt){
                if($language=='vi'){
                    if($prompt->reference_name=='seo_description'){
                        if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                            $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_description');
                            break;
                        }
                    }
                }else {
                    if($prompt->reference_name=='seo_description'&&$prompt->type=='translate_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'seo_description');
                        break;
                    }
                }
            }
        @endphp
        <div class="formBox_full_item">
            <div class="inputWithNumberChacractor">
                <span class="inputWithNumberChacractor_label" data-toggle="tooltip" data-placement="top" title="
                    Đây là Mô tả được hiển thị ngoài Google... Tốt nhất nên từ 140 - 160 ký tự, có chứa từ khóa chính tranh top và mô tả được cái người dùng đang cần
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="seo_description">Mô tả SEO</label>
                    @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                        <i class="fa-solid fa-pencil reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                    @endif
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="seo_description">
                    {{ !empty($itemSeo->seo_description) ? mb_strlen($itemSeo->seo_description) : 0 }}
                </div>
            </div>
            <textarea class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" id="seo_description"  name="seo_description" rows="5" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} required>{{ old('seo_description') ?? $itemSeo['seo_description'] ?? '' }}</textarea>
            <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
        </div>
        {{-- <!-- One Row -->
        <div class="formBox_full_item">
            <div class="inputWithNumberChacractor">
                <span class="inputWithNumberChacractor_label" data-toggle="tooltip" data-placement="top" title="
                    Đây là Mô tả được hiển thị ngoài Google (bản tiếng anh)... Tốt nhất nên từ 140 - 160 ký tự, có chứa từ khóa chính tranh top và mô tả được cái người dùng đang cần
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="seo_description">SEO Description (En)</label>
                </span>
                <div class="inputWithNumberChacractor_count" data-charactor="en_seo_description">
                    {{ !empty($itemSeo->seo_description) ? mb_strlen($itemSeo->seo_description) : 0 }}
                </div>
            </div>
            <textarea class="form-control" id="en_seo_description"  name="en_seo_description" rows="5" required>{{ old('en_seo_description') ?? $item->en_seo['seo_description'] ?? '' }}</textarea>
            <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
        </div> --}}
        <!-- One Row -->
        @php
            $chatgptDataAndEvent = [];
            foreach($prompts as $prompt){
                if($language=='vi'){
                    if($prompt->reference_name=='slug'){
                        if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                            $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'slug');
                            break;
                        }
                    }
                }else {
                    if($prompt->reference_name=='slug'&&$prompt->type=='translate_content'){
                        $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, 'slug');
                        break;
                    }
                }
            }
        @endphp
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là URL để người dùng truy cập... viết liền không dấu và ngăn cách nhau bởi dấu gạch (-)... nên chứa từ khóa SEO chính và ngắn gọn
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label inputRequired" for="slug">Đường dẫn tĩnh</label>
                @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                    <i class="fa-solid fa-pencil reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                @endif
                <!-- Hàm này dùng idSeo->parent bảng tiếng việt gửi vào trong để tìm parent những ngôn ngữ khác (vì những trang create chưa có trang) -->
                <i class="fa-solid fa-arrow-down reloadContentIcon" onclick="convertStrToSlug('slug', $('#title'), '{{ $itemSeo->type ?? $item->seo->type ?? null }}', '{{ $language }}', {{ $item->seo->parent ?? 0 }});"></i>
            </span>
            @php
                $slug       = null;
                if(empty($idSeoSource)) { /* không phải đang dùng tính năng copy trang gốc => không copy slug */
                    $slug   = old('slug') ?? $itemSeo['slug'] ?? '';
                } 
            @endphp
            <input type="text" id="slug" class="form-control" name="slug" value="{{ $slug }}" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }} required>
            <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
        </div>
        {{-- <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là URL để người dùng truy cập (bản tiếng anh)... viết liền không dấu và ngăn cách nhau bởi dấu gạch (-)... nên chứa từ khóa SEO chính và ngắn gọn
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label inputRequired" for="en_slug">Slug (En)</label>
            </span>
            <input type="text" id="en_slug" class="form-control" name="en_slug" value="{{ old('en_slug') ?? $item->en_seo['slug'] ?? '' }}" required>
            <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
        </div> --}}
        <!-- One Row -->
        {{-- <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là đánh dấu trang gốc của trang này, thẻ link canonical của trang này sẽ được khai báo là đường dẫn của trang gốc để Google biết đây là trang copy của trang gốc
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="link_canonical">Trang gốc</label>
            </span>
            <select class="select2 form-select select2-hidden-accessible" id="link_canonical" name="link_canonical">
                <option value="">- Lựa chọn -</option>
                @foreach($sources as $source)
                    @php
                        $selected   = null;
                        $seoChoose  = [];
                        foreach($source->seos as $seo){
                            if(!empty($seo->infoSeo->language)){
                                if($language==$seo->infoSeo->language){
                                    $seoChoose = $seo->infoSeo;
                                    if(!empty($idSeoSource)&&$idSeoSource==$seoChoose->id){
                                        $selected = 'selected';
                                    }else if(!empty($itemSeo->link_canonical)&&$itemSeo->link_canonical==$seoChoose->id){
                                        $selected = 'selected';
                                    } 
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if(!empty($seoChoose))
                        <option value="{{ $seoChoose->id }}" {{ $selected }}>{{ $source->seo->slug_full }}</option>
                    @endif
                @endforeach
            </select>
        </div> --}}
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là đánh dấu trang gốc của trang này, thẻ link canonical của trang này sẽ được khai báo là đường dẫn của trang gốc để Google biết đây là trang copy của trang gốc
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="link_canonical">Trang gốc</label>
            </span>
            <select class="select2 form-select select2-hidden-accessible" id="link_canonical" name="link_canonical">
                <option value="">- Lựa chọn -</option>
                @foreach($sources as $source)
                    @php
                        $selected   = null;
                        $seoChoose  = [];
                        foreach($source->seos as $seo){
                            if(!empty($seo->infoSeo->language)){
                                if($language==$seo->infoSeo->language){
                                    $seoChoose = $seo->infoSeo;
                                    if(old('link_canonical') == $seoChoose->id || (!empty($idSeoSource) && $idSeoSource == $seoChoose->id) || (!empty($itemSeo->link_canonical) && $itemSeo->link_canonical == $seoChoose->id)){
                                        $selected = 'selected';
                                    }
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if(!empty($seoChoose))
                        <option value="{{ $seoChoose->id }}" {{ $selected }}>{{ $source->seo->slug_full }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        
        {{-- <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là thẻ khai báo Link Canonical để chuyển giá trị trang trùng lặp nội dung về trang chính (bản tiếng anh). Bỏ trống tức trang chính là trang này
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="en_link_canonical">Link Canonical</label>
            </span>
            <div class="input-group input-group-merge">
                <span class="input-group-text" style="background:#efefef;">{{ env('APP_URL') }}/</span>
                <input type="text" name="en_link_canonical" class="form-control" value="{{ old('en_link_canonical') ?? $item->en_seo->link_canonical ?? null }}" style="padding-left:1rem;" />
            </div>
        </div> --}}
        <!-- One Row -->
        @if(!empty($parents))
            <div class="formBox_full_item">
                <span data-toggle="tooltip" data-placement="top" title="
                    Là trang cha chứa trang hiện tại... URL cũng sẽ được hiển thị theo cấp cha - con
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label" for="parent">Trang cha</label>
                </span>
                <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                    <select class="select2 form-select select2-hidden-accessible" id="parent" name="parent">
                        <option value="0">- Lựa chọn -</option>
                        @foreach($parents as $page)
                            @php
                                $selected   = null;
                                $seoChoose  = [];
                                foreach($page->seos as $seo){
                                    if(!empty($seo->infoSeo->language)){
                                        if($language==$seo->infoSeo->language){
                                            $seoChoose = $seo->infoSeo;
                                            if(old('parent') == $seoChoose->id || (!empty($itemSeo->parent) && $itemSeo->parent == $seoChoose->id)) {
                                                $selected = 'selected';
                                            }
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            @if(!empty($seoChoose))
                                <option value="{{ $seoChoose->id }}" {{ $selected }}>{{ $page->seo->title }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        {{-- <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Nhập vào một số để thể hiện độ ưu tiên khi hiển thị cùng các Category khác (Số càng nhỏ càng ưu tiên cao - Để trống tức là không ưu tiên)
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="ordering">Thứ tự</label>
            </span>
            <input type="number" min="0" id="ordering" class="form-control" name="ordering" value="{{ old('ordering') ?? $itemSeo['ordering'] ?? '' }}">
        </div> --}}
        <!-- One Row -->
        <div class="formBox_full_item">
            <div class="flexBox">
                <div class="flexBox_item">
                    <span data-toggle="tooltip" data-placement="top" title="
                        Đây là Số lượt đánh giá này được hiển thị trên trang website và ngoài Google để thể hiện sự uy tín (tự nhập tùy thích)
                    ">
                        <i class="explainInput" data-feather='alert-circle'></i>
                        <label class="form-label inputRequired" for="rating_aggregate_count">Lượt đánh giá</label>
                    </span>
                    <input type="number" id="rating_aggregate_count" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="rating_aggregate_count" value="{{ old('rating_aggregate_count') ?? $itemSeo['rating_aggregate_count'] ?? $item->seo['rating_aggregate_count'] ?? rand(1000,10000) }}" required>
                    <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
                </div>
                <div class="flexBox_item" style="margin-left:1rem;">
                    <span data-toggle="tooltip" data-placement="top" title="
                        Đây là Điểm đánh giá tương ứng này được hiển thị trên trang website và ngoài Google để thể hiện sự uy tín (tự nhập tùy thích)
                    ">
                        <i class="explainInput" data-feather='alert-circle'></i>
                        <label class="form-label inputRequired" for="rating_aggregate_star">Điểm đánh giá</label>
                    </span>
                    <input type="text" id="rating_aggregate_star" class="form-control {{ !empty($flagCopySource)&&$flagCopySource==true ? 'inputSuccess' : '' }}" name="rating_aggregate_star" value="{{ old('rating_aggregate_star') ?? $itemSeo['rating_aggregate_star'] ?? $item->seo['rating_aggregate_star'] ?? '4.'.rand(6,8) }}" required>
                    <div class="invalid-feedback">{{ config('message.admin.validate.not_empty') }}</div>
                </div>
            </div>
        </div>
        {{-- <!-- One Row -->
        <div class="formBox_full_item">
            <span data-toggle="tooltip" data-placement="top" title="
                Đây là Mã Topic dùng đánh dấu Topic Clusther - Content Hub (sẽ cập nhật tính năng SEO này sau)
            ">
                <i class="explainInput" data-feather='alert-circle'></i>
                <label class="form-label" for="topic">Mã Topic</label>
            </span>
            <input type="text" id="topic" class="form-control" name="topic" disabled>
        </div> --}}
    </div>
</div>
@push('scriptCustom')
    <script type="text/javascript">

        function convertStrToSlug(idWrite, inpputData, type, language, idParentVI) {
            const string = $(inpputData).val();
            $.ajax({
                url         : '{{ route("admin.helper.convertStrToSlug") }}',
                type        : 'get',
                dataType    : 'html',
                data        : { 
                    string,
                    type,
                    language,
                    id_parent_vi : idParentVI,
                 }
            }).done(function(data){
                $('#'+idWrite).val(data);
            })
        }

    </script>
@endpush