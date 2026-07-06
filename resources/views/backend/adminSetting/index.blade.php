@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <?php $title_name = ucwords(str_replace('_', ' ', (string) $adminSettingType)); ?>
    <div class="admin-settings-page">
        <div class="card admin-settings-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-3">
                <div class="admin-page-heading">
                    <h3 class="admin-page-title">{{ __('Admin Setting') }} - {{ $title_name }}</h3>
                    <p class="admin-page-subtitle">{{ __('Review application configuration values by section.') }}</p>
                </div>
                <a href="{{ route('admin-settings.edit',['admin_setting_type'=>$adminSettingType]) }}"
                   class="btn btn-primary back-button">
                    <i class="fa fa-pen-to-square me-1"></i>{{__('Edit :settingName Setting',['settingName' =>$title_name])}}
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <div class="list-group admin-settings-nav">
                            <?php $default = true; ?>
                            @foreach($settings['settingSections'] as $settingSection)
                                <?php
                                    $current_route = is_current_route('admin-settings.index', 'active', ['admin_setting_type'=>$settingSection]);
                                    if($default){
                                        $current_route = is_current_route('admin-settings.index', 'active', null,['admin_setting_type'=>$settingSection]);
                                    }
                                ?>
                                <a class="list-group-item list-group-item-action {{ $current_route }}"
                                   href="{{route('admin-settings.index',['admin_setting_type'=>$settingSection])}}">
                                    {{ ucwords(str_replace('_', ' ', (string) $settingSection)) }}
                                </a>
                                <?php $default = false; ?>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle admin-settings-table">
                                {!! $settings['html'] !!}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
