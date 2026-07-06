@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-management-page user-wallets-page">
        @php
            $filters = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card user-filter-card', 'card-body'],
                $list['filters']
            );
            $pagination = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card user-pagination-card', 'card-body'],
                $list['pagination']
            );
        @endphp

        {!! $filters !!}

        <div class="card user-table-card user-wallets-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="user-table-heading">
                    <h3 class="user-table-title">{{ __('Wallets') }}</h3>
                    <p class="user-table-subtitle">{{ __('Review balances, reserved funds, and wallet history for this user.') }}</p>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th class="all">{{ __('Wallet') }}</th>
                                <th class="min-phone-l">{{ __('Wallet Name') }}</th>
                                <th class="min-phone-l text-end">{{ __('Total Balance') }}</th>
                                <th class="min-phone-l text-end">{{ __('On Order') }}</th>
                                <th class="text-end all no-sort">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($list['query'] as $wallet)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $wallet->item }}</span>
                                    </td>
                                    <td>{{ $wallet->item_name }}</td>
                                    <td class="text-end">{{ $wallet->primary_balance }}</td>
                                    <td class="text-end">{{ $wallet->on_order_balance }}</td>
                                    <td class="cm-action text-end">
                                        @if(in_array($wallet->item_type, config('commonconfig.currency_transferable')))
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-outline-secondary table-action-button dropdown-toggle"
                                                        type="button"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        aria-label="{{ __('Action') }}">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    @if(has_permission('reports.admin.wallets.deposits'))
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('reports.admin.wallets.deposits', ['id' => $wallet->id]) }}">
                                                                <i class="fa fa-arrow-down me-2 text-success"></i>{{ __('Deposit History') }}
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if(has_permission('reports.admin.wallets.withdrawals'))
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('reports.admin.wallets.withdrawals', ['id' => $wallet->id]) }}">
                                                                <i class="fa fa-arrow-up me-2 text-warning"></i>{{ __('Withdrawal History') }}
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if(has_permission('admin.users.wallets.edit'))
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.users.wallets.edit', ['id' => $wallet->user_id, 'walletId' => $wallet->id]) }}">
                                                                <i class="fa fa-wallet me-2 text-primary"></i>{{ __('Give Amount') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @else
                                            <span class="text-body-secondary">-</span>
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
        $(document).ready(function () {
            //Init jquery Date Picker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'bottom',
                todayHighlight: true
            });
        });
    </script>
@endsection
