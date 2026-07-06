@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="admin-list-page notices-page">
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
                    <h3 class="admin-page-title">{{ __('Notices') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Review account notifications and manage read status.') }}</p>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Notice') }}</th>
                            <th class="min-phone-l">{{ __('Date') }}</th>
                            <th class="min-phone-l text-center">{{ __('Status') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $notice)
                            <tr class="{{ $notice->read_at ? '' : 'notice-row-unread' }}">
                                <td>
                                    <span class="admin-list-message">{{ $notice->data }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $notice->created_at }}</span>
                                </td>
                                <td class="text-center">
                                    @if($notice->read_at)
                                        <span class="badge text-bg-success admin-list-status-badge">
                                            <i class="fa fa-check me-1"></i>{{ __('Read') }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-warning admin-list-status-badge">
                                            <i class="fa fa-circle-o me-1"></i>{{ __('Unread') }}
                                        </span>
                                    @endif
                                </td>
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
                                            <li>
                                                @if($notice->read_at)
                                                    <a class="dropdown-item" href="{{ route('notices.mark-as-unread', $notice->id) }}">
                                                        <i class="fa fa-circle-o me-2 text-warning"></i>{{ __('Mark as unread') }}
                                                    </a>
                                                @else
                                                    <a class="dropdown-item" href="{{ route('notices.mark-as-read', $notice->id) }}">
                                                        <i class="fa fa-check-circle-o me-2 text-success"></i>{{ __('Mark as read') }}
                                                    </a>
                                                @endif
                                            </li>
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
