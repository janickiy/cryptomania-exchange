@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="admin-list-page system-notices-page">
        @php
            $filters = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card admin-list-filter-card', 'card-body'],
                $list['filters']
            );
            $pagination = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card admin-list-pagination-card', 'card-body'],
                $list['pagination']
            );
        @endphp

        {!! $filters !!}

        <div class="card admin-list-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('System Notices') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Manage global messages, visibility windows, and publication status.') }}</p>
                </div>

                @if(has_permission('system-notices.create'))
                    <a href="{{ route('system-notices.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>{{ __('Create Notice') }}
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Title') }}</th>
                            <th class="min-phone-l">{{ __('Start Time') }}</th>
                            <th class="min-phone-l">{{ __('End Time') }}</th>
                            <th class="min-phone-l text-center">{{ __('Type') }}</th>
                            <th class="min-phone-l text-center">{{ __('Status') }}</th>
                            <th class="none">{{ __('Description') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $notice)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $notice->title }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $notice->start_at ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $notice->end_at ?: '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge text-bg-{{ $notice->type }} admin-list-type-badge">
                                        {{ ucfirst($notice->type) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($notice->status == ACTIVE_STATUS_ACTIVE)
                                        <span class="badge text-bg-success admin-list-status-badge">
                                            <i class="fa fa-check me-1"></i>{{ active_status($notice->status) }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary admin-list-status-badge">
                                            <i class="fa fa-ban me-1"></i>{{ active_status($notice->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $notice->description }}</td>
                                <td class="cm-action text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary admin-list-action-button"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="{{ __('Action') }}">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @if(has_permission('system-notices.edit'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('system-notices.edit', $notice->id) }}">
                                                        <i class="fa fa-pencil me-2 text-primary"></i>{{ __('Edit') }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(has_permission('system-notices.destroy'))
                                                <li>
                                                    <a class="dropdown-item text-danger confirmation"
                                                       data-alert="{{ __('Are you sure?') }}"
                                                       data-form-id="system-notice-{{ $notice->id }}"
                                                       data-form-method="DELETE"
                                                       href="{{ route('system-notices.destroy', $notice->id) }}">
                                                        <i class="fa fa-trash-o me-2"></i>{{ __('Delete') }}
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

        {!! $pagination !!}
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
