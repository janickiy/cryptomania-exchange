@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="stock-pair-form-page">
        <div class="card stock-pair-form-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Edit Stock Pair') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Update the currencies and latest market price for this trading pair.') }}</p>
                </div>

                <a href="{{ route('admin.stock-pairs.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i>{{ __('Back to list') }}
                </a>
            </div>

            <div class="card-body admin-form-body">
                {!! Form::open(['route'=>['admin.stock-pairs.update', $stockPair->id], 'class'=>'validator admin-section-form stock-pair-form', 'enctype'=>'multipart/form-data']) !!}
                    @method('PUT')
                    @include('backend.stockPairs._edit_form')
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
