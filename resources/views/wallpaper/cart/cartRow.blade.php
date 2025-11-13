@php
    $idProduct              = $product->id;
    $keyId                  = $product->id.implode('-', $arrayProductPrice);
    // $eventUpdateCart        = "updateCart('js_updateCart_idWrite_$keyId', 'js_updateCart_total', 'js_updateCart_count', 'js_addToCart_quantity_".$keyId."', 'cartMain')";
    $eventRemoveProductCart = "removeProductCart($idProduct, '".json_encode($arrayProductPrice)."', 'js_updateCart_idWrite_".$keyId."', 'js_updateCart_total', 'js_updateCart_count')";
    $url                    = null;
    $title                  = config('data_language_1.'.$language.'.undefined');
    foreach($product->seos as $seo){
        if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
            $title      = $seo->infoSeo->title;
            $url        = $seo->infoSeo->slug_full;
            break;
        }
    }
    $cartToView             = \App\Http\Controllers\CartController::convertInfoCartToView($product, $arrayProductPrice, $language);
    $xhtmlPrice             = \App\Helpers\Number::getFormatPriceByLanguage($cartToView['price'], $language);
    /* ảnh */
    $image                  = \App\Helpers\Image::getUrlImageSmallByUrlImage($cartToView['image']);
@endphp

<div class="cartProductBox_body_item_info">
    <a href="/{{ $url }}" class="cartProductBox_body_item_info_image">
        <img src="{{ $image }}" alt="{{ $title }}" title="{{ $title }}" />
    </a>
    <div class="cartProductBox_body_item_info_content">
        <a href="/{{ $url }}" class="cartProductBox_body_item_info_content_title maxLine_2">
            {{ $title }}
        </a>
        <div class="cartProductBox_body_item_info_content_option"> 
            {{ $cartToView['option_name'] }}
        </div>
    </div>
</div>
<div class="cartProductBox_body_item_price">
    <div class="cartProductBox_body_item_price_now">{!! $xhtmlPrice !!}</div>
    {{-- <div class="cartProductBox_body_item_price_old">
        <div class="cartProductBox_body_item_price_old_percent">
            -{{ 20 }}%
        </div>
        <div class="cartProductBox_body_item_price_old_number">
            {!! 123 !!}
        </div>
    </div> --}}
    <div class="cartProductBox_body_item_price_icon" onClick="{{ $eventRemoveProductCart }}">
        <svg><use xlink:href="#icon_trash"></use></svg>
    </div>
</div>