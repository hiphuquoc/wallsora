@php
    $randomWallpaper = \App\Models\FreeWallpaper::inRandomOrder()->first();
@endphp
<form id="formLogin" method="get" style="width:100%;">
<div id="modalLoginFormCustomerBox" class="modalLoginFormCustomerBox">
    <!-- modal background -->
    <div class="modalLoginFormCustomerBox_bg" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');"></div>
    <!-- modal box -->
    <div class="modalLoginFormCustomerBox_box">
        <div class="modalLoginFormCustomerBox_box_left" style="background:url('{{ \App\Helpers\Image::getUrlImageCloud($randomWallpaper->file_cloud) }}') no-repeat center;background-size: cover;">
        </div>
        <div class="modalLoginFormCustomerBox_box_right">
            <!-- close -->
            <div class="modalLoginFormCustomerBox_box_right_close" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
                <svg><use xlink:href="#icon_close"></use></svg>
            </div>
            <!-- form -->
            @php
                $loginByLanguage = config('data_language_1.'.$language.'.login');
            @endphp
            <div class="loginFormCustomer">
                <div class="loginFormCustomer_title">
                    {{ $loginByLanguage.' '.config('main_'.env('APP_NAME').'.company_name') }}
                </div>
                <div class="loginFormCustomer_body">
                    <div class="loginFormCustomer_body_item">
                        <!-- input -->
                        <div class="formBox">
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label>{{ config('data_language_1.'.$language.'.login_email') }}</label>
                                    <input type="text" name="email" required />
                                </div>
                            </div>
                            <div class="formBox_item">
                                <div class="inputWithLabelInside">
                                    <label>{{ config('data_language_1.'.$language.'.password') }}</label>
                                    <input type="password" name="password" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="formBox_item" style="display:flex;justify-content:space-between;align-item:flex-end;">
                                <label class="checkBox" for="remember" style="font-size:0.85rem;">
                                    <input type="checkbox" id="remember" name="remember" checked />
                                    <div>{{ config('data_language_1.'.$language.'.remember_me') }}</div>
                                </label>
                                <div id="noticeLogin" class="noticeLogin"> 
                                    <!-- thông báo đăng nhập -->
                                    {{-- Tên đăng nhập và mật khẩu không khớp! --}}
                                </div>
                            </div>
                        </div>
                        <div class="loginFormCustomer_body_item">
                            <!-- button -->
                            <button type="button" class="button" onClick="submitFormLogin('formLogin');" disabled>{{ $loginByLanguage }}</div>
                            <!-- đăng nhập google -->
                            <!-- login social -->
                            <div class="loginFormSocial">
                                <div class="loginFormSocial_title">
                                    {{ config('data_language_1.'.$language.'.or_login_with') }}
                                </div>
                                <div class="loginFormSocial_body">
                                    <div class="loginFormSocial_body_item">
                                        <div id="g_id_onload" 
                                            data-_token="{{ csrf_token() }}" 
                                            data-client_id="{{ env('GOOGLE_DRIVE_CLIENT_ID') }}" 
                                            data-context="signin"
                                            data-ux_mode="popup"
                                            data-login_uri="{{ env('APP_URL') }}/auth/google/callback" 
                                            data-auto_prompt="false"
                                            data-auto_select="true"
                                            data-itp_support="true">
                                        </div>

                                        <div class="g_id_signin"
                                            data-type="standard"
                                            data-shape="rectangular"
                                            data-theme="filled_white"
                                            data-text="signin_with"
                                            data-size="large"
                                            data-logo_alignment="left">
                                        </div>
                                    </div>
                                    {{-- <div class="loginFormSocial_body_item">
                                        @php
                                            $buttonTitleLoginWithFB = 'Login with Facebook';
                                        @endphp
                                        <a class="facebookButtonLogin" href="{{ route('main.facebook.redirect') }}">
                                            <img src="{{ Storage::url('') }}" alt="{{ $buttonTitleLoginWithFB }}" title="{{ $buttonTitleLoginWithFB }}" />
                                            <div class="maxLine_1">{{ $buttonTitleLoginWithFB }}</div>
                                        </a>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
</div>
</form>
{{-- @push('scriptCustom')
    <script type="text/javascript">
       
    </script>
@endpush --}}