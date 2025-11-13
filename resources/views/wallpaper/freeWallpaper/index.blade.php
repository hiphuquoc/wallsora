@extends('layouts.wallpaper')
@push('cssFirstView')
    <!-- trường hợp là local thì dùng vite để chạy npm run dev lúc code -->
    @if(env('APP_ENV')=='local')
        @vite('resources/sources/main/freewallpaper-first-view.scss')
    @else
        @php
            $manifest           = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFirstView       = $manifest['resources/sources/main/freewallpaper-first-view.scss']['file'];
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
        $highPrice          = 0;
        $lowPrice           = 0;
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
    @include('wallpaper.schema.faq', ['data' => $item->faqs])
    <!-- END:: FAQ Schema -->
<!-- ===== END:: SCHEMA ===== -->
@endpush
@section('content')
    <!-- share social -->
    @include('wallpaper.template.shareSocial')
    <!-- content -->
    <div class="articleBox distanceBetweenBox">

        <div class="pagefreeWallpaper distanceBetweenSubbox">
            <!-- breadcrumb -->
            @include('wallpaper.template.breadcrumb')
            <!-- Gallery và Product detail -->
            @include('wallpaper.freeWallpaper.body')
        </div>
        
        <!-- Related -->
        @if($total>0)
            <div class="relatedProductBox">
                <div class="relatedProductBox_title">
                    <h2>{!! config('data_language_1.'.$language.'.suggestions_for_you') !!}</h2>
                </div>
                <div class="relatedProductBox_box">
                    @include('wallpaper.category.box', [
                        'total'             => $total,
                        'loaded'            => $loaded,
                        'arrayIdCategoyr'   => $arrayIdCategory,
                        'wallpapers'        => $related,
                        'language'          => $language,
                        'idNot'             => $idNot
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
            'title'     => $item->name,
            'option'    => null,
            'quantity'  => 0,
            'price'     => 0,
            'image'     => null,
            'language'  => $language
        ])
    </div>

    {{-- @include('wallpaper.modal.viewImageFull')

    @include('wallpaper.modal.paymentMethod') --}}
@endpush
@push('bottom')
    <!-- === START:: Zalo Ring === -->
    {{-- @include('main.snippets.zaloRing') --}}
    <!-- === END:: Zalo Ring === -->
@endpush
@push('scriptCustom')
    <script type="text/javascript">

        /* thả cảm xúc */
        function toogleHeartFeelingFreeWallpaper(idFreeWallpaper) {
            fetch("{{ route('ajax.toogleHeartFeelingFreeWallpaper') }}?free_wallpaper_info_id=" + idFreeWallpaper, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                if (data === "true") {
                    $('.freeWallpaperDetailBox .heart').addClass('selected');
                } else {
                    $('.freeWallpaperDetailBox .heart').removeClass('selected');
                }
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
    </script>
@endpush