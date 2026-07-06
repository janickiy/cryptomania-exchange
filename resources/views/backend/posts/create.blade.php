@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="trade-analysis-form-page">
        <div class="card trade-analysis-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Create Trade Analysis') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Prepare a market insight, featured image, and publication state.') }}</p>
                </div>
                <a href="{{ route('trade-analyst.posts.index') }}" class="btn btn-outline-secondary back-button">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>

            <div class="card-body admin-form-body">
                {{ Form::open(['route'=>'trade-analyst.posts.store', 'method' => 'post', 'class'=>'trade-analysis-form admin-section-form validator','files'=> true]) }}
                @include('backend.posts._form',['buttonText' => __('Create')])
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@section('before-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/bootstrap-fileinput/css/jasny-bootstrap.css') }}">
@endsection

@section('script')
    <script src="{{ asset('common/vendors/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('common/vendors/bootstrap-fileinput/js/jasny-bootstrap.js') }}"></script>
    <script>

        tinymce.init({
            selector: "#content_textarea",
            menubar: false,
            theme: "modern",
            relative_urls: false,
            force_div_newlines: true,
            force_h1_newlines: true,
            force_h2_newlines: true,
            force_h3_newlines: true,
            force_h4_newlines: true,
            force_h5_newlines: true,
            force_h6_newlines: true,
            force_ul_newlines: true,
            force_ol_newlines: true,
            force_li_newlines: true,
            force_hr_newlines: true,
            forced_br_newlines: true,
            forced_p_newlines: false,
            forced_root_block: false,
            remove_linebreaks: true,
            convert_urls: false,
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
            toolbar2: "print preview | forecolor backcolor | code link image",
            image_advtab: false,
        });


        $(document).ready(function () {
            $('.validator').cValidate();

        });
    </script>
@endsection
