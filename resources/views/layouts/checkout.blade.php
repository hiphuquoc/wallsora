<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>
            @yield('title')
        </title>
        <!-- BEGIN: FONT AWESOME -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <!-- END: FONT AWESOME -->
        @vite(['resources/sources/main/style.scss'])
        <style type="text/css">
            /* font */
            @font-face{
                font-family:'SVN-Gilroy Bold';
                font-style:normal;
                font-weight:700;
                src:url("/fonts/svn-gilroy_semibold.ttf")
            }
            @font-face{
                font-family:'SVN-Gilroy';
                font-style:normal;
                font-weight:500;
                src:url("/fonts/svn-gilroy_medium.ttf")
            }
        </style>
        <!-- BEGIN: Jquery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <!-- END: Jquery -->
        <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
    </head>
    <body>

        @yield('content')

    </body>
</html>