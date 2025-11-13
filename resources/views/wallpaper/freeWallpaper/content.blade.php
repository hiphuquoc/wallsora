@if(!empty($itemSeo->contents))
    <div class="contentBox">
        @foreach($itemSeo->contents as $content)
            {!! $content->content !!}
        @endforeach
    </div>
@endif
