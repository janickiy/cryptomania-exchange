@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="admin-list-page trade-analysis-page trade-analysis-questions-page">
        @php
            $filters = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card admin-list-filter-card', 'card-body'],
                $list['filters']
            );
            $pagination = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card admin-list-pagination-card', 'card-body'],
                $list['pagination']
            );
        @endphp

        {!! $filters !!}

        <div class="card admin-list-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Questions') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Review trader questions and manage analyst responses.') }}</p>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Title') }}</th>
                            <th class="none">{{ __('Content') }}</th>
                            <th class="min-phone-l">{{ __('Questioned By') }}</th>
                            <th class="min-phone-l">{{ __('Created Date') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $question)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $question->title }}</span>
                                </td>
                                <td>{!! $question->content !!}</td>
                                <td>{{ $question->full_name }}</td>
                                <td>
                                    <span class="text-secondary">{{ $question->created_at }}</span>
                                </td>
                                <td class="cm-action text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary admin-list-action-button"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="{{ __('Action') }}">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('faq.show', $question->id) }}">
                                                    <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
                                                </a>
                                            </li>
                                            @if(has_permission('trade-analyst.questions.answer'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('trade-analyst.questions.answer', $question->id) }}">
                                                        <i class="fa fa-commenting-o me-2 text-info"></i>{{ __('Answer') }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(has_permission('trade-analyst.questions.destroy'))
                                                <li>
                                                    <a class="dropdown-item text-danger confirmation"
                                                       data-form-id="delete-{{ $question->id }}"
                                                       data-form-method="DELETE"
                                                       href="{{ route('trade-analyst.questions.destroy', $question->id) }}"
                                                       data-alert="{{ __('Do you want to delete this question?') }}">
                                                        <i class="fa fa-trash-o me-2"></i>{{ __('Delete') }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
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
