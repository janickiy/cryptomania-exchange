@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    @php
        $emailStatusColor = config('commonconfig.email_status.' . $user->is_email_verified . '.color_class');
        $accountStatusColor = config('commonconfig.account_status.' . $user->is_active . '.color_class');
        $financialStatusColor = config('commonconfig.financial_status.' . $user->is_financial_active . '.color_class');
        $maintenanceStatusColor = config('commonconfig.maintenance_accessible_status.' . $user->is_accessible_under_maintenance . '.color_class');
    @endphp

    <div class="row g-4 profile-dashboard">
        <div class="col-xl-3 col-lg-4">
            @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('user', $user->id)])
        </div>
        <div class="col-xl-9 col-lg-8">
            <div class="card profile-main-card">
                <div class="card-header p-0 border-bottom">
                    @include('backend.profile.profile_nav')
                </div>

                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <h3 class="card-title mb-1">{{ $user->userInfo->full_name }}</h3>
                            <div class="text-body-secondary">{{ $user->userRoleManagement->role_name }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="fa fa-pencil me-1"></i>{{ __('Edit Profile') }}
                        </a>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="info-box profile-info-box">
                                <span class="info-box-icon text-bg-{{ $accountStatusColor }}"><i class="fa fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('Account Status') }}</span>
                                    <span class="info-box-number">{{ account_status($user->is_active) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box profile-info-box">
                                <span class="info-box-icon text-bg-{{ $financialStatusColor }}"><i class="fa fa-credit-card"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('Financial Status') }}</span>
                                    <span class="info-box-number">{{ financial_status($user->is_financial_active) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box profile-info-box">
                                <span class="info-box-icon text-bg-{{ $maintenanceStatusColor }}"><i class="fa fa-shield"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('Maintenance Access') }}</span>
                                    <span class="info-box-number">{{ maintenance_accessible_status($user->is_accessible_under_maintenance) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle profile-details-table mb-0">
                            <tbody>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <td>{{ $user->userInfo->full_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('User Role') }}</th>
                                <td>{{ $user->userRoleManagement->role_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Email') }}</th>
                                <td>
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                        <span>{{ $user->email }}</span>
                                        @if( admin_settings('require_email_verification') == ACTIVE_STATUS_ACTIVE )
                                            <span class="badge text-bg-{{ $emailStatusColor }}">{{ email_status($user->is_email_verified) }}</span>
                                            @if($user->is_email_verified != EMAIL_VERIFICATION_STATUS_ACTIVE)
                                                <a class="btn btn-sm btn-outline-primary ms-md-auto" href="{{ route('verification.form') }}">
                                                    {{ __('Verify Account') }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Username') }}</th>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Address') }}</th>
                                <td>{{ $user->userInfo->address ?: __('Not provided') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Account Status') }}</th>
                                <td><span class="badge text-bg-{{ $accountStatusColor }}">{{ account_status($user->is_active) }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('Financial Status') }}</th>
                                <td><span class="badge text-bg-{{ $financialStatusColor }}">{{ financial_status($user->is_financial_active) }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('Maintenance Access Status') }}</th>
                                <td><span class="badge text-bg-{{ $maintenanceStatusColor }}">{{ maintenance_accessible_status($user->is_accessible_under_maintenance) }}</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
