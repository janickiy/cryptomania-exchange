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
                            <h3 class="card-title mb-1">{{ __('Google Authentication') }}</h3>
                            <div class="text-body-secondary">{{ __('Manage Google Authenticator protection for your account.') }}</div>
                        </div>
                        @if(!empty(Auth::user()->google2fa_secret))
                            <span class="badge text-bg-success align-self-start">
                                <i class="fa fa-shield me-1"></i>{{ __('Enabled') }}
                            </span>
                        @else
                            <span class="badge text-bg-warning align-self-start">
                                <i class="fa fa-shield me-1"></i>{{ __('Disabled') }}
                            </span>
                        @endif
                    </div>

                    @if(!empty(Auth::user()->google2fa_secret))
                        @include('backend.google2fa._edit_form')
                    @else
                        @include('backend.google2fa._create_form')
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">{{ __('View Profile') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });
    </script>
@endsection
