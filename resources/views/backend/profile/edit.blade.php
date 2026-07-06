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
                            <h3 class="mb-1 fw-bold">{{ __('Edit Profile') }}</h3>
                            <div class="text-body-secondary">{{ __('Update your personal account information.') }}</div>
                        </div>
                        <span class="badge text-bg-primary align-self-start">
                            <i class="fa fa-pencil me-1"></i>{{ __('Profile') }}
                        </span>
                    </div>

                    {{ Form::open(['route'=>['profile.update'],'class'=>'edit-profile-form','method'=>'put']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">

                        <div class="row g-3">
                            <div class="col-md-6 {{ $errors->has('first_name') ? 'has-error' : '' }}">
                                <label for="{{ fake_field('first_name') }}" class="form-label required">{{ __('First Name') }}</label>
                                {{ Form::text(fake_field('first_name'), old('first_name', $user->userInfo->first_name), ['class'=>'form-control', 'id' => fake_field('first_name'),'data-cval-name' => 'The first name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('first_name') }}">{{ $errors->first('first_name') }}</span>
                            </div>

                            <div class="col-md-6 {{ $errors->has('last_name') ? 'has-error' : '' }}">
                                <label for="{{ fake_field('last_name') }}" class="form-label required">{{ __('Last Name') }}</label>
                                {{ Form::text(fake_field('last_name'), old('last_name', $user->userInfo->last_name), ['class'=>'form-control', 'id' => fake_field('last_name'),'data-cval-name' => 'The last name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('last_name') }}">{{ $errors->first('last_name') }}</span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <div class="form-control bg-body-tertiary">{{ $user->email }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('Username') }}</label>
                                <div class="form-control bg-body-tertiary">{{ $user->username }}</div>
                            </div>

                            <div class="col-12 {{ $errors->has('address') ? 'has-error' : '' }}">
                                <label for="{{ fake_field('address') }}" class="form-label">{{ __('Address') }}</label>
                                {{ Form::textarea(fake_field('address'),  old('address', $user->userInfo->address), ['class'=>'form-control', 'id' => fake_field('address'), 'rows'=>2,'data-cval-name' => 'The address name field','data-cval-rules' => 'escapeInput']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('address') }}">{{ $errors->first('address') }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            {{ Form::submit(__('Update Information'),['class'=>'btn btn-primary form-submission-button']) }}
                            {{ Form::reset(__('Reset'),['class'=>'btn btn-outline-secondary reset-button']) }}
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
            $('.edit-profile-form').cValidate();
        });
    </script>
@endsection
