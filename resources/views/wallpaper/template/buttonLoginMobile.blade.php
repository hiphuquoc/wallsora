@if(!empty($user))
    @php
        $myDownloadsByLanguage = config('data_language_1.'.$language.'.my_downloads');
    @endphp
    <div onClick="toggleMenuListMobile();">
        <div class="headerBottom_item_icon">
            <svg><use xlink:href="#icon_user"></use></svg>
        </div>
        <div class="headerBottom_item_text">
            <div class="maxLine_1" style="width:100%;">{{ $user->name ?? ''}}</div>
            <div class="headerBottom_item_text_modal">
                <a href="/{{ $urlMyDownload }}" class="loginBox_list_item">
                    <svg><use xlink:href="#icon_download"></use></svg>
                    <div>{{ $myDownloadsByLanguage }}</div>
                </a> 
                <a href="{{ route('admin.logout') }}" class="loginBox_list_item">
                    <svg><use xlink:href="#icon_right_from_bracket"></use></svg>
                    <div>{{ config('data_language_1.'.$language.'.logout') }}</div>
                </a>
            </div>
            <div class="headerBottom_item_text_background"></div>
            <script type="text/javascript">
                function toggleMenuListMobile(){
                    const flagShow = $('.headerBottom_item_text_modal').css('display');
                    if(flagShow=='none'){
                        $('.headerBottom_item_text_modal').css('display', 'block');
                        $('.headerBottom_item_text_background').css('display', 'block');
                    }else {
                        $('.headerBottom_item_text_modal').css('display', 'none');
                        $('.headerBottom_item_text_background').css('display', 'none');
                    }
                }
            </script>
        </div>
    </div>
@else 
    @php
        $loginByLanguage = config('data_language_1.'.$language.'.login');
    @endphp
    <div onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="headerBottom_item_icon">
            <svg><use xlink:href="#icon_sign_in_alt"></use></svg>
        </div>
        <div class="headerBottom_item_text"><div class="maxLine_1" style="width:100%;">{{ $loginByLanguage }}</div></div>
    </div>
@endif