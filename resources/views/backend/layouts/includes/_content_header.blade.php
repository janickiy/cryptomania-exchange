<div class="app-content-header">
    <div class="container-fluid page-title-bar">
        @include('backend.layouts.includes._breadcrumb')
    </div>
</div>


<section class="system-notices">
    <div class="row">
        @foreach(get_system_notices() as $notice)
            <div class="col-lg-12">
                <div class="alert alert-{{ $notice->type }} alert-dismissible">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h4>{{ $notice->title }}</h4>
                    {{ $notice->description }}
                </div>
            </div>
        @endforeach
    </div>
</section>
