@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="stock-pairs-page">
        @php
            $filters = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card stock-filter-card', 'card-body'],
                $list['filters']
            );
            $pagination = str_replace(
                ['box box-primary box-borderless', 'box-body'],
                ['card stock-pagination-card', 'card-body'],
                $list['pagination']
            );
        @endphp

        {!! $filters !!}

        <div class="card stock-pairs-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Coin Pair Management') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Manage exchange pairs, prices, availability, and default markets.') }}</p>
                </div>
                @if(has_permission('admin.stock-pairs.create'))
                    <a href="{{ route('admin.stock-pairs.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>{{ __('Create Pair') }}
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Stock Pair') }}</th>
                            <th class="min-phone-l">{{ __('Exchangeable Item') }}</th>
                            <th class="min-phone-l">{{ __('Base Item') }}</th>
                            <th class="min-phone-l text-end">{{ __('Last Price') }}</th>
                            <th class="min-phone-l text-center">{{ __('Active Status') }}</th>
                            <th class="min-phone-l text-center">{{ __('Default Status') }}</th>
                            <th class="min-phone-l">{{ __('Created Date') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $stockPair)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $stockPair->stock_item }}/{{ $stockPair->base_stock_item }}</span>
                                </td>
                                <td>{{ $stockPair->stock_item }} ({{ $stockPair->stock_name }})</td>
                                <td>{{ $stockPair->base_stock_item }} ({{ $stockPair->base_stock_name }})</td>
                                <td class="text-end">{{ $stockPair->last_price }}</td>
                                <td class="text-center">
                                    @if($stockPair->is_active == ACTIVE_STATUS_ACTIVE)
                                        <span class="badge text-bg-success stock-status-badge">
                                            <i class="fa fa-check me-1"></i>{{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary stock-status-badge">
                                            <i class="fa fa-ban me-1"></i>{{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($stockPair->is_default == ACTIVE_STATUS_ACTIVE)
                                        <span class="badge text-bg-primary stock-status-badge">
                                            <i class="fa fa-star me-1"></i>{{ __('Default') }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-light stock-status-badge">
                                            <i class="fa fa-minus me-1"></i>{{ __('No') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $stockPair->created_at->toFormattedDateString() }}</span>
                                </td>
                                <td class="cm-action text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary table-action-button"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="{{ __('Action') }}">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @if(has_permission('admin.stock-pairs.show'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.stock-pairs.show', $stockPair->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-pairs.edit'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.stock-pairs.edit', $stockPair->id) }}">
                                                        <i class="fa fa-pencil me-2 text-primary"></i>{{ __('Edit') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(
                                                has_permission('admin.stock-pairs.toggle-status') &&
                                                $stockPair->is_default != ACTIVE_STATUS_ACTIVE
                                            )
                                                <li>
                                                    <a class="dropdown-item confirmation"
                                                       data-form-id="update-{{ $stockPair->id }}"
                                                       data-form-method="PUT"
                                                       href="{{ route('admin.stock-pairs.toggle-status', $stockPair->id) }}"
                                                       data-alert="{{ __("Do you want to change this stock pair's status?") }}">
                                                        <i class="fa fa-sliders me-2 text-warning"></i>{{ __('Change Status') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(
                                                has_permission('admin.stock-pairs.make-status-default') &&
                                                $stockPair->is_default != ACTIVE_STATUS_ACTIVE &&
                                                $stockPair->is_active == ACTIVE_STATUS_ACTIVE
                                            )
                                                <li>
                                                    <a class="dropdown-item confirmation"
                                                       data-form-id="update-default-{{ $stockPair->id }}"
                                                       data-form-method="PUT"
                                                       href="{{ route('admin.stock-pairs.make-status-default', $stockPair->id) }}"
                                                       data-alert="{{ __('Do you want to make this stock pair  default?') }}">
                                                        <i class="fa fa-star me-2 text-warning"></i>{{ __('Make Default Pair') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(
                                                has_permission('admin.stock-pairs.destroy') &&
                                                $stockPair->is_default != ACTIVE_STATUS_ACTIVE
                                            )
                                                <li>
                                                    <a class="dropdown-item text-danger confirmation"
                                                       data-form-id="delete-{{ $stockPair->id }}"
                                                       data-form-method="DELETE"
                                                       href="{{ route('admin.stock-pairs.destroy', $stockPair->id) }}"
                                                       data-alert="{{ __('Do you want to delete this stock item?') }}">
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
