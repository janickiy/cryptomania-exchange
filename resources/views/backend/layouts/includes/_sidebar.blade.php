<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        @if(admin_settings('company_logo'))
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ get_image(admin_settings('company_logo')) }}" class="brand-image opacity-75 shadow">
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
                <span class="brand-text fw-light text-uppercase">{{ env('APP_NAME') }}</span>
            </a>
        @endif
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
        {!! get_nav('back-end') !!}
        </nav>
    </div>
</aside>
