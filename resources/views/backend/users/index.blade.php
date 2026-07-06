@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-management-page">
        {!!  $list['filters'] !!}

        <div class="card user-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="user-table-heading">
                    <h3 class="user-table-title">{{ __('Users') }}</h3>
                    <p class="user-table-subtitle">{{ __('Manage platform accounts, access groups, and user activity.') }}</p>
                </div>
                @if(has_permission('users.create'))
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>{{ __('Create User') }}
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Email') }}</th>
                            <th class="min-phone-l">{{ __('First Name') }}</th>
                            <th class="min-phone-l">{{ __('Last Name') }}</th>
                            <th class="min-phone-l">{{ __('User Group') }}</th>
                            <th class="min-phone-l">{{ __('Username') }}</th>
                            <th class="none">{{ __('Registered Date') }}</th>
                            <th class="text-center min-phone-l">{{ __('Status') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $key=>$user)
                            <tr>
                                <td>
                                    @if(has_permission('users.show'))
                                        <a href="{{ route('users.show', $user->id) }}">{{ $user->email }}</a>
                                    @else
                                        {{ $user->email }}
                                    @endif
                                </td>
                                <td>{{ $user->first_name ?: '-' }}</td>
                                <td>{{ $user->last_name ?: '-' }}</td>
                                <td>{{ $user->role_name ?: '-' }}</td>
                                <td>{{ $user->username ?: '-' }}</td>
                                <td>
                                    <span class="text-secondary">{{ $user->created_at->format('M j, Y') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($user->is_active)
                                        <span class="badge text-bg-success user-status-badge">
                                            <i class="fa fa-check me-1"></i>{{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary user-status-badge">
                                            <i class="fa fa-ban me-1"></i>{{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="cm-action text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary table-action-button dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="{{ __('Action') }}">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @if(has_permission('users.show'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('users.show',$user->id)}}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(has_permission('users.edit'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('users.edit',$user->id)}}">
                                                        <i class="fa fa-pen-to-square me-2 text-primary"></i>{{ __('Edit Info') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('users.edit.status',$user->id)}}">
                                                        <i class="fa fa-sliders me-2 text-primary"></i>{{ __('Edit Status') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.users.wallets'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.wallets',$user->id)}}">
                                                        <i class="fa fa-wallet me-2 text-info"></i>{{ __('View Wallets') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('reports.admin.open-orders'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('reports.admin.open-orders', $user->id)}}">
                                                        <i class="fa fa-list-check me-2 text-info"></i>{{ __('View Open Orders') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('reports.admin.trades'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('reports.admin.trades', $user->id)}}">
                                                        <i class="fa fa-clock-rotate-left me-2 text-info"></i>{{ __('View trade history') }}
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
