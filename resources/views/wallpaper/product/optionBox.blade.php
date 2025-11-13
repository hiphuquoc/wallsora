{{-- <div class="productDetailBox_detail_option_title">
    Tùy chọn 
</div> --}}
<div class="productDetailBox_detail_option_box">
    <div class="productDetailBox_detail_option_box_item" style="background:none;font-size:0.95rem;padding:0;margin:0 10px 0 0;">
        {{ config('data_language_1.'.$language.'.options') }}
    </div>
    <!-- của all -->
    @php
        $tmp                    = [];
        foreach($item->prices as $price) $tmp[] = $price->id;
        $stringProductPriceAll  = implode('-', $tmp);
    @endphp
    <div id="js_addToCart_option_all" class="productDetailBox_detail_option_box_item selected"
        data-product_price_id="{{ $stringProductPriceAll }}" 
        onClick="setOptionProduct(this, '{{ $item->id }}', 'all');">
            {{ config('data_language_1.'.$language.'.full_set') }}
    </div>  
    <!-- của từng ảnh con -->
    @foreach($item->prices as $option)
        <div id="js_addToCart_option_{{ $option->id }}" class="productDetailBox_detail_option_box_item"
            data-product_price_id="{{ $option->id }}" 
            onClick="setOptionProduct(this, '{{ $item->id }}', 'per');">
            {{ config('data_language_1.'.$language.'.image').' '.$loop->index+1 }}
        </div>  
    @endforeach
</div>