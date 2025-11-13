<div class="headerBottom">
    <!-- hỗ trợ -->
    @php
        $contacts               = config('main_'.env('APP_NAME').'.contacts');
        $phoneCustomerService   = null;
        foreach($contacts as $contact){
            if($contact['type']=='customer service') {
                $phoneCustomerService = $contact['phone'];
                break;
            }
        }
        $nameSupport            = config('data_language_1.'.$language.'.support');
        $altSupport             = \App\Helpers\Charactor::concatenateWords([$nameSupport, env('APP_NAME')], $language);
        $nameGuide              = config('data_language_1.'.$language.'.download_guide');
        $altGuide               = \App\Helpers\Charactor::concatenateWords([$nameGuide, config('data_language_2.'.$language.'.phone_wallpaper')], $language);
    @endphp
    <a class="headerBottom_item" href="https://zalo.me/{{ $phoneCustomerService }}">
        <div class="headerBottom_item_icon">
            <svg><use xlink:href="#icon_headphone"></use></svg>
        </div>
        <div class="headerBottom_item_text maxLine_1"><div class="maxLine_1" style="width:100%;">{{ $nameSupport }}</div></div>
    </a>
    <!-- hướng dẫn tải -->
    <a id="js_loadLinkDownloadGuide" class="headerBottom_item" href="/">
        <div class="headerBottom_item_icon">
            <svg><use xlink:href="#icon_book_open"></use></svg>
        </div>
        <div class="headerBottom_item_text"><div class="maxLine_1" style="width:100%;">{{ $nameGuide }}</div></div>
    </a>
    <!-- đăng nhập => Ajax -->
    <div id="js_checkLoginAndSetShow_buttonMobile" class="headerBottom_item"></div>
</div>
@push('scriptCustom')
    <script defer type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            loadLinkDownloadGuide('{{ $language }}');
        });

        function loadLinkDownloadGuide(language){
            let dataForm = {};
            dataForm.language = language;            
            const queryString = new URLSearchParams(dataForm).toString();
            fetch("/loadLinkDownloadGuide?" + queryString, {
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
                $('#js_loadLinkDownloadGuide').attr('href', data);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        }
    </script>
@endpush