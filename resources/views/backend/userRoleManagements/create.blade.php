@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="role-management-page">
        <div class="card role-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div>
                    <h3 class="card-title mb-1">{{ __('Create User Role') }}</h3>
                    <p class="mb-0 text-secondary">{{ __('Select the routes this role can access.') }}</p>
                </div>
                <a href="{{ route('user-role-managements.index') }}" class="btn btn-outline-secondary back-button">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                </a>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => ['user-role-managements.store'], 'method' => 'POST', 'class' => 'user-role-management-form']) !!}
                    @include('backend.userRoleManagements._form', ['buttonText' => __('Create')])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('backend/assets/js/role_manager.js') }}"></script>
    <script>
        $(document).ready(function () {
            function toggleChecks(selector, checked) {
                var $items = $(selector);

                if ($.fn.iCheck) {
                    $items.iCheck(checked ? 'check' : 'uncheck');
                    return;
                }

                $items.prop('checked', checked).trigger('change');
            }

            $(document).on('ifChecked', '.module', function () {
                toggleChecks('.module_action_' + $(this).attr('data-id'), true);
            });
            $(document).on('ifUnchecked', '.module', function () {
                toggleChecks('.module_action_' + $(this).attr('data-id'), false);
            });

            $(document).on('ifChecked', '.task', function () {
                toggleChecks('.task_action_' + $(this).attr('data-id'), true);
            });

            $(document).on('ifUnchecked', '.task', function () {
                toggleChecks('.task_action_' + $(this).attr('data-id'), false);
            });

            $(document).on('change', '.module', function () {
                if (!$(this).data('iCheck')) {
                    toggleChecks('.module_action_' + $(this).attr('data-id'), $(this).is(':checked'));
                }
            });

            $(document).on('change', '.task', function () {
                if (!$(this).data('iCheck')) {
                    toggleChecks('.task_action_' + $(this).attr('data-id'), $(this).is(':checked'));
                }
            });

            $('.user-role-management-form').cValidate({});
        });
    </script>
@endsection
