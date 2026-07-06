@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-management-page report-open-orders-page">
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
                <h3 class="admin-page-title">{{ __('Open Orders') }}</h3>
                <p class="admin-page-subtitle">{{ __('Review active orders with market, category, price, amount, total, and stop rate details.') }}</p>
            </div>
        </div>

        {!! $filters !!}

        <div class="card user-table-card report-open-orders-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Market') }}</th>
                            <th class="min-desktop">{{ __('Type') }}</th>
                            <th class="min-desktop">{{ __('Category') }}</th>
                            <th class="all text-end">{{ __('Price') }}</th>
                            <th class="min-desktop text-end">{{ __('Amount') }}</th>
                            <th class="min-desktop text-end">{{ __('Total') }}</th>
                            @if(!$hideUser)
                                <th class="min-desktop">{{ __('User') }}</th>
                            @endif
                            <th class="none text-end">{{ __('Stop/Rate') }}</th>
                            <th class="min-desktop">{{ __('Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $transaction)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $transaction->stock_item_abbr }}/{{ $transaction->base_item_abbr }}</span>
                                </td>
                                <td>{{ exchange_type($transaction->exchange_type) }}</td>
                                <td>{{ category_type($transaction->category) }}</td>
                                <td class="text-end">{{ $transaction->price }} <span class="fw-semibold">{{ $transaction->base_item_abbr }}</span></td>
                                <td class="text-end">{{ $transaction->amount }} <span class="fw-semibold">{{ $transaction->stock_item_abbr }}</span></td>
                                <td class="text-end">{{ bcmul($transaction->amount, $transaction->price) }} <span class="fw-semibold">{{ $transaction->base_item_abbr }}</span></td>
                                @if(!$hideUser)
                                    <td>
                                        @if(has_permission('users.show'))
                                            <a href="{{ route('users.show', $transaction->user_id) }}">{{ $transaction->email }}</a>
                                        @else
                                            {{ $transaction->email }}
                                        @endif
                                    </td>
                                @endif
                                <td class="text-end">
                                    @if(!is_null($transaction->stop_limit))
                                        {{ $transaction->stop_limit }}
                                        <span class="fw-semibold">{{ $transaction->base_item_abbr }}</span>
                                    @else
                                        <span class="text-body-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $transaction->created_at->toFormattedDateString() }}</span>
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
