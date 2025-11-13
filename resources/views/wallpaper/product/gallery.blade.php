@if(!empty($prices)&&$prices->isNotEmpty())
    <!-- ảnh của all => đặt ẩn để addtocart -->
    <div style="display:none;">
        @php
            $imageAll = config('image.default');
            if(!empty($prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)){
                $imageAll = \App\Helpers\Image::getUrlImageCloud($prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper);
            }
        @endphp
        <img src="{{ $imageAll }}" alt="{{ $itemSeo->title }}" title="{{ $itemSeo->title }}" data-option="js_addToCart_option_all" />
    </div>
    <!-- ảnh của từng biến thể -->
    <div class="galleryProductBox">
        @php
            $i = 0;
        @endphp
        @foreach($prices as $price)
            @foreach($price->wallpapers as $wallpaper)
                @if(!empty($wallpaper->infoWallpaper))
                    @php
                        /* lấy ảnh mini */
                        $imageMini      = \App\Helpers\Image::getUrlImageMiniByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                        $imageSmall     = \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                        $imageLarge     = \App\Helpers\Image::getUrlImageLargeByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                    @endphp
                    <div class="galleryProductBox_item" onClick="toogleModalViewImageFull('{{ $i }}');">
                        {{-- @if($i<2)
                            <!-- thêm src và data-option để mô phỏng thẻ image dùng cho tính năng addToCart -->
                            <div class="galleryProductBox_item_backgroundImage" src="{{ $image }}" data-src="{{ $image }}" data-option="js_addToCart_option_{{ $price->id }}" onClick="toogleModalViewImageFull('{{ $i }}');" style="background:url('{{ $image }}') no-repeat center center / cover;"></div>
                        @else  --}}
                            <div class="zIndexHide">
                                <picture>
                                    <source media="(max-width: 767px)" srcset="{{ $imageSmall }}">
                                    <img class="lazyload" 
                                        src="{{ $imageMini }}" 
                                        data-src="{{ $imageLarge }}" 
                                        alt="{{ $itemSeo->title }}" 
                                        title="{{ $itemSeo->title }}" 
                                        data-option="js_addToCart_option_{{ $price->id }}" 
                                        loading="lazy"
                                    />
                                </picture>
                            </div>
                        {{-- @endif --}}
                        <div class="galleryProductBox_item_background"></div>
                        <div class="galleryProductBox_item_backgroundTopMore"></div>
                        <div class="galleryProductBox_item_backgroundSideMore"></div>
                    </div>
                    @php
                        ++$i;
                    @endphp
                @endif
            @endforeach
        @endforeach
    </div>
    
@endif