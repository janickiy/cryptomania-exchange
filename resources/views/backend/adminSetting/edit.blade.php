@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <?php $title_name = ucwords(str_replace('_', ' ', (string) $adminSettingType)); ?>
    <div class="admin-settings-page">
        <div class="card admin-settings-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Admin Setting') }} - {{ $title_name }}</h3>
                    <p class="admin-page-subtitle">{{ __('Edit application configuration values for this section.') }}</p>
                </div>
                <a href="{{ route('admin-settings.index',['admin_setting_type'=>$adminSettingType]) }}"
                   class="btn btn-outline-secondary back-button">
                    <i class="fa fa-eye me-1"></i>{{__('View :settingName Setting',['settingName' =>$title_name])}}
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <div class="list-group admin-settings-nav">
                            @foreach($settings['settingSections'] as $settingSection)
                                <a class="list-group-item list-group-item-action {{ is_current_route('admin-settings.edit', 'active', ['admin_setting_type'=>$settingSection]) }}"
                                   href="{{route('admin-settings.edit',['admin_setting_type'=>$settingSection])}}">
                                    {{ ucwords(str_replace('_', ' ', (string) $settingSection)) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-9">
                        {{ Form::open(['route'=>['admin-settings.update','admin_setting_type'=>$adminSettingType], 'method'=>'PUT','files'=> true]) }}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle admin-settings-table">
                                {!! $settings['html'] !!}
                                <tr>
                                    <td colspan="2" class="text-end">
                                        {{ Form::submit(__('Update :settingName Setting',['settingName' =>$title_name]),['class'=>'btn btn-primary']) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
