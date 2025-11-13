<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
@if(Route::is('main.confirm'))
    <meta name="robots" content="noindex,nofollow">
@else
    @if(!empty($index)&&$index=='no')
        <meta name="robots" content="noindex,nofollow">
    @else 
        <meta name="robots" content="index,follow">
    @endif
@endif
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="fragment" content="!" />
@if(!empty($language))
    <meta name="language" content="{{ $language }}" />
@endif
<!-- Dmca -->
<meta name='dmca-site-verification' content='{{ env('DMCA_VALIDATE') }}' />
<!-- Tối ưu hóa việc tải ảnh từ Google Cloud Storage -->
<link rel="preconnect" href="https://cdn.wallsora.com" crossorigin>
<link rel="dns-prefetch" href="https://cdn.wallsora.com">
{{-- <link rel="preconnect" href="https://images.dmca.com">
<link rel="dns-prefetch" href="https://images.dmca.com"> --}}
{{-- <link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com"> --}}
<!-- Favicon -->
<link rel="shortcut icon" href="https://cdn.wallsora.com/storage/images/favicon-wallsora.webp" type="image/x-icon" />
<!-- view mode -->
<script src="{{ asset('js/viewMode.js') }}" async></script>

<!-- CSS Khung nhìn đầu tiên - Inline Css -->
@stack('cssFirstView')
<!-- Css tải sau -->
@stack('headCustom')
@if(env('APP_ENV')=='local')
    <!-- tải font nếu dev -->
    <style type="text/css">
        @font-face {
            font-family: 'SVN-Gilroy';
            font-style: normal;
            font-display: swap;
            font-weight: 500;
            src: url('/fonts/svn-gilroy_medium.ttf');
        }

        @font-face {
            font-family: 'SVN-Gilroy Med';
            font-style: normal;
            font-display: swap;
            font-weight: 700;
            src: url('/fonts/svn-gilroy_med.ttf');
        }

        @font-face {
            font-family: 'SVN-Gilroy Semi';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/fonts/svn-gilroy_semibold.ttf');
        }

        @font-face {
            font-family: 'SVN-Gilroy Bold';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/fonts/svn-gilroy_semibold.ttf');
        }
    </style>
@endif
{{-- @include('wallpaper.snippets.fonts') --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+Chettan+2:wght@400..800&display=swap" rel="stylesheet">