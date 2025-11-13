<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">
            Phiên bản của sản phẩm
            <i class="fa-solid fa-circle-xmark" data-repeater-delete></i>
        </h4>
    </div>
    <div class="card-body">
        <input type="hidden" name="id" value="{{ $price['id'] ?? null }}" />
        <div class="formBox">
            <div class="formBox_full flexBox">
                <div class="flexBox_item">
                    <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="code_name">Mã Tên</label>
                <input class="form-control" name="code_name" type="text" value="{{ $price['code_name'] ?? null }}" required />
                    </div>
                </div>
                <div class="flexBox_item">
                    <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="price">Giá bán $</label>
                        <input class="form-control" name="price" type="number" value="{{ $price['price'] ?? null }}" required />
                    </div>
                </div>
            </div>
            
            @if(!empty($price->id)&&$type=='edit') 
                <div id="js_loadWallpaperByProductPrice_{{ $price->id }}" class="formBox_full">
                    <!-- load Ajax -->
                </div>
            @endif

        </div>
    </div>
</div>

@if(!empty($price->id)&&$type=='edit')
    <!-- form modal chọn wallpaper -->
    <form id="formSearchWallpapers" method="POST" action="#">
    @csrf
    <div class="modal fade" id="formModal_{{ $price->id }}" tabindex="-1" aria-labelledby="addNewCardTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin:0 auto;">
            <div class="modal-content">
                <div class="modal-body">
                    
                    <div class="searchViewBefore">
                        <div class="searchViewBefore_input">
                            <!-- value = null không lưu giá trị search cũ -->
                            <input type="text" placeholder="Tìm wallpaper..." value="" data-product-price-id="{{ $price->id }}" onkeyup="searchWallpapersWithDelay(this)" autocomplete="off" />
                            <div>
                                <svg><use xlink:href="#icon_search"></use></svg>
                            </div>
                        </div>
                        <div class="searchViewBefore_selectbox">
                            {{-- @foreach($wallpapers as $wallpaper)
                                @php
                                    $selected       = false;
                                    foreach($price->wallpapers as $w){
                                        if($wallpaper->id==$w->infoWallpaper->id) $selected = 'selected';
                                    }
                                @endphp
                                @include('admin.product.oneRowSearchWallpaper', [
                                    'wallpaper'         => $wallpaper,
                                    'idProductPrice'    => $price->id,
                                    'selected'          => $selected
                                ])
                            @endforeach --}}
                            {{-- @for($i=0;$i<10;++$i)
                                <div id="js_deleteWallpaperAndSource_{{ $i }}" class="searchViewBefore_selectbox_item" style="width:33.33%;padding:0.75rem 0.5rem;">
                                    <div class="searchViewBefore_selectbox_item_imageFull" data-wallpaper-id="4005" data-product-price-id="1449" onclick="chooseWallpaperForProductPrice(this);">
                                        <img src="https://cdn.wallsora.com/wallpapers/hinh-nen-dien-thoai-chuot-con-don-giang-sinh-4k-tuyet-dep-031QXBSCHP-small.webp" />
                                        <div class="searchViewBefore_selectbox_item_imageFull_message">
                                            <div><i class="fa-solid fa-check"></i> Đã chọn</div>
                                        </div>
                                    </div>
                                    
                                    <div class="searchViewBefore_selectbox_item_action">
                                        <div class="used"><i class="fa-solid fa-check"></i> <span>2</span></div>
                                        <div class="remove" onclick="deleteWallpaperAndSource('js_deleteWallpaperAndSource_', {{ $id }});"><i class="fa-solid fa-trash-can"></i></div>
                                    </div>
                                </div>
                            @endfor --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </form>
@endif
@pushonce('scriptCustom')
    <script type="text/javascript">

        function chooseWallpaperForProductPrice(elementImage){
            const itemBox           = $(elementImage).closest('.searchViewBefore_selectbox_item');
            const idWallpaper       = $(elementImage).data('wallpaper-id');
            const idProductPrice    = $(elementImage).data('product-price-id');
            var action              = 'create';
            if(itemBox.hasClass('selected')) action = 'delete';
            $.ajax({
                url: "{{ route('admin.productPrice.addWallpaperToProductPrice') }}",
                type: "post",
                dataType: 'json',
                data: {
                    wallpaper_id : idWallpaper,
                    product_price_id : idProductPrice,
                    action
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                if(action=='create'){
                    itemBox.addClass('selected');
                }else if(action=='delete'){
                    itemBox.removeClass('selected');
                }
                loadWallpaperByProductPrice(idProductPrice);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
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
            const language          = $('#language').val();
            $.ajax({
                url: "{{ route('admin.wallpaper.searchWallpapers') }}",
                type: "post",
                dataType: 'html',
                data: {
                    key_search : valueInput,
                    product_price_id : idProductPrice,
                    language,
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