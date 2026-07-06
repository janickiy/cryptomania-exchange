<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ company_name() }}</title>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('common/images/favicon-32x32.png') }}">
    @yield('before-style')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/v4-shims.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4/dist/css/adminlte.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('common/vendors/iCheck/all.css') }}">

    <!-- for datatable and date picker -->
    <link rel="stylesheet" href="{{ asset('common/vendors/datepicker/datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendors/datatable_responsive/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/adminlte4-compat.css') }}?v={{ filemtime(public_path('backend/assets/css/adminlte4-compat.css')) }}">

    @yield('after-style')
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
        {!! NoCaptcha::renderJs() !!}
    @endif

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
