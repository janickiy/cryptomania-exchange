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
                            <h3 class="card-title mb-1">{{ __('Change Avatar') }}</h3>
                            <div class="text-body-secondary">{{ __('Upload a square profile image for your account.') }}</div>
                        </div>
                        <img src="{{ get_avatar($user->avatar) }}" alt="{{ __('Profile Image') }}"
                             class="profile-avatar-preview rounded-circle shadow-sm">
                    </div>

                    {{ Form::open(['route'=>['profile.avatar.update'],'class'=>'validator','method'=>'put', 'enctype'=>'multipart/form-data']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">

                        <div class="profile-file-drop {{ $errors->has('avatar') ? 'has-error' : '' }}">
                            <div class="profile-file-drop-icon text-bg-primary">
                                <i class="fa fa-image"></i>
                            </div>
                            <div class="profile-file-drop-content">
                                <label for="{{ fake_field('avatar') }}" class="form-label required">{{ __('Upload new avatar') }}</label>
                                {{ Form::file(fake_field('avatar'), ['class' => 'form-control', 'id' => fake_field('avatar'), 'data-cval-name' => 'The avatar','data-cval-rules' => 'required|files:jpg,png,jpeg|max:2048']) }}
                                <div class="form-text">{{ __('Upload avatar 300x300 and less than or equal 2MB.') }}</div>
                            </div>
                            <span class="validation-message cval-error"
                                  data-cval-error="{{ fake_field('avatar') }}">{{ $errors->first('avatar') }}</span>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            {{ Form::submit(__('Upload Avatar'), ['class'=>'btn btn-primary form-submission-button']) }}
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
