@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-form-page user-status-page">
        <div class="row g-3">
            <div class="col-lg-3">
                @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('admin', $user->id)])
            </div>

            <div class="col-lg-9">
                <div class="card user-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <div class="user-form-heading">
                            <h3 class="user-form-title">{!! __('Status Details of :user', ['user' => '<strong>' . e($user->userInfo->full_name) . '</strong>']) !!}</h3>
                            <p class="user-form-subtitle">{{ __('Manage access, verification, financial, and maintenance availability for this account.') }}</p>
                        </div>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary back-button">
                            <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                        </a>
                    </div>

                    <div class="card-body user-form-body">
                        @if($user->id == Auth::user()->id)
                            <div class="alert alert-warning mb-0">
                                <i class="fa fa-exclamation-triangle me-2"></i>{{ __('You cannot change your own status.') }}
                            </div>
                        @elseif(in_array($user->id, config('commonconfig.fixed_users')))
                            <div class="alert alert-warning mb-0">
                                <i class="fa fa-exclamation-triangle me-2"></i>{{ __("You cannot change primary user's status.") }}
                            </div>
                        @else
                            {{ Form::model($user,['route'=>['users.update.status',$user->id],'class'=>'user-form user-status-form','method'=>'put']) }}
                            @include('backend.users._edit_status_form')
                            {{ Form::close() }}
                        @endif
                    </div>

                    <div class="card-footer user-edit-actions">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fa fa-eye me-1"></i>{{ __('View Information') }}
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-warning">
                            <i class="fa fa-pencil me-1"></i>{{ __('Edit Information') }}
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-primary ms-auto">
                            <i class="fa fa-users me-1"></i>{{ __('View All Users') }}
                        </a>
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
            $('.user-form').cValidate({});
        });
    </script>
@endsection
