<div class="formBox_full_item">
    <div data-bs-toggle="modal" data-bs-target="#formModal_{{ $idProductPrice }}" style="color:#7367f0;cursor:pointer;">
        Wallpapers đã chọn <span class="badge badge-center bg-primary">{{ !empty($wallpapers)&&$wallpapers->isNotEmpty() ? $wallpapers->count() : 0}}</span>
    </div>
    <div class="searchViewBefore">
        <div class="searchViewBefore_selectbox">
            @if(!empty($wallpapers)&&$wallpapers->isNotEmpty()&&!empty($idProductPrice))
                @foreach($wallpapers as $wallpaper)
                    <div id="js_deleteWallpaperToProductPrice_{{ $idProductPrice.$wallpaper->id }}" class="searchViewBefore_selectbox_item" data-wallpaper-id="{{ $wallpaper->id }}" data-product-price-id="{{ $idProductPrice }}">
                        <div class="searchViewBefore_selectbox_item_image">
                            <img src="{{ \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->file_cloud_wallpaper) }}" />
                        </div>
                        <div class="searchViewBefore_selectbox_item_content">
                            <div class="searchViewBefore_selectbox_item_content_title maxLine_2">
                                {{ $wallpaper->name }}
                            </div>
                            <div>
                                <div>{{ $wallpaper->file_name }}</div> 
                                @php
                                    $linkSource = \App\Helpers\Image::getUrlImageCloud($wallpaper->file_cloud_source);
                                @endphp
                                <div>Source: <a href="{{ $linkSource }}" target="_blank">{{ $linkSource }}</a></div>
                            </div>
                        </div>
                        {{-- <div class="searchViewBefore_selectbox_item_message">
                            <div><i class="fa-solid fa-check"></i> Đã chọn</div>
                        </div> --}}
                        <div class="searchViewBefore_selectbox_item_cancel" onClick="deleteWallpaperToProductPrice('js_deleteWallpaperToProductPrice_{{ $idProductPrice.$wallpaper->id }}', {{ $idProductPrice }}, {{ $wallpaper->id }});">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>