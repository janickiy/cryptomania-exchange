@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-form-page user-edit-page">
        <div class="row g-3">
            <div class="col-lg-3">
            @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('admin', $user->id)])
            </div>

            <div class="col-lg-9">
                <div class="card user-form-card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <div class="user-form-heading">
                            <h3 class="user-form-title">{!! __('Basic Details of :user', ['user' => '<strong>' . e($user->userInfo->full_name) . '</strong>']) !!}</h3>
                            <p class="user-form-subtitle">{{ __('Update profile details and access group for this account.') }}</p>
                        </div>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary back-button">
                            <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                        </a>
                    </div>

                    <div class="card-body user-form-body">
                        {{ Form::model($user,['route'=>['users.update',$user->id],'class'=>'user-form user-edit-form','method'=>'put']) }}
                        @include('backend.users._edit_form')
                        {{ Form::close() }}
                    </div>

                    <div class="card-footer user-edit-actions">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fa fa-eye me-1"></i>{{ __('View Information') }}
                        </a>
                        <a href="{{ route('users.edit.status', $user->id) }}" class="btn btn-outline-warning">
                            <i class="fa fa-sliders me-1"></i>{{ __('Edit Status') }}
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
