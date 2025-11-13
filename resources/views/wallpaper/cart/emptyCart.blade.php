<div class="emptyCartBox">
    <svg><use xlink:href="#icon_blank_cart"></use></svg>
    <div class="emptyCart_text">{{ config('data_language_1.'.$language.'.your_cart_is_empty') }}</div> 
    <a href="/{{ config('language.'.$language.'.key') }}" class="emptyCartBox_button button" aria-label="{{ config('data_language_1.'.$language.'.continue_shopping') }}">{{ config('data_language_1.'.$language.'.continue_shopping') }}</a>
</div>