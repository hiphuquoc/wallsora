<input type="hidden" id="total" name="total" value="{{ $total }}" />
<input type="hidden" id="loaded" name="loaded" value="{{ $loaded ?? 0 }}" />
<input type="hidden" id="idNot" name="idNot" value="{{ $idNot ?? 0 }}" />
<input type="hidden" id="arrayIdCategory" name="arrayIdCategory" value="{{ json_encode($arrayIdCategory) }}" />
<div class="freeWallpaperBox">
    @if($total>0)
        @foreach($wallpapers as $wallpaper)
            @include('wallpaper.category.item', [
                'wallpaper' => $wallpaper,
                'language'  => $language,
                'user'      => $user ?? null
            ])
        @endforeach
    @else 
        <div>{{ config('data_language_1.'.$language.'.no_suitable_results_found') }}</div>
    @endif
</div>
@push('scriptCustom')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            /* lazyload image */
            lazyload();
            /* tính lại để hiển thị so le chính xác */
            updateGridRowSpan();
            $(window).resize(updateGridRowSpan);
            /* load more */
            loadFreeWallpaperMore(20);
            window.addEventListener('scroll', function() {
                loadFreeWallpaperMore(20);
            });      
        });

        function updateGridRowSpan() {
            const gridContainer = $('.freeWallpaperBox'); 
            const rowHeight = parseFloat(gridContainer.css('grid-auto-rows')); // Lấy giá trị auto-row từ CSS
            const rowGap = parseFloat(gridContainer.css('gap').split(' ')[0] || 0); // Lấy khoảng cách dọc (vertical gap)
            
            let firstItemWidth = 0;
            
            // Duyệt qua các phần tử để tìm phần tử đầu tiên có chiều rộng > 0
            gridContainer.find('.freeWallpaperBox_item').each(function () {
                firstItemWidth = $(this).width();
                if (firstItemWidth > 0) {
                    return false; // Thoát khỏi vòng lặp khi tìm thấy phần tử hợp lệ
                }
            });

            // Nếu tìm thấy chiều rộng hợp lệ, tiến hành tính toán
            if (firstItemWidth > 0) {
                gridContainer.find('.freeWallpaperBox_item').each(function () {
                    const itemWidth = $(this).data('width'); // Lấy chiều rộng từ data-attribute
                    const itemHeight = $(this).data('height'); // Lấy chiều cao từ data-attribute
                    
                    // Kiểm tra nếu itemWidth và itemHeight có giá trị
                    if (itemWidth > 0 && itemHeight > 0) {
                        const aspectRatio = itemHeight / itemWidth; // Tính tỷ lệ
                        const imageHeight = aspectRatio * firstItemWidth; // Tính chiều cao ảnh dựa trên chiều rộng

                        // Tính số hàng cần chiếm, bao gồm khoảng cách giữa các hàng
                        const rows = Math.ceil((imageHeight + rowGap) / (rowHeight + rowGap));

                        // Cập nhật thuộc tính grid-row-end
                        $(this).css('grid-row-end', `span ${rows}`);
                    }
                });
            }
        }
        /* loadmore wallpaper */
        function loadFreeWallpaperMore(requestLoad = 20) {
            var boxCategory = $('.freeWallpaperBox');
            const total = $('#total').val();
            const loaded = $('#loaded').val();
            // Tính toán vị trí và kích thước của box
            const boxOffset = boxCategory.offset().top; // Vị trí trên cùng của box
            const boxHeight = boxCategory.outerHeight(); // Chiều cao của box
            // Vị trí cuộn hiện tại
            const scrollTop = $(window).scrollTop(); // Vị trí cuộn từ trên xuống
            // Kiểm tra xem cuộn gần đến đáy box chưa
            if (scrollTop + $(window).height() + 500 > boxOffset + boxHeight && boxCategory.length && !boxCategory.hasClass('loading')) {
                if (parseInt(total) > parseInt(loaded)) {
                    loadFreeWallpaperMoreToController(requestLoad);
                }
            }
        }
        function loadFreeWallpaperMoreToController(requestLoad){
            /* Lấy chuỗi query parameters từ URL */
            var queryString = window.location.search;
            var urlParams = new URLSearchParams(queryString);
            var params = {};
            for (const [key, value] of urlParams) {
                params[key] = value;
            }
            /* lấy thông tin của input */
            var boxCategory         = $('.freeWallpaperBox');
            const total             = $('#total').val();
            const loaded            = $('#loaded').val();
            /* thêm class để đánh dấu đăng load => không load nữa */
            boxCategory.addClass('loading');
            /* lấy dữ liệu */
            params.total            = total;
            params.loaded           = loaded;
            params.array_category_info_id = $('#arrayIdCategory').val();
            params.request_load     = requestLoad;
            params.idNot            = $('#idNot').val();
            params.language         = $('#language').val();
            const queryParams = new URLSearchParams(params).toString();
            fetch("{{ route('main.category.loadmoreFreeWallpapers') }}?" + queryParams, {
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
                /* xóa bỏ class để thể hiện đã load xong */
                boxCategory.removeClass('loading');
                /* append dữ liệu */
                $('#loaded').val(data.loaded);
                $('#total').val(data.total);
                if (data.content!='') boxCategory.append(data.content);
                /* tải các ảnh trong khung nhìn của content */
                setTimeout(function () {
                    lazyload();
                    updateGridRowSpan();
                }, 0);
                /* thêm thông báo nếu empty */
                if (boxCategory.children().length == 0) boxCategory.html('<div>' + "{{ config('data_language_1.' . $language . '.no_suitable_results_found') }}" + '</div>');
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
        function loadOneFreeWallpaper(idFreeWallpaper, idWrite, language) {
            const queryParams = new URLSearchParams({
                free_wallpaper_info_id: idFreeWallpaper,
                language: language,
            }).toString();

            fetch("{{ route('ajax.loadOneFreeWallpaper') }}?" + queryParams, {
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
                $('#' + idWrite).html(data);
                lazyload();
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
        /* hiển thị box cảm xúc khi nhấn vào icon */ 
        function showBoxFeeling(element) {
            // Tìm phần tử .freeWallpaperBox_item_box
            var boxItem = $(element).closest('.freeWallpaperBox_item_box');
            // Toggle class active cho .feeling
            console.log(boxItem.find('.feeling'));
            boxItem.find('.feeling').attr('style', 'display:flex !important;');
        }
        /* thả cảm xúc */
        function setFeelingFreeWallpaper(element, idFreeWallpaper, type) {
            const queryParams = new URLSearchParams({
                type: type,
                free_wallpaper_info_id: idFreeWallpaper
            }).toString();

            fetch("{{ route('ajax.setFeelingFreeWallpaper') }}?" + queryParams, {
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
                $(element).closest('.feeling').css('display', 'none');
                /* tải lại box */
                var box = $(element).closest('.freeWallpaperBox_item');
                var idBox = box.attr('id');
                var language = $('#language').val();
                loadOneFreeWallpaper(idFreeWallpaper, idBox, language);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
    </script>
@endpush