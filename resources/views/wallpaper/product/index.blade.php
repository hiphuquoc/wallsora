@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/product-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/product-first-view.scss']['file'];
        @endphp
        <style type="text/css">
            {!! file_get_contents(asset('build/' . $cssFirstView)) !!}
        </style>
    @endif
@endpush
@push('headCustom')
<!-- ===== START:: SCHEMA ===== -->
    <!-- STRAT:: Product Schema -->
    @php
        $highPrice          = \App\Helpers\Number::getPriceOriginByCountry($item->price);
        $lowPrice           = $highPrice;
        foreach($item->prices as $price){
            $priceTmp       = \App\Helpers\Number::getPriceOriginByCountry($price->price);
            if($priceTmp<$lowPrice){
                $lowPrice   = $priceTmp;
                break;
            }
        }
    @endphp
    @include('wallpaper.schema.product', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Product Schema -->

    <!-- STRAT:: Title - Description - Social -->
    @include('wallpaper.schema.social', ['item' => $item, 'lowPrice' => $lowPrice, 'highPrice' => $highPrice])
    <!-- END:: Title - Description - Social -->

    <!-- STRAT:: Organization Schema -->
    @include('wallpaper.schema.organization')
    <!-- END:: Organization Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.article', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: ImageObject Schema -->
    @php
        $dataImages = new \Illuminate\Database\Eloquent\Collection;
        $dataImages->push($item);
    @endphp
    @include('wallpaper.schema.imageObject', ['data' => $dataImages])
    <!-- END:: ImageObject Schema -->

    <!-- STRAT:: Article Schema -->
    @include('wallpaper.schema.creativeworkseries', compact('item'))
    <!-- END:: Article Schema -->

    <!-- STRAT:: FAQ Schema -->
    @php
        $dataFaq    = config('data_question_product.'.$language);
    @endphp
    @include('wallpaper.schema.faqProduct', ['dataFaq' => $dataFaq])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <!-- share social -->
    @include('wallpaper.template.shareSocial')
    <!-- content -->
    <div class="distanceBetweenBox articleBox maxContent-1200">

        <!-- Gallery và Product detail -->
        @include('wallpaper.product.body')
        
        <!-- Related -->
        @if($total>0)
            <div class="relatedProductBox">
                <div class="relatedProductBox_title">
                    <h2>{!! config('data_language_1.'.$language.'.suggestions_for_you') !!}</h2>
                </div>
                <div class="relatedProductBox_box">
                    @include('wallpaper.template.wallpaperGridWithLoadMore', [
                        'loaded'            => 0,
                        'total'             => $total,
                        'idProduct'         => $item->id,
                    ])
                </div>
            </div>
        @endif
    </div>
@endsection
@push('modal')
    <!-- Message Add to Cart -->
    <div id="js_addToCart_idWrite">
        @include('wallpaper.cart.cartMessage', [
            'title'     => '',
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null,
            'language'  => $language
        ])
    </div>

    @include('wallpaper.modal.viewImageFull')

    @include('wallpaper.modal.paymentMethod')
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            loadProductPrice({{ $item->id }});
        });

        /* thay đổi option sản phẩm */
        function loadProductPrice(idProduct){
            // Tạo URL với tham số truy vấn
            const url = new URL('{{ route("ajax.loadProductPrice") }}');
            const language = $('#language').val();
            url.searchParams.append('product_info_id', idProduct);
            url.searchParams.append('language', language);
            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.content!='') $('#js_loadProductPrice').html(data.content);
                if(data.price_all_mobile!='') $('#js_loadProductPrice_priceAllMobile').html(data.price_all_mobile);
            })
            .catch(error => {
                console.error('Error fetching option product data:', error);
            });
        }

        /* thay đổi option sản phẩm */
        function setOptionProduct(element, idProduct, type){
            /* xử lý cho việc thay đổi button */
            $(element).parent().children().each(function(){
                $(this).removeClass('selected');
            });
            $(element).addClass('selected');
            /* xử lý cho việc hiển thị lại giá theo option */
            const idKey         = $(element).data('product_price_id');
            const elementPrice  = $('#'+idKey);
            elementPrice.parent().children().each(function(){
                $(this).removeClass('selected');
            });
            elementPrice.addClass('selected');
            /* set lại sự kiện button addToCart cho đúng option vừa chọn */
            $('#js_addToCart_button').attr("onclick", "addToCart('"+idProduct+"', '"+idKey+"', '"+type+"');");
        }
    </script>
@endpush