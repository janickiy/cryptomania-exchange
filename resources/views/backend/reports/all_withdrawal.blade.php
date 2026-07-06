@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-management-page report-payment-page">
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

        <div class="report-page-heading">
            <div class="admin-page-heading">
                <h3 class="admin-page-title">{{ __('List of Withdrawals') }}</h3>
                <p class="admin-page-subtitle">{{ __('Review outgoing transactions with status, user, address, and reference details.') }}</p>
            </div>
        </div>

        {!! $filters !!}

        <div class="card user-table-card report-payment-table-card">
            <div class="card-header">
                @include('backend.reports._payment_nav', ['routeName' => 'reports.admin.all-withdrawals'])
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                    <thead>
                    <tr>
                        <th class="min-desktop">{{ __('Ref ID') }}</th>
                        <th class="all">{{ __('Stock Name') }}</th>
                        <th class="all text-end">{{ __('Amount') }}</th>
                        @if(!$status)
                            <th class="all text-center">{{ __('Status') }}</th>
                        @endif
                        <th class="all">{{ __('User') }}</th>
                        <th class="none">{{ __('Address') }}</th>
                        <th class="none">{{ __('Txn Id') }}</th>
                        <th class="min-desktop">{{ __('Date') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list['query'] as $transaction)
                        <tr>
                            <td><code class="report-ref-code">{{ $transaction->ref_id }}</code></td>
                            <td><span class="fw-semibold">{{ $transaction->item_name }}</span> <span class="text-secondary">({{ $transaction->item }})</span></td>
                            <td class="text-end">{{ $transaction->amount }} <span class="fw-semibold">{{ $transaction->item }}</span></td>
                            @if(!$status)
                                <td class="text-center">
                                    <span class="badge text-bg-{{ config('commonconfig.payment_status.' . $transaction->status . '.color_class') }} report-status-badge">
                                        {{ payment_status($transaction->status) }}
                                    </span>
                                </td>
                            @endif
                            <td>
                                @if(has_permission('users.show'))
                                    <a href="{{ route('users.show', $transaction->user_id) }}">{{ $transaction->email }}</a>
                                @else
                                    {{ $transaction->email }}
                                @endif
                            </td>
                            <td>{{ $transaction->address }}</td>
                            <td>{{ $transaction->txn_id }}</td>
                            <td><span class="text-secondary">{{ $transaction->created_at->toFormattedDateString() }}</span></td>
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
