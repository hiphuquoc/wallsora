<!-- form modal chọn wallpaper -->
<form id="formSearchThumnails" method="POST" action="#">
    @csrf
    <div class="modal fade" id="modalSearchThumnails" tabindex="-1" aria-labelledby="addNewCardTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin:0 auto;">
            <div class="modal-content">
                <div class="modal-body">
                    
                    <div class="searchViewBefore">
                        <div class="searchViewBefore_input">
                            <!-- value = null không lưu giá trị search cũ -->
                            <input type="text" placeholder="Tìm thumnail..." value="" data-product-price-id="{{ 0 }}" onkeyup="searchWallpapersWithDelay(this)" autocomplete="off" disabled />
                            <div>
                                <svg><use xlink:href="#icon_search"></use></svg>
                            </div>
                        </div>
                        <div id="js_seachFreeWallpaperOfCategory_idWrite" class="searchViewBefore_selectbox">
                            <!-- load ajax -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>


@pushonce('scriptCustom')
    <script type="text/javascript">

        function chooseFreeWallpaperForCategory(elementImage){
            const itemBox           = $(elementImage).closest('.searchViewBefore_selectbox_item');
            const idFreewallpaper   = $(elementImage).data('free-wallpaper-id');
            const idCategory        = $(elementImage).data('category-id');
            var action              = 'create';
            if(itemBox.hasClass('selected')) action = 'delete';
            $.ajax({
                url: "{{ route('admin.category.chooseFreeWallpaperForCategory') }}",
                type: "post",
                dataType: 'json',
                data: {
                    free_wallpaper_info_id  : idFreewallpaper,
                    category_info_id        : idCategory,
                    action
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                if(response){
                    itemBox.addClass('selected');
                } else {
                    itemBox.removeClass('selected');
                }
                loadFreeWallpaperOfCategory(idCategory);
                seachFreeWallpaperOfCategory(elementImage);
            });
        }

        /* mỗi khi người dùng nhập một ký tự mới, hàm searchWallpapersWithDelay sẽ đặt một hẹn giờ (setTimeout) để gọi hàm searchWallpapers sau 0.5 giây. Nếu có thêm ký tự nào được nhập trong khoảng 0.5 giây, hẹn giờ trước đó sẽ bị xóa và hẹn giờ mới sẽ được đặt lại. Điều này giúp tạo ra hiệu ứng chờ giữa các lần nhập. */
        var searchTimer;
        function searchWallpapersWithDelay(input) {
            clearTimeout(searchTimer);
            
            searchTimer = setTimeout(function () {
                searchWallpapers(input);
            }, 500);
        }
        function searchWallpapers(input){
            const valueInput        = $(input).val();
            const idProductPrice    = $(input).data('product-price-id');
            $.ajax({
                url: "{{ route('admin.wallpaper.searchWallpapers') }}",
                type: "post",
                dataType: 'html',
                data: {
                    key_search : valueInput,
                    product_price_id : idProductPrice
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                const selectBox = $(input).parent().parent().find('.searchViewBefore_selectbox');
                selectBox.html(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }

        function deleteWallpaperAndSource(idBox, idWallpaper){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                const heightBox = $('#'+idBox).outerHeight();
                addLoading(idBox, heightBox);
                $.ajax({
                    url         : "{{ route('admin.wallpaper.deleteWallpaperAndSource') }}",
                    type        : "post",
                    dataType    : "json",
                    data        : { 
                        '_token'        : '{{ csrf_token() }}', 
                        id  : idWallpaper
                    }
                }).done(function(data){
                    setTimeout(() => {
                        if(data==true) $('#'+idBox).hide();
                    }, 500)
                });
            }
        }

    </script>
@endpushonce