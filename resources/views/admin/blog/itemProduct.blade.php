@if(!empty($product))
    @php
    $title = $product->seo->title ?? '';
    @endphp
    <div class="productWithWallpapers_item">
    <div class="productWithWallpapers_item_title">
        <div class="maxLine_1">{{ $title }}</div>
        <div class="productWithWallpapers_item_title_into" onclick="chooseProduct('js_chooseProduct_{{ $product->id }}');">
            <i class="fa-solid fa-arrow-right-long"></i>
        </div>
    </div>
    <div id="js_chooseProduct_{{ $product->id }}" data-product_info_id="{{ $product->id }}" class="productWithWallpapers_item_image">
        @foreach($product->prices as $price)
            @if(!empty($price->wallpapers)&&$price->wallpapers->isNotEmpty())
                @foreach($price->wallpapers as $wallpaper)
                    @if(!empty($wallpaper->infoWallpaper->file_cloud_wallpaper))
                        @php
                            $srcImage = \App\Helpers\Image::getUrlImageSmallByUrlImage($wallpaper->infoWallpaper->file_cloud_wallpaper);
                        @endphp
                        <div id="js_loadPrice_item_{{ $wallpaper->id ?? 0 }}" data-wallpaper_info_id="{{ $wallpaper->id }}" onclick="selectedWallpaper(this);">
                            <img alt="{{ $title }}" title="{{ $title }}" src="{{ $srcImage }}" />
                        </div>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>
</div>
@endif