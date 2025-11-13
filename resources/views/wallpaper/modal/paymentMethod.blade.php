<div id="modalPaymentMethod" class="modalBox">
    <!-- nền modal -->
    <div class="modalBox_bg"></div>
    <!-- body modal -->
    <div class="modalBox_box">
        <!-- nút thoát -->
        <div class="modalBox_box_close" onClick="openCloseModal('modalPaymentMethod');">
            <svg><use xlink:href="#icon_close"></use></svg>
        </div>

        <div class="modalBox_box_title">{{ config('data_language_3.'.$language.'.you_are_paying_for_the_product') }}</div>

        <form id="formModalPaymentMethod" method="get">
            <div class="modalBox_box_body">

                <!-- item ===== thông tin sản phẩm -->
                <div class="modalBox_box_body_item">
                    <div class="cartMessage_box_body" style="margin-bottom:0 !important;">
                        @php
                            $titleProduct   = $itemSeo->title ?? '-';
                            $option         = config('data_language_1.'.$language.'.full_set');
                            $imageSmall     = config('image.default');
                            if(!empty($item->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper)){
                                $imageSmall = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper);
                            }
                        @endphp
                        <div class="cartMessage_box_body_image">
                            <img class="lazyload" loading="lazy" src="{{ $imageSmall }}" alt="{{ $titleProduct }}" title="{{ $titleProduct }}">
                        </div>
                        <div class="cartMessage_box_body_content">
                            <div class="cartMessage_box_body_content_title maxLine_2">{{ $titleProduct }}</div>
                            <div class="cartMessage_box_body_content_price">
                                <div id="js_calculaterRightPrice">{{ config('data_language_3.'.$language.'.loading') }}</div>
                                <span> - {{ $option }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- item ===== email -->
                <div class="modalBox_box_body_item">
                    <div class="modalBox_box_body_item_subtitle">{{ config('data_language_1.'.$language.'.step_1_buy_now') }}</div>
                    <div class="modalBox_box_body_item_input">
                        <div class="inputWithLabelInside">  
                            <label for="email">{{ config('data_language_1.'.$language.'.email') }}</label>
                            <input type="text" id="email" name="email" onkeyup="validateWhenType(this, 'email')" value="{{ Auth::user()->email ?? null }}" />
                        </div>
                        <div class="modalBox_box_body_item_input_note">
                            <svg><use xlink:href="#icon_snowflake"></use></svg>
                            {!! config('data_language_1.'.$language.'.email_prompt') !!}
                        </div>
                    </div>
                </div>

                <!-- item ===== phương thức thanh toán -->
                <div class="modalBox_box_body_item">
                    <div class="modalBox_box_body_item_subtitle">{{ config('data_language_1.'.$language.'.step_2_buy_now') }}</div>
                    <div class="modalBox_box_body_item_input">
                        <div class="paymentMethodBox">
                            @include('wallpaper.template.paymentMethod', [
                                'language'  => $language,
                                'action'    => 'paymentNow',
                            ])
                        </div>
                    </div>
                </div>
                
            </div>
        </form>
        
    </div>
</div>
@push('scriptCustom')
    {{-- <script src="{{ asset('sources/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-select2.min.js') }}"></script> --}}
    <script type="text/javascript">
        function calculaterRightPrice(idWrite, idProduct){ /* do cache html nên truyền product_info_id */
            const language  = $('#language').val();
            const queryParams = new URLSearchParams({
                product_info_id : idProduct,
                language,
            }).toString();

            fetch("{{ route('ajax.calculaterRightPrice') }}?" + queryParams, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                $('#'+idWrite).html(data.content);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
        /* hàm này phải để ngoài vì trùng cart -> index const clicked */
        let clicked = false;
        function paymentNow(element, idMethod) {
            const toggleText = @json(config('data_language_3.'.$language.'.the_system_is_redirecting_you_to_the_checkout_page'));
            toggleFullLoading(toggleText);
            if (!clicked) {
                clicked = true;
                const email = $('#email').val();
                const idProduct = $('#product_info_id').val();
                const language_sub = $('#language').val();
                let idPrice = 0;
                $(document).find('[data-product_price_id]').each(function() {
                    if ($(this).hasClass('selected')) {
                        idPrice = $(this).attr('data-product_price_id');
                        return false;
                    }
                });
                if (idPrice != 0 && idProduct != '' && idMethod != '') {
                    const queryParams = new URLSearchParams({
                        product_info_id: idProduct,
                        product_price_id: idPrice,
                        payment_method_info_id: idMethod,
                        email,
                        language_sub, /* đặt tên để đồng bộ với tính năng paymentCart */
                    }).toString();

                    fetch("{{ route('main.paymentNow') }}?" + queryParams, {
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
                        return response.json();
                    })
                    .then(data => {
                        window.location.href = data.url;
                        setTimeout(() => {
                            toggleFullLoading();
                        }, 1500);
                    })
                    .catch(error => {
                        console.error("Fetch request failed:", error);
                    });

                    // Reset the click status after a certain amount of time
                    setTimeout(() => {
                        clicked = false;
                    }, 1500);
                }
            }
        }
    </script>
@endpush