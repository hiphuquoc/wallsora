@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh sách Wallpaper trả phí</div>
<!-- ===== START: SEARCH FORM ===== -->

<div class="searchBox">
    <div class="searchBox_item">
        <form id="formSearch" method="get" action="{{ route('admin.wallpaper.list') }}">
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
            $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewWallpaperInfo', config('setting.admin_array_number_view'), $viewPerPage, $total);
            echo $xhtmlSettingView;
        @endphp
    </div>
</div>

<!-- ===== END: SEARCH FORM ===== -->
{{-- <div id="js_uploadImage_idWrite" class="imageBox" style="padding-bottom:2rem;">
    @if(!empty($list))
    @foreach($list as $item)
        @include('admin.wallpaper.oneRow', compact('item'))
    @endforeach
    @endif
</div> --}}
<div id="js_uploadAndChangeWallpaperWithSource_idWrite" class="wallpaperBox" style="padding-bottom:2rem;">
    @if(!empty($list))
        @foreach($list as $item)
            <div id="js_deleteWallpaperAndSource_{{ $item->id }}" class="wallpaperBox_item">
                @include('admin.wallpaper.oneRow', compact('item'))
            </div>
        @endforeach
    @endif
</div>

<!-- Pagination -->
{{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }}

<!-- ===== START: MODAL ===== -->
<form id="formWallpaperWithSource" method="post" enctype="multipart/form-data">
@csrf
    <div class="modal modal-top fade" id="modalFormWallpaper" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog" style="max-width:1000px;">
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
            const idBox         = 'js_deleteWallpaperAndSource_'+idWallpaper;
            var boxWallpaper    = $('#'+idBox);
            const heightBox     = boxWallpaper.outerHeight();
            addLoading(idBox, heightBox);
            $.ajax({
                url: "{{ route('admin.wallpaper.loadOneRow') }}",
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

        function readImageWhenChoose(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    const parentBox = $(input).parent();
                    parentBox.css({
                        'background' : "url('"+e.target.result+"') no-repeat",
                        'background-size' : '100% 100%'
                    });
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function addFormUploadSource(input) {
            if (input.files && input.files.length > 0) {
                var dataId = [];
                for (let i = 0; i < input.files.length; ++i) {
                    dataId.push(i);
                }
                /* Gửi dataId vào ajax để tạo form */
                $.ajax({
                    url: "{{ route('admin.wallpaper.loadFormUploadSourceAndWallpaper') }}",
                    type: "post",
                    dataType: "html",
                    data: {
                        data_id: dataId
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).done(function (response) {
                    /* Append form vào #js_addFormUploadSource_box */
                    $('#js_addFormUploadSource_box').html(response);  
                    /* Refresh lại slick */
                    refreshSlick('formWallpaperBox_gallery');
                    /* Tách input multiple ra và điền giá trị vào từng input wallpaper */
                    for (let i = 0; i < dataId.length; ++i) {
                        let boxSource   = $('#js_addFormUploadSource_wallpaper_'+dataId[i]);
                        let reader      = new FileReader();
                        reader.onload   = function (e) {
                            /* Sử dụng .css() để đặt background image */
                            boxSource.css({
                                'background'        : "url('" + e.target.result + "')",
                                'background-size'   : '100% 100%'
                            });
                        };
                        reader.readAsDataURL(input.files[i]); // Đọc từng tệp ảnh riêng lẻ
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Ajax request failed: " + textStatus, errorThrown);
                });

            }
        }

        // function uploadAndChangeWallpaperWithSource(idWallpaper = '') {
        //     const tmp          = validateForm('formWallpaperWithSource');
        //     if(tmp==''){
        //         /* bật loading */
        //         addLoadingModal();
        //         /* lấy thêm các input khác */
        //         var inputName       = $('#name').val();
        //         var inputDesc       = $('#description').val();
        //         /* upload (create) */ 
        //         if(idWallpaper==''){
        //             /* Lấy danh sách các file từ input có id là 'inputFile' */
        //             var fileWallpapers  = $('#wallpapers')[0].files;
        //             // Sử dụng Promise đã tạo
        //             uploadWallpaperWithSource(inputName, inputDesc, fileWallpapers)
        //                 .then(function () {
        //                     // Tất cả các công việc đã hoàn thành, thực hiện các hành động cần thiết sau đó
        //                     addLoadingModal();
        //                     $('#modalFormWallpaper').modal('hide');
        //                 })
        //                 .catch(function () {
        //                     // Xử lý khi có lỗi
        //                 });
        //         }else {
        //             var formData        = new FormData();
        //             formData.append('name', inputName);
        //             formData.append('description', inputDesc);
        //             const fileWallpaper = $('input[name="wallpapers[0]"]')[0].files;
        //             formData.append('files[wallpaper]', fileWallpaper[0]);
        //             const fileSource    = $('input[name="sources[0]"]')[0].files;
        //             formData.append('files[source]', fileSource[0]);
        //             /* truyền thêm wallpaper_id */
        //             formData.append('wallpaper_id', idWallpaper);
        //             $.ajax({
        //                 url: "{{ route('admin.wallpaper.changeWallpaperWithSource') }}",
        //                 type: "post",
        //                 data: formData,
        //                 processData: false, // Không xử lý dữ liệu gửi đi
        //                 contentType: false, // Không thiết lập header Content-Type
        //                 headers: {
        //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                 },
        //             }).done(function (response) {
        //                 /* load lại box */
        //                 loadOneRow(idWallpaper);
        //                 /* tắt modal và loading */
        //                 addLoadingModal();
        //                 $('#modalFormWallpaper').modal('hide');
        //             }).fail(function (jqXHR, textStatus, errorThrown) {
        //                 console.error("Ajax request failed: " + textStatus, errorThrown);
        //             });
        //         }   
        //         /* tải lại source => dùng cho loadOneRow */
        //         setTimeout(function(){
        //             loadImageFromGoogleCloud();
        //         }, 300);             
        //     }else {
        //         /* có 1 vài trường required bị bỏ trống */
        //         let messageError        = 'Không được bỏ trống trường ';
        //         tmp.forEach(function(value, index, array){
        //             switch (true) {
        //                 case /^sources\[\d+\]$/.test(value):
        //                     var index = parseInt(value.match(/\d+/)[0]) + 1;
        //                     messageError += '<strong>Ảnh gốc ' + index + '</strong>';
        //                     break;
        //                 case value === 'wallpapers[]':
        //                     messageError += '<strong>Wallpaper</strong>';
        //                     break;
        //                 case value === 'file_name':
        //                     messageError += '<strong>Đường dẫn ảnh</strong>';
        //                     break;
        //                 case value === 'name':
        //                     messageError += '<strong>Alt wallpaper</strong>';
        //                     break;
        //                 default:
        //                     break;
        //             }
        //             if(index!=parseInt(tmp.length-1)) messageError += ', ';
        //         })
        //         $('#js_validateFormModalHotelContact_message').css('display', 'block').html(messageError);
        //     }
        // }

        // function uploadWallpaperWithSource(inputName, inputDesc, fileWallpapers) {
        //     return new Promise(function (resolve, reject) {
        //         // Mảng chứa tất cả các promises từ các request AJAX
        //         var promises = [];
        //         for (var i = 0; i < fileWallpapers.length; i++) {
        //             var formData = new FormData();
        //             formData.append('name', inputName);
        //             formData.append('description', inputDesc);
        //             formData.append('count', i);
        //             formData.append('files[wallpaper]', fileWallpapers[i]);
                    
        //             const inputSource = 'sources[' + i + ']';
        //             const fileSource  = $('input[name="' + inputSource + '"]')[0].files;
        //             formData.append('files[source]', fileSource[0]);
        //             // Thực hiện request AJAX và đưa promise vào mảng
        //             promises.push(
        //                 $.ajax({
        //                     url: "{{ route('admin.wallpaper.uploadWallpaperWithSource') }}",
        //                     type: "post",
        //                     dataType: 'json',
        //                     data: formData,
        //                     processData: false,
        //                     contentType: false,
        //                     timeout: 0,
        //                     headers: {
        //                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                     },
        //                 })
        //             );
        //         }
        //         // Khi tất cả các promises đã hoàn thành, resolve Promise chính
        //         Promise.all(promises)
        //             .then(function (responses) {
        //                 // Các responses chứa kết quả từ mỗi request AJAX
        //                 responses.forEach(function (response) {
        //                     // Thêm box mới upload vào
        //                     var newDiv = $("<div>", {
        //                         id: "js_deleteWallpaperAndSource_" + response.id,
        //                         class: "wallpaperBox_item",
        //                         html: response.content
        //                     });
        //                     // Thêm div mới vào đầu của container
        //                     newDiv.prependTo("#js_uploadAndChangeWallpaperWithSource_idWrite");
        //                 });
        //                 // Gọi resolve để kết thúc Promise
        //                 resolve();
        //             })
        //             .catch(function (error) {
        //                 console.error("One or more AJAX requests failed:", error);
        //                 // Gọi reject để kết thúc Promise với lỗi
        //                 reject();
        //             });
        //     });
        // }

        function uploadAndChangeWallpaperWithSource(idWallpaper = '') {
            const tmp = validateForm('formWallpaperWithSource');
            if(tmp == ''){
                // Bật loading
                addLoadingModal();
                // Lấy thêm các input khác
                var inputName = $('#name').val();
                var inputDesc = $('#description').val();
                
                if(idWallpaper == ''){
                    var fileWallpapers = $('#wallpapers')[0].files;
                    // Gọi upload và xử lý sau khi hoàn thành
                    uploadWallpaperWithSource(inputName, inputDesc, fileWallpapers)
                        .then(function () {
                            // Tất cả các công việc đã hoàn thành
                            addLoadingModal();
                            $('#modalFormWallpaper').modal('hide');
                            setTimeout(function() {
                                loadImageFromGoogleCloud();
                            }, 300);
                        })
                        .catch(function () {
                            // Xử lý lỗi nếu có (có thể thêm thông báo lỗi ở đây)
                        });
                } else {
                    // var formData = new FormData();
                    // formData.append('name', inputName);
                    // formData.append('description', inputDesc);
                    // const fileWallpaper = $('input[name="wallpapers[0]"]')[0].files;
                    // formData.append('files[wallpaper]', fileWallpaper[0]);
                    // const fileSource = $('input[name="sources[0]"]')[0].files;
                    // formData.append('files[source]', fileSource[0]);
                    // formData.append('wallpaper_id', idWallpaper);
                    
                    // $.ajax({
                    //     url: "{{ route('admin.wallpaper.changeWallpaperWithSource') }}",
                    //     type: "post",
                    //     data: formData,
                    //     processData: false,
                    //     contentType: false,
                    //     headers: {
                    //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    //     }
                    // }).done(function (response) {
                    //     loadOneRow(idWallpaper);
                    //     addLoadingModal();
                    //     $('#modalFormWallpaper').modal('hide');
                    // }).fail(function (jqXHR, textStatus, errorThrown) {
                    //     console.error("Ajax request failed: " + textStatus, errorThrown);
                    // });
                }
            } else {
                let messageError = 'Không được bỏ trống trường ';
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
                    if(index != parseInt(tmp.length - 1)) messageError += ', ';
                });
                $('#js_validateFormModalHotelContact_message').css('display', 'block').html(messageError);
            }
        }
        function retryAjax(requestFunc) {
            return new Promise((resolve, reject) => {
                const attempt = () => {
                    requestFunc().done(resolve).fail(() => {
                        console.error("Retrying failed request...");
                        attempt();
                    });
                };
                attempt();
            });
        }
        function uploadWallpaperWithSource(inputName, inputDesc, fileWallpapers) {
            return new Promise(function (resolve, reject) {
                var promises = [];
                
                for (var i = 0; i < fileWallpapers.length; i++) {
                    const formData = new FormData();
                    formData.append('name', inputName);
                    formData.append('description', inputDesc);
                    formData.append('count', i);
                    formData.append('files[wallpaper]', fileWallpapers[i]);
                    
                    const inputSource = 'sources[' + i + ']';
                    const fileSource = $('input[name="' + inputSource + '"]')[0].files;
                    formData.append('files[source]', fileSource[0]);

                    // Tạo hàm request AJAX
                    const requestFunc = () => $.ajax({
                        url: "{{ route('admin.wallpaper.uploadWallpaperWithSource') }}",
                        type: "post",
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                        timeout: 0,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    // Đẩy request vào mảng với cơ chế retry
                    promises.push(retryAjax(requestFunc));
                }

                Promise.all(promises)
                    .then(function (responses) {
                        responses.forEach(function (response) {
                            var newDiv = $("<div>", {
                                id: "js_deleteWallpaperAndSource_" + response.id,
                                class: "wallpaperBox_item",
                                html: response.content
                            });
                            newDiv.prependTo("#js_uploadAndChangeWallpaperWithSource_idWrite");
                        });
                        resolve();
                    })
                    .catch(function (error) {
                        console.error("One or more AJAX requests failed:", error);
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
                url         : "{{ route('admin.wallpaper.loadModalUploadAndEdit') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'    : '{{ csrf_token() }}', 
                    wallpaper_id : idWallpaper
                }
            }).done(function(data){
                $('#js_loadModalUploadAndEdit_box').html(data);
                // console.log($('#modalFormWallpaper').html());
            });
        }

        function deleteWallpaperAndSource(idBox, idWallpaper){
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

        function addLoading(idBox, heightBox = 300){
            const htmlLoadding  = '<div style="display:flex;align-items:center;justify-content:center;height:'+heightBox+'px;width:100%;"><div class="spinner-grow text-primary me-1" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            $('#'+idBox).html(htmlLoadding);
        }

    </script>
@endpush