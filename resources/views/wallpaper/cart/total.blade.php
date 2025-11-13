<!-- thành tiền -->
<div class="cartSectionBox_body_item">
    <div>{{ config('data_language_1.'.$language.'.total_price') }}</div> 
    <div>{!! \App\Helpers\Number::getFormatPriceByLanguage($intoMoney, $language) !!}</div>
</div>
<!-- phí thanh toán (nếu có) -->
@if(!empty($taxNumber)&&$taxNumber>0)
    <div class="cartSectionBox_body_item" style="padding-top:0;border-top:0;">
        <div>{{ config('data_language_1.'.$language.'.payment_fee') }}</div> 
        <div>{!! \App\Helpers\Number::getFormatPriceByLanguage($taxNumber, $language) !!}</div>
    </div>
@endif

<!-- tổng cộng -->
<div class="cartSectionBox_body_item total">
    {{ config('data_language_1.'.$language.'.total') }}
    <div class="total_number">
        <span id="js_updateCart_total">
            {!! \App\Helpers\Number::getFormatPriceByLanguage($total, $language) !!}
        </span>
    </div>
</div>