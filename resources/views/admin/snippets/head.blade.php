<head>
  <meta name="robots" content="noindex,nofollow">
  {{-- <meta name="csrf-token" content="LdjJ2w20tGUL3y60AsF4ToCMKWgnRVrAypJ4E78v"> --}}
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
  <meta name="description" content="Trang quản trị nội dung ®Websitekiengiang">
  <meta name="keywords" content="Trang quản trị nội dung ®Websitekiengiang">
  <meta name="author" content="Hitour">
  <title>Trang quản trị nội dung {{ config('main_'.env('APP_NAME').'.company_name') }}</title>
  <!-- Favicon -->
  <link rel="shortcut icon" href="https://cdn.wallsora.com/storage/images/favicon-wallsora.webp" type="image/x-icon" />
  <!-- BEGIN: Vendor CSS-->
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/vendors.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/plugins/forms/form-validation.css') }}">
  <!-- custom tags -->
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/tagify/tagify.css') }}" /> 
  <!-- END: Vendor CSS-->
  <!-- BEGIN: Theme CSS-->
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/bootstrap.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/bootstrap-extended.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/colors.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/components.css') }}">
  <!-- BEGIN: Page CSS-->
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/plugins/forms/form-validation.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.min.css') }}">
  <!-- END: Page CSS-->
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/vendors/css/extensions/toastr.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('sources/admin/app-assets/css/plugins/extensions/ext-component-toastr.css') }}">

  <!-- BEGIN: Custom CSS-->
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
  @vite('resources/sources/admin/style.scss')
  <!-- END: Custom CSS-->
  <!-- BEGIN: FONT AWESOME -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <!-- END: FONT AWESOME -->

  <!-- BEGIN: SLICK -->
  {{-- <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/> --}}
  <!-- END: SLICK -->

 </head>