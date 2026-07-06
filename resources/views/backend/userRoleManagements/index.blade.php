@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="role-management-page">
        {!! $list['filters'] !!}

        <div class="card role-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="role-table-heading">
                    <h3 class="role-table-title">{{ __('Role Management') }}</h3>
                    <p class="role-table-subtitle">{{ __('Manage access groups and route permissions.') }}</p>
                </div>
                @if(has_permission('user-role-managements.create'))
                    <a href="{{ route('user-role-managements.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>{{ __('Create User Role') }}
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th class="all">{{ __('Role Name') }}</th>
                                <th class="min-phone-l">{{ __('Created Date') }}</th>
                                <th class="min-phone-l text-center">{{ __('Status') }}</th>
                                <th class="text-end all no-sort">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list['query'] as $userRoleManagement)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $userRoleManagement->role_name }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ $userRoleManagement->created_at->toFormattedDateString() }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($userRoleManagement->is_active == ACTIVE_STATUS_ACTIVE)
                                            <span class="badge text-bg-success role-status-badge">
                                                <i class="fa fa-check me-1"></i>{{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-secondary role-status-badge">
                                                <i class="fa fa-ban me-1"></i>{{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="cm-action text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary role-action-button dropdown-toggle"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                    aria-label="{{ __('Action') }}">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('user-role-managements.edit', $userRoleManagement->id) }}">
                                                        <i class="fa fa-pen-to-square me-2 text-primary"></i>{{ __('Edit') }}
                                                    </a>
                                                </li>
                                                @if(!in_array($userRoleManagement->id, $defaultRoles))
                                                    <li>
                                                        <a class="dropdown-item text-danger confirmation"
                                                           data-alert="{{ __('Do you want to delete this role?') }}"
                                                           data-form-id="ur-{{ $userRoleManagement->id }}"
                                                           data-form-method="DELETE"
                                                           href="{{ route('user-role-managements.destroy', $userRoleManagement->id) }}">
                                                            <i class="fa fa-trash-can me-2"></i>{{ __('Delete') }}
                                                        </a>
                                                    </li>
                                                    @if($userRoleManagement->is_active == ACTIVE_STATUS_ACTIVE)
                                                        <li>
                                                            <a class="dropdown-item confirmation"
                                                               data-form-id="ur-status-{{ $userRoleManagement->id }}"
                                                               data-form-method="PUT"
                                                               href="{{ route('user-role-managements.status', $userRoleManagement->id) }}"
                                                               data-alert="{{ __('Do you want to disable this role?') }}">
                                                                <i class="fa fa-circle-xmark me-2 text-warning"></i>{{ __('Disable') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif
                                                @if($userRoleManagement->is_active == ACTIVE_STATUS_INACTIVE)
                                                    <li>
                                                        <a class="dropdown-item confirmation"
                                                           data-form-id="ur-status-{{ $userRoleManagement->id }}"
                                                           data-form-method="PUT"
                                                           href="{{ route('user-role-managements.status', $userRoleManagement->id) }}"
                                                           data-alert="{{ __('Do you want to active this role?') }}">
                                                            <i class="fa fa-square-check me-2 text-success"></i>{{ __('Active') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {!! $list['pagination'] !!}
    </div>
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{ asset('common/vendors/datepicker/datepicker.js') }}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/table-datatables-responsive.js')}}"></script>
    <script type="text/javascript">
        //Init jquery Date Picker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom',
            todayHighlight: true,
        });
    </script>
@endsection
