@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="admin-list-page review-withdrawals-page">
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
                    <h3 class="admin-page-title">{{ __('Withdrawals for Reviewing') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Review pending withdrawal requests, wallet details, and approval actions.') }}</p>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="min-desktop">{{ __('Ref ID') }}</th>
                            <th class="all">{{ __('Stock Item') }}</th>
                            <th class="all text-end">{{ __('Amount') }}</th>
                            <th class="min-desktop">{{ __('Address') }}</th>
                            <th class="min-phone-l text-center">{{ __('Status') }}</th>
                            <th class="none">{{ __('Withdrawn by') }}</th>
                            <th class="none">{{ __('Txn Id') }}</th>
                            <th class="min-desktop">{{ __('Date') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $reviewWithdrawal)
                            <tr>
                                <td><code class="review-withdrawal-ref-code">{{ $reviewWithdrawal->ref_id }}</code></td>
                                <td>
                                    <span class="fw-semibold">{{ $reviewWithdrawal->item_name }}</span>
                                    <span class="text-secondary">({{ $reviewWithdrawal->item }})</span>
                                </td>
                                <td class="text-end">{{ $reviewWithdrawal->amount }} <span class="fw-semibold">{{ $reviewWithdrawal->item }}</span></td>
                                <td><code class="review-withdrawal-address-code">{{ $reviewWithdrawal->address }}</code></td>
                                <td class="text-center">
                                    <span class="badge text-bg-{{ config('commonconfig.payment_status.' . $reviewWithdrawal->status . '.color_class') }} admin-list-status-badge">
                                        {{ payment_status($reviewWithdrawal->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if(has_permission('users.show'))
                                        <a href="{{ route('users.show', $reviewWithdrawal->user_id) }}">{{ $reviewWithdrawal->email }}</a>
                                    @else
                                        {{ $reviewWithdrawal->email }}
                                    @endif
                                </td>
                                <td>{{ $reviewWithdrawal->txn_id ?: '-' }}</td>
                                <td><span class="text-secondary">{{ $reviewWithdrawal->created_at->toFormattedDateString() }}</span></td>
                                <td class="cm-action text-end">
                                    @if(has_permission('admin.review-withdrawals.show'))
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
                                                    <a class="dropdown-item" href="{{ route('admin.review-withdrawals.show', $reviewWithdrawal->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
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
