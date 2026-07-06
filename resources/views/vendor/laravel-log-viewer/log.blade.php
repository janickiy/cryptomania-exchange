@extends('backend.layouts.main_layout')
@section('content')
    <div class="logs-page">
        <div class="card logs-toolbar-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Log files') }}</h3>
                    <p class="admin-page-subtitle">{{ __('Inspect, download, and clear application log files.') }}</p>
                </div>
            </div>
            <div class="card-body">
                <div class="logs-file-list">
                    @foreach($files as $file)
                        <a class="btn {{($current_file == $file) ? 'btn-primary' : 'btn-outline-secondary'}}"
                           href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}">
                            <i class="fa fa-file-lines me-1"></i>{{ $file }}
                        </a>
                    @endforeach
                </div>

                @if($current_file)
                    <div class="logs-actions">
                        <a class="btn btn-outline-primary btn-sm"
                           href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}">
                            <span class="fa fa-download me-1"></span>{{ __('Download file') }}
                        </a>
                        <a class="btn btn-outline-danger btn-sm" id="delete-log"
                           href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}">
                            <span class="fa fa-trash me-1"></span>{{ __('Delete current file') }}
                        </a>
                        @if(count($files) > 1)
                            <a class="btn btn-danger btn-sm" id="delete-all-log" href="?delall=true">
                                <span class="fa fa-trash me-1"></span>{{ __('Delete all files') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="card logs-table-card">
            <div class="card-body p-0">
                @if ($logs === null)
                    <div class="p-3 text-secondary">
                        {{ __('Log file >50M, please download it.') }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="table-log" class="table datatable dt-responsive display nowrap dc-table"
                               style="width:100% !important;">
                            <thead>
                            <tr>
                                <th class="all">SL</th>
                                <th class="all">Level</th>
                                <th class="min-phone-l">Context</th>
                                <th class="min-phone-l">Date</th>
                                <th class="min-phone-l">Content</th>
                                <th class="none">Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $key => $log)
                                <tr data-display="stack{{{$key}}}">
                                    <td>sl</td>
                                    <td class="text-{{{$log['level_class']}}}"><span
                                                class="fa fa-{{{$log['level_img']}}}"
                                                aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
                                    <td class="text">{{$log['context']}}</td>
                                    <td class="date">{{{$log['date']}}}</td>
                                    <td class="text">
                                        <code style="display:block;background:rgba(0,0,0,0.3)">
                                            {{{substr($log['text'],0,150)}}} ...
                                        </code>
                                    </td>
                                    <td>
                                        <code style="display:block;background:rgba(0,0,0,0.3)">
                                            {{{$log['text']}}}
                                            @if (isset($log['in_file']))
                                                <br/>{{{$log['in_file']}}}
                                            @endif
                                        </code>
                                        {{--@if ($log['stack'])--}}
                                        {{--<br>--}}
                                        {{--<code class="stack" id="stack{{{$key}}}"--}}
                                        {{--style="white-space: pre-wrap; color:#668899;background:rgba(0,0,0,0.3); display:block">{{{ trim($log['stack']) }}}--}}
                                        {{--</code>--}}
                                        {{--@endif--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.dc-table').dataTable({
                'paging': true,
                'searching': true,
                'bInfo': false,
                "language": {
                    "aria": {
                        "sortAscending": ": {{ __('activate to sort column ascending') }}",
                        "sortDescending": ": {{ __('activate to sort column descending') }}"
                    },
                    "emptyTable": "{{ __('No data available in table') }}",
                    "info": "{{ __('Showing :start to :end of _TOTAL_ entries',['start'=>'_START_','end'=>'_END_']) }}",
                    "infoEmpty": "{{ __('No entries found') }}",
                    "infoFiltered": "{{ __('(filtered1 from :max total entries)',['max'=>'_MAX_']) }}",
                    "lengthMenu": "{{ __(':menu entries',['menu'=>'_MENU_']) }}",
                    "search": "{{ __('Search') }}:",
                    "zeroRecords": "{{ __('No matching records found') }}"
                },
                buttons: [
                    // { extend: 'print', className: 'btn dark btn-outline' },
                    // { extend: 'pdf', className: 'btn green btn-outline' },
                    // { extend: 'csv', className: 'btn purple btn-outline ' }
                ],

                responsive: {
                    details: {}
                }
            });

            if ($('#log-table')) {
                $('#log-table').dataTable({
                    "stateSave": true,
                    "stateSaveCallback": function (settings, data) {
                        window.localStorage.setItem("datatable", JSON.stringify(data));
                    },
                    "stateLoadCallback": function (settings) {
                        var data = JSON.parse(window.localStorage.getItem("datatable"));
                        if (data) data.start = 0;
                        return data;
                    }
                })
            }
        })
        $(document).ready(function () {
            $('#delete-log, #delete-all-log').click(function () {
                return confirm('Are you sure?');
            });
        });
    </script>
@endsection
