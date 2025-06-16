<!-- BEGIN: Google Analytics -->
@if(env('APP_ENV')=='production')
    <script defer>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
    
        function loadGoogleAnalytics() {
            var script = document.createElement('script');
            script.src = 'https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}';
            document.head.appendChild(script);
    
            gtag('js', new Date());
            gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
        }
    
        window.addEventListener('scroll', loadGoogleAnalytics, { once: true });
    </script>
@endif
<!-- END: Google Analytics -->

<!-- BEGIN: Jquery -->
<script defer src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- END: Jquery -->
<script defer type="text/javascript">

    document.addEventListener('DOMContentLoaded', function() {
        /* check để xem có cookie csrf chưa (do lần đầu truy cập trang không có lỗi google login) */
        // checkToSetCsrfFirstTime();
        
        /* lazyload ảnh lần đầu */
        lazyload();
        /* lazyload ảnh khi scroll */
        $(window).on('scroll', function() {
            lazyload();
        });
        
        /* tải lại view sort cart */
        viewSortCart();

        // /* hiệu ứng */
        // $('.effectFadeIn').each(function(){
        //     $(this).css('opacity', 0);
        // });

        // buildTocContentMain('js_articleBox');

        // $('img').each(function() {
        //     if (!$(this).attr('alt') || !$(this).attr('title')) {
        //         console.log(this);
        //     }
        // });

        /* check login để hiện thị button */
        checkLoginAndSetShow();

        /* thông tin khách hàng */
        settingGPSVisitor();
        settingTimezoneVisitor();

        // headerSide
        var Url = decodeURIComponent(document.URL);
        $('.headerSide .filterLinkSelected a').each(function(){
            const regex = new RegExp("^" + $(this).attr('href'));
            if(regex.test(Url)) {
                /* mở thẻ cha chứa phần tử trang hiện tại */
                $(this).closest('ul').addClass('active');
                /* mở luôn thẻ chứa các phần tử con của trang hiện tại */
                $(this).next('ul').addClass('active');
                $(this).closest('ul').children().each(function(){
                    $(this).removeClass('selected');
                })
                
                $(this).closest('li').addClass('selected');
                /* thay icon */
                $(this).closest('ul').closest('li').find('.actionMenu').addClass('isOpen');
            }
        });
        /* tải status collapsed */
        getStatusCollapse();

    });

    function settingGPSVisitor() {
        // Kiểm tra xem đã thiết lập GPS thành công (dựa trên localStorage)
        if (localStorage.getItem('gps_set') == 'true') {
            return; // Nếu đã thiết lập thành công, không làm gì thêm
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Tạo URL với tham số truy vấn
                const url = new URL('{{ route("main.settingGPSVisitor") }}');
                url.searchParams.append('latitude', latitude);
                url.searchParams.append('longitude', longitude);

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Lưu trạng thái đã thiết lập GPS vào localStorage
                    localStorage.setItem('gps_set', data.flag);
                    // refresh
                    if(data.flag) location.reload();
                })
                .catch(error => {
                    console.error('Error fetching GPS data:', error);
                });
            }, function(error) {
                // console.error('Error retrieving location:', error);
            });
        } else {
            // console.error('Geolocation is not supported by this browser.');
        }
    }

    function settingTimezoneVisitor(){
        // Lấy múi giờ từ thiết bị người dùng
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const url = new URL('{{ route("main.settingTimezoneVisitor") }}');
        url.searchParams.append('timezone', timezone);
        // Gửi múi giờ đến server qua GET request
        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token nếu cần cho GET
            }
        })
        .then(response => response.json())
        .then(data => {
            // Lưu trạng thái đã thiết lập GPS vào localStorage
            localStorage.setItem('timezone_set', data.flag);
        })
        .catch(error => {
            console.error('Error setting timezone:', error);
        });
    }

    // function settingIpVisitor() {
    //     // Kiểm tra xem đã thiết lập GPS thành công (dựa trên localStorage)
    //     if (localStorage.getItem('ip_set') == 'true') {
    //         return; // Nếu đã thiết lập thành công, không làm gì thêm
    //     }
    //     // Tạo URL với tham số truy vấn
    //     const url = new URL('{{ route("main.settingIpVisitor") }}');
    //     fetch(url, {
    //         method: 'GET',
    //         headers: {
    //             'Content-Type': 'application/json',
    //         }
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         // Lưu trạng thái đã thiết lập Ip vào localStorage
    //         localStorage.setItem('ip_set', data.flag);
    //     })
    //     .catch(error => {
    //         console.error('Error fetching Ip data:', error);
    //     });
    // }

    // function getInfoVisitor(){
    //     /* time zone */
    //     const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    // }
    
    function lazyload(){
        /* đối với ảnh */
        $('img.lazyload').each(function() {
            if (!$(this).hasClass('loaded')) {
                var distance = $(window).scrollTop() - $(this).offset().top + 900;
                if (distance > 0) {
                    $(this).attr('src', $(this).attr('data-src'));
                    $(this).addClass('loaded').removeClass('loading_1');
                }
            }
        });
        /* đối với div dùng background */
        $('div.lazyload').each(function() {
            if (!$(this).hasClass('loaded')) {
                var distance = $(window).scrollTop() - $(this).offset().top + 900;
                if (distance > 0) {
                    $(this).css({
                        background  : 'url("'+$(this).attr('data-src')+'") no-repeat center center / cover'
                    });
                    $(this).addClass('loaded');
                }
            }
        });
    }

    /* đang xây dựng */
    function noticeContrustion(){
        alert('Tính năng thanh toán online đang được xây dựng. Vui lòng quay lại sau hoặc liên hệ Zalo 0968617168');
    }

    function openCloseElemt(idElemt){
        let displayE    = $('#' + idElemt).css('display');
        if(displayE=='none'){
            $('#' + idElemt).css('display', 'block');
            $('body').css('overflow', 'hidden');
        }else {
            $('#' + idElemt).css('display', 'none');
            $('body').css('overflow', 'unset');
        }
    }
    
    // /* fixed menu khi scroll đối với giao diện nhỏ hơn 991px */
    // window.addEventListener('scroll', function() {
    //     // const heightMenu = $('.headerMain').outerHeight();
    //     if($(window).scrollTop()>300){
    //         $('.headerMain').addClass('fixed');
    //         $('.headerMain').css('opacity', '1');
    //     }
    //     if($(window).scrollTop()<=300){
    //         $('.headerMain').removeClass('fixed');
    //         $('.headerMain').css('opacity', '0');
    //     }
    //     if($(window).scrollTop()<55){
    //         $('.headerMain').removeClass('fixed');
    //         $('.headerMain').css('opacity', '1');
    //     }
    // });
    // /* ===== Hiệu ứng ===== */
    // const percentHeightScreenEffect = 1.3;
    // /* hiệu ứng fade in */
    // window.addEventListener('scroll', function() {
    //     $('.effectFadeIn').each(function(){
    //         const positionElement   = $(this).offset().top;
    //         const heightWindow      = $(window).height();
    //         const positionScroll    = $(window).scrollTop();
    //         if(parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
    //             $(this).animate({
    //                 opacity : 1,
    //             }, 800);
    //         }
    //     })
    // });
    // /* hiệu ứng rơi xuống => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    // window.addEventListener('scroll', function() {
    //     $('.effectDropdown').each(function(){
    //         /* ẩn trước */
    //         if(!$(this).hasClass('alreadyEffectDropdown')) $(this).css('opacity', 0);
    //         /* thao tác */
    //         const positionElement   = $(this).offset().top;
    //         const heightWindow      = $(window).height();
    //         const positionScroll    = $(window).scrollTop();
    //         if(!$(this).hasClass('alreadyEffectDropdown')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
    //                 const marginTopReal = parseInt($(this).css('margin-top'));
    //                 $(this).css({
    //                     'margin-top'    : (marginTopReal - 200)+'px'
    //                 });
    //                 $(this).animate({
    //                     'margin-top'    : marginTopReal+'px',
    //                     'opacity'       : 1
    //                 }, 800);
    //                 /* thực hiện rồi thì không thực hiện nữa */
    //                 $(this).addClass('alreadyEffectDropdown');
    //         }
    //     })
    // });
    // /* hiệu ứng xuất hiện từ trái qua phải => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    // window.addEventListener('scroll', function() {
    //     $('.effectLeftToRight').each(function(){
    //         /* ẩn trước */
    //         if(!$(this).hasClass('alreadyEffectLeftToRight')) $(this).css('opacity', 0);
    //         /* thao tác */
    //         const positionElement           = $(this).offset().top;
    //         const heightWindow              = $(window).height();
    //         const positionScroll            = $(window).scrollTop();
    //         if(!$(this).hasClass('alreadyEffectLeftToRight')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
    //                 const marginLeftReal    = parseInt($(this).css('margin-left'));
    //                 $(this).css({
    //                     'margin-left'   : (marginLeftReal - 200)+'px'
    //                 });
    //                 $(this).animate({
    //                     'margin-left'    : marginLeftReal+'px',
    //                     'opacity'       : 1
    //                 }, 800);
    //                 /* thực hiện rồi thì không thực hiện nữa */
    //                 $(this).addClass('alreadyEffectLeftToRight');
    //         }
    //     })
    // });
    // /* hiệu ứng xuất hiện từ dưới lên => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    // window.addEventListener('scroll', function() {
    //     $('.effectBottomToTop').each(function(){
    //         /* ẩn trước */
    //         if(!$(this).hasClass('alreadyEffectBottomToTop')) $(this).css('opacity', 0);
    //         /* thao tác */
    //         const positionElement           = $(this).offset().top;
    //         const heightWindow              = $(window).height();
    //         const positionScroll            = $(window).scrollTop();
    //         if(!$(this).hasClass('alreadyEffectBottomToTop')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
    //                 const marginTopReal     = parseInt($(this).css('margin-top'));
    //                 $(this).css({
    //                     'margin-top'    : (marginTopReal + 200)+'px'
    //                 });
    //                 $(this).animate({
    //                     'margin-top'    : marginTopReal+'px',
    //                     'opacity'       : 1
    //                 }, 800);
    //                 /* thực hiện rồi thì không thực hiện nữa */
    //                 $(this).addClass('alreadyEffectBottomToTop');
    //         }
    //     })
    // });
    /* Go to top */
    mybutton 					    = document.getElementById("smoothScrollToTop");
    mybutton.style.display 	        = "none";
    window.onscroll                 = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
            mybutton.style.display 	= "flex";
        } else {
            mybutton.style.display 	= "none";
        }
    }
    function smoothScrollToTop() {
        // const currentScroll = document.documentElement.scrollTop;
        // if (currentScroll > 0) {
        //     window.requestAnimationFrame(smoothScrollToTop);
        //     window.scrollTo(0, currentScroll - currentScroll / 8);
        // }
        document.documentElement.scrollTop          = 0;
    }
    /* link to a href #id smooth */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định

            let targetId = this.getAttribute('href').substring(1); // Lấy ID từ href
            let targetElement = document.getElementById(targetId);

            if (targetElement) {
                let headerOffset = 80; // Điều chỉnh khoảng cách mong muốn
                let elementPosition = targetElement.getBoundingClientRect().top + window.scrollY;
                let offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth" // Cuộn mượt
                });
            }
        });
    });
    /* toggle menu mobile */
    function toggleMenuMobile(idElement){
        const element   = $('#'+idElement);
        const displayE  = element.css('display');
        if(displayE=='none'){
            /* hiển thị */
            element.css('display', 'flex');
            $('body').css('overflow', 'hidden');
            $('#js_blurBackground').addClass('blurBackground');
            $('.menuTopBackground').addClass('blurBackground');
            $('.backgroundBlurMobileMenu').css('display', 'block');
        }else {
            element.css('display', 'none');
            $('body').css('overflow', 'unset');
            $('#js_blurBackground').removeClass('blurBackground');
            $('.menuTopBackground').removeClass('blurBackground');
            $('.backgroundBlurMobileMenu').css('display', 'none');
        }
    }
    /* thay đổi option của product phần hiển thị ngoài */
    function changeOption(idShow){
        const elemtShow     = $('#'+idShow);
        const parent        = elemtShow.parent();
        /* ẩn tất cả phần tử con */
        parent.children().each(function(){
            $(this).removeClass('show').addClass('hide');
        })
        /* bật lại phần tử được chọn */
        elemtShow.removeClass('hide').addClass('show');
        /* lazy load cho ảnh trong phần tử */
        elemtShow.find('img.lazyloadAfter').each(function(){
            $(this).addClass('lazyload');
            lazyload();
        })
    }
    /* hiện thông báo cho sản phẩm vào giỏ hàng thành công */
    function openCloseModal(idModal, action = null){
        const elementModal  = $('#'+idModal);
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
    // /* tăng giảm số lượng input quantity */
    // function plusMinusQuantity(idInput, action){
    //     const elementInput  = $('#'+idInput);
    //     const valueInput    = elementInput.val();
    //     if(action=='minus'){
    //         if(valueInput>1) elementInput.val(parseInt(valueInput)-1);
    //     }else {
    //         elementInput.val(parseInt(valueInput)+1);
    //     }
    // }
    /* thêm sản phẩm vào giỏ hàng */
    function addToCart(idProduct, idPrice, type) {
        let dataForm = {};
        dataForm.product_info_id = idProduct;
        dataForm.product_price_id = idPrice;
        dataForm.type = type;
        dataForm.language = $('#language').val();
        const queryString = new URLSearchParams(dataForm).toString();

        fetch("/addToCart?" + queryString, {
            method: 'GET',
            mode: 'cors',
            // headers: {
            //     'Content-Type': 'application/json',
            //     'X-CSRF-TOKEN': '{{ csrf_token() }}'
            // }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            /* reset lại value số lượng */
            $('#js_addToCart_quantity').val(1);
            /* hiện thông báo trong 5s */
            $('#js_addToCart_idWrite').html(data);
            openCloseModal('cartMessage');
            /* cập nhật lại thông tin giỏ hàng */ 
            viewSortCart();
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* tải lại thông tin icon giỏ hàng */
    function viewSortCart() {
        let dataForm = {};
        dataForm.language = $('#language').val();            
        const queryString = new URLSearchParams(dataForm).toString();
        fetch('/viewSortCart?' + queryString, {
            method: 'GET',
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            $('#js_viewSortCart_idWrite').html(data); // Sử dụng dữ liệu nhận được từ phản hồi để cập nhật HTML
        })
        .catch(error => {
            console.error('There was a problem with your fetch operation:', error);
        });
    }
    /* xóa sản phẩm khỏi cart */ 
    function removeProductCart(idProduct, idProductPrice, idRow, idTotal, idCount) {
        /* tải loading */ 
        loadLoading(idRow);
        const language = $('#language').val();
        fetch("/removeProductCart?product_info_id=" + idProduct + "&product_price_id=" + idProductPrice + "&language=" + language, {
            method: 'GET',
            mode: 'cors'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            /* cart trống */
            if (response.empty_cart !== '') $('#js_checkEmptyCart_idWrite').html(response.empty_cart);
            $('#' + idTotal).html(response.total);
            $('#' + idCount).html(response.count);
            setTimeout(() => {
                $('#' + idRow).remove();
            }, 300);
            /* trong page giỏ hàng => tải lại thành tiền */
            if (typeof loadTotalCart === 'function') {
                loadTotalCart($('#payment_method_info_id').val());
            }
            /* trường hợp xóa không còn sản phẩm */
            if (response.isEmpty !== '') {
                $('#js_checkEmptyCart_idWrite').html(response.isEmpty);
                $('#js_scrollMenu').remove();
            }
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* add loading icon */
    function loadLoading(action = 'show') {
        if(action == 'show'){
            $('.loadingBox').addClass('show');
        }else if(action == 'hide'){
            $('.loadingBox').removeClass('show');
        }else {
            $('.loadingBox').toggleClass('show');
        }
    }
    /* tính năng registry email ở footer */
    function submitFormRegistryEmail(idForm) {
        event.preventDefault();
        const inputEmail = $('#' + idForm).find('[name*=registry_email]');
        const valueEmail = inputEmail.val();
        if (isValidEmail(valueEmail)) {
            fetch("/registryEmail?registry_email=" + encodeURIComponent(valueEmail), {
                method: 'GET',
                mode: 'cors'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                /* bật thông báo */
                setMessageModal(response.title, response.content);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        } else {
            inputEmail.val('');
            inputEmail.attr('placeholder', 'Email không hợp lệ!');
        }
    }
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    /* toc content */
    function buildTocContentMain(idElement){
        fixedTocContentIcon();
        setHeightTocFixed();

        $(window).resize(function() {
            fixedTocContentIcon();
            setHeightTocFixed();
        });

        $('.tocFixedIcon, .tocContentMain.tocFixed .tocContentMain_close').click(function(){
            let elementMenu = $('.tocContentMain.tocFixed');
            let displayMenu = elementMenu.css('display');
            if(displayMenu=='none'){
                elementMenu.css('display', 'block');
            }else {
                elementMenu.css('display', 'none');
            }
            // fixedTocContentIcon();
        });

        $('.tocContentMain_title, .tocContentMain_close').click(function(){
            let elemtMenu   = $('.tocContentMain .tocContentMain_list');
            let displayMenu = elemtMenu.css('display');
            if(displayMenu=='none'){
                elemtMenu.css('display', 'block');
                $('.tocContentMain_close').removeClass('hidden');
            }else {
                elemtMenu.css('display', 'none');
                $('.tocContentMain_close').addClass('hidden');
            }
        });

        function fixedTocContentIcon(){
            /* thiết lập vị trí nút nhấn */
            const elemtBox = $('#js_buildTocContentMain_element');
            const isRTL = $('html').attr('dir') == 'rtl';
            const positionBox = isRTL 
                ? $(window).width() - elemtBox.offset().left - elemtBox.outerWidth() 
                : elemtBox.offset().left;

            if (isRTL) {
                $('.tocFixedIcon').css('right', parseInt(positionBox - 50));
            } else {
                $('.tocFixedIcon').css('left', parseInt(positionBox - 50));
            }
        }

        function setHeightTocFixed(){
            let heightToc   = parseInt($(window).height() - 210);
            $('.tocContentMain.tocFixed .tocContentMain_list').css({
                'height' : heightToc+'px',
                'max-height'    : 'unset',
            });
        }

        let element         = $('#tocContentMain');
        if(element.length>0){
            let boxContent      = $('#'+idElement);
            let heightB         = boxContent.outerHeight();
            window.addEventListener('scroll', function() {
                let positionB       = boxContent.offset().top;
                let heightFooter    = $('.copyright').outerHeight();
                let positionE   = element.offset().top;
                let heightE     = element.outerHeight();
                let scrollNow   = $(document).scrollTop();
                let minScroll   = parseInt(heightE + positionE);
                let maxScroll   = parseInt(heightB + positionB - heightFooter);
                if(scrollNow > minScroll && scrollNow < maxScroll){ 
                    $('.tocFixedIcon').css('display', 'block');
                    /* thiết lập chiều ngang của box fixed */ 
                    const width = $('.layoutHeaderSide_header').outerWidth();
                    $('.tocFixed').css('width', width);
                }else {
                    $('.tocFixedIcon').css('display', 'none');
                }
            });
        }
    }
    /* validate form khi nhập */
    function validateWhenType(elementInput, type = 'empty'){
        const idElement         = $(elementInput).attr('id');
        const parent            = $(document).find('[for*="'+idElement+'"]').parent();
        /* validate empty */
        if(type=='empty'){
            const valueElement  = $.trim($(elementInput).val());
            if(valueElement!=''&&valueElement!='0'){
                parent.removeClass('validateErrorEmpty');
                parent.addClass('validateSuccess');
            }else {
                parent.removeClass('validateSuccess');
                parent.addClass('validateErrorEmpty');
            }
        }
        /* validate phone */ 
        if(type=='phone'){
            const valueElement = $.trim($(elementInput).val());
            if(valueElement.length>=10&&/^\d+$/.test(valueElement)){
                parent.removeClass('validateErrorPhone');
                parent.addClass('validateSuccess');
            }else {
                parent.removeClass('validateSuccess');
                parent.addClass('validateErrorPhone');
            }
        }
        /* validate email */ 
        if(type=='email'){
            const valueElement = $.trim($(elementInput).val());
            /* check empty (nếu required) */
            if($(elementInput).prop('required')){
                if(valueElement==''){
                    parent.removeClass('validateSuccess');
                    parent.removeClass('validateErrorEmail');
                    parent.addClass('validateErrorEmpty');
                    return false;
                }
                /* check email hợp lệ */
                if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valueElement)){
                    parent.removeClass('validateErrorEmail');
                    parent.removeClass('validateErrorEmpty');
                    parent.addClass('validateSuccess');
                }else {
                    parent.removeClass('validateSuccess');
                    parent.removeClass('validateErrorEmpty');
                    parent.addClass('validateErrorEmail');
                }
            }else {
                /* check email hợp lệ */
                if(valueElement!=''){ /* khi nào người dùng nhập -> có giá trị mới tiến hành validate */
                    if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valueElement)){
                        parent.removeClass('validateErrorEmail');
                        parent.removeClass('validateErrorEmpty');
                        parent.addClass('validateSuccess');
                    }else {
                        parent.removeClass('validateSuccess');
                        parent.removeClass('validateErrorEmpty');
                        parent.addClass('validateErrorEmail');
                    }
                }
            }
        }
    }
    /* load quận/huyện */
    function loadDistrictByIdProvince(elementProvince, idWrite){
        const valueProvince = $(elementProvince).val();
        fetch('/loadDistrictByIdProvince?province_info_id='+valueProvince, {
            method  : 'GET',
            mode    : 'cors',
        })
        .then(response => {
            if (!response.ok){
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            $('#'+idWrite).html(response);
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* validate form */
    function validateForm(idForm){
        let error       = [];
        /* input required không được bỏ trống */
        $('#'+idForm).find('input[required]').each(function(){
            /* đưa vào mảng */
            if($(this).val()==''){
                error.push($(this).attr('name'));
            }
        })
        /* select */
        $('#'+idForm).find('select[required]').each(function(){
            if($(this).val()==0) error.push($(this).attr('name'));
        })
        return error;
    }
    // /* check csrf first time */
    // function checkToSetCsrfFirstTime(){
    //     /* dùng cho trường hợp người dùng vào trang lần đầu chưa có cookie CSRF */
    //     const flag = '{{ $_COOKIE["XSRF-TOKEN"] ?? "" }}';
    //     if(flag==''){
    //         $.ajax({
    //             url: '{{ route("main.setCsrfFirstTime") }}',
    //             dataType: 'json',
    //             type: 'get',
    //             success: function(response) {
    //                 if(response==true) location.reload();
    //             }
    //         });
    //     }
    // }
    /* check đăng nhập */
    function checkLoginAndSetShow(){
        let dataForm = {};
        dataForm.language = $('#language').val();            
        const queryString = new URLSearchParams(dataForm).toString();
        fetch('/checkLoginAndSetShow?' + queryString, {
            method  : 'GET',
            mode    : 'cors',
        })
        .then(response => {
            if (!response.ok){
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            /* button desktop */
            $('#js_checkLoginAndSetShow_button').html(response.button);
            $('#js_checkLoginAndSetShow_button').css('display', 'flex');
            /* button mobile */
            $('#js_checkLoginAndSetShow_buttonMobile').html(response.button_mobile);
            /* modal chung */
            $('#js_checkLoginAndSetShow_modal').html(response.modal);
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* bật tắt bộ lọc nâng cao */
    function toggleFilterAdvanced(idElement){
        $('#'+idElement).toggleClass('active');
    }
    /* set chế độ xem */
    function setViewBy(key) {
        fetch("/setViewBy?key=" + key, {
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
            location.reload();
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* set chế độ sắp xếp */
    function setSortBy(key) {
        fetch("/setSortBy?key=" + key, {
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
            location.reload();
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }

        // function setFeelingAndSubmit(element) {
        //     // Xác định input checkbox trong phần tử cha của element
        //     var checkbox = $(element).find('input[type="checkbox"]');

        //     // Toggle trạng thái checked của checkbox
        //     checkbox.prop('checked', !checkbox.prop('checked'));

        //     // Submit form
        //     $(element).closest('form').submit();
        // }
    /* set filter */
    function setFilter(element){
        var checkbox = $(element).find('input[type="radio"]');
        checkbox.prop('checked', true);
        $(element).closest('form').submit();
    }
    /* tải box bộ lọc trang miễn phí */
    function showSortBoxFreeWallpaper() {
        const id = "{{ $item->id ?? 0 }}";
        const total = "{{ $total ?? 0 }}";
        const language = $('#language').val();
        // Lấy chuỗi query parameters từ URL
        var queryString = window.location.search;
        var urlParams = new URLSearchParams(queryString);
        var params = {};
        for (const [key, value] of urlParams) params[key] = value;

        // Thêm các giá trị id và total vào params
        params['id'] = id;
        params['total'] = total;
        params['language'] = language;

        const queryParams = new URLSearchParams(params).toString();

        fetch("/showSortBoxFreeWallpaper?" + queryParams, {
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
            $('#formViewBy').html(data);
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* tải box bộ lọc trang trả phí */
    function showSortBoxWallpaper() {
        const id = "{{ $item->id ?? 0 }}";
        const total = "{{ $total ?? 0 }}";
        const type = "{{ $item->seo->type ?? '' }}";
        const language = "{{ $language ?? '' }}";
        // Lấy chuỗi query parameters từ URL
        var queryString = window.location.search;
        
        // Tạo một đối tượng URLSearchParams từ chuỗi query parameters
        var urlParams = new URLSearchParams(queryString);

        // Lấy tất cả các tham số truyền qua URL
        var params = {};
        for (const [key, value] of urlParams) {
            params[key] = value;
        }

        // Thêm các giá trị id và total vào params
        params['id'] = id;
        params['total'] = total;
        params['type'] = type;
        params['language'] = language;
        const queryParams = new URLSearchParams(params).toString();

        fetch("/showSortBoxWallpaper?" + queryParams, {
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
            $('#formViewBy').html(data);
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    /* full loading */
    function toggleFullLoading(textContent = '') {
        const elemt         = $('#js_toggleFullLoading');
        const elemtTextBox  = $('#js_toggleFullLoading_text');
        const body          = $('body');

        // Toggle the 'show' class and determine the new state
        elemt.toggleClass('show');
        
        // Check if the element has the 'show' class after toggling
        const isVisible = elemt.hasClass('show');

        // Update text content if provided
        if (textContent) {
            elemtTextBox.text(textContent);
        }

        // Update body overflow based on visibility state
        if (isVisible) {
            body.css('overflow', 'hidden'); // Add overflow: hidden when element is visible
        } else {
            body.css('overflow', 'unset'); // Remove overflow style when element is hidden
        }
    }

    // xử lý của headerSide (vì menu truyền vào service nên script chuyển ra đây)
    function showHideListMenuMobile(element, idMenu){
        let elementMenu     = $('#'+idMenu);
        let flag            = elementMenu.height();
        if(flag<=0){
            elementMenu.addClass('active');
        }else {
            elementMenu.removeClass('active');
        }
        /* toggle icon */
        const elementIcon = $(element).find('.actionMenu');
        if ($(elementIcon).hasClass('isOpen')) {
            $(elementIcon).removeClass('isOpen');
        } else {
            $(elementIcon).addClass('isOpen');
        }
    }

    function settingCollapsedMenu(){
        const element       = $('#js_settingCollapsedMenu');
        element.find('.layoutHeaderSide_header').css('width', '4.25rem');
        /* xác định hành động */
        var action          = 'on';
        if(element.hasClass('collapsed'))  action = 'off';
        /* thiết lập */
        let dataForm        = {};
        dataForm.action     = action;            
        const queryString   = new URLSearchParams(dataForm).toString();
        fetch('/settingCollapsedMenu?' + queryString, {
            method  : 'GET',
            mode    : 'cors',
        })
        .then(response => {
            if (!response.ok){
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            if(action=='on'){
                element.addClass('collapsed');
            }else {
                element.removeClass('collapsed');
            }
            setTimeout(() => {
                element.find('.layoutHeaderSide_header').attr('style', '');
            }, 200);
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }

    function getStatusCollapse() {
        fetch('/getStatusCollapse', {
            method: 'GET',
            mode: 'cors',
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.status == 'on') {
                $('#js_settingCollapsedMenu').addClass('collapsed');
            } else {
                $('#js_settingCollapsedMenu').removeClass('collapsed');
            }
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
</script>