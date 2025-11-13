<!-- TOC CONTENT -->
<div class="tocContentMain">
    <div class="tocContentMain_title">
        <svg><use xlink:href="#icon_list_menu"></use></svg>
        <span class="tocContentMain_title_text">{{ config('data_language_1.'.$language.'.table_of_contents') }}</span>
    </div>
    <div class="tocContentMain_list customScrollBar-y" style="display: block;">
        @foreach($data as $item)
            <a href="#{{ $item['id'] ?? null }}" class="tocContentMain_list_item">
                {{ $loop->index+1 }}. {!! $item['title'] ?? null !!}
            </a>
        @endforeach
    </div>
    <div class="tocContentMain_close"></div>
    </div>
<!-- TOC CONTENT FIXED ICON -->
<div class="tocFixedIcon"><div><svg><use xlink:href="#icon_list_menu"></use></svg></div></div>
<!-- TOC CONTENT FIXED -->
<div class="tocContentMain tocFixed">
    <div class="tocContentMain_title">
        <svg><use xlink:href="#icon_list_menu"></use></svg>
        <span class="tocContentMain_title_text">{{ config('data_language_1.'.$language.'.table_of_contents') }}</span>
    </div>
    <div class="tocContentMain_list customScrollBar-y">
        @foreach($data as $item)
            <a href="#{{ $item['id'] ?? null }}" class="tocContentMain_list_item">
                {{ $loop->index+1 }}. {!! $item['title'] ?? null !!}
            </a>
        @endforeach
    </div>
    <div class="tocContentMain_close"></div>
</div>