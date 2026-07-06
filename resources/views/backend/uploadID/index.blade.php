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
                            <h3 class="card-title mb-1">{{ __('Upload ID') }}</h3>
                            <div class="text-body-secondary">{{ __('Submit identity documents for account verification.') }}</div>
                        </div>
                        <span class="badge text-bg-info align-self-start">
                            <i class="fa fa-id-card me-1"></i>{{ id_status($user->userInfo->is_id_verified) }}
                        </span>
                    </div>

                    @if($user->userInfo->is_id_verified == ID_STATUS_UNVERIFIED)
                        @include('backend.uploadID._create_form')
                    @else
                        @include('backend.uploadID._show')
                    @endif
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

        new Vue({
            el: "#app",
            data: {
                step: 1,
                idType: false
            },
            methods : {
                nextStep : function(id) {
                    this.step = 2;
                    this.idType = id;
                },
                previousStep : function () {
                    this.step = 1;
                    this.idType = false;
                }
            }
        });
    </script>
@endsection
