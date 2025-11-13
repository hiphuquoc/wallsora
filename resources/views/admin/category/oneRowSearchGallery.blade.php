
@if(!empty($freeWallpaper->infoFreewallpaper))

    <div class="searchViewBefore_selectbox_item {{ $selected }}" style="width:33.33%;padding:0.75rem 0.5rem;">
        <div class="searchViewBefore_selectbox_item_imageFull" data-free-wallpaper-id="{{ $freeWallpaper->free_wallpaper_info_id ?? 0 }}" data-category-id="{{ $freeWallpaper->category_info_id ?? 0 }}" onclick="chooseFreeWallpaperForCategory(this);" style="aspect-ratio:1/2;">
            <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($freeWallpaper->infoFreewallpaper->file_cloud) }}" />
            <div class="searchViewBefore_selectbox_item_imageFull_message">
                <div><i class="fa-solid fa-check"></i> Đã chọn</div>
            </div>
        </div>
    </div>

@endif