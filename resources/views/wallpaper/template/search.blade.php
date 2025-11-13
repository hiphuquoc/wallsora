@php
    $labelName          = config('data_language_1.'.$language.'.what_do_you_want_to_find_today');
    $typeSearch         = config('data_language_2.'.$language.'.list_type_search') ?? [];
    $keyActiveFirstTime = 'paid_wallpaper';

@endphp

<!-- search mobile -->
<div class="menuTop_item iconSearchMobile" onclick="toggleSearchMobile();">
    <div class="iconSearchMobile_icon"></div>
</div>
<!-- search desktop -->
<div class="menuTop_item searchViewBefore">
    <form action="{{ route('routing', ['slug' => config('language.'.$language.'.slug_page_premium_wallpaper')]).'?search=' }}" method="GET">
        <div class="searchViewBefore_showBox">
            <div class="searchViewBefore_showBox_typeBox" onclick="openSelected('js_openSelected_typeBoxList', 'searchViewBefore_showBox_typeBox')">
                <div class="searchViewBefore_showBox_typeBox_text maxLine_1">
                    @foreach($typeSearch as $key => $t)
                        @if($key==$keyActiveFirstTime)
                            {{ $t }}
                            @break;
                        @endif
                    @endforeach
                </div> 
                <svg><use xlink:href="#icon_arrow_down"></use></svg>
                @if(!empty($typeSearch))
                    <div id="js_openSelected_typeBoxList" class="searchViewBefore_showBox_typeBox_list">
                        @foreach($typeSearch as $key => $t)
                            @php
                                $selected = $key==$keyActiveFirstTime ? 'selected' : '';
                            @endphp
                            <div class="searchViewBefore_showBox_typeBox_list_item maxLine_1 {{ $selected }}" data-input="{{ $key }}">{{ $t }}</div>
                        @endforeach
                    </div>
                @endif
                <!-- Hidden input to store selected data-input value -->
                <input type="hidden" id="search_type" name="search_type" value="{{ $keyActiveFirstTime }}" />
            </div>
            
            <div class="searchViewBefore_showBox_inputBox">
                <!-- value = null không lưu giá trị search cũ -->
                <input id="searchAjax_input" class="searchViewBefore_showBox_input" type="text" name="search" placeholder="{{ $labelName }}" value="" onkeyup="searchAjaxWithDelay(this)" autocomplete="off" onclick="openSelectedRight('js_openSelectedRight_elemt', 'searchViewBefore_showBox_inputBox')" />
                <button type="submit" class="searchViewBefore_showBox_inputBox_button" aria-label="{{ $labelName }}">
                    <svg><use xlink:href="#icon_search"></use></svg>
                    <span>{{ config('data_language_1.'.$language.'.search') }}</span>
                </button>
                <div id="js_openSelectedRight_elemt" class="searchViewBefore_showBox_inputBox_list">
                    <div id="js_searchAjax">
                        <!-- load Ajax -->
                        <div id="js_searchAjax_idWrite" class="searchViewBeforeOfPremiumWallpaper">
                            
                        </div>
                        <!-- loading -->
                        <div class="loadingBox">
                            <div class="loadingIcon"></div>
                            <div class="loadingBox_backgroundBlur"></div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </form>
    
    <div class="searchViewBefore_background" onClick="closeBoxSearchMobile();"></div>
</div>

@push('scriptCustom')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Call the function with your selectBox ID and hidden input ID
            handleSelectItem('js_openSelected_typeBoxList', 'search_type');
        });

        function toggleSearchMobile() {
            const iconSearch    = document.querySelector('.iconSearchMobile_icon');
            const searchView = document.querySelector('.searchViewBefore');
            const searchList = document.querySelector('.searchViewBefore_showBox_inputBox_list');
            if (searchView) {
                searchView.classList.toggle('active');
                searchList.classList.toggle('active');
                iconSearch.classList.toggle('active');
            } else {
                console.error("Phần tử .searchViewBefore không tồn tại.");
            }
        }

        // function toggleSearchMobile(){
        //     const elementSearchBox = $('.searchViewBefore');
        //     elementSearchBox.css({
        //         'opacity'   : '1',
        //         'width'     : '100%',
        //         'z-index'   : '1'
        //     });
        //     $('.searchViewBefore input').focus();
        //     /* mở phần hiển thị kết quả search (nếu đang đóng) */
        //     $('#js_searchAjax_idWrite').css('height', 'auto');
        //     $('.searchViewBefore_background').css('display', 'block');
        // }

        

        // function closeBoxSearchMobile(){
        //     toggleSearchMobile();
        //     $('.searchViewBefore_selectbox').css('height', '0');
        //     $('.searchViewBefore_background').css('display', 'none');
        // }
        /* mỗi khi người dùng nhập một ký tự mới, hàm searchWallpapersWithDelay sẽ đặt một hẹn giờ (setTimeout) để gọi hàm searchWallpapers sau 0.5 giây. Nếu có thêm ký tự nào được nhập trong khoảng 0.5 giây, hẹn giờ trước đó sẽ bị xóa và hẹn giờ mới sẽ được đặt lại. Điều này giúp tạo ra hiệu ứng chờ giữa các lần nhập. */
        var searchTimer;
        function searchAjaxWithDelay(input) {
            /* khi gõ làm mờ box kết quả (bật loading) */
            $('#js_searchAjax .loadingBox').addClass('show'); /* hiệu ứng làm mờ box */
            /* tính toán thời gian và gọi hàm search */
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                searchAjax();
            }, 500);
        }
        /* tìm kiếm sản phẩm ajax */
        function searchAjax(){
            /* lấy dữ liệu */
            const valueElement  = $('#searchAjax_input').val();
            const searchType    = $('#search_type').val();
            const language      = $('#language').val();
            $.ajax({
                url         : '{{ route("search.searchAjax") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    search      : valueElement,
                    search_type : searchType,
                    language,
                },
                success     : function(response){
                    setTimeout(() => {
                        if(response!='') $('#js_searchAjax_idWrite').html(response);
                        /* tắt loadding */ 
                        $('#js_searchAjax .loadingBox').removeClass('show'); /* hiệu ứng làm mờ box */
                    }, 500);
                }
            });
        }
        /* mở selectBox */
        function openSelected(selectBoxId, parentClass) {
            const selectBox = $('#' + selectBoxId);

            // Toggle class 'active' to open/close the select box
            selectBox.toggleClass('active');

            if (selectBox.hasClass('active')) {
                // Gắn sự kiện click một lần khi SelectBox đang mở
                $(document).on('click.outsideSelectBox', function(event) {
                    if (!$(event.target).closest('.' + parentClass).length) {
                        selectBox.removeClass('active'); // Close the select box
                        $(document).off('click.outsideSelectBox'); // Remove this event listener
                    }
                });
            } else {
                // Khi select box đóng thì cũng bỏ sự kiện click bên ngoài
                $(document).off('click.outsideSelectBox');
            }
        }
        /* mở selectBox bên phải */
        function openSelectedRight(selectBoxId, parentClass) {
            const selectBox = $('#' + selectBoxId);
            const inputParent = $('.' + parentClass);

            // Mở danh sách khi con trỏ chuột nằm trong ô input
            selectBox.addClass('active');

            // Gắn sự kiện click bên ngoài để đóng danh sách
            $(document).on('click.outsideInputBox', function(event) {
                // Kiểm tra nếu click không thuộc input và không thuộc danh sách
                if (!$(event.target).closest(inputParent).length && !$(event.target).closest(selectBox).length) {
                    selectBox.removeClass('active'); // Đóng danh sách
                    $(document).off('click.outsideInputBox'); // Loại bỏ sự kiện
                }
            });

            // Đảm bảo danh sách đóng khi input bị mất focus
            inputParent.find('input').on('blur', function () {
                setTimeout(() => {
                    if (!inputParent.find('input:focus').length) {
                        selectBox.removeClass('active');
                        $(document).off('click.outsideInputBox');
                    }
                }, 150); // Delay nhỏ để đảm bảo không xung đột khi click vào danh sách
            });
        }

        function handleSelectItem(selectBoxId, hiddenInputId) {
            $('#' + selectBoxId).on('click', '.searchViewBefore_showBox_typeBox_list_item', function() {
                // Remove 'selected' class from all items
                $('#' + selectBoxId + ' .searchViewBefore_showBox_typeBox_list_item').removeClass('selected');
                
                // Add 'selected' class to the clicked item
                $(this).addClass('selected');
                
                // Update the text in the select box display (optional)
                const selectedText = $(this).text().trim();
                $(this).closest('.searchViewBefore_showBox_typeBox').find('.searchViewBefore_showBox_typeBox_text').text(selectedText);
                
                // Get the value from the data-input attribute
                const selectedValue = $(this).data('input');
                
                // Update hidden input value with the selected data-input value
                $('#' + hiddenInputId).val(selectedValue);

                // tải lại khung search theo tùy chọn mới
                $('#js_searchAjax .loadingBox').addClass('show'); /* hiệu ứng làm mờ box */
                searchAjax();
            });
        }
    </script>
@endpush
