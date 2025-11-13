<div class="distanceBetweenSubbox">
    <!-- breadcrumb -->
    @include('wallpaper.template.breadcrumb')

    <!-- Product Detail Box -->
    <div class="pageProductDetailBox">
        <div class="pageProductDetailBox_left distanceBetweenBox">
            <!-- Gallery Desktop -->
            @include('wallpaper.product.gallery', ['prices' => $item->prices])

            <!-- bảng thông tin Chi tiết từng hình nền -->
            <div id="detailPerWallpaper" class="infoImageSourceTable">
                <div class="infoImageSourceTable_title">
                    <h2>{{ config('data_language_1.'.$language.'.wallpaper_details') }}</h2>
                </div>
                <div class="infoImageSourceTable_box">
                    <table>
                        <thead>
                            <tr>
                                <td>{{ config('data_language_1.'.$language.'.table_name') }}</td>
                                <td>{{ config('data_language_1.'.$language.'.table_resolution') }}</td>
                                <td>{{ config('data_language_1.'.$language.'.table_file_size') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->prices as $price)
                                @foreach($price->wallpapers as $wallpaper)
                                    <tr>
                                        <td>{{ config('data_language_1.'.$language.'.image') }} {{ $price->code_name ?? '-' }}</td>
                                        <td>{{ $wallpaper->infoWallpaper->width_source ?? '-' }}x{{ $wallpaper->infoWallpaper->height_source ?? '-' }} px</td>
                                        <td>{{ !empty($wallpaper->infoWallpaper->file_size_source) ? number_format($wallpaper->infoWallpaper->file_size_source/1024/1024, 1) : '-' }} mb</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Nội dung - một thẻ div bao bọc để tránh bị display: flex; gap... -->
            @php
                $xhtmlContent = '';
                foreach($itemSeo->contents as $content) $xhtmlContent .= $content->content;
            @endphp
            <div class="contentBox">
                {!! $xhtmlContent !!}
            </div>
            <!-- câu hỏi thường gặp -->
            @if(!empty($dataFaq))
                <div class="questionAnswerBox">
                    <div class="questionAnswerBox_title">
                        <h2>{{ config('data_language_2.'.$language.'.question_and_answer') }}</h2>
                    </div>
                    <div  class="questionAnswerBox_box">
                        @foreach($dataFaq as $faq)
                            <div class="questionAnswerBox_box_item" onclick="showHideAnswer(this);">
                                <div class="questionAnswerBox_box_item_question">{!! $faq['question'] !!}</div>
                                <div class="questionAnswerBox_box_item_answer">{!! $faq['answer'] !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="pageProductDetailBox_right">
            <div class="productDetailBox">
                <div class="productDetailBox_detail distanceBetweenSubbox">
                    <!-- id hidden -->
                    <input type="hidden" id="product_info_id" name="product_info_id" value="{{ $item->id ?? null }}" />
                    <!-- tiêu đề -->
                    <h1 class="titlePage customTitlePage">{{ $itemSeo->title }}</h1>
                    <!-- yêu thích -->
                    <div class="soldedBox">
                        @php
                            $countHeart = 0;
                            foreach($item->prices as $price){
                                foreach($price->wallpapers as $wallpaper) {
                                    $countHeart += $wallpaper->infoWallpaper->heart;
                                }
                            }
                        @endphp
                        @if($countHeart>0)
                            <div class="soldedBox_item heart">
                                <svg><use xlink:href="#icon_heart"></use></svg>
                                <div>{{ number_format($countHeart) }}</div>
                            </div>
                        @endif
                        @if($item->sold>0)
                            <div class="soldedBox_item download">
                                <svg><use xlink:href="#icon_download"></use></svg>
                                <div>{{ $item->sold }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="distanceBetweenBox">
                        <!-- mô tả sản phẩm -->
                        <div id="js_viewMoreContent_content" class="productDetailBox_detail_description">
                            {!! config('data_language_2.'.$language.'.product_description') !!}
                        </div>

                        <!-- option -->
                        <div class="productDetailBox_detail_option hide-991">
                            @include('wallpaper.product.optionBox', compact('item'))
                        </div>
                        <!-- giá -->
                        <div id="js_loadProductPrice" class="productDetailBox_detail_price hide-991">
                            <!-- load ajax (loadProductPrice) nên truyền collection rỗng -->
                            @include('wallpaper.product.priceBox', [
                                'item'      => new \Illuminate\Database\Eloquent\Collection,
                                'prices'    => new \Illuminate\Database\Eloquent\Collection,
                            ])
                        </div>

                        <!-- button đặt hàng -->
                        <div class="productDetailBox_detail_checkout">
                            <div class="productDetailBox_detail_checkout_button">
                                <div class="show-991">
                                    <!-- load ajax (loadProductPrice) -->
                                    <div id="js_loadProductPrice_priceAllMobile" class="priceMobile">--</div>
                                </div>
                                @php
                                    $buttonNameCart     = config('data_language_1.'.$language.'.add_to_cart');
                                    $altCart            = \App\Helpers\Charactor::concatenateWords([$buttonNameCart, $itemSeo->title], $language);
                                    $buttonNamePayment  = config('data_language_1.'.$language.'.buy_now');
                                    $altPayment         = \App\Helpers\Charactor::concatenateWords([$buttonNamePayment, $itemSeo->title], $language);
                                    /* chuyển array price sang key all */
                                    $keyPriceAll        = [];
                                    foreach($item->prices as $price) $keyPriceAll[]  = $price->id;
                                    $keyPriceAll        = implode('-', $keyPriceAll);
                                @endphp
                                <button id="js_addToCart_button" type="button" class="button secondary" onClick="addToCart('{{ $item->id }}', '{{ $keyPriceAll }}', 'all');" aria-label="{{ $altCart }}">
                                    <svg><use xlink:href="#icon_cart"></use></svg>
                                    <div class="maxLine_1">{{ $buttonNameCart }}</div>
                                </button>
                                <button type="button" class="button buyNow" onClick="openCloseModal('modalPaymentMethod'); calculaterRightPrice('js_calculaterRightPrice', {{ $item->id ?? 0 }});" aria-label="{{ $altPayment }}">
                                    <svg><use xlink:href="#icon_money_bill_wave"></use></svg>
                                    <div class="maxLine_1">{{ $buttonNamePayment }}</div>
                                </button>
                            </div>
                        </div>
                        <!-- giải thích thanh toán và nhận ảnh => desktop -->
                        <div class="hide-991">
                            {!! config('data_language_1.'.$language.'.product_guide_after_payment') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scriptCustom')
    <script type="text/javascript">
        function showHideAnswer(element) {
            let question    = element.querySelector('.questionAnswerBox_box_item_question');
            let answer = element.querySelector('.questionAnswerBox_box_item_answer');
            question.classList.toggle('show');
            answer.classList.toggle('show');
        }
    </script>
@endpush