@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="stock-items-page">
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

        <div class="card stock-items-table-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Coin Management') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Manage currencies, icons, transfer type, and availability status.') }}</p>
                </div>

                @if(has_permission('admin.stock-items.create'))
                    <a href="{{ route('admin.stock-items.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>{{ __('Create Coin') }}
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th class="min-phone-l text-center">{{ __('Emoji') }}</th>
                            <th class="all">{{ __('Stock Item') }}</th>
                            <th class="min-phone-l">{{ __('Stock Item Name') }}</th>
                            <th class="min-phone-l text-center">{{ __('Stock Item Type') }}</th>
                            <th class="min-phone-l text-center">{{ __('Active Status') }}</th>
                            <th class="min-phone-l">{{ __('Created Date') }}</th>
                            <th class="text-end all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $stockItem)
                            <tr>
                                <td class="text-center">
                                    @if(!is_null(get_item_emoji($stockItem->item_emoji)))
                                        <img src="{{ get_item_emoji($stockItem->item_emoji) }}" alt="{{ __('Item Emoji') }}" class="stock-item-table-emoji">
                                    @else
                                        <span class="stock-item-table-placeholder">
                                            <i class="fa fa-money"></i>
                                        </span>
                                    @endif
                                </td>
                                <td><span class="fw-semibold">{{ $stockItem->item }}</span></td>
                                <td>{{ $stockItem->item_name }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-info stock-status-badge">
                                        {{ stock_item_types($stockItem->item_type) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($stockItem->is_active == ACTIVE_STATUS_ACTIVE)
                                        <span class="badge text-bg-success stock-status-badge">
                                            <i class="fa fa-check me-1"></i>{{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary stock-status-badge">
                                            <i class="fa fa-ban me-1"></i>{{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td><span class="text-secondary">{{ $stockItem->created_at->toFormattedDateString() }}</span></td>

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
                                            @if(has_permission('admin.stock-items.show'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.stock-items.show', $stockItem->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>{{ __('Show') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-items.edit'))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.stock-items.edit', $stockItem->id) }}">
                                                        <i class="fa fa-pencil me-2 text-primary"></i>{{ __('Edit') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-items.toggle-status'))
                                                <li>
                                                    <a class="dropdown-item confirmation"
                                                       data-form-id="update-{{ $stockItem->id }}"
                                                       data-form-method="PUT"
                                                       href="{{ route('admin.stock-items.toggle-status', $stockItem->id) }}"
                                                       data-alert="{{ __("Do you want to change this stock item's status?") }}">
                                                        <i class="fa fa-sliders me-2 text-warning"></i>{{ __('Change Status') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-items.destroy'))
                                                <li>
                                                    <a class="dropdown-item text-danger confirmation"
                                                       data-form-id="delete-{{ $stockItem->id }}"
                                                       data-form-method="DELETE"
                                                       href="{{ route('admin.stock-items.destroy', $stockItem->id) }}"
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
