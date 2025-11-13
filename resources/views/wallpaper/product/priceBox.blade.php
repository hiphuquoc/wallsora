
@php
    /* giá gạch bỏ */
    $pMax               = !empty($item->price) ? $item->price : 0; 
    /* giá bán thật (có đơn vị tiền tệ) */
    $pOrigin            = \App\Helpers\Number::getPriceOriginByCountry($pMax);
    if(!empty($pOrigin)){
        $xhtmlPriceOrigin = \App\Helpers\Number::getFormatPriceByLanguage($pOrigin, $language);
    }else {
        /* hiển thị trước khi load ajax */
        $xhtmlPriceOrigin = '--';
    }
    /* giá gạch bỏ (có đơn vị tiền tệ) */
    $xhtmlPriceMax      = null;
    $xhtmlSaleOff       = null;
    if(!empty($pMax>$pOrigin)) {
        /* sale_off => ghi chú: đoạn này phải nằm ở trên trước khi pMax được dùng number_format */
        $saleOff        = \App\Helpers\Number::calculatorSaleOffByPriceMaxAndPriceOriginByCountry($pMax, $pOrigin);
        $xhtmlSaleOff   = '<div class="productDetailBox_detail_price_item_saleoff">- '.$saleOff.'%</div>';
        /* giá gạch bỏ (có đơn vị tiền tệ) */
        $pMax           = \App\Helpers\Number::getFormatPriceByLanguage($pMax, $language, false);
        $xhtmlPriceMax  = '<div class="productDetailBox_detail_price_item_old">'.$pMax.'</div>';
    }
    /* chuỗi json thay đổi price hiển thị chi chọn option */
    $tmp            = [];
    if(!empty($item->prices)) foreach($item->prices as $price) $tmp[] = $price->id;
    $idKey          = implode('-', $tmp);
@endphp
<!-- của trọn bộ -->
<div id="{{ $idKey }}" class="productDetailBox_detail_price_item selected">
    <div class="productDetailBox_detail_price_item_real">{!! $xhtmlPriceOrigin !!}</div>
    {!! $xhtmlPriceMax !!}
    {!! $xhtmlSaleOff !!}
</div>

@foreach($prices as $price)
    @php
        /* giá gạch bỏ */
        $pMax               = $price->price ?? 0; 
        /* giá bán thật (có đơn vị tiền tệ) */
        $pOrigin            = \App\Helpers\Number::getPriceOriginByCountry($pMax);
        $xhtmlPriceOrigin   = \App\Helpers\Number::getFormatPriceByLanguage($pOrigin, $language);
        /* giá gạch bỏ (có đơn vị tiền tệ) */
        $xhtmlPriceMax      = null;
        $xhtmlSaleOff       = null;
        if(!empty($pMax>$pOrigin)) {
            /* sale_off => ghi chú: đoạn này phải nằm ở trên trước khi pMax được dùng number_format */
            $saleOff        = \App\Helpers\Number::calculatorSaleOffByPriceMaxAndPriceOriginByCountry($pMax, $pOrigin);
            $xhtmlSaleOff   = '<div class="productDetailBox_detail_price_item_saleoff">- '.$saleOff.'%</div>';
            /* giá gạch bỏ (có đơn vị tiền tệ) */
            $pMax           = \App\Helpers\Number::getFormatPriceByLanguage($pMax, $language, false);
            $xhtmlPriceMax  = '<div class="productDetailBox_detail_price_item_old">'.$pMax.'</div>';
        }
    @endphp
    <!-- của từng giá -->
    <div id="{{ $price->id }}" class="productDetailBox_detail_price_item">
        <div class="productDetailBox_detail_price_item_real">{!! $xhtmlPriceOrigin !!}</div>
        {!! $xhtmlPriceMax !!}
        {!! $xhtmlSaleOff !!}
    </div>
@endforeach

