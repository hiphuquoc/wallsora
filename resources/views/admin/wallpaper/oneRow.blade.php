<div class="wallpaperBox_item_source">
    <img data-google-cloud="{{ $item->file_cloud_source }}" data-size="300" />
    <div class="wallpaperBox_item_source_info">
        <div>{{ $item->width_source }}x{{ $item->height_source }} px - {{ round($item->file_size_source/(1024*1024), 1) }} Mb</div>
    </div>
</div>
<div class="wallpaperBox_item_wallpaper">
    <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($item->file_cloud_wallpaper) }}" />
    <div class="wallpaperBox_item_wallpaper_info">
        <div>{{ $item->width_wallpaper }}x{{ $item->height_wallpaper }} px - {{ round($item->file_size_wallpaper/(1024*1024), 2) }} Mb</div>
    </div>
</div>
<div class="wallpaperBox_item_info">
    <div>{{ $item->name }}</div>
</div>
<div class="wallpaperBox_item_action">
    {{-- <!-- copy đường dẫn -->
    <div id="js_copyClipboard_anh-dep-tour-du-lich-tho-chu-11-750x460-type-manager-upload" data-bs-placement="top" data-bs-original-title="Đã copy ảnh!"><i class="fa-regular fa-folder-open"></i></div> --}}
    {{-- <!-- thay ảnh -->
    <div data-bs-toggle="modal" data-bs-target="#modalImage" onclick="loadModal('changeImage', 'anh-dep-tour-du-lich-tho-chu-11-750x460-type-manager-upload.webp');">
        <i class="fa-solid fa-arrow-right-arrow-left"></i>
        <div>Thay ảnh</div>
    </div> --}}
    <!-- thay tên ảnh -->
    <div data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#modalFormWallpaper" onclick="loadModalUploadAndEdit({{ $item->id }});">
        <i class="fa-solid fa-pen-to-square"></i>
        <div>Chỉnh sửa</div>
    </div>
    <!-- xóa ảnh -->
    <div class="remove" onclick="deleteWallpaperAndSource('js_deleteWallpaperAndSource_{{ $item->id }}', {{ $item->id }});">
        <i class="fa-solid fa-trash-can"></i>
        <div>Xóa ảnh</div>
    </div>
</div>