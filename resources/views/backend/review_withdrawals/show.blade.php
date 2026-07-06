@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="review-withdrawal-show-page">
        <div class="row g-3 align-items-start">
            <div class="col-lg-3">
                @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('admin', $user->id)])
            </div>

            <div class="col-lg-9">
                <div class="card review-withdrawal-show-card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <div class="admin-page-heading">
                            <h3 class="admin-page-title">
                                {!! __('Withdrawal Details of :user for :stockItem', ['user' => '<strong>' . e($user->userInfo->full_name) . '</strong>', 'stockItem' => '<strong>' . e($withdrawal->stockItem->item) . '</strong>']) !!}
                            </h3>
                            <p class="admin-page-subtitle">{{ __('Check the request details before approving or declining the withdrawal.') }}</p>
                        </div>

                        <a href="{{ route('admin.review-withdrawals.index') }}" class="btn btn-primary">
                            <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle review-withdrawal-detail-table mb-0">
                                <tbody>
                                <tr>
                                    <th>{{ __('Ref ID') }}</th>
                                    <td><code class="review-withdrawal-ref-code">{{ $withdrawal->ref_id }}</code></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Stock Item') }}</th>
                                    <td>{{ $withdrawal->stockItem->item_name }} <span class="text-secondary">({{ $withdrawal->stockItem->item }})</span></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Withdrawal Amount') }}</th>
                                    <td><span class="fw-semibold text-danger">{{ $withdrawal->amount }} {{ $withdrawal->stockItem->item }}</span></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td><code class="review-withdrawal-address-code">{{ $withdrawal->address }}</code></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Current Wallet Balance') }}</th>
                                    <td><span class="fw-semibold text-success">{{ $withdrawal->wallet->primary_balance }} {{ $withdrawal->stockItem->item }}</span></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        <span class="badge text-bg-{{ config('commonconfig.payment_status.' . $withdrawal->status . '.color_class') }} admin-list-status-badge">
                                            {{ payment_status($withdrawal->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Txn ID') }}</th>
                                    <td>{{ $withdrawal->txn_id ?: '-' }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($withdrawal->status == PAYMENT_REVIEWING && (has_permission('admin.review-withdrawals.approve') || has_permission('admin.review-withdrawals.decline')))
                        <div class="card-footer">
                            <div class="review-withdrawal-actions">
                                @if(has_permission('admin.review-withdrawals.approve'))
                                    <a href="{{ route('admin.review-withdrawals.approve', ['id' => $withdrawal->id]) }}"
                                       class="btn btn-primary confirmation"
                                       data-form-id="approve-{{ $withdrawal->id }}"
                                       data-form-method="PUT"
                                       data-alert="{{__('Do you want to approve this withdrawal?')}}">
                                        <i class="fa fa-check me-1"></i>{{ __('Approve') }}
                                    </a>
                                @endif

                                @if(has_permission('admin.review-withdrawals.decline'))
                                    <a href="{{ route('admin.review-withdrawals.decline', ['id' => $withdrawal->id]) }}"
                                       class="btn btn-outline-danger confirmation"
                                       data-form-id="decline-{{ $withdrawal->id }}"
                                       data-form-method="PUT"
                                       data-alert="{{__('Do you want to decline this withdrawal?')}}">
                                        <i class="fa fa-times me-1"></i>{{ __('Decline') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
