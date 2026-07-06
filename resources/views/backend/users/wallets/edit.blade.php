@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-form-page wallet-balance-page">
        <div class="card user-form-card wallet-balance-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="user-form-heading">
                    <h3 class="user-form-title">{{ __('Give :stockItem Amount', ['stockItem' => $wallet->stockItem->item]) }}</h3>
                    <p class="user-form-subtitle">{{ __('Adjust the selected wallet balance for this user account.') }}</p>
                </div>
                <a href="{{ route('admin.users.wallets', $wallet->user_id) }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back to wallets') }}
                </a>
            </div>

            <div class="card-body user-form-body">
                {!! Form::open(['route' => ['admin.users.wallets.update', 'id' => $wallet->user_id, 'walletId' => $wallet->id], 'method' => 'post', 'class'=>'validator user-form wallet-balance-form']) !!}
                {{ Form::hidden('base_key', base_key()) }}

                <section class="user-form-section">
                    <div class="user-form-section-header">
                        <h4 class="user-form-section-title">{{ __('Wallet Balance') }}</h4>
                    </div>

                    <div class="user-form-grid">
                        <div class="user-form-field {{ $errors->has('amount') ? 'has-error' : '' }}">
                            <label for="amount" class="form-label required">{{ __('Amount') }}</label>
                            {{ Form::text('amount',  old('amount', null), ['class'=>'form-control', 'id' => 'amount','data-cval-name' => 'The amount field','data-cval-rules' => 'required|numeric|escapeInput|between:0.00000001, 99999999999.99999999', 'placeholder' => __('ex: 0.00000001')]) }}
                            <span class="validation-message cval-error" data-cval-error="amount">{{ $errors->first('amount') }}</span>
                        </div>
                    </div>
                </section>

                <div class="user-form-actions">
                    {{ Form::submit(__('Give Amount'),['class'=>'btn btn-primary form-submission-button']) }}
                    <a href="{{ route('admin.users.wallets', $wallet->user_id) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });
    </script>
@endsection
