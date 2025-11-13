<div class="menuTopElement"><!-- giả lập menuTop để set chiều cao --></div> 
<div class="menuTop">

    <div class="menuTop_item">
        <div class="logoMain">
            <a href="/{{ config('language.'.$language.'.key') }}" class="logoMain_show" aria-label="{{ config('data_language_1.'.$language.'.home') }} {{ env('DOMAIN_NAME') }}">
                @if(Route::is('main.home'))
                    <h1 style="opacity:0;">{{ config('data_language_1.'.$language.'.home').' '.config('main_'.env('APP_NAME').'.company_name') }}</h1>
                @endif
            </a>
        </div>
    </div>
    <!-- search box -->
    @include('wallpaper.template.search')

    <div class="menuTop_item rightBox">
        @if(empty($item->type->code)||$item->type->code!='cart')
            <div id="js_viewSortCart_idWrite">
                @include('wallpaper.cart.cartSort', ['products' => null])
            </div>
        @endif
        <!-- button đăng nhập desktop -->
        <div id="js_checkLoginAndSetShow_button" class="hide-991" style="height:100%;display:none !important;">
            <!-- tải ajax checkLoginAndSetShow() -->
        </div>

        <!-- language -->
        <div class="languageBox">
            <input type="hidden" id="language" name="language" value="{{ $language ?? '' }}" />
            <div class="languageBox_show" onclick="closeLanguageBoxList('ja_closeLanguageBoxList');">
                <svg><use xlink:href="#icon_global"></use></svg>
                {!! strtoupper(config('language.'.$language.'.key')) !!}
            </div>
            @if(!empty($item->seos)&&$item->seos->isNotEmpty())
                <div id="ja_closeLanguageBoxList" class="languageBox_list">
                    <div class="languageBox_list_close" onclick="closeLanguageBoxList('ja_closeLanguageBoxList');">
                        <svg><use xlink:href="#icon_close"></use></svg>
                    </div>
                    <div class="languageBox_list_content">
                        @foreach(config('language') as $ld)
                            @php
                                // Lấy query string nếu có
                                $queryString = request()->getQueryString() ? '?' . request()->getQueryString() : '';
                                
                                // Tìm URL theo ngôn ngữ
                                $urlOfPageWithLanguage = '';
                                $flagHas = false;
                                foreach ($item->seos as $seo) {
                                    if (!empty($seo->infoSeo) && $seo->infoSeo->language === $ld['key']) {
                                        $urlOfPageWithLanguage = $seo->infoSeo->slug_full . $queryString;
                                        $flagHas = true;
                                        break;
                                    }
                                }

                                // Kiểm tra selected
                                $isSelected = !empty($seo->infoSeo->language) && $seo->infoSeo->language == $language;
                            @endphp

                            @if($flagHas)
                                @if($isSelected)
                                    <span class="languageBox_list_content_item maxLine_1 selected">{{ $ld['name_by_language'] }}</span>
                                @else
                                    <a href="{{ $urlOfPageWithLanguage }}" class="languageBox_list_content_item maxLine_1">{{ $ld['name_by_language'] }}</a>
                                @endif
                            @else
                                <div class="languageBox_list_content_item maxLine_1">{{ $ld['name_by_language'] }}</div>
                            @endif
                        @endforeach
                        
                    </div>
                </div>
            @endif
            <div id="ja_closeLanguageBoxList_background" class="languageBox_background"></div>
        </div>
        <!-- view mode -->
        @include('wallpaper.template.viewMode')
        
        <!-- icon menu mobile -->
        <div class="iconMenuMobile show-991" onClick="toggleMenuMobile('js_toggleMenuMobile');">
            <svg><use xlink:href="#icon_bars"></use></svg>
        </div>
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">
        /* đóng mở trang ngôn ngữ */
        function closeLanguageBoxList(idElemt, action = 'toggle'){
            /* trường hợp toogle */ 
            let displayE    = $('#' + idElemt).css('display');
            if(displayE=='none'){
                $('#' + idElemt).css('display', 'flex');
                $('#' + idElemt + '_background').css('display', 'flex');
                $('body').css('overflow', 'hidden');
            }else {
                $('#' + idElemt).css('display', 'none');
                $('#' + idElemt + '_background').css('display', 'none');
                $('body').css('overflow', 'unset');
            }
            /* trường hợp close */ 
            if(action=='close'){
                $('#' + idElemt).css('display', 'none');
                $('#' + idElemt + '_background').css('display', 'none');
                $('body').css('overflow', 'unset');
            }
            /* trường hợp open */ 
            if(action=='open'){
                $('#' + idElemt).css('display', 'flex');
                $('#' + idElemt + '_background').css('display', 'flex');
                $('body').css('overflow', 'hidden');
            }
        }
        // /* đóng mở thiết lập giao diện */
        // function closeLanguageBoxList(idElemt){
        //     let displayE    = $('#' + idElemt).css('display');
        //     if(displayE=='none'){
        //         $('#' + idElemt).css('display', 'flex');
        //         $('#' + idElemt + '_background').css('display', 'flex');
        //         $('body').css('overflow', 'hidden');
        //     }else {
        //         $('#' + idElemt).css('display', 'none');
        //         $('#' + idElemt + '_background').css('display', 'none');
        //         $('body').css('overflow', 'unset');
        //     }
        // }
        /* ===== đăng nhập google */
        function toggleModalCustomerLoginForm(idElement) {
            const element = $('#' + idElement);
            const displayE = element.css('display');

            if (displayE == 'none') {
                /* hiển thị modal */
                element.css('display', 'flex');
                $('body').css('overflow', 'hidden');
                // Kiểm tra xem script Google Sign-In đã được tải chưa
                if (!document.getElementById('google-signin-script')) {
                    var script = document.createElement('script');
                    script.src = 'https://accounts.google.com/gsi/client';
                    script.id = 'google-signin-script'; // Thêm id để kiểm soát nếu script đã tồn tại
                    document.head.appendChild(script);
                }
            } else {
                /* ẩn modal */
                element.css('display', 'none');
                $('body').css('overflow', 'unset');
            }
        }
        function submitFormLogin(idForm){
            const error     = validateFormLogin(idForm);
            if(error.length==0){
                /* tải loading */ 
                // loadLoading(idForm);
                /* lấy dữ liệu truyền đi */
                var data    = $('#'+idForm).serializeArray();
                $.ajax({
                    url         : '{{ route("admin.loginCustomer") }}',
                    type        : 'post',
                    dataType    : 'json',
                    data        : {
                        '_token'    : '{{ csrf_token() }}',
                        data        : data
                    },
                    success     : function(response){
                        if(response.flag==true){
                            window.location.href = '';
                        }else {
                            $('#noticeLogin').html(response.message);
                        }
                    }
                });
            }else {
                $.each(error, function(index, value){
                    const input = $('#'+idForm).find('[name='+value.name+']');
                    input.attr('placeholder', value.notice).css('border', '1px solid red');
                });
            }
        }
        /* validate form */
        function validateFormLogin(idForm){
            let error       = [];
            /* input required không được bỏ trống */
            $('#'+idForm).find('input[required]').each(function(){
                /* đưa vào mảng */
                if($(this).val()==''){
                    const errorItem = [];
                    errorItem['name']       = $(this).attr('name');
                    errorItem['notice']     = 'Không được để trống trường này';
                    error.push(errorItem);
                }
            });
            return error;
        }
    </script>
@endpush