@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="stock-pair-form-page">
        <div class="card stock-pair-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Create Stock Pair') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Connect two currencies, set the market price, and configure pair availability.') }}</p>
                </div>
                <a href="{{ route('admin.stock-pairs.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back to list') }}
                </a>
            </div>

            <div class="card-body admin-form-body">
                {!! Form::open(['route'=>'admin.stock-pairs.store', 'method' => 'post', 'class'=>'validator admin-section-form stock-pair-form']) !!}
                    @include('backend.stockPairs._create_form')
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
