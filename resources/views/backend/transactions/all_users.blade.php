@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="user-management-page report-transactions-page">
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
                <h3 class="admin-page-title">{{ $title }}</h3>
                <p class="admin-page-subtitle">{{ __('Review account ledger transactions by user, stock item, journal, amount, and date.') }}</p>
            </div>
        </div>

        {!! $filters !!}

        <div class="card user-table-card report-transactions-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                    <thead>
                    <tr>
                        @if(!isset($userId))
                            <th class="all">{{ __('Email') }}</th>
                            <th class="none">{{ __('First Name') }}</th>
                            <th class="none">{{ __('Last Name') }}</th>
                        @endif
                        <th class="all">{{ __('Stock Item') }}</th>
                        <th class="all">{{ __('Transaction Type') }}</th>
                        @if(!$journalType)
                            <th class="all">{{ __('Journal') }}</th>
                        @endif
                        <th class="all text-end">{{ __('Amount') }}</th>
                        <th class="min-desktop">{{ __('Date') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list['query'] as $transaction)
                        <tr>
                            @if(!isset($userId))
                                <td>
                                    @if(has_permission('users.show'))
                                        <a href="{{ route('users.show', $transaction->user_id) }}">{{ $transaction->email }}</a>
                                    @else
                                        {{ $transaction->email }}
                                    @endif
                                </td>
                                <td>{{ $transaction->first_name ?: '-' }}</td>
                                <td>{{ $transaction->last_name ?: '-' }}</td>
                            @endif
                            <td><span class="fw-semibold">{{ $transaction->item }}</span></td>
                            <td>{{ get_transaction_type($transaction->transaction_type) }}</td>
                            @if(!$journalType)
                                @php
                                    $journal = array_flip(config('commonconfig.journal_type'))[$transaction->journal];
                                @endphp
                                <td>
                                    <span class="badge text-bg-info admin-list-type-badge">{{ \Illuminate\Support\Str::title(str_replace('-',' ',$journal)) }}</span>
                                </td>
                            @endif
                            <td class="text-end">{{ $transaction->amount }}</td>
                            <td><span class="text-secondary">{{ $transaction->created_at }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="card report-summary-card">
            <div class="card-header">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Summary') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Grouped totals by coin and ledger journal.') }}</p>
                </div>
            </div>
            <div class="card-body">
                @php
                    $journal = array_flip(config('commonconfig.journal_type'));
                @endphp
                <div class="row g-3">
                    @forelse($summary->groupBy(['item','journal']) as $coin => $coinSummary)
                        <div class="col-md-4 col-sm-6">
                            <div class="table-responsive report-summary-table-wrap">
                                <table class="table table-sm table-striped table-bordered align-middle mb-0 report-summary-table">
                                    <thead>
                                    <tr>
                                        <th class="text-center" colspan="2">{{ __('Summary (:coin)',['coin'=>$coin]) }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($coinSummary as $transactionType => $transaction)
                                        <tr>
                                            <td><strong>{{ \Illuminate\Support\Str::title(str_replace('-',' ',$journal[$transactionType])) }}</strong></td>
                                            <td class="text-end">{{ $transaction->first()->amount }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-secondary mb-0">{{ __("No summary found.") }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {!! $pagination !!}

        <div class="card report-journal-card">
            <div class="card-body">
                @include('backend.transactions._transaction_nav', ['routeName' => request()->route()->getName()])
            </div>
        </div>
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

        $('a').tooltip();
    </script>
@endsection
