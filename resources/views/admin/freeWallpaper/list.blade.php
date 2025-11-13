@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Wallpaper Miễn Phí</div>
<!-- ===== START: SEARCH FORM ===== -->
<div class="searchBox">
    <div class="searchBox_item">
        <form id="formSearch" method="get" action="{{ route('admin.freeWallpaper.list') }}">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}">
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button>
            </div>
        </form>
    </div>
    <div class="searchBox_item">
        <button class="btn btn-primary waves-effect" data-bs-toggle="modal" data-bs-target="#modalFormWallpaper"  id="button-addon2" aria-label="Tải lên" onClick="loadModalUploadAndEdit();">Tải lên</button>
    </div>
    <div class="searchBox_item" style="margin-left:auto;text-align:right;">
        @php
            $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewFreeWallpaperInfo', config('setting.admin_array_number_view'), $viewPerPage, $total);
            echo $xhtmlSettingView;
        @endphp
    </div>
</div>

<!-- ===== END: SEARCH FORM ===== -->
<div id="js_uploadAndChangeWallpaper_idWrite" class="freeWallpaperBox" style="padding-bottom:2rem;">
    @if(!empty($list))
        @foreach($list as $item)
            <div id="js_deleteWallpaper_{{ $item->id }}" class="freeWallpaperBox_item">
                @include('admin.freeWallpaper.oneRow', compact('item'))
            </div>
        @endforeach
    @endif
</div>

<!-- Pagination -->
{{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }}

<!-- ===== START: MODAL ===== -->
<form id="formWallpaper" method="post" enctype="multipart/form-data">
@csrf
    <div class="modal modal-top fade" id="modalFormWallpaper" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog" style="max-width:800px;">
            <div id="js_loadModalUploadAndEdit_box">
                <!-- load Ajax -->
            </div>
        </div>
    </div>
</form>
<!-- ===== END:: MODAL ===== -->
    
@endsection
@push('scriptCustom')
    <!-- BEGIN: SLICK -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- END: SLICK -->
    <script type="text/javascript">
    
        function loadOneRow(idWallpaper){
            const idBox         = 'js_deleteWallpaper_'+idWallpaper;
            var boxWallpaper    = $('#'+idBox);
            const heightBox     = boxWallpaper.outerHeight();
            addLoading(idBox, heightBox);
            $.ajax({
                url: "{{ route('admin.freeWallpaper.loadOneRow') }}",
                type: "post",
                dataType: "html",
                data: {
                    id : idWallpaper
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function (response) {
                boxWallpaper.html(response);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ajax request failed: " + textStatus, errorThrown);
            });
        }

        // function readImageWhenChoose(input) {
        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();

        //         reader.onload = function (e) {
        //             const parentBox = $(input).parent();
        //             parentBox.css({
        //                 'background' : "url('"+e.target.result+"') no-repeat",
        //                 'background-size' : '100% auto'
        //             });
        //         };

        //         reader.readAsDataURL(input.files[0]);
        //     }
        // }

        function addFormUpload(input) {
            if (input.files && input.files.length > 0) {
                var dataId = [];
                for (let i = 0; i < input.files.length; ++i) {
                    dataId.push(i);
                }
                /* Gửi dataId vào ajax để tạo form */
                $.ajax({
                    url: "{{ route('admin.freeWallpaper.addFormUpload') }}",
                    type: "post",
                    dataType: "html",
                    data: {
                        data_id: dataId
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).done(function (response) {
                    /* Append form vào #js_loadModalUploadAndEdit_box */
                    $('#js_addFormUpload_box').html(response);  
                    /* Refresh lại slick */
                    refreshSlick('formFreeWallpaperBox');
                    /* Tách input multiple ra và điền giá trị vào từng input wallpaper */
                    for (let i = 0; i < dataId.length; ++i) {
                        let box   = $('#js_addFormUpload_wallpaper_'+dataId[i]);
                        let reader      = new FileReader();
                        reader.onload   = function (e) {
                            /* Sử dụng .css() để đặt background image */
                            box.css({
                                'background'        : "url('" + e.target.result + "') no-repeat",
                                'background-size'   : '100% auto'
                            });
                        };
                        reader.readAsDataURL(input.files[i]); // Đọc từng tệp ảnh riêng lẻ
                    }
                    /* select2 */ 
                    $(".select2").select2();
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Ajax request failed: " + textStatus, errorThrown);
                });

            }
        }

        function uploadAndChangeWallpaper(idWallpaper = '') {
            const tmp          = validateForm('formWallpaperWithSource');
            if(tmp==''){
                /* bật loading */
                addLoadingModal();
                /* lấy thêm các input khác */
                var inputName       = $('#name').val();
                var inputDesc       = $('#description').val();
                /* upload (create) */ 
                if(idWallpaper==''){
                    /* Lấy danh sách các file từ input có id là 'inputFile' */
                    var fileWallpapers  = $('#wallpapers')[0].files;
                    // Sử dụng Promise đã tạo
                    uploadWallpaper(fileWallpapers)
                        .then(function () {
                            // Tất cả các công việc đã hoàn thành, thực hiện các hành động cần thiết sau đó
                            addLoadingModal();
                            $('#modalFormWallpaper').modal('hide');
                        })
                        .catch(function () {
                            // Xử lý khi có lỗi
                        });
                }else {
                    var formData = new FormData();
                    const i     = 0;
                    formData.append('count', i);
                    formData.append('wallpaper_info_id', idWallpaper);
                    /* không sửa ảnh */
                    // formData.append('files[wallpaper]', fileWallpapers[i]);
                    
                    // Lặp qua tất cả các input và textarea trong #js_uploadWallpaper_i
                    $(".js_uploadWallpaper_" + i + " input, .js_uploadWallpaper_" + i + " textarea, .js_uploadWallpaper_" + i + " select").each(function() {
                        var inputName = $(this).attr('name');
                        var inputValue = $(this).val();
                        // Kiểm tra xem input có tên và giá trị không rỗng
                        if (inputName && inputValue !== undefined) {
                            // Lọc tên để chỉ giữ lại phần không có tiền tố [i]
                            var filteredName = inputName.replace(/\[\d+\]/, '');
                            // Thêm input vào FormData
                            formData.append(filteredName, inputValue);
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.freeWallpaper.updateWallpaper') }}",
                        type: "post",
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                    }).done(function (response) {
                        /* load lại box */
                        loadOneRow(idWallpaper);
                        /* tắt modal và loading */
                        addLoadingModal();
                        $('#modalFormWallpaper').modal('hide');
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        console.error("Ajax request failed: " + textStatus, errorThrown);
                    });
                }   
                /* tải lại source => dùng cho loadOneRow */
                setTimeout(function(){
                    loadImageFromGoogleCloud();
                }, 300);             
            }else {
                /* có 1 vài trường required bị bỏ trống */
                let messageError        = 'Không được bỏ trống trường ';
                tmp.forEach(function(value, index, array){
                    switch (true) {
                        case /^sources\[\d+\]$/.test(value):
                            var index = parseInt(value.match(/\d+/)[0]) + 1;
                            messageError += '<strong>Ảnh gốc ' + index + '</strong>';
                            break;
                        case value === 'wallpapers[]':
                            messageError += '<strong>Wallpaper</strong>';
                            break;
                        case value === 'file_name':
                            messageError += '<strong>Đường dẫn ảnh</strong>';
                            break;
                        case value === 'name':
                            messageError += '<strong>Alt wallpaper</strong>';
                            break;
                        default:
                            break;
                    }
                    if(index!=parseInt(tmp.length-1)) messageError += ', ';
                })
                $('#js_validateFormModalHotelContact_message').css('display', 'block').html(messageError);
            }
        }

        function uploadWallpaper(fileWallpapers) {
            return new Promise(function (resolve, reject) {
                // Mảng chứa tất cả các promises từ các request AJAX
                var promises = [];
                
                for (var i = 0; i < fileWallpapers.length; i++) {
                    var formData = new FormData();
                    formData.append('count', i);
                    formData.append('files[wallpaper]', fileWallpapers[i]);
                    
                    // Lặp qua tất cả các input và textarea trong #js_uploadWallpaper_i
                    $(".js_uploadWallpaper_" + i + " input, .js_uploadWallpaper_" + i + " textarea, .js_uploadWallpaper_" + i + " select").each(function() {
                        var inputName = $(this).attr('name');
                        var inputValue = $(this).val();
                        // Kiểm tra xem input có tên và giá trị không rỗng
                        if (inputName && inputValue !== undefined) {
                            // Lọc tên để chỉ giữ lại phần không có tiền tố [i]
                            var filteredName = inputName.replace(/\[\d+\]/, '');
                            // Thêm input vào FormData
                            formData.append(filteredName, inputValue);
                        }
                    });

                    // Thực hiện request AJAX và đưa promise vào mảng
                    promises.push(
                        $.ajax({
                            url: "{{ route('admin.freeWallpaper.uploadWallpaper') }}",
                            type: "post",
                            dataType: 'json',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                        })
                    );
                }

                // Khi tất cả các promises đã hoàn thành, resolve Promise chính
                Promise.all(promises)
                    .then(function (responses) {
                        // Các responses chứa kết quả từ mỗi request AJAX
                        responses.forEach(function (response) {
                            // Thêm box mới upload vào
                            var newDiv = $("<div>", {
                                id: "js_deleteWallpaper_" + response.id,
                                class: "freeWallpaperBox_item",
                                html: response.content
                            });
                            // Thêm div mới vào đầu của container
                            newDiv.prependTo("#js_uploadAndChangeWallpaper_idWrite");
                        });
                        // Gọi resolve để kết thúc Promise
                        resolve();
                    })
                    .catch(function (error) {
                        console.error("One or more AJAX requests failed:", error);
                        // Gọi reject để kết thúc Promise với lỗi
                        reject();
                    });
            });
        }

        function validateForm(idForm) {
            let error = [];
            $('#' + idForm).find(':input[required]').each(function () {
                if ($(this).val() === '') {
                    error.push($(this).attr('name'));
                }
            });
            return error;
        }

        function addLoadingModal() {
            const displayLoading = $('#js_addLoadingModal').css('display');
            if(displayLoading=='none'){
                $('#js_addLoadingModal').css('display', 'flex');
            }else {
                $('#js_addLoadingModal').css('display', 'none');
            }
        }

        function loadModalUploadAndEdit(idWallpaper = ''){
            $.ajax({
                url         : "{{ route('admin.freeWallpaper.loadModalUploadAndEdit') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}', 
                    wallpaper_id : idWallpaper
                }
            }).done(function(data){
                $('#js_loadModalUploadAndEdit_box').html(data);
                /* select2 */ 
                $(".select2").select2();
                // console.log($('#modalFormWallpaper').html());
            });
        }

        function deleteWallpaper(idBox, idWallpaper){
            const heightBox = $('#'+idBox).outerHeight();
            addLoading(idBox, heightBox);
            $.ajax({
                url         : "{{ route('admin.freeWallpaper.deleteWallpaper') }}",
                type        : "post",
                dataType    : "json",
                data        : { 
                    '_token'        : '{{ csrf_token() }}', 
                    id  : idWallpaper
                }
            }).done(function(data){
                setTimeout(() => {
                    if(data==true) $('#'+idBox).remove();
                }, 500)
            });
        }

        function autoFillNameAndEnName(keyId) {
            var valueName = '{{ config("main.auto_fill.alt.vi") }} ';

            const limitBox = $('.js_uploadWallpaper_' + keyId);

            // Lấy giá trị của input tag name
            var tagNameValue = limitBox.find('[name*="tag"]').val();

            // Kiểm tra nếu có giá trị trong tagNameValue
            if (tagNameValue) {
                // Phân tích chuỗi JSON
                var tags = JSON.parse(tagNameValue);
                
                // Duyệt qua từng tag và thêm vào valueName
                tags.forEach(function(tag) {
                    valueName += tag.value + ' ';
                });
            }

            // Tiếp tục lấy giá trị từ các select như cũ
            limitBox.find('select').each(function() {
                // Chọn tất cả các option được chọn trong select
                var selectedOptions = $(this).find('option:selected');

                // Lặp qua từng option được chọn
                selectedOptions.each(function() {
                    // Lấy giá trị của thuộc tính data-name
                    var dataNameValue = $(this).data('name');

                    // Kiểm tra xem có giá trị data-name hay không
                    if (dataNameValue) {
                        // Nếu có giá trị data-name, cập nhật giá trị valueName
                        valueName += dataNameValue + ' ';
                    }
                });
            });

            /* điền vào value của name */
            limitBox.find('[name*="name"]').val(valueName.trim());
        }

        function addLoading(idBox, heightBox = 300){
            const htmlLoadding  = '<div style="display:flex;align-items:center;justify-content:center;height:'+heightBox+'px;width:100%;"><div class="spinner-grow text-primary me-1" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            $('#'+idBox).html(htmlLoadding);
        }

    </script>
@endpush