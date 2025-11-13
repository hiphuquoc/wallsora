@extends('layouts.wallpaper')
@push('headCustom')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/cart-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/cart-first-view.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(asset('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@section('content')

    <div class="articleBox distanceBetweenBox">

        <div class="pageCartBox distanceBetweenSubbox">
            <!-- breadcrumb -->
            @include('wallpaper.template.breadcrumb')
            <!-- tiêu đề -->
            <h1 class="titlePage">{{ config('data_language_1.'.$language.'.product_list') }} (<span id="js_updateCart_count" class="highLight">{{ $detailCart['count'] ?? 0 }}</span>)</h1>
            <form id="formPaymentMethod" action="{{ route('main.paymentCart') }}" method="post" style="width:100%;">
                @csrf
                <!-- tạo language phụ trong form để gửi đi -->
                <input type="hidden" id="language_sub" name="language_sub" value="{{ $language }}" />
                <!-- layout -->
                <div class="layoutPageCart">
                    <div id="js_checkEmptyCart_idWrite" class="layoutPageCart_left distanceBetweenBox">

                        @if(!empty($products)&&$products->isNotEmpty())
                            <!-- danh sách sản phẩm -->
                            <div class="layoutPageCart_left_item">
                                <div class="cartSectionBox">
                                    <div class="cartSectionBox_body">
                                        <div class="cartProductBox_head">
                                            <div>{{ config('data_language_1.'.$language.'.product') }}</div>
                                            <div>{{ config('data_language_1.'.$language.'.unit_price') }}</div>
                                        </div>
                                        <div class="cartProductBox_body">
                                            @foreach($products as $product)
                                                <!-- trường hợp trọn bộ -->
                                                @if(count($product->cart['product_price_id'])>=$product->prices->count())
                                                    @php
                                                        $arrayProductPrice  = $product->cart['product_price_id'];
                                                        $keyId              = !empty($product->id) ? $product->id.implode('-', $product->cart['product_price_id']) : null;
                                                    @endphp
                                                    <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartProductBox_body_item">
                                                        @include('wallpaper.cart.cartRow', compact('product', 'arrayProductPrice','language'))
                                                    </div>
                                                @else 
                                                    @foreach($product->cart['product_price_id'] as $p)
                                                        @php
                                                            $arrayProductPrice  = [$p];
                                                            $keyId              = !empty($product->id) ? $product->id.$p : null;
                                                        @endphp
                                                        <div id="{{ 'js_updateCart_idWrite_'.$keyId }}" class="cartProductBox_body_item">
                                                            @include('wallpaper.cart.cartRow', compact('product', 'arrayProductPrice', 'language'))
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- form email và phương thức thanh toán -->
                            @include('wallpaper.cart.formPayment', [
                                'language'  => $language,
                                'action'    => 'chooseOptionPayment'
                            ])
                        @else 
                            @include('wallpaper.cart.emptyCart', compact('language'))
                        @endif

                    </div>
                    <div class="layoutPageCart_right">
                        @if(!empty($products)&&$products->isNotEmpty())
                        <div id="js_scrollMenu" class="cartSectionBox">
                            <div id="js_loadTotalCart" class="cartSectionBox_body">
                                @include('wallpaper.cart.total', [
                                    'intoMoney'     => $detailCart['into_money'],
                                    'total'         => $detailCart['total'],
                                    'language'      => $language,
                                    'taxNumber'     => 0
                                ])
                            </div>
                            {{-- <div class="cartSectionBox_notice">
                                Dùng mã giảm giá của {{ config('main_'.env('APP_NAME').'.company_name') }} ở bước sau
                            </div> --}}
                            <div class="cartSectionBox_button">
                                <div class="button" onClick="submitFormPayment('formPaymentMethod');">{{ config('data_language_1.'.$language.'.payment') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('modal')

@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        let clicked = false;
        function submitFormPayment(idForm){
            event.preventDefault();
            const toggleText = @json(config('data_language_3.'.$language.'.the_system_is_redirecting_you_to_the_checkout_page'));
            toggleFullLoading(toggleText);
            if (!clicked) {
                clicked = true;
                
                const error     = validateForm(idForm);
                if(error==''){
                    $('#'+idForm).submit();
                }else {
                    /* thêm class thông báo lỗi cho label của input */
                    for(let i = 0;i<error.length;++i){
                        const idInput = $('#'+idForm).find('[name='+error[i]+']').attr('id');
                        if(idInput!=''){
                            const elementLabel = $('#'+idForm).find('[for='+idInput+']');
                            elementLabel.addClass('error');
                        }
                    }
                    /* scroll đến thông báo lỗi đầu tiên */
                    $('[class*=error]').each(function(){
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 90
                        }, 300);
                        // $(window).scrollTop(parseInt($(this).offset().top - 90));
                        return false;
                    });
                }

                setTimeout(() => {
                    clicked = false;
                    toggleFullLoading();
                }, 1500);
            }
        }

    </script>
@endpush