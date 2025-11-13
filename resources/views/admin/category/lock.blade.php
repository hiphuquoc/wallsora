@php
    $flagPrevent = false;
    if(!empty($itemSeo->jobAutoTranslate)){
        foreach ($itemSeo->jobAutoTranslate as $jb) {
            if($jb->status==0) {
                $flagPrevent = true;
                break;
            }
        }
    }
    $display = $flagPrevent==true ? 'display: block;' : 'display: none;';
@endphp
<div id="lock" style="width:100%;height:100%;position:fixed;top:0;left:0;background:#fff;z-index:999;opacity:0.6;{!! $display !!}">
    <span style="background:#000;z-index:1000;position:relative;top:50%;left:50%;padding:0.5rem 1.5rem;color:#fff;border-radius:7px;">Trang đang tạm khóa để tự động dịch...</span>
</div>

