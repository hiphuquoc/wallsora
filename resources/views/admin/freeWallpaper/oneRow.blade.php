<div class="freeWallpaperBox_item_wallpaper">
    <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($item->file_cloud) }}" />
</div>
<div class="freeWallpaperBox_item_info">
    <div>{{ $item->name }}</div>
    <div>{{ $item->width }}x{{ $item->height }} px - {{ round($item->file_size/(1024*1024), 1) }} Mb</div>
</div>
<div class="freeWallpaperBox_item_action">
    <!-- thay tên ảnh -->
    <div data-bs-toggle="modal" data-bs-target="#modalFormWallpaper" onclick="loadModalUploadAndEdit({{ $item->id }});">
        <i class="fa-solid fa-pen-to-square"></i>
        <div>Chỉnh sửa</div>
    </div>
    <!-- xóa ảnh -->
    <div class="remove" onclick="deleteWallpaper('js_deleteWallpaper_{{ $item->id }}', {{ $item->id }});">
        <i class="fa-solid fa-trash-can"></i>
        <div>Xóa ảnh</div>
    </div>
</div>