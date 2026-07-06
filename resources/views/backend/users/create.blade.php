@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-form-page">
        <div class="card user-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="user-form-heading">
                    <h3 class="user-form-title">{{ __('Create New User') }}</h3>
                    <p class="user-form-subtitle">{{ __('Set profile details, access group, and account availability.') }}</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary back-button">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
            <div class="card-body user-form-body">
                {!! Form::open(['route'=>'users.store', 'method' => 'post', 'class'=>'user-form user-create-form']) !!}
                @include('backend.users._create_form')
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.user-form').cValidate({});
        });
    </script>
@endsection
