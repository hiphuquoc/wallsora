@if(!empty($product))
    @php
        $keyId              = !empty($product->id) ? $product->id.implode('-', $arrayProductPrice) : null;
        // $eventUpdateCart    = "updateCart('js_updateCart_idWrite_".$keyId."', 'js_updateCart_total', 'js_updateCart_count', 'js_addToCart_quantity_".$keyId."')";
        $url                = null;
        $title              = config('data_language_1.'.$language.'.undefined');
        foreach($product->seos as $seo){
            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                $title      = $seo->infoSeo->title;
                $url        = $seo->infoSeo->slug_full;
                break;
            }
        }
        $cartToView         = \App\Http\Controllers\CartController::convertInfoCartToView($product, $arrayProductPrice, $language);
        $xhtmlPrice             = \App\Helpers\Number::getFormatPriceByLanguage($cartToView['price'], $language);
        /* action */
        $eventRemoveProductCart = "removeProductCart($product->id, '".json_encode($arrayProductPrice)."', 'js_updateCart_idWrite_".$keyId."', 'js_updateCart_total', 'js_updateCart_count')";
        /* ảnh */
        $image                  = \App\Helpers\Image::getUrlImageSmallByUrlImage($cartToView['image']);
    @endphp
    <a href="/{{ $url }}" class="cartBox_list_item_image">
        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
    </a>
    <div class="cartBox_list_item_content">
        <a href="/{{ $url }}" class="cartBox_list_item_content_title maxLine_2">
            {{ $title }}
        </a>
        <div class="cartBox_list_item_content_price">
            {!! $xhtmlPrice !!} <span>- {!! $cartToView['option_name'] !!}</span>
        </div>
        <div class="cartBox_list_item_content_action" onclick="{{ $eventRemoveProductCart }}">
            <svg><use xlink:href="#icon_trash"></use></svg>
        </div>
    </div>
@endif