@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="admin-list-page trade-analysis-page trade-analysis-posts-page">
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
                    <h3 class="admin-page-title">{{ __('Posts') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Manage trade analysis drafts, publication status, and public views.') }}</p>
                </div>

                @if(has_permission('trade-analyst.posts.create'))
                    <a href="{{ route('trade-analyst.posts.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>{{ __('Create Post') }}
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Title') }}</th>
                            <th class="min-phone-l">{{ __('Analyst') }}</th>
                            <th class="min-phone-l">{{ __('Created Date') }}</th>
                            <th class="min-phone-l text-center">{{ __('Publish Status') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $post)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $post->title }}</span>
                                </td>
                                <td>{{ $post->full_name }}</td>
                                <td>
                                    <span class="text-secondary">{{ $post->created_at }}</span>
                                </td>
                                <td class="text-center">
                                    @if($post->is_published == ACTIVE_STATUS_ACTIVE)
                                        <span class="badge text-bg-success admin-list-status-badge">
                                            <i class="fa fa-check me-1"></i>{{ __('Published') }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary admin-list-status-badge">
                                            <i class="fa fa-file-o me-1"></i>{{ __('Draft') }}
                                        </span>
                                    @endif
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
                                            @if($post->is_published == ACTIVE_STATUS_ACTIVE)
                                                <li>
                                                    <a class="dropdown-item" target="_blank" href="{{ route('trading-views.show', $post->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('trade-analyst.posts.edit'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('trade-analyst.posts.edit', $post->id) }}">
                                                        <i class="fa fa-pencil me-2 text-primary"></i>{{ __('Edit') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('trade-analyst.posts.toggle-status'))
                                                <li>
                                                    <a class="dropdown-item confirmation"
                                                       data-form-id="update-{{ $post->id }}"
                                                       data-form-method="PUT"
                                                       href="{{ route('trade-analyst.posts.toggle-status', $post->id) }}"
                                                       data-alert="{{ __('Do you want to change the publish status?') }}">
                                                        <i class="fa fa-sliders me-2 text-warning"></i>{{ __('Change Status') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('trade-analyst.posts.destroy'))
                                                <li>
                                                    <a class="dropdown-item text-danger confirmation"
                                                       data-form-id="delete-{{ $post->id }}"
                                                       data-form-method="DELETE"
                                                       href="{{ route('trade-analyst.posts.destroy', $post->id) }}"
                                                       data-alert="{{ __('Do you want to delete this trade analysis?') }}">
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
