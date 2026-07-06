@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="admin-list-page id-management-page">
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

        <div class="card admin-list-table-card id-management-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('ID Management') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Review submitted identity documents and verification status.') }}</p>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                    <thead>
                    <tr>
                        <th class="all">{{ __('Email') }}</th>
                        <th class="min-phone-l">{{ __('ID Type') }}</th>
                        <th class="min-phone-l text-center">{{ __('Verification Status') }}</th>
                        <th class="text-end all no-sort">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list['query'] as $user)
                        <tr>
                            <td>
                                @if(has_permission('users.show'))
                                    <a href="{{ route('users.show', $user->id) }}">{{ $user->email }}</a>
                                @else
                                    {{ $user->email }}
                                @endif
                            </td>
                            <td>{{ $user->id_type ? id_type($user->id_type) : '-' }}</td>
                            <td class="text-center">
                                <span class="badge text-bg-{{ config('commonconfig.id_status.' . $user->is_id_verified . '.color_class') }} admin-list-status-badge">
                                    {{ id_status($user->is_id_verified) }}
                                </span>
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
                                        @if(has_permission('admin.id-management.show'))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.id-management.show',$user->id)}}">
                                                    <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
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
