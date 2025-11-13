@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Ảnh</div>
<!-- ===== START: SEARCH FORM ===== -->

<div class="searchBox">
    <div class="searchBox_item">
        <form id="formSearch" method="get" action="{{ route('admin.image.list') }}">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}">
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button>
            </div>
        </form>
    </div>
    <div class="searchBox_item">
        <form id="formUpload" method="get" enctype="multipart/form-data" multiple>
        @csrf
            <div class="input-group">
                <input class="form-control" type="file" name="image_upload[]" multiple>
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tải lên">Tải lên</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== END: SEARCH FORM ===== -->
<div id="js_uploadImage_idWrite" class="imageBox" style="padding-bottom:2rem;">
    @if(!empty($list))
        @foreach($list as $infoImageCloud)
            @include('admin.image.oneRow', compact('infoImageCloud'))
        @endforeach
    @endif
</div>

<!-- ===== START: MODAL ===== -->
<form id="formModal" method="post" action="{{ route('admin.image.changeImage') }}" enctype="multipart/form-data">
@csrf
    <!-- Input Hidden -->
    <div id="modalImage" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <h4>Thay đổi ảnh</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="js_loadModal_message" class="error" style="margin-bottom:1rem;display:none;">Các trường bắt buộc không được để trống!</div>
                    <div id="js_loadModal_body">
                        <!-- load Ajax -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Đóng">Đóng</button>
                    <button id="js_loadModal_action" type="submit" class="btn btn-primary" tableindex="0" aria-label="Xác nhận">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- ===== END:: MODAL ===== -->
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">

        function loadModal(idImageCloud){
            $.ajax({
                url         : "{{ route('admin.image.loadModal') }}",
                type        : "get",
                dataType    : "html",
                data        : {
                    image_cloud_id : idImageCloud, 
                }
            }).done(function(data){
                $('#js_loadModal_body').html(data);
            });
        }

        /* ChangeImage submit files */
        $("#formModal").on('submit', function(e) {
            e.preventDefault();
            const idImageCloud  = $('#image_cloud_id').val();
            $.ajax({
                url             : "{{ route('admin.image.changeImage') }}",
                type            : "POST",
                dataType        : 'json',
                data            : new FormData(this),
                contentType     : false,
                cache           : false,
                processData     : false,
                success         : function(data){
                    if(data.flag){
                        /* tải lại imageBox */
                        loadImageBox(idImageCloud);
                        /* tắt modal */
                        $('#modalImage').modal('hide');
                    }
                }
            });
        });

        function loadImageBox(idImageCloud){
            // const heightBox = $('#'+idBox).outerHeight();
            const idBox         = 'js_removeImage_'+idImageCloud;
            const elementBox    = $('#'+idBox);
            const heightBox     = elementBox.outerHeight();

            addLoading(idBox, heightBox);
            $.ajax({
                url         : "{{ route('admin.image.loadImage') }}",
                type        : "get",
                dataType    : "html",
                data        : { 
                    image_cloud_id  : idImageCloud
                }
            }).done(function(data){
                setTimeout(() => {
                    $('#'+idBox).replaceWith(data);
                }, 500);
            });
        }

        /* Upload and append thêm ảnh */
        $("#formUpload").on('submit', function(e) {
            e.preventDefault();
            // Mở loading
            openCloseFullLoading();
            $.ajax({
                url             : "{{ route('admin.image.uploadImages') }}",
                type            : "POST",
                dataType        : 'json',
                data            : new FormData(this),
                contentType     : false,
                cache           : false,
                processData     : false,
                success         : function(data){
                    const elementWrite  = $('#js_uploadImage_idWrite');
                    let contentOld      = elementWrite.html();
                    elementWrite.html(data.content+contentOld);
                    document.getElementById("formUpload").reset();
                    openCloseFullLoading();
                }
            });
        });

        function removeImage(id){
            const idBox         = 'js_removeImage_'+id;
            const elementBox    = $('#'+idBox);
            const heightBox     = elementBox.outerHeight();
            addLoading(idBox, heightBox);
            $.ajax({
                url         : "{{ route('admin.image.removeImage') }}",
                type        : "post",
                dataType    : "html",
                data        : { 
                    '_token'        : '{{ csrf_token() }}', 
                    image_cloud_id  : id
                }
            }).done(function(data){
                setTimeout(() => {
                    if(data==true) elementBox.hide();
                }, 500)
            });
        }

        function addLoading(idBox, heightBox = 300){
            const htmlLoadding  = '<div style="display:flex;align-items:center;justify-content:center;height:'+heightBox+'px;"><div class="spinner-grow text-primary me-1" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            $('#'+idBox).html(htmlLoadding);
        }

    </script>
@endpush