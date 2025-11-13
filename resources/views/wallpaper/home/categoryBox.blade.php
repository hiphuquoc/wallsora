<div class="categoryGrid">
    <div class="categoryGrid_title">
        {{-- <input type="hidden" id="total" name="total" value="{{ $total ?? 0 }}" />
        <input type="hidden" id="loaded" name="loaded" value="{{ $data['items']->count() ?? 0 }}" /> --}}
        @php
            $titleBox = '';
            if($type=='category_info') $titleBox = config('data_language_2.'.$language.'.wallpaper_by_themes');
            if($type=='style_info') $titleBox = config('data_language_2.'.$language.'.wallpaper_by_styles');
            if($type=='event_info') $titleBox = config('data_language_2.'.$language.'.wallpaper_by_events');
        @endphp
        <h2>{{ $titleBox }}</h2>
    </div>
    <div class="categoryGrid_box">
        @foreach($categories as $category)
            @foreach($category->seos as $categorySeo)
                @if(!empty($categorySeo->infoSeo->type)&&$categorySeo->infoSeo->type==$type) <!-- kiểm tra có ngôn ngữ -->
                    <div id="js_loadInfoCategory_{{ $category->id }}" class="categoryGrid_box_item js_loadInfoCategory" data-category_info_id="{{ $category->id }}">
                        <!-- load Ajax -->
                    </div>
                    @break
                @endif
            @endforeach
        @endforeach
    </div>
</div>
@pushonce('scriptCustom')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            // Gọi hàm loadVisibleCategories khi trang vừa load
            loadVisibleCategories();

            // Gọi lại hàm loadVisibleCategories mỗi khi scroll
            $(window).on('scroll', function() {
                loadVisibleCategories();
            });
        });

        function loadVisibleCategories() {
            // Lấy vị trí hiện tại của scrollTop
            var scrollTop = $(window).scrollTop();
            var windowHeight = $(window).height();

            // Kiểm tra từng phần tử với class 'js_loadInfoCategory'
            $('.js_loadInfoCategory').each(function() {
                var elementTop = $(this).offset().top;

                // Kiểm tra nếu phần tử cách scrollTop 500px
                if (elementTop < scrollTop + windowHeight + 500 && !$(this).hasClass('loaded')) {
                    // Gọi hàm loadInfoCategory và đánh dấu phần tử là đã load
                    let idCategory  = $(this).data('category_info_id');
                    let idWrite     = $(this).attr('id');
                    loadInfoCategory(idCategory, idWrite);
                    $(this).addClass('loaded'); // Để tránh load lại cùng một phần tử
                }
            });
        }
        
        function loadInfoCategory(idCategory, idWrite) {
            let dataForm = {};
            dataForm.category_info_id = idCategory;
            dataForm.language = $('#language').val();
            const queryString = new URLSearchParams(dataForm).toString();
            fetch("/loadInfoCategory?" + queryString, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                // Tạo một container ẩn để chứa dữ liệu tạm thời
                const tempDiv = $('<div></div>').html(data);

                // Tìm tất cả thẻ img trong dữ liệu
                const images = tempDiv.find('img');
                let imagesLoaded = 0;

                if (images.length === 0) {
                    // Nếu không có hình ảnh nào, thì thêm trực tiếp vào phần tử
                    $('#'+idWrite).html(data);
                    lazyload(); // Tải lazy load cho các hình ảnh khác nếu có
                } else {
                    // Nếu có hình ảnh, kiểm tra xem tất cả có tải xong không
                    images.each(function() {
                        const img = new Image();
                        img.src = $(this).attr('src');
                        img.onload = function() {
                            imagesLoaded++;
                            if (imagesLoaded === images.length) {
                                // Khi tất cả ảnh đã được tải, chèn nội dung vào HTML
                                $('#'+idWrite).html(data);
                                lazyload(); // Tải lazy load sau khi nội dung đã được chèn
                            }
                        };
                        img.onerror = function() {
                            imagesLoaded++;
                            if (imagesLoaded === images.length) {
                                // Nếu có lỗi với bất kỳ ảnh nào, vẫn chèn nội dung khi tất cả đã thử tải
                                $('#'+idWrite).html(data);
                                lazyload();
                            }
                        };
                    });
                }
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }

        function openCloseCategoryListForMobile(idList){
            const element = $('#'+idList);
            if(element.hasClass('zoom')){
                element.removeClass('zoom');
            }else {
                element.addClass('zoom');
            }
        }
    </script>
@endPushonce