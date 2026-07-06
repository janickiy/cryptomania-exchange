@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="system-notice-form-page">
        <div class="card system-notice-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Edit System Notice') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Update message content, visibility window, and publication status.') }}</p>
                </div>
                <a href="{{ route('system-notices.index') }}" class="btn btn-outline-secondary back-button">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
            <div class="card-body admin-form-body">
                {!! Form::model($systemNotice, ['route'=>['system-notices.update',$systemNotice->id], 'method' => 'post', 'class'=>'system-notice-form admin-section-form']) !!}
                @method('PUT')
                @include('backend.systemNotice._form',['buttonText'=> __('Update')])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('after-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('backend/assets/js/moment.js') }}"></script>
    <script src="{{ asset('common/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            //Init jquery Date Picker
            $('#start_time').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });

            $('#end_time').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });

            $("#start_time").on("dp.change", function (e) {
                $('#end_time').data("DateTimePicker").minDate(e.date);
            });
            $("#end_time").on("dp.change", function (e) {
                $('#start_time').data("DateTimePicker").maxDate(e.date);
            });

            $('.system-notice-form').cValidate({});
        });
    </script>
@endsection
