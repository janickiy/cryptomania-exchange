@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    @php
        $itemEmojiUrl = get_item_emoji($stockItem->item_emoji);
        $apiServices = api_services();
        $apiServiceName = $stockItem->api_service !== null && array_key_exists($stockItem->api_service, $apiServices)
            ? $apiServices[$stockItem->api_service]
            : __('Not configured');
        $activeStatusColor = $stockItem->is_active == ACTIVE_STATUS_ACTIVE ? 'success' : 'secondary';
        $icoStatusColor = $stockItem->is_ico == ACTIVE_STATUS_ACTIVE ? 'primary' : 'secondary';
        $exchangeStatusColor = $stockItem->exchange_status == ACTIVE_STATUS_ACTIVE ? 'success' : 'secondary';
        $depositStatusColor = $stockItem->deposit_status == ACTIVE_STATUS_ACTIVE ? 'success' : 'secondary';
        $withdrawalStatusColor = $stockItem->withdrawal_status == ACTIVE_STATUS_ACTIVE ? 'success' : 'secondary';
    @endphp

    <div class="stock-pair-show-page stock-item-show-page">
        <div class="card stock-pair-show-card stock-item-show-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{!! __('Details of :stockItem', ['stockItem' => '<strong>' . e($stockItem->item) . '</strong>']) !!}</h3>
                    <p class="admin-page-subtitle">{{ __('Review currency settings, transfer limits, fees, and exchange availability.') }}</p>
                </div>

                <div class="stock-pair-header-actions">
                    @if(has_permission('admin.stock-items.edit'))
                        <a href="{{ route('admin.stock-items.edit', $stockItem->id) }}" class="btn btn-outline-primary">
                            <i class="fa fa-pencil me-1"></i>{{ __('Edit Stock Item') }}
                        </a>
                    @endif

                    @if(has_permission('admin.stock-items.index'))
                        <a href="{{ route('admin.stock-items.index') }}" class="btn btn-primary">
                            <i class="fa fa-list me-1"></i>{{ __('View All Stock Items') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="info-box stock-pair-summary-box">
                    <span class="info-box-icon text-bg-info"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Stock Item Type') }}</span>
                        <span class="info-box-number">{{ stock_item_types($stockItem->item_type) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box stock-pair-summary-box">
                    <span class="info-box-icon text-bg-{{ $activeStatusColor }}"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Active Status') }}</span>
                        <span class="info-box-number">{{ active_status($stockItem->is_active) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box stock-pair-summary-box">
                    <span class="info-box-icon text-bg-{{ $exchangeStatusColor }}"><i class="fa fa-exchange"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Exchange Status') }}</span>
                        <span class="info-box-number">{{ active_status($stockItem->exchange_status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-6">
                <div class="card stock-pair-detail-card stock-item-detail-card h-100">
                    <div class="card-header">
                        <h4 class="stock-pair-card-title">{{ __('Currency Details') }}</h4>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table stock-item-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __('Stock Item') }}</th>
                                    <td class="fw-semibold">{{ $stockItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Stock Item Name') }}</th>
                                    <td>{{ $stockItem->item_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Stock Item Emoji') }}</th>
                                    <td>
                                        @if($itemEmojiUrl)
                                            <img src="{{ $itemEmojiUrl }}" alt="{{ __('Emoji') }}" class="stock-item-table-emoji">
                                        @else
                                            <span class="stock-item-table-placeholder"><i class="fa fa-money"></i></span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Stock Item Type') }}</th>
                                    <td>{{ stock_item_types($stockItem->item_type) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Is ICO') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $icoStatusColor }} stock-pair-status-badge stock-item-status-badge">
                                            {{ active_status($stockItem->is_ico) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created At') }}</th>
                                    <td>{{ $stockItem->created_at?->toFormattedDateString() }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card stock-pair-detail-card stock-item-detail-card h-100">
                    <div class="card-header">
                        <h4 class="stock-pair-card-title">{{ __('Transfer And Exchange Settings') }}</h4>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table stock-item-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __('Active Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $activeStatusColor }} stock-pair-status-badge stock-item-status-badge">
                                            {{ active_status($stockItem->is_active) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Exchange Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $exchangeStatusColor }} stock-pair-status-badge stock-item-status-badge">
                                            {{ active_status($stockItem->exchange_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Deposit Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $depositStatusColor }} stock-pair-status-badge stock-item-status-badge">
                                            {{ active_status($stockItem->deposit_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Withdrawal Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ $withdrawalStatusColor }} stock-pair-status-badge stock-item-status-badge">
                                            {{ active_status($stockItem->withdrawal_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('API Service') }}</th>
                                    <td>{{ $apiServiceName }}</td>
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
                <div class="card stock-pair-detail-card stock-item-detail-card h-100">
                    <div class="card-header">
                        <h4 class="stock-pair-card-title">{{ __('Limits And Fees') }}</h4>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle stock-pair-detail-table stock-item-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __('Daily Withdrawal Limit') }}</th>
                                    <td>{{ $stockItem->daily_withdrawal_limit }} {{ $stockItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Minimum Withdrawal Amount') }}</th>
                                    <td>{{ $stockItem->minimum_withdrawal_amount }} {{ $stockItem->item }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Deposit Fee') }}</th>
                                    <td>{{ $stockItem->deposit_fee }}%</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Withdrawal Fee') }}</th>
                                    <td>{{ $stockItem->withdrawal_fee }}%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if(in_array($stockItem->item_type, config('commonconfig.currency_transferable')))
                <div class="col-xl-6">
                    <div class="card stock-pair-detail-card stock-item-detail-card h-100">
                        <div class="card-header">
                            <h4 class="stock-pair-card-title">{!! __('Transaction report of :stockItem', ['stockItem' => '<strong>' . e($stockItem->item) . '</strong>']) !!}</h4>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle stock-pair-detail-table stock-item-detail-table mb-0">
                                    <tbody>
                                    <tr>
                                        <th>{{ __('Total Deposit') }}</th>
                                        <td>{{ $stockItem->total_deposit }} {{ $stockItem->item }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Deposit Fee') }}</th>
                                        <td>{{ $stockItem->total_deposit_fee }} {{ $stockItem->item }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Withdrawal') }}</th>
                                        <td>{{ $stockItem->total_withdrawal }} {{ $stockItem->item }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Withdrawal Fee') }}</th>
                                        <td>{{ $stockItem->total_withdrawal_fee }} {{ $stockItem->item }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
