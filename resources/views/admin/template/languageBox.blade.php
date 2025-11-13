<div class="languageBox">
    @foreach(config('language') as $lang)
        @php
            /* trang đang sửa có ngôn ngữ ? */
            $selected = null;
            if(!empty($language)&&$language==$lang['key']) $selected = 'selected';
            /* các trang đã tồn tại bảng ngôn ngữ này trong CSDL */
            $disable        = 'disable';
            $languageLink   = route($routeName, [
                "language"  => $lang['key'], 
                "id"        => $item->id ?? 0
            ]);
            if(!empty($item->seos)){
                foreach($item->seos as $s){
                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$lang['key']){
                        $disable = null;
                        break;
                    }
                }
            }
        @endphp
        <a href="{{ $languageLink }}" class="languageBox_item {{ $selected }} {{ $disable }}">
            <div class="maxLine_1">
                {{ $lang['key']}} - {{ $lang['name_by_language'] }}
            </div>
        </a>
    @endforeach
</div>