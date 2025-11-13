<div id="js_toggleModalLogin_idWrite" class="formModalBox" style="display:flex;">
    
    <div class="formModalBox_bg js_toggleModalLogin"></div>
    <div class="formModalBox_box" style="max-width:500px;">
        <form id="formLogin" method="get" style="width:100%;">
            <div class="formModalBox_box_body">
                <!-- Form Login -->
                <div class="formLogin">
                    <div class="formLogin_logo">
                        <img src="https://websitekiengiang.com/storage/images/logo-color-full.png" alt="logo website kiên giang" title="logo website kiên giang" />
                    </div>
                    <div class="formLogin_text">
                        <div class="formLogin_text_title">Chào mừng bạn! 👋</div>
                        <div class="formLogin_text_description">Bạn đang truy cập hệ thống dành cho thành viên quản trị. Vui lòng đăng nhập trước khi thao tác!</div>
                    </div>
                    <div class="formLogin_form">
                        <div class="formLogin_form_item">
                            <label class="required">Email</label>
                            <input type="text" name="email" tabindex="1" required />
                        </div>
                        <div class="formLogin_form_item">
                            <label class="required">Mật khẩu</label>
                            <div class="inputWithIcon" >
                                <input type="password" name="password"  tabindex="2" required />
                            </div>
                        </div>
                        <div id="js_noticeLogin" class="formLogin_form_item" style="display:none;color:red;">
                            <!-- thông báo lỗi -->
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="formModalBox_box_footer">
                <div class="formModalBox_box_footer_item button" tabindex="3" onClick="submitFormLogin('formLogin')" style="width:100%;border-radius:7px;text-align:center;">
                    Đăng nhập
                </div>
            </div>
        </form>
    </div>

</div>
@push('scriptCustom')
    <script type="text/javascript">
        /* submit form */
        function submitFormLogin(idForm){
            const error     = validateFormLogin(idForm);
            if(error.length==0){
                /* tải loading */ 
                // loadLoading(idForm);
                /* lấy dữ liệu truyền đi */
                var data    = $('#'+idForm).serializeArray();
                $.ajax({
                    url         : '{{ route("admin.loginAdmin") }}',
                    type        : 'post',
                    dataType    : 'json',
                    data        : {
                        '_token'    : '{{ csrf_token() }}',
                        data        : data
                    },
                    success     : function(response){
                        if(response.flag==true){
                            window.location.href = "{{ route('admin.product.list') }}";
                        }else {
                            $('#js_noticeLogin').html(response.message).css('display', 'block');
                        }
                    }
                });
            }else {
                $.each(error, function(index, value){
                    const input = $('#'+idForm).find('[name='+value.name+']');
                    input.attr('placeholder', value.notice).css('border', '1px solid red');
                });
            }
        }
        /* validate form */
        function validateFormLogin(idForm){
            let error       = [];
            /* input required không được bỏ trống */
            $('#'+idForm).find('input[required]').each(function(){
                /* đưa vào mảng */
                if($(this).val()==''){
                    const errorItem = [];
                    errorItem['name']       = $(this).attr('name');
                    errorItem['notice']     = 'Không được để trống trường này';
                    error.push(errorItem);
                }
            });
            /* validate số điện thoại => bắt đầu bằng số 0 và 10 số */
            // var phone       = $('#phone').val();
            // var phoneRegex  = /^0\d{9}$/;
            // if (!phoneRegex.test(phone)) {
            //     const errorItem         = [];
            //     errorItem['name']       = 'phone';
            //     errorItem['notice']     = 'Điện thoại không hợp lệ';
            //     error.push(errorItem);
            // }
            return error;
        }
    </script>
@endpush