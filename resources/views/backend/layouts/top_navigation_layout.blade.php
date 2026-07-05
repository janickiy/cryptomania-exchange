@include('backend.layouts.includes._frontend_header')
<body class="hold-transition skin-blue layout-top-nav frontend-liquid-glass">
    <div class="wrapper" id="app">
@include('backend.layouts.includes._top_navigation_header')

    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
        @yield('extended-content')
    </div>
@include('backend.layouts.includes._frontend_footer')
