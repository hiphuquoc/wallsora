<!-- === START:: Scripts Default === -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/core/app-menu.js') }}"></script>
<!-- === END:: Scripts Default === -->
<!-- Include jQuery UI library -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<!-- Include jQuery UI Sortable library -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-select2.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/forms/form-repeater.min.js') }}"></script>
<!-- custom tag -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/tagify/tagify.js') }}"></script>
<!-- BEGIN: SWEET ALERT -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js') }}"></script>
<!-- END: SWEET ALERT -->
<!-- BEGIN: SLICK -->
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<!-- END: SLICK -->
<!-- BEGIN: MENU -->
<script src="{{ asset('sources/admin/app-assets/js/core/app.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/scripts/customizer.min.js') }}"></script>
<script src="{{ asset('sources/admin/app-assets/js/core/app-menu.min.js') }}"></script>
<!-- END: MENU -->

<script src="{{ asset('sources/admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>

<!-- END: Page Vendor JS-->
<script defer>
    $(window).on('load', function () {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
        $('[data-bs-toggle="tooltip"]').tooltip();
        loadImageFromGoogleCloud();
    })

    function createToast(type, title, message) {
        const toastContainer = document.getElementById('toast-container') || document.body;
        
        // Tạo ID duy nhất cho mỗi Toast
        const toastId = 'toast-' + Date.now();

        // Tạo cấu trúc HTML của Toast
        const toastHTML = `
            <div id="${toastId}" class="toast toast-${type}" aria-live="polite" style="display: block; opacity: 1;">
                <div class="toast-progress" style="width: 0%;"></div>
                <button type="button" class="toast-close-button" role="button">×</button>
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
        `;

        // Thêm Toast vào container
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);

        // Tự động ẩn Toast sau 3 giây và xóa khỏi DOM
        setTimeout(() => {
            toastElement.style.opacity = 0;
            setTimeout(() => toastElement.remove(), 300); // Xóa sau khi hiệu ứng mờ hoàn tất
        }, 10000);

        // Xử lý sự kiện đóng thủ công
        const closeButton = toastElement.querySelector('.toast-close-button');
        if (closeButton) {
            closeButton.addEventListener('click', () => toastElement.remove());
        }
    }

    function openCloseFullLoading(){
        const htmlLoading = $('#js_fullLoading_bg');
        if(htmlLoading.is(":visible")){
            htmlLoading.css('display', 'none');
            $('#js_fullLoading_blur').css({
                'filter' : 'unset',
                'overflow'  : 'unset',
            });
        } else {
            htmlLoading.css('display', 'flex');
            $('#js_fullLoading_blur').css({
                'filter'    : 'blur(8px)',
                'overflow'  : 'hidden',
            });
        }
    }
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({ html : true })
    })
    /* COUNT CHARACTOR */
    $('input, textarea').on('input', function(){
        const idElemt           = $(this).attr('id');
        if(idElemt){
            const lengthInput   = $(this).val().length;
            const elemtShow     = $(document).find("[data-charactor='" + idElemt + "']");
            elemtShow.html(lengthInput);
        }
    })
    /* Setting view */
    function settingView(name, valDefault){
        $.ajax({
            url         : '{{ route("admin.setting.view") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                name,
                default : valDefault
            },
            success     : function(result){
                location.reload();
            }
        });
    }
    function submitForm(idForm, addParams = {}){
        const form = $('#' + idForm);
        if(form.valid()){
            // Thêm các tham số bổ sung (nếu có) vào form
            $.each(addParams, function(key, value) {
                $('<input>').attr({
                    type: 'hidden',
                    name: key,
                    value: value
                }).appendTo(form);
            });
            // Submit form
            form.submit();
        }
    }
    /* copy to clipboard */
    function copyToClipboard(idContent, callbackFunction=null) {
        // Get the text field
        var copyText = document.getElementById(idContent);

        // Select the text field
        copyText.select();
        // copyText.setSelectionRange(0, 99999); // For mobile devices - input không phải hidden

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);
    
        // Alert the copied text
        callbackFunction;
    }
    /* loading trong khi chờ ajax */
    function addLoading(idWrite){
        const html = '{{ view("admin.template.loading") }}';
        $('.'+idWrite).append(html);
    }
    function removeLoading(){
        $('.js_loading_element').remove();
    }
    /* load image from goole cloud */
    function loadImageFromGoogleCloud(){
        $(document).find('img[data-google-cloud]').each(function(){
            var elementImg          = $(this);
            const urlGoogleCloud    = elementImg.attr('data-google-cloud');
            const size              = elementImg.attr('data-size');
            $.ajax({
                url         : '{{ route("ajax.loadImageFromGoogleCloud") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    url_google_cloud    : urlGoogleCloud,
                    size
                },
                success     : function(response){
                    elementImg.attr('src', response);
                }
            });
        });
    }
    /* function viết content */
    function callAI(action){
        $('[data-type="'+action+'"]').each(function() {
            const id                = $(this).data('id');
            const language          = $(this).data('language');
            const id_prompt         = $(this).data('id_prompt');
            const id_content        = $(this).data('id_content');
            chatGpt($(this), id, language, id_prompt, id_content);
        });
    }
    /* ai chatgpt */
    function chatGpt(input, id, language, id_prompt, id_content){ /* id_content hiện chỉ dùng cho content do có nhiều phần tử content dùng chung 1 prompt */
        addAndRemoveClass($(input), 'inputLoading', 'inputSuccess inputError');
        /* vô hiệu hóa box dùng tiny */ 
        const idBox = $(input).attr('id');
        var editor = tinymce.get(idBox);
        if (editor) editor.getBody().setAttribute('contenteditable', false);
        $.ajax({
            url         : '{{ route("main.chatGpt") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                id, language, id_prompt, id_content
            }
        }).done(function(data){
            /* điền dữ liệu vào */
            if(data.error=='') {
                addAndRemoveClass($(input), 'inputSuccess', 'inputLoading inputError');
                $(input).val(data.content);
            }else {
                addAndRemoveClass($(input), 'inputError', 'inputLoading inputSuccess');
            }
            /* Cập nhật nội dung Tiny */
            if($(input).is('textarea')){
                const idBox = $(input).attr('id');
                if (editor) {
                    editor.setContent(data.content);
                    editor.getBody().setAttribute('contenteditable', true);
                }
            }
            /* đếm lại kí tụ nếu có */
            const idInput           = $(input).attr('id');
            if(idInput){
                const lengthInput   = $(input).val().length;
                const elemtShow     = $(document).find("[data-charactor='" + idInput + "']");
                elemtShow.html(lengthInput);
            }
        })
    }
    function addAndRemoveClass(input, add, remove){
        $(input).addClass(add).removeClass(remove);
        /* kiểm tra có phải input tiny */ 
        var inputTiny = $(input).next();
        if(inputTiny.hasClass('tox-tinymce')){
            inputTiny.addClass(add).removeClass(remove);
        }
        /* kiểm tra có phải input tag */
        var inputTag = $(input).prev();
        if(inputTag.is('tags')){
            inputTag.addClass(add).removeClass(remove);
        }
    }
    function clearCacheHtml(){
        Swal.fire({
            title: 'Xác nhận xóa CacheHTML',
            html: '<div>CacheHTML của tất cả các trang sẽ được xóa và làm mới lại.</div>',
            preConfirm: () => {
                Swal.showLoading()
                return new Promise((resolve) => {
                    setTimeout(() => {
                        $.ajax({
                            url         : '{{ route("admin.cache.clearCache") }}',
                            type        : 'get',
                            dataType    : 'html',
                            success     : function(response){
                                resolve(response)
                            }
                        });
                    }, 500)
                })
            },
            confirmButtonText: 'Xác nhận'
        })
    }
    /* tạo job dịch tự động từng trang ngôn ngữ riêng biệt */
    function createJobTranslateContent(idSeoVI, language) {
        Swal.fire({
            title: 'Xác nhận thao tác',
            html: `<div>Hành động này sẽ tiến hành dịch trang ngôn ngữ <span style="color:red;font-weight:bold;">${language}</span>. Nếu trước đó đã có nội dung thì sẽ bị xóa bỏ tất cả để dịch lại nội dung mới.</div>`,
            preConfirm: () => {
                Swal.showLoading();
                return new Promise((resolve) => {
                    $.ajax({
                        url: '{{ route("admin.translate.createJobTranslateContentAjax") }}',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_seo_vi: idSeoVI,
                            language: language  // ✅ Fix lỗi
                        }
                    })
                    .done(function (response) {
                        createToast(response.toast_type, response.toast_title, response.toast_message);
                        $('#lock').css('display', 'block');
                        resolve(true);  // ✅ Giúp đóng modal
                    })
                    .fail(function () {
                        createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                        resolve(false); // ✅ Đóng modal ngay cả khi có lỗi
                    });
                });
            },
            confirmButtonText: 'Xác nhận'
        });
    }
    function createMultiJobTranslateContent(slugVi, id = 0, reload = true) {
        var htmlBody = `
            <div style="margin-top: 10px; text-align: left;">
                <label>
                    <input type="radio" name="option" value="1" />
                    1. Dịch nội dung <span style="color:red;">*chỉ trang EN</span> - nội dung có sẵn sẽ bị đè
                </label>
            </div>
            <div style="margin-top: 10px; text-align: left;">
                <label>
                    <input type="radio" name="option" value="2" />
                    2. Dịch nội dung tất cả các ngôn ngữ <span style="color:red;">*ngoại trừ EN</span> - nội dung có sẵn sẽ bị đè
                </label>
            </div>`;

        @if (!empty($languageNotEnoughContent['html']))
            // Chuyển đổi array PHP thành JSON để nhúng vào input
            var arrayLanguageNotEnoughContent = @json($languageNotEnoughContent['array']);
            htmlBody += `
                <div style="margin-top: 10px; text-align: left;">
                    <label>
                        <input type="radio" name="option" value="3" checked />
                        <input type="hidden" name="array_language_not_enough_content" value='${JSON.stringify(arrayLanguageNotEnoughContent)}' />
                        3. Dịch các ngôn ngữ chưa đủ nội dung {!! $languageNotEnoughContent['html'] !!}
                    </label>
                </div>`;
        @endif

        Swal.fire({
            title: 'Xác nhận tùy chọn',
            html: htmlBody,
            preConfirm: () => {
                // Tìm giá trị radio được chọn
                const selectedOption = Swal.getPopup().querySelector('input[name="option"]:checked');
                const arrayLanguageInput = Swal.getPopup().querySelector('input[name="array_language_not_enough_content"]');

                if (!selectedOption) {
                    Swal.showValidationMessage('Vui lòng chọn một tùy chọn');
                    return false;
                }

                // Parse array từ input nếu tồn tại
                let arrayLanguageNotEnoughContent = [];
                if (arrayLanguageInput) {
                    try {
                        arrayLanguageNotEnoughContent = JSON.parse(arrayLanguageInput.value);
                    } catch (e) {
                        Swal.showValidationMessage('Dữ liệu không hợp lệ trong array_language_not_enough_content');
                        return false;
                    }
                }

                return { 
                    option: selectedOption.value, 
                    arrayLanguage: arrayLanguageNotEnoughContent // Trả về array
                };
            },
            showLoaderOnConfirm: true,
            confirmButtonText: 'Xác nhận'
        }).then((result) => {
            if (result.isConfirmed) {
                const optionValue = result.value.option;
                const arrayLanguage = result.value.arrayLanguage;

                // Gửi dữ liệu qua AJAX
                openCloseFullLoading();
                $.ajax({
                    url: "{{ route('admin.translate.createMultiJobTranslateContent') }}",
                    type: "post",
                    dataType: "json",
                    data: { 
                        '_token': '{{ csrf_token() }}',
                        slug_vi: slugVi,
                        option: optionValue, // Truyền giá trị tùy chọn
                        array_language: arrayLanguage // Truyền array
                    }
                })
                .done(function(response) {
                    // Hiển thị Toast từ response
                    createToast(response.toast_type, response.toast_title, response.toast_message);
                })
                .fail(function() {
                    // Hiển thị thông báo lỗi mặc định
                    createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                })
                .always(function() {
                    setTimeout(() => openCloseFullLoading(), 300);
                });
            }
        });
    }
    function createJobTranslateAndCreatePageAjax(slugVi, id = 0) {
        Swal.fire({
            title: 'Xác nhận thao tác',
            html: '<div>Hành động này sẽ tiến hành tạo những trang ngôn ngữ còn thiếu bằng AI.</div>',
            preConfirm: () => {
                Swal.showLoading();
                return new Promise((resolve) => {
                    $.ajax({
                        url: "{{ route('admin.translate.createJobTranslateAndCreatePageAjax') }}",
                        type: "post",
                        dataType: "json",
                        data: {
                            '_token': '{{ csrf_token() }}',
                            slug_vi: slugVi,
                        }
                    })
                    .done(function (response) {
                        setTimeout(() => createToast(response.toast_type, response.toast_title, response.toast_message), 300);
                        resolve(true);  // ✅ Giúp đóng modal
                    })
                    .fail(function () {
                        setTimeout(() => createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'), 300);
                        resolve(false); // ✅ Đóng modal ngay cả khi có lỗi
                    });
                });
            },
            confirmButtonText: 'Xác nhận'
        });
    }
    function getPromptTextById(idSeo, idPrompt, language) {
        // // Hiển thị trạng thái Loading
        // openCloseFullLoading();

        $.ajax({
            url: "{{ route('admin.prompt.getPromptTextById') }}",
            type: "post",
            dataType: "json",
            data: {
                '_token': '{{ csrf_token() }}',
                seo_id  : idSeo,
                prompt_info_id : idPrompt,
                language
            }
        })
        .done(function (response) {
            // Hiển thị Toast từ response
            setTimeout(() => createToast(response.toast_type, response.toast_title, response.toast_message), 300);

            if (response.flag) {
                if (response.content) {
                    // Sử dụng Clipboard API để copy nội dung
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(response.content)
                            .then(() => {
                                // Thông báo thành công đã được xử lý bởi response toast
                            })
                            .catch(err => {
                                console.error('Không thể copy text: ', err);
                                createToast('error', 'Thất bại', '❌ Không thể copy nội dung vào clipboard');
                            });
                    } else {
                        // Fallback cho trường hợp không hỗ trợ Clipboard API
                        const textarea = document.createElement('textarea');
                        textarea.value = response.content;
                        textarea.style.position = 'fixed';  // Tránh làm ảnh hưởng đến layout
                        textarea.style.opacity = '0';       // Ẩn element
                        document.body.appendChild(textarea);
                        textarea.select();
                        try {
                            document.execCommand('copy');
                            // Thông báo thành công đã được xử lý bởi response toast
                        } catch (err) {
                            console.error('Không thể copy text: ', err);
                            createToast('error', 'Thất bại', '❌ Không thể copy nội dung vào clipboard');
                        }
                        document.body.removeChild(textarea);
                    }
                } else {
                    createToast('error', 'Thất bại', '❌ Không tìm thấy nội dung để copy');
                }
            }
        })
        .fail(function () {
            // Hiển thị thông báo lỗi mặc định
            setTimeout(() => createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'), 300);
        })
        .always(function () {
            // Tắt trạng thái Loading
            // setTimeout(() => openCloseFullLoading(), 300);
        });
    }
    function improveContent(input, ordering, idSeo){
        addAndRemoveClass($(input), 'inputLoading', 'inputSuccess inputError');
        /* vô hiệu hóa box dùng tiny */ 
        const idBox = $(input).attr('id');
        var editor = tinymce.get(idBox);
        if (editor) editor.getBody().setAttribute('contenteditable', false);
        $.ajax({
            url         : '{{ route("main.improveContent") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                ordering, 
                seo_id : idSeo,
            }
        }).done(function(data){
            /* điền dữ liệu vào */
            if(data.error=='') {
                addAndRemoveClass($(input), 'inputSuccess', 'inputLoading inputError');
                $(input).val(data.content);
            }else {
                addAndRemoveClass($(input), 'inputError', 'inputLoading inputSuccess');
            }
            /* Cập nhật nội dung Tiny */
            if($(input).is('textarea')){
                const idBox = $(input).attr('id');
                if (editor) {
                    editor.setContent(data.content);
                    editor.getBody().setAttribute('contenteditable', true);
                }
            }
            /* đếm lại kí tụ nếu có */
            const idInput           = $(input).attr('id');
            if(idInput){
                const lengthInput   = $(input).val().length;
                const elemtShow     = $(document).find("[data-charactor='" + idInput + "']");
                elemtShow.html(lengthInput);
            }
        })
    }
    function createJobWriteContent(idSeo) {
        var htmlBody = `
            <div>Thao tác này sẽ tiến hành <span style="color:red;font-weight:bold">*xóa nội dung bảng VI</span> của trang này và viết lại (chạy ngầm).<br/>Bạn có chắc muốn thực hiện?</div>`;

        Swal.fire({
            title: 'Xác nhận thao tác',
            html: htmlBody,
            preConfirm: () => {
                
            },
            showLoaderOnConfirm: true,
            confirmButtonText: 'Xác nhận'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.translate.createJobWriteContent') }}",
                    type: "post",
                    dataType: "json",
                    data: { 
                        '_token': '{{ csrf_token() }}',
                        seo_id : idSeo,
                    }
                })
                .done(function(response) {
                    // Hiển thị Toast từ response
                    createToast(response.toast_type, response.toast_title, response.toast_message);
                })
                .fail(function() {
                    // Hiển thị thông báo lỗi mặc định
                    createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                })
            }
        });
    }
    function updateNotes(idSeo) {
        var htmlBody = `
            <div>
                <textarea id="notesTextarea" rows="10" placeholder="Nhập thông tin cần notes..." 
                    style="border-radius:12px;outline:none;border:none;background:#EDF2F7;width:100%;padding:1rem;"></textarea>
            </div>`;

        Swal.fire({
            title: 'Xác nhận tùy chọn',
            html: htmlBody,
            didOpen: () => {
                // Đặt focus vào textarea ngay khi modal mở
                document.getElementById("notesTextarea").focus();
            },
            preConfirm: () => {
                // Lấy giá trị từ textarea
                const notes = document.getElementById("notesTextarea").value.trim();
                
                // Kiểm tra nếu rỗng, yêu cầu nhập lại
                if (!notes) {
                    Swal.showValidationMessage("Vui lòng nhập nội dung ghi chú.");
                    return false;
                }

                return notes; // ✅ Trả về để sử dụng trong `.then()`
            },
            showLoaderOnConfirm: true,
            confirmButtonText: 'Xác nhận'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.ajax({
                    url: "{{ route('admin.updateNotes') }}",
                    type: "post",
                    dataType: "json",
                    data: { 
                        '_token': '{{ csrf_token() }}',
                        seo_id: idSeo,
                        notes: result.value  // ✅ Lấy giá trị đã nhập
                    }
                })
                .done(function(response) {
                    createToast(response.toast_type, response.toast_title, response.toast_message);
                    $('#js_updateNotes_notes_'+idSeo).html('Ghi chú: ' + result.value);
                })
                .fail(function() {
                    createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                });
            }
        });
    }

    function checkTranslateOfPage(idSeo){
        Swal.fire({
            title: 'Xác nhận thao tác',
            html: '<div>Hành động này sẽ gọi AI để kiểm tra lại tất cả bản dịch title, seo_title và seo_description của trang.</div>',
            preConfirm: () => {
                Swal.showLoading();
                return new Promise((resolve) => {
                    $.ajax({
                        url: "{{ route('admin.checkTranslateOfPage.callAI') }}",
                        type: "post",
                        dataType: "json",
                        data: {
                            '_token': '{{ csrf_token() }}',
                            seo_id: idSeo,
                        }
                    })
                    .done(function (response) {
                        setTimeout(() => createToast(response.toast_type, response.toast_title, response.toast_message), 300);
                        resolve(true);  // ✅ Giúp đóng modal
                    })
                    .fail(function () {
                        setTimeout(() => createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'), 300);
                        resolve(false); // ✅ Đóng modal ngay cả khi có lỗi
                    });
                });
            },
            confirmButtonText: 'Xác nhận'
        });
    }

</script>