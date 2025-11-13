<!-- nút chỉ hiển thị khi có trang chưa tạo -->
@php
    /* đếm ngôn ngữ mặc định */
    $countLanguageDefault           = count(config('language'));
    /* arary ngôn ngữ đã có seo */
    $countLanguageHas               = 0;
    foreach($item->seos as $seo) if(!empty($seo->infoSeo->language)) $countLanguageHas += 1;
    $flagShowButtonTranslatePage    = $countLanguageHas < $countLanguageDefault ? true : false;
@endphp
@if($flagShowButtonTranslatePage)
    <div class="actionBox_item maxLine_1" onclick="createJobTranslateAndCreatePageAjax('{{ $item->seo->slug ?? '' }}');">
        <i class="fa-solid fa-plus"></i>Tạo trang ngôn ngữ còn thiếu (chạy ngầm)
    </div>
@endif