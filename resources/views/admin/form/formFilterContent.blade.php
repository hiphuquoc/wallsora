@foreach($prompts as $prompt)
    <!-- tiếng việt -> form viết content (đối với bản viết có nhiều box theo layout prompt viết bài) -->
    @if($language=='vi') 
        @if($prompt->reference_name=='content'&&($prompt->type=='auto_content'||$prompt->type=='auto_content_for_image'))
            <div class="pageAdminWithRightSidebar_main_content_item width100">
                <div class="card">
                    <div class="card-body">
                        @php
                            $key                    = $prompt->ordering;
                            $contentsByLanguageUse  = $itemSeoSourceToCopy->contents ?? $itemSeo->contents ?? [];
                            /* lấy content theo ordering */
                            $xhtmlContent           = '';
                            if(!empty($contentsByLanguageUse)&&$contentsByLanguageUse->count()>0){
                                foreach($contentsByLanguageUse as $c){
                                    if($c->ordering==$key) {
                                        $xhtmlContent = $c->content;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @include('admin.form.formContent', [
                            'prompt'            => $prompt,
                            'content'           => $xhtmlContent, 
                            'flagCopySource'    => !empty($itemSeoSourceToCopy) ? true : false,
                            'idBox'             => 'content_'.$key,
                            'ordering'          => $key,
                        ])
                            
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- tiếng khác -> form dịch -->
        @if($prompt->type=='translate_content'&&$prompt->reference_name=='content')
            @php
                $contentsByLanguageUse   = $itemSeoSourceToCopy->contents ?? $itemSeo->contents ?? [];
                /* chọn ngôn ngữ dịch 
                    => nếu trang en chọn ngôn ngữ vi làm bản dịch
                    => nếu ngôn ngữ != en chọn ngôn ngữ en làm bản dịch (nếu có)
                */
                if($language=='en'){
                    $contentsSourceUse  = $itemSourceToCopy->seo->contents ?? $item->seo->contents ?? [];
                }else {
                    $contentsSourceUse  = [];
                    /* kiểm tra trang source to copy có bản en không */
                    if(!empty($itemSourceToCopy->seos)){
                        foreach($itemSourceToCopy->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='en') {
                                if(!empty($seo->infoSeo->contents)&&$seo->infoSeo->contents->isNotEmpty()) $contentsSourceUse = $seo->infoSeo->contents;
                                break;
                            }
                        }
                    }
                    /* kiểm tra tiếp trang source to copy bản vi */
                    if(empty($contentsSourceUse)&&!empty($itemSourceToCopy->seo->contents)) $contentsSourceUse = $itemSourceToCopy->seo->contents;
                    /* kiếm tra tiếp item có bản en không */
                    if(empty($contentsSourceUse)){
                        foreach($item->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language=='en') {
                                if(!empty($seo->infoSeo->contents)&&$seo->infoSeo->contents->isNotEmpty()) $contentsSourceUse = $seo->infoSeo->contents;
                                break;
                            }
                        }
                    }
                    /* kiểm tra tiếp item bản vi */
                    if(empty($contentsSourceUse)&&!empty($item->seo->contents)) $contentsSourceUse = $item->seo->contents;
                }
                
            @endphp
            @if(!empty($contentsSourceUse))
                @foreach($contentsSourceUse as $content)
                    @php
                        $key                = $content->ordering;
                        /* lấy content theo ordering */
                        $xhtmlContent       = '';
                        foreach($contentsByLanguageUse as $c){
                            if($c->ordering==$key) {
                                $xhtmlContent = $c->content;
                                break;
                            }
                        }
                    @endphp
                    <div class="pageAdminWithRightSidebar_main_content_item width100">
                        <div class="card">
                            <div class="card-body">
                                @include('admin.form.formContent', [
                                    'prompt'            => $prompt,
                                    'content'           => $xhtmlContent, 
                                    'flagCopySource'    => !empty($itemSourceToCopy) ? true : false,
                                    'idBox'             => 'content_'.$key,
                                    'idContent'         => $content->id ?? 0, /* truyền id của content dùng làm ngôn ngữ dịch (để dịch) */
                                    'ordering'          => $key,
                                ]) 
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    @endif
@endforeach