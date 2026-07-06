@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    @php
        $activeStatusColor = $stockPair->is_active == ACTIVE_STATUS_ACTIVE ? 'success' : 'secondary';
        $defaultStatusColor = $stockPair->is_default == ACTIVE_STATUS_ACTIVE ? 'primary' : 'light';
    @endphp

    <div class="stock-pair-show-page">
        <div class="card stock-pair-show-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{!! __('Details of :stockPair', ['stockPair' => '<strong>' . e($stockPair->stock_pair) . '</strong>']) !!}</h3>
                    <p class="admin-page-subtitle">{{ __('Review pair settings, 24-hour market movement, orders, and trade totals.') }}</p>
                </div>

                <div class="stock-pair-header-actions">
                    @if(has_permission('admin.stock-pairs.edit'))
                        <a href="{{ route('admin.stock-pairs.edit', $stockPair->id) }}" class="btn btn-outline-primary">
                            <i class="fa fa-pencil me-1"></i>{{ __('Edit Stock Pair') }}
                        </a>
                    @endif

                    @if(has_permission('admin.stock-pairs.index'))
                        <a href="{{ route('admin.stock-pairs.index') }}" class="btn btn-primary">
                            <i class="fa fa-list me-1"></i>{{ __('View all Stock Pair') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="info-box stock-pair-summary-box">
                    <span class="info-box-icon text-bg-info"><i class="fa fa-exchange"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('STOCK ITEM BUY FEES VOLUME') }}</span>
                        <span class="info-box-number">{{ $stockPair->exchanged_buy_fee }} {{ $stockPair->stockItem->item }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-box stock-pair-summary-box">
                    <span class="info-box-icon text-bg-warning"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('BASE ITEM SALE FEES VOLUME') }}</span>
                        <span class="info-box-number">{{ $stockPair->exchanged_sale_fee }} {{ $stockPair->baseItem->item }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-6">
                <div class="card stock-pair-detail-card h-100">
                    <div class="card-header">
                        <h4 class="stock-pair-card-title">{{ __('Pair Details') }}</h4>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __('Stock Pair') }}</th>
                                    <td class="fw-semibold">{{ $stockPair->stock_pair }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Exchangeable Item') }}</th>
                                    <td>{{ $stockPair->stockItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Base Item') }}</th>
                                    <td>{{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Active Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $activeStatusColor }} stock-pair-status-badge">
                                            {{ active_status($stockPair->is_active) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Default Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $defaultStatusColor }} stock-pair-status-badge">
                                            {{ active_status($stockPair->is_default) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created Date') }}</th>
                                    <td>{{ $stockPair->created_at }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card stock-pair-detail-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <h4 class="stock-pair-card-title">{!! __('24 Hour Exchange of :stockPair', ['stockPair' => '<strong>' . e($stockPair->stock_pair) . '</strong>']) !!}</h4>

                        @if(has_permission('reports.admin.stock-pairs.trades'))
                            <a href="{{ route('reports.admin.stock-pairs.trades', ['id' => $stockPair->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-history me-1"></i>{{ __('View Trade History') }}
                            </a>
                        @endif
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __('Last Price') }}</th>
                                    <td class="fw-semibold">{{ $stockPair->last_price }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('24hr Change') }}</th>
                                    <td>{{ number_format($stockPair->change_24, 8) }}%</td>
                                </tr>
                                <tr>
                                    <th>{{ __('24hr High') }}</th>
                                    <td>{{ number_format($stockPair->high_24, 8) }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('24hr Low') }}</th>
                                    <td>{{ number_format($stockPair->low_24, 8) }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('24hr Volume') }}</th>
                                    <td>
                                        {{ number_format($stockPair->exchanged_stock_item_volume_24, 8) }} {{ $stockPair->stockItem->item }}
                                        / {{ number_format($stockPair->exchanged_base_item_volume_24, 8) }} {{ $stockPair->baseItem->item }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card stock-pair-detail-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <h4 class="stock-pair-card-title">{!! __('Order Summery of :stockPair', ['stockPair' => '<strong>' . e($stockPair->stock_pair) . '</strong>']) !!}</h4>

                        @if(has_permission('reports.admin.stock-pairs.open-orders'))
                            <a href="{{ route('reports.admin.stock-pairs.open-orders', ['id' => $stockPair->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-list-alt me-1"></i>{{ __('View Open Orders') }}
                            </a>
                        @endif
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __(':item Buy Order Volume', ['item' => $stockPair->stockItem->item]) }}</th>
                                    <td>{{ $stockPair->stock_item_buy_order_volume }} {{ $stockPair->stockItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __(':item Sell Order Volume', ['item' => $stockPair->stockItem->item]) }}</th>
                                    <td>{{ $stockPair->stock_item_sale_order_volume }} {{ $stockPair->stockItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __(':item Buy Order Volume', ['item' => $stockPair->baseItem->item]) }}</th>
                                    <td>{{ $stockPair->stock_item_buy_order_volume }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __(':item Sell Order Volume', ['item' => $stockPair->baseItem->item]) }}</th>
                                    <td>{{ $stockPair->stock_item_sale_order_volume }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card stock-pair-detail-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <h4 class="stock-pair-card-title">{!! __('Trade Summery of :stockPair', ['stockPair' => '<strong>' . e($stockPair->stock_pair) . '</strong>']) !!}</h4>

                        @if(has_permission('reports.admin.stock-pairs.trades'))
                            <a href="{{ route('reports.admin.stock-pairs.trades', ['id' => $stockPair->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-history me-1"></i>{{ __('View Trade History') }}
                            </a>
                        @endif
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __(':item Exchanged Buy Total', ['item' => $stockPair->baseItem->item]) }}</th>
                                    <td>{{ $stockPair->exchanged_buy_total }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __(':item Exchanged Sale Total', ['item' => $stockPair->baseItem->item]) }}</th>
                                    <td>{{ $stockPair->exchanged_sale_total }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __(':item Exchanged Maker Total', ['item' => $stockPair->baseItem->item]) }}</th>
                                    <td>{{ $stockPair->exchanged_maker_total }} {{ $stockPair->baseItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __(':item Exchanged amount', ['item' => $stockPair->stockItem->item]) }}</th>
                                    <td>{{ $stockPair->exchanged_amount }} {{ $stockPair->stockItem->item }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
