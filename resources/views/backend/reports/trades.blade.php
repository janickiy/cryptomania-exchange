@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-management-page report-trades-page">
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
            $categoryRouteName = 'reports.admin.allTrades';
            $categoryRouteParameters = [];

            if (request()->routeIs('reports.admin.trades')) {
                $categoryRouteName = 'reports.admin.trades';
                $categoryRouteParameters = ['userId' => request()->route('userId')];
            } elseif (request()->routeIs('reports.admin.stock-pairs.trades')) {
                $categoryRouteName = 'reports.admin.stock-pairs.trades';
                $categoryRouteParameters = ['id' => request()->route('id')];
            }
        @endphp

        <div class="report-page-heading">
            <div class="admin-page-heading">
                <h3 class="admin-page-title">{{ __('Trades') }}</h3>
                <p class="admin-page-subtitle">{{ __('Review executed trades with market, fee, total, user, and date details.') }}</p>
            </div>
        </div>

        {!! $filters !!}

        <div class="card user-table-card report-trades-table-card">
            <div class="card-header">
                @include('backend.reports._category_nav', [
                    'routeName' => $categoryRouteName,
                    'routeParameters' => $categoryRouteParameters,
                ])
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Market') }}</th>
                            <th class="all">{{ __('Type') }}</th>
                            @if(!$categoryType)
                                <th class="min-desktop">{{ __('Category') }}</th>
                            @endif
                            <th class="all text-end">{{ __('Price') }}</th>
                            <th class="min-desktop text-end">{{ __('Amount') }}</th>
                            <th class="min-desktop text-end">{{ admin_settings('referral') ? __('Fee + Referral Earning') : __('Fee') }}</th>
                            <th class="min-desktop text-end">{{ __('Total') }}</th>
                            <th class="all">{{ __('User') }}</th>
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
                                @if(!$categoryType)
                                    <td>{{ category_type($transaction->category) }}</td>
                                @endif
                                <td class="text-end">{{ $transaction->price }}</td>
                                <td class="text-end">{{ $transaction->amount }}</td>
                                <td class="text-end">
                                    {{ bcadd($transaction->fee,$transaction->referral_earning) }}
                                    <span class="text-body-secondary">
                                        ({{ $transaction->is_maker == 1 ?
                                                number_format($transaction->maker_fee, 2) . '%' :
                                                number_format($transaction->taker_fee, 2) . '%' }})
                                    </span>
                                </td>
                                <td class="text-end">{{ $transaction->total }}</td>
                                <td>
                                    @if(has_permission('users.show'))
                                        <a href="{{ route('users.show', $transaction->user_id) }}">{{ $transaction->email }}</a>
                                    @else
                                        {{ $transaction->email }}
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
