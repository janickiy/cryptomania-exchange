@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="dashboard-page">
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="info-box dashboard-summary-box">
                    <span class="info-box-icon text-bg-info"><i class="fa fa-thermometer"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('CPU Traffic') }}</span>
                        <span class="info-box-number">{{ $cpuUsages }}<small>%</small></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-box dashboard-summary-box">
                    <span class="info-box-icon text-bg-danger"><i class="fa fa-snowflake-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('TOTAL STOCK PAIR') }}</span>
                        <span class="info-box-number">{{ $stockPairs->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-box dashboard-summary-box">
                    <span class="info-box-icon text-bg-success"><i class="fa fa-empire"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('TOTAL STOCK ITEM') }}</span>
                        <span class="info-box-number">{{ $totalStockItem }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="info-box dashboard-summary-box">
                    <span class="info-box-icon text-bg-warning"><i class="fa fa-user-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('TOTAL MEMBER') }}</span>
                        <span class="info-box-number">{{ $totalUser }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
            <div>
                <h3 class="dashboard-section-title mb-1">{{ __('Market Overview') }}</h3>
                <div class="text-body-secondary">{{ __('Active trading pairs and exchange volumes') }}</div>
            </div>
            <span class="badge text-bg-primary dashboard-count-badge">{{ $stockPairs->count() }} {{ __('pairs') }}</span>
        </div>

        <div class="row g-4 dashboard-pair-grid">
            @foreach($stockPairs as $stockPair)
                @php
                    $changeClass = 'text-body';
                    $changeIcon = 'fa-sort';

                    if ($stockPair->change_24 > 0) {
                        $changeClass = 'text-success';
                        $changeIcon = 'fa-caret-up';
                    } elseif ($stockPair->change_24 < 0) {
                        $changeClass = 'text-danger';
                        $changeIcon = 'fa-caret-down';
                    }
                @endphp

                <div class="col-xl-6">
                    <div class="card dashboard-pair-card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">{{ $stockPair->stock_item_abbr }}/{{ $stockPair->base_item_abbr }}</h4>
                                <div class="text-body-secondary small">{{ __('Trading pair') }}</div>
                            </div>
                            <span class="badge text-bg-light border {{ $changeClass }}">
                                <i class="fa {{ $changeIcon }} me-1"></i>{{ $stockPair->change_24 }}%
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="row g-3 dashboard-market-stats">
                                <div class="col-6 col-lg-3">
                                    <div class="dashboard-stat">
                                        <span class="dashboard-stat-value">{{ $stockPair->last_price }}</span>
                                        <span class="dashboard-stat-label">{{ __('Last Price') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="dashboard-stat">
                                        <span class="dashboard-stat-value">{{ $stockPair->high_24 }}</span>
                                        <span class="dashboard-stat-label">{{ __('24 High') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="dashboard-stat">
                                        <span class="dashboard-stat-value">{{ $stockPair->low_24 }}</span>
                                        <span class="dashboard-stat-label">{{ __('24 Low') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="dashboard-stat">
                                        <span class="dashboard-stat-value {{ $changeClass }}">
                                            <i class="fa {{ $changeIcon }} me-1"></i>{{ $stockPair->change_24 }}%
                                        </span>
                                        <span class="dashboard-stat-label">{{ __('24 Change') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-striped align-middle dashboard-pair-table mb-0">
                                    <tbody>
                                    <tr>
                                        <th>{{ __('On buy order base item volume') }}</th>
                                        <td>{{ $stockPair->base_item_buy_order_volume }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('On buy order stock item volume') }}</th>
                                        <td>{{ $stockPair->stock_item_buy_order_volume }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('On sell order base item volume') }}</th>
                                        <td>{{ $stockPair->base_item_sale_order_volume }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('On sell order stock item volume') }}</th>
                                        <td>{{ $stockPair->stock_item_sale_order_volume }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Buy Exchanged') }}</th>
                                        <td>{{ $stockPair->exchanged_buy_total }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Sell Exchanged') }}</th>
                                        <td>{{ $stockPair->exchanged_sale_total }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Amount Exchanged') }}</th>
                                        <td>{{ $stockPair->exchanged_amount }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Market Exchanged') }}</th>
                                        <td>{{ $stockPair->exchanged_maker_total }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Buy Fee') }}</th>
                                        <td>{{ $stockPair->exchanged_buy_fee }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Sell Fee') }}</th>
                                        <td>{{ $stockPair->exchanged_sale_fee }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
