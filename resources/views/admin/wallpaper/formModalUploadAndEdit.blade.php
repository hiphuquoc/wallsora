<div class="modal-content" style="position:relative;">
    <div class="modal-body">
        <div class="formWallpaperBox">
            <div id="js_addFormUploadSource_box" class="formWallpaperBox_gallery"> 
                <!-- load Ajax -->
                @if(!empty($wallpaper))
                    @include('admin.wallpaper.oneFormUploadSourceAndWallpaper', ['idBox' => 0, 'wallpaper' => $wallpaper])
                @endif
            </div>
            <div class="formWallpaperBox_form formBox">
                <div class="formBox_full">
                    @if(empty($wallpaper)) <!-- trương hợp edit -->
                        <div class="formBox_full_item">
                            <label class="form-label" for="wallpapers" style="color:#26cf8e;font-size:1.05rem;font-weight:normal;"><i class="fa-solid fa-cloud-arrow-up"></i> Tải trước wallpaper</label>
                            <input class="form-control" type="file" id="wallpapers" name="wallpapers[]" onchange="addFormUploadSource(this);" multiple="" required />
                        </div>
                    @endif
                    {{-- <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="file_name">Đường dẫn ảnh</label>
                        <textarea class="form-control" id="file_name" name="file_name" rows="1" required>{{ $wallpaper->file_name ?? null }}</textarea>
                    </div> --}}
                    <div class="formBox_full_item">
                        <label class="form-label inputRequired" for="name">Alt ảnh</label>
                        <textarea class="form-control" id="name" name="name" rows="1" required>{{ $wallpaper->name ?? null }}</textarea>
                    </div>
                    <div class="formBox_full_item">
                        <label class="form-label" for="description">Mô tả ngắn</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer -->
    <div class="modal-footer">
        <div id="js_validateFormModalHotelContact_message" class="error" style="display:none;"><!-- Load Ajax --></div>
        <button type="button" class="btn btn-secondary waves-effect waves-float waves-light" data-bs-dismiss="modal" aria-label="Đóng">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" onClick="uploadAndChangeWallpaperWithSource({{ $wallpaper->id ?? null }});">Tải lên</button>
    </div>
    <!-- icon loading -->
    <div id="js_addLoadingModal" style="position:absolute;top:0;left:0;display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:rgba(255,255,255,0.5);display:none;">
        <div class="spinner-grow text-primary me-1" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.formWallpaperBox_gallery').slick({
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: true,
        prevArrow: '<div class="slick-arrow slick-prev" aria-label="Slide trước" style="cursor:pointer;"><i class="fa-solid fa-angle-left"></i></div>',
        nextArrow: '<div class="slick-arrow slick-next" aria-label="Slide tiếp theo" style="cursor:pointer;"><i class="fa-solid fa-angle-right"></i></div>'
    });

    /* sữa lỗi width child của slick = 0 */
    $('#modalFormWallpaper').on('shown.bs.modal', function () {
        refreshSlick('formWallpaperBox_gallery');
    });

    function refreshSlick(className){
        $('.'+className).slick('refresh');
    }
</script>