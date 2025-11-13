@if(!empty($wallpaper->infoWallpaper))
    <div class="wallpaperSourceGrid_item">
        <div class="wallpaperSourceGrid_item_image">
            <img class="lazyload" src="{{ \App\Helpers\Image::getUrlImageCloud($wallpaper->infoWallpaper->file_cloud_source) }}" loading="lazy" />
        </div>
        <a href="{{ route('ajax.downloadImgFreeWallpaper', ['file_cloud' => $wallpaper->infoWallpaper->file_cloud_source]) }}" class="wallpaperSourceGrid_item_action" download>
            <svg><use xlink:href="#icon_download"></use></svg>
            <div>{{ config('data_language_1.'.$language.'.click_to_download') }}</div>
        </a>
        {{-- <div class="wallpaperSourceGrid_item_background"></div> --}}
    </div>
@endif