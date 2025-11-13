@if(!empty($itemSeo->link_canonical))
        <a href="{{ URL::current().'?id='.$item->id.'&language='.$language.'&id_seo_source='.$itemSeo->link_canonical }}" class="actionBox_item maxLine_1">
            <i class="fa-solid fa-file-import"></i>Copy từ trang Gốc
        </a>
    @endif    