@push('headCustom')
    <!-- BEGIN: SLICK -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <!-- END: SLICK -->
@endpush

<div id="js_toogleModalViewImageFull" class="viewImageFullBox">
    <div class="viewImageFullBox_box">
       <div class="viewImageFullBox_box_image notYetSlick">
            @foreach($item->prices as $price)
                @php
                    $title = $price->name ?? $price->seo->title ?? null;
                @endphp
                @foreach($price->wallpapers as $wallpaper)
                    @if(!empty($wallpaper->infoWallpaper))
                        <div>
                            <img src="{{ \App\Helpers\Image::getUrlImageCloud($wallpaper->infoWallpaper->file_cloud_wallpaper) }}" data-src="{{ \App\Helpers\Image::getUrlImageCloud($wallpaper->infoWallpaper->file_cloud_wallpaper) }}" alt="{{ $title }}" title="{{ $title }}" loading="lazy" />
                            {{-- <div class="viewImageFullBox_box_image_background"></div> --}}
                            <div class="viewImageFullBox_box_image_backgroundTopMore"></div>
                            <div class="viewImageFullBox_box_image_backgroundSideMore"></div>
                        </div>
                    @endif
                @endforeach
            @endforeach
       </div>
       <div class="viewImageFullBox_box_close" onClick="toogleModalViewImageFull(0, 'close');">
        <svg><use xlink:href="#icon_close"></use></svg>
        </div>
    </div>
    <div class="viewImageFullBox_background" onClick="toogleModalViewImageFull(0, 'close');"></div>
</div>

@pushonce('scriptCustom')
    <!-- BEGIN: SLICK -->
    <script defer type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <!-- END: SLICK -->
    <script type="text/javascript">
        /* ẩn hiện riêng cho modal view image all */
        function toogleModalViewImageFull(numberActive = 0, action = null){
            const elementSlick = $('.viewImageFullBox_box_image');
            /* kiểm tra slick rồi thôi */
            if(elementSlick.hasClass('notYetSlick')){
                /* load image phần tử active trước */ 
                const imageActive = elementSlick.children().eq(numberActive);
                imageActive.attr('src', imageActive.attr('data-src'));
                imageActive.removeAttr('data-src');
                /* load image phần tử còn lại*/
                elementSlick.find('img[data-src]').each(function(){
                    setTimeout(() => {
                        $(this).attr('src', $(this).attr('data-src'));
                        $(this).removeAttr('data-src');
                    }, 0);
                });
                /* slick */
                elementSlick.slick({
                    infinite: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    prevArrow:"<button type='button' class='slick-prev pull-left'><i class='fa fa-angle-left' aria-hidden='true'></i></button>",
                    nextArrow:"<button type='button' class='slick-next pull-right'><i class='fa fa-angle-right' aria-hidden='true'></i></button>",
                    arrows: true,
                    dots: true,
                    customPaging: function(slider, i) {
                        return '<button type="button" role="button" tabindex="0" />'
                    }
                });
                elementSlick.removeClass('notYetSlick');
            }
            // Sử dụng setTimeout để đợi đến khi Slick hiển thị
            setTimeout(function(){
                // Active phần tử được truyền vào
                elementSlick.slick('slickGoTo', numberActive);
            }, 0);
            /* cập nhật lại button */
            elementSlick.on('afterChange', function(event, slick, currentSlide){
                // Lấy ra index của phần tử active hiện tại
                var activeIndex = slick.currentSlide;
                // Cập nhật lại active cho các nút điều hướng
                $('.slick-prev').removeClass('slick-disabled');
                $('.slick-next').removeClass('slick-disabled');
                if(activeIndex == 0){
                    $('.slick-prev').addClass('slick-disabled');
                }
                else if(activeIndex == slick.slideCount - 1){
                    $('.slick-next').addClass('slick-disabled');
                }
            });
            /* mở modal */
            const elementModal  = $('#js_toogleModalViewImageFull');
            const flag          = elementModal.css('display');
            /* tooggle */
            if(action==null){
                if(flag=='none'){
                    elementModal.css('display', 'flex');
                    $('body').css('overflow', 'hidden');
                }else {
                    elementModal.css('display', 'none');
                    $('body').css('overflow', 'unset');
                }
            }
            /* đóng */
            if(action=='close'){
                elementModal.css('display', 'none');
                $('body').css('overflow', 'unset');
            }
            /* mở */
            if(action=='open'){
                elementModal.css('display', 'flex');
                $('body').css('overflow', 'hidden');
            }
        }
    </script>
@endpushonce