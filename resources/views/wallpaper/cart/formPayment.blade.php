<!-- form email -->
<div class="layoutPageCart_left_item">
    <div class="layoutPageCart_left_item_head">{{ config('data_language_1.'.$language.'.information') }}</div>
    <div class="layoutPageCart_left_item_body">
        <div class="inputWithLabelInside">
            <label for="email">{{ config('data_language_1.'.$language.'.email_receive') }}</label>
            <input type="text" id="email" name="email" value="{{ Auth::user()->email ?? null }}" onkeyup="validateWhenType(this, 'email')">
        </div>
        <div class="layoutPageCart_left_item_body_note">
            <svg><use xlink:href="#icon_snowflake"></use></svg>
            {!! config('data_language_1.'.$language.'.email_prompt') !!}
        </div>
    </div>
    {{-- <div class="layoutPageCart_left_item_footer">
        {{ config('data_language_1.'.$language.'.email_prompt') }}
    </div> --}}
</div>
<!-- form payment -->
<div class="layoutPageCart_left_item">
    <label class="layoutPageCart_left_item_head" for="payment_method_info_id">{{ config('data_language_1.'.$language.'.payment_method') }}</label>
    <input type="hidden" id="payment_method_info_id" name="payment_method_info_id" value="" required />
    <div class="layoutPageCart_left_item_body">

        <div class="paymentMethodBox twoColumn">
            @include('wallpaper.template.paymentMethod')
        </div>
        
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            validateWhenType($('#email'), 'email');
        });
    </script>
@endpush