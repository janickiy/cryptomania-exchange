@include('backend.layouts.includes._header')
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper" id="app">
@include('backend.layouts.includes._top_header')
@include('backend.layouts.includes._sidebar')

    <main class="app-main">
        @include('backend.layouts.includes._content_header')

        <div class="app-content">
            <div class="container-fluid">
                @yield('content')
                @yield('extended-content')
            </div>
        </div>
    </main>
@include('backend.layouts.includes._footer')
