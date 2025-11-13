<div class="modal-content" style="position:relative;">
    <input type="hidden" name="wallpaper_info_id" value="{{ $wallpaper->id ?? null }}" />
    <div class="modal-body">
        <div>
            @if(empty($wallpaper)) <!-- trương hợp edit -->
                <div class="formBox_full_item" style="margin-bottom:1rem;">
                    <label class="form-label" for="wallpapers" style="color:#26cf8e;font-size:1.05rem;font-weight:normal;"><i class="fa-solid fa-cloud-arrow-up"></i> Tải trước wallpaper</label>
                    <input class="form-control" type="file" id="wallpapers" name="wallpapers[]" onchange="addFormUpload(this);" multiple="" required />
                </div>
            @endif
            <div id="js_addFormUpload_box" class="formFreeWallpaperBox">

                <!-- load Ajax -->
                @if(!empty($wallpaper))
                    @include('admin.freeWallpaper.oneFormUpload', ['idBox' => 0, 'wallpaper' => $wallpaper])
                @endif

            </div>
        </div>
    </div>
    <!-- footer -->
    <div class="modal-footer">
        <div id="js_validateFormModalHotelContact_message" class="error" style="display:none;"><!-- Load Ajax --></div>
        <button type="button" class="btn btn-secondary waves-effect waves-float waves-light" data-bs-dismiss="modal" aria-label="Đóng">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" onClick="uploadAndChangeWallpaper({{ $wallpaper->id ?? null }});">Tải lên</button>
    </div>
    <!-- icon loading -->
    <div id="js_addLoadingModal" style="position:absolute;top:0;left:0;display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:rgba(255,255,255,0.5);display:none;">
        <div class="spinner-grow text-primary me-1" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.formFreeWallpaperBox').slick({
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
        refreshSlick('formFreeWallpaperBox');
    });

    function refreshSlick(className){
        $('.'+className).slick('refresh');
    }
</script>