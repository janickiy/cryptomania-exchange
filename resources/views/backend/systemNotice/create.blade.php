@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="system-notice-form-page">
        <div class="card system-notice-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Create New Notice') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Publish timed system messages for users and administrators.') }}</p>
                </div>
                <a href="{{ route('system-notices.index') }}" class="btn btn-outline-secondary back-button">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
            <div class="card-body admin-form-body">
                {!! Form::open(['route'=>'system-notices.store', 'method' => 'post', 'class'=>'system-notice-form admin-section-form']) !!}
                @include('backend.systemNotice._form',['buttonText'=> __('Create')])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('after-style')
    @include('backend.systemNotice._datetime_picker_styles')
@endsection

@section('script')
    @include('backend.systemNotice._datetime_picker_scripts')
@endsection
