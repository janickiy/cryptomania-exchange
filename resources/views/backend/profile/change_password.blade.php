@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row g-4 profile-dashboard profile-action-page">
        <div class="col-xl-3 col-lg-4">
            @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('user', $user->id)])
        </div>
        <div class="col-xl-9 col-lg-8">
            <div class="card profile-main-card profile-form-card">
                <div class="card-header p-0 border-bottom">
                    @include('backend.profile.profile_nav')
                </div>

                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <h3 class="card-title mb-1">{{ __('Change Password') }}</h3>
                            <div class="text-body-secondary">{{ __('Update the password used to sign in to your account.') }}</div>
                        </div>
                        <span class="badge text-bg-primary align-self-start">
                            <i class="fa fa-lock me-1"></i>{{ __('Security') }}
                        </span>
                    </div>

                    {{ Form::open(['route'=>['profile.update-password'],'class'=>'validator','method'=>'put']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">

                        <div class="row g-3">
                            <div class="col-12 {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label for="{{ fake_field('password') }}" class="form-label required">{{ __('Current Password') }}</label>
                                {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter current password'), 'id' => fake_field('password'),'data-cval-name' => 'The password','data-cval-rules' => 'required|escapeInput']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
                            </div>

                            <div class="col-md-6 {{ $errors->has('new_password') ? 'has-error' : '' }}">
                                <label for="new_password" class="form-label required">{{ __('New Password') }}</label>
                                {{ Form::password('new_password', ['class'=>'form-control', 'placeholder' => __('Enter new password'), 'id' => 'new_password','data-cval-name' => 'The new password','data-cval-rules' => 'required|escapeInput|between:6,32|followedBy:new_password_confirmation']) }}
                                <span class="validation-message cval-error" data-cval-error="new_password">{{ $errors->first('new_password') }}</span>
                            </div>

                            <div class="col-md-6 {{ $errors->has('new_password_confirmation') ? 'has-error' : '' }}">
                                <label for="new_password_confirmation" class="form-label required">{{ __('Confirm New Password') }}</label>
                                {{ Form::password('new_password_confirmation', ['class'=>'form-control', 'placeholder' => __('Confirm new password'), 'id' => 'new_password_confirmation','data-cval-name' => 'The confirm new password','data-cval-rules' => 'required|escapeInput|between:6,32|follow:new_password']) }}
                                <span class="validation-message cval-error" data-cval-error="new_password_confirmation">{{ $errors->first('new_password_confirmation') }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            {{ Form::submit(__('Update Password'),['class'=>'btn btn-primary form-submission-button']) }}
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">{{ __('View Profile') }}</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate();
        });
    </script>
@endsection
