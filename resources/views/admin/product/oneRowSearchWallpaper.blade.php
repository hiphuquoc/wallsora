@if(!empty($wallpaper)&&!empty($idProductPrice))
    @php
        $selected = !empty($selected)&&$selected==true ? 'selected' : '';
    @endphp
    <div id="js_deleteWallpaperAndSource_{{ $wallpaper->id }}" class="searchViewBefore_selectbox_item {{ $selected }}" style="width:33.33%;padding:0.75rem 0.5rem;">
        <div class="searchViewBefore_selectbox_item_imageFull" data-wallpaper-id="{{ $wallpaper->id }}" data-product-price-id="{{ $idProductPrice }}" onclick="chooseWallpaperForProductPrice(this);">
            <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->file_cloud_wallpaper) }}" />
            <div class="searchViewBefore_selectbox_item_imageFull_message">
                <div><i class="fa-solid fa-check"></i> Đã chọn</div>
            </div>
        </div>
        
        <div class="searchViewBefore_selectbox_item_action">
            @if(!empty($wallpaper->priceUses)&&$wallpaper->priceUses->isNotEmpty())
                <div class="used"><i class="fa-solid fa-check"></i> <span>{{ $wallpaper->priceUses->count() }}</span></div>
            @endif
            <div class="remove" onclick="deleteWallpaperAndSource('js_deleteWallpaperAndSource_{{ $wallpaper->id }}', {{ $wallpaper->id }});"><i class="fa-solid fa-trash-can"></i></div>
        </div>
    </div>

@endif