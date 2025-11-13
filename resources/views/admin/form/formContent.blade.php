<div class="formBox">
    <div class="formBox_full">
        <div class="formBox_full_item">
            <!-- One Column -->
            @php
                $chatgptDataAndEvent = [];
                if(!empty($prompt)){
                    if($language=='vi'){
                        if($prompt->reference_name=='content'){
                            if($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'){
                                $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, $idBox, $idContent ?? 0);
                            }
                        }
                    }else {
                        if($prompt->reference_name=='content'&&$prompt->type=='translate_content'){
                            $chatgptDataAndEvent = \App\Helpers\Charactor::generateChatgptDataAndEvent($item, $prompt, $language, $idBox, $idContent ?? 0);
                        }
                    }
                }
                $content = old('content') ?? $content ?? '';
            @endphp
            <div>
                <label class="form-label inputRequired" for="content">Nội dung</label>
                @if(!empty($itemSeo->id))
                    <i class="fa-regular fa-copy reloadContentIcon" onclick="getPromptTextById({{ $itemSeo->id }}, {{ $prompt->id }}, '{{ $language }}')"></i>
                @endif
                @if(!empty($chatgptDataAndEvent['eventChatgpt']))
                    <i class="fa-solid fa-pen-nib reloadContentIcon" onclick="{{ $chatgptDataAndEvent['eventChatgpt'] ?? null }}"></i>
                @endif
                @if(!empty($content)&&$language=='vi'&&$type=='edit')
                    <i class="fa-solid fa-wand-magic-sparkles reloadContentIcon" onclick="improveContent($('#content_{{ $ordering }}'), {{ $ordering }}, {{ $itemSeo->id }});"></i>
                @endif
            </div>
            <div class="{{ !empty($flagCopySource)&&$flagCopySource==true ? 'boxInputSuccess' : '' }}">
                <textarea class="form-control tinySelector" id="{{ $idBox }}"  name="content[{{ $ordering }}]" rows="30" {{ $chatgptDataAndEvent['dataChatgpt'] ?? null }}>{!! is_array($content) ? implode('', $content) : $content !!}</textarea>
            </div>
        </div>
        {{-- <div class="formBox_full_item">
            <textarea class="form-control" id="en_content"  name="en_content" rows="20">{{ old('en_content') ?? $enContent ?? '' }}</textarea>
        </div> --}}
    </div>
</div>

@pushonce('scriptCustom')
    <!-- Place the first <script> tag in your HTML's <head> -->
    
    <script src="https://cdn.tiny.cloud/1/{{ env('TINY_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // plugins: 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
        tinymce.init({
            selector: '.tinySelector',
            menubar: false,
            plugins: 'code anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags typography inlinecss',
            toolbar: 'code | blocks | bold italic underline strikethrough | link image media table | align lineheight | checklist numlist bullist indent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
            entity_encoding : "raw",
            init_instance_callback: function (editor) {
                editor.on('change', function () {
                    Prism.highlightAll();
                });
            }
        });

    </script>

@endpushonce