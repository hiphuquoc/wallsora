<!-- form modal chọn wallpaper -->
<form id="formChooseProduct" method="POST" action="#">
    @csrf
    <div class="modal fade" id="modalChooseProduct" tabindex="-1" aria-labelledby="addNewCardTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin:0 auto;width:1500px;max-width:unset;height:100vh;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="formChooseProduct">
                        <div class="formChooseProduct_chooseBox customScrollBar-y">
                            <!-- khung search -->
                            @include('admin.blog.search')
                            <!-- hiển thị -->
                            <div id="js_loadProduct" class="productWithWallpapers">
                                <!-- tải ajax -->
                                <div class="productWithWallpapers_item">Không có sản phẩm phù hợp!</div>
                            </div>
                        </div>
                        <div class="formChooseProduct_confirmBox customScrollBar-y">
                            <!-- danh sách sản phẩm đã chọn -->
                            <div id="js_loadThemeProductChoosed" class="productWithWallpapers">
                                <!-- load ajax: loadThemeProductChoosed -->
                            </div>
                        </div>
                        <!-- box content trả ra -->
                        <div class="formChooseProduct_htmlBox">
                            <textarea id="titleInput" name="titleInput" rows="2" placeholder="Input...">{{ $item->seo->title }}</textarea>
                            <textarea id="htmlResponse" name="htmlResponse" rows="7" placeholder="Onput..."></textarea>
                        </div>
                        <!-- action -->
                        <div class="formChooseProduct_action">
                            <!-- action xóa session -->
                            <div class="formChooseProduct_action_item" onclick="clearProductChoosed();">
                                <i class="fa-solid fa-rotate"></i>
                            </div>
                            <!-- action viết content -->
                            <div class="formChooseProduct_action_item" onclick="writeSuggestBlogWithAI();">
                                <i class="fa-solid fa-pencil"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

@pushonce('scriptCustom')
    <script type="text/javascript">
        function loadProduct(){
            const search_name       = $('#search_name').val();
            const search_category   = $('#search_category').val();
            const search_tag        = $('#search_tag').val();
            $.ajax({
                url         : '{{ route("admin.blog.loadProduct") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    search_name, search_category, search_tag
                },
                success     : function(data){
                    $('#js_loadProduct').html(data);
                },
                error       : function() {
                    $('#js_loadProduct').html('');
                }
            });
        }

        /* mỗi khi người dùng nhập một ký tự mới, hàm searchWallpapersWithDelay sẽ đặt một hẹn giờ (setTimeout) để gọi hàm searchWallpapers sau 0.5 giây. Nếu có thêm ký tự nào được nhập trong khoảng 0.5 giây, hẹn giờ trước đó sẽ bị xóa và hẹn giờ mới sẽ được đặt lại. Điều này giúp tạo ra hiệu ứng chờ giữa các lần nhập. */
        var searchTimer;
        function loadProductWithDelay() {
            clearTimeout(searchTimer);
            
            searchTimer = setTimeout(function () {
                loadProduct();
            }, 500);
        }

        function selectedWallpaper(element) {
            const parent = $(element).parent();
            const selectedElements = parent.find('.selected');
            
            if ($(element).hasClass('selected')) {
                // Nếu element đã được chọn, thì bỏ chọn
                $(element).removeClass('selected');
            } else {
                if (selectedElements.length >= 3) {
                    // Nếu đã đủ 3 phần tử được chọn, bỏ chọn phần tử đầu tiên trong danh sách
                    selectedElements.first().removeClass('selected');
                }
                // Chọn element hiện tại
                $(element).addClass('selected');
            }
        }

        function chooseProduct(idBox) {
            const boxElement = $('#' + idBox);
            const idProduct = boxElement.data('product_info_id');

            // Tạo mảng chứa các giá trị `data-wallpaper_info_id` từ các phần tử con có class `selected`
            const arrayWallpaperId = boxElement.find('.selected').map(function() {
                return $(this).data('wallpaper_info_id');
            }).get(); // `get()` chuyển đổi kết quả từ jQuery object thành mảng thông thường

            $.ajax({
                url         : '{{ route("admin.blog.chooseProduct") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    product_info_id : idProduct,
                    array_wallpaper_info_id : arrayWallpaperId,
                },
                success     : function(data) {
                    // Ẩn phần tử cha với class `.productWithWallpapers_item`
                    boxElement.closest('.productWithWallpapers_item').hide();
                    loadThemeProductChoosed();
                }
            });
        }

        function loadThemeProductChoosed(){
            $.ajax({
                url         : '{{ route("admin.blog.loadThemeProductChoosed") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    // product_info_id : idProduct,
                    // array_wallpaper_info_id : arrayWallpaperId,
                },
                success     : function(data) {
                    $('#js_loadThemeProductChoosed').html(data);
                }
            });
        }

        function removeOneProductChoosed(idProduct){
            $.ajax({
                url         : '{{ route("admin.blog.removeOneProductChoosed") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    product_info_id : idProduct,
                },
                success     : function(data) {
                    $('#js_removeOneProductChoosed_'+idProduct).hide();
                }
            });
        }

        function clearProductChoosed(){
            $.ajax({
                url         : '{{ route("admin.blog.clearProductChoosed") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    // product_info_id : idProduct,
                    // array_wallpaper_info_id : arrayWallpaperId,
                },
                success     : function(data) {
                    loadThemeProductChoosed();
                }
            });
        }

        function writeSuggestBlogWithAI() {
            // Mở loading
            openCloseFullLoading();

            // Lấy tiêu đề từ input
            const title_input = $('#titleInput').val();

            // Gọi getListProductChoose và xử lý khi dữ liệu đã sẵn sàng
            getListProductChoose().then(array_product_choose => {
                const promises = [];
                const orderedHTMLResults = []; // Mảng để lưu HTML theo thứ tự

                // Lặp qua từng sản phẩm trong array_product_choose
                Object.entries(array_product_choose).forEach(([product_info_id, wallpapers], index) => {
                    // Gọi callAIWritePerProduct và đẩy vào mảng promises, đồng thời lưu vị trí
                    const promise = callAIWritePerProduct(product_info_id, wallpapers, title_input)
                        .then(data => {
                            orderedHTMLResults[index] = data; // Lưu kết quả HTML vào đúng vị trí trong mảng
                        });
                    promises.push(promise);
                });

                // Khi tất cả các promise đã hoàn thành
                Promise.all(promises).then(() => {
                    // Nối các phần tử trong `orderedHTMLResults` và ghi vào #htmlResponse
                    $('#htmlResponse').html(orderedHTMLResults.join(''));
                    // Đóng loading
                    openCloseFullLoading();
                }).catch(error => {
                    console.error("An error occurred:", error);
                    openCloseFullLoading();
                });
            });
        }

        function getListProductChoose() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url         : '{{ route("admin.blog.getListProductChoose") }}',
                    type        : 'get',
                    dataType    : 'json',
                    success     : function(response) {
                        resolve(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }

        function callAIWritePerProduct(product_info_id, wallpapers, title_input) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url         : '{{ route("admin.blog.callAIWritePerProduct") }}',
                    type        : 'get',
                    dataType    : 'html', // Dữ liệu trả về là HTML
                    data        : {
                        product_info_id: product_info_id,
                        wallpapers: wallpapers,
                        title_input: title_input
                    },
                    success     : function(data) {
                        resolve(data); // Trả về HTML của từng sản phẩm
                    },
                    error       : function(error) {
                        reject(error);
                    }
                });
            });
        }

    </script>
@endpushonce