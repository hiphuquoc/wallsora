<!-- load more -->
<input type="hidden" id="js_loadMoreWallpaper_search" value="{{ $search ?? null }}" />
<input type="hidden" id="js_loadMoreWallpaper_total" value="{{ $total ?? 0 }}" />
<input type="hidden" id="js_loadMoreWallpaper_loaded" value="{{ $loaded ?? 0 }}" />
<!-- bắt buộc phải viết như vậy -->
@if(!empty($arrayIdCategory))
    <input type="hidden" id="js_loadMoreWallpaper_array_category_info_id" value="{{ json_encode($arrayIdCategory) }}" />
@else 
    <input type="hidden" id="js_loadMoreWallpaper_array_category_info_id" value="[]" />
@endif
<!-- bắt buộc phải viết như vậy -->
@if(!empty($arrayIdTag))
    <input type="hidden" id="js_loadMoreWallpaper_array_tag_info_id" value="{{ json_encode($arrayIdTag) }}" />
@else 
    <input type="hidden" id="js_loadMoreWallpaper_array_tag_info_id" value="[]" />
@endif
<input type="hidden" id="js_loadMoreWallpaper_sort_by" value="{{ $sortBy ?? null }}" />
<input type="hidden" id="js_loadMoreWallpaper_id_product" value="{{ $idProduct ?? 0 }}" />
{{-- <input type="hidden" id="js_loadMoreWallpaper_view_by" value="{{ Cookie::get('view_by') ?? 'each_set' }}" /> --}}
<!-- box -->
<div id="js_loadMoreWallpaper_box" class="wallpaperGridBox">
    @php
        $loadFirstTime = 10;
    @endphp
    @for($i=1;$i<=$loadFirstTime;++$i)
        <div class="wallpaperGridBox_itemBackground"></div>
    @endfor
</div>
@push('scriptCustom')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            /* load more */
            loadMoreWallpaper();
            $(window).on('scroll', function() {
                loadMoreWallpaper();
            });    
        });

        /* loadMoreWallpaper wallpaper */
        var isFirstLoad = true; // Khai báo một biến để theo dõi xem đã load lần đầu tiên chưa
        function loadMoreWallpaper(requestLoad = 20) {
            /* lấy dữ liệu */
            var boxCategory = $('#js_loadMoreWallpaper_box');
            const total = $('#js_loadMoreWallpaper_total').val();
            const loaded = $('#js_loadMoreWallpaper_loaded').val();
            if(boxCategory.length&&!boxCategory.hasClass('loading')){
                if(isFirstLoad){
                    loadMoreWallpaperToController("{{ $loadFirstTime }}", true);
                }else {
                    if(parseInt(total)>parseInt(loaded)){
                        loadMoreWallpaperToController(requestLoad, false);
                    }
                }
            }
        }
        function loadMoreWallpaperToController(requestLoad, firstTime = false) {
            /* hiện icon loading */
            loadLoading();

            // Lấy chuỗi query parameters từ URL
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const params = {};
            for (const [key, value] of urlParams) {
                params[key] = value;
            }

            /* lấy dữ liệu */
            const boxCategory = $('#js_loadMoreWallpaper_box');
            const total = $('#js_loadMoreWallpaper_total').val();
            const loaded = $('#js_loadMoreWallpaper_loaded').val();

            /* thêm class để đánh dấu đang load => không load nữa */
            boxCategory.addClass('loading');

            /* lấy dữ liệu */
            params.search = $('#js_loadMoreWallpaper_search').val();
            params.total = total;
            params.loaded = loaded;
            params.array_category_info_id = $('#js_loadMoreWallpaper_array_category_info_id').val();
            params.array_tag_info_id = $('#js_loadMoreWallpaper_array_tag_info_id').val();
            params.request_load = requestLoad;
            params.id_product = $('#js_loadMoreWallpaper_id_product').val();
            params.language = $('#language').val();

            // Tạo URL với các query parameters
            const url = new URL('{{ route("main.category.loadMoreWallpaper") }}');
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

            // Sử dụng fetch để thay thế ajax
            fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Tạo một container ẩn để xử lý nội dung
                const tempDiv = $('<div></div>').html(data.content);
                
                // Tìm tất cả hình ảnh trong content
                const images = tempDiv.find('img');
                let imagesLoaded = 0;

                if (images.length === 0) {
                    // Nếu không có hình ảnh nào, thì thêm trực tiếp vào boxCategory
                    if (firstTime) {
                        boxCategory.html(data.content);
                    } else {
                        boxCategory.append(data.content);
                    }

                    finalizeLoading(data, boxCategory);
                } else {
                    // Nếu có hình ảnh, đợi tất cả hình ảnh được tải xong
                    images.each(function () {
                        const img = new Image();
                        img.src = $(this).attr('src');
                        img.onload = function () {
                            imagesLoaded++;
                            if (imagesLoaded === images.length) {
                                // Khi tất cả hình ảnh đã được load, hiển thị content
                                if (firstTime) {
                                    boxCategory.html(data.content);
                                } else {
                                    boxCategory.append(data.content);
                                }

                                finalizeLoading(data, boxCategory);
                            }
                        };
                    });
                }
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                /* xử lý lỗi nếu cần */
            });

            // Đặt isFirstLoad thành false sau lần đầu load
            isFirstLoad = false;
        }

        function finalizeLoading(data, boxCategory) {
            // Cập nhật các thông tin sau khi load
            $('#js_loadMoreWallpaper_loaded').val(data.loaded);
            $('#js_loadMoreWallpaper_total').val(data.total); /* cập nhật lại total do load ajax cache */
            
            /* tải lazy load */
            setTimeout(() => {
                lazyload();
            }, 0);

            /* xóa bỏ class để thể hiện đã load xong */
            boxCategory.removeClass('loading');

            /* thêm thông báo nếu empty */
            if (boxCategory.children().length === 0) {
                boxCategory.html('<div>' + "{{ config('data_language_1.'.$language.'.no_suitable_results_found') }}" + '</div>');
            }

            /* tắt icon loading */
            loadLoading('hide');
        }
    </script>
@endpush