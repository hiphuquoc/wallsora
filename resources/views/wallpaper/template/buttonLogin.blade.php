@if(!empty($user))
    @php
        $accountInformationByLanguage = config('data_language_1.'.$language.'.account_information');
    @endphp
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        <div class="loginBox_show">
            <svg><use xlink:href="#icon_user"></use></svg>
            <div class="loginBox_show_text maxLine_1">{{ $user->name ?? '' }}</div>
        </div>
        <div class="loginBox_list">
            <a href="/{{ $urlMyDownload }}" class="loginBox_list_item">
                <svg><use xlink:href="#icon_download"></use></svg>
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.my_downloads') }}</div>
            </a> 
            <a href="{{ route('admin.logout') }}" class="loginBox_list_item">
                <svg><use xlink:href="#icon_right_from_bracket"></use></svg>
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.logout') }}</div>
            </a>
        </div>
        <div class="loginBox_background">

        </div>
    </div>
@else 
    <div class="loginBox" onClick="toggleModalCustomerLoginForm('modalLoginFormCustomerBox');">
        @php
            $loginByLanguage = config('data_language_1.'.$language.'.login');
        @endphp
        <svg><use xlink:href="#icon_sign_in_alt"></use></svg>
        <div class="maxLine_1">{{ $loginByLanguage }}</div>
    </div>
@endif