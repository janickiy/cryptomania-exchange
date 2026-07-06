@extends('backend.layouts.main_layout')
@section('title', $title)
@section('after-style')
    <link rel="stylesheet" href="{{asset('backend/assets/css/menu.css')}}">
@endsection
@section('content')
    <div class="menu-manager-page">
        <div class="row g-3">
            <div class="col-lg-3">
                <div class="card menu-panel-card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Select Nav') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group menu-place-list">
                            @foreach($navigationPlaces as $navigationPlace)
                                <a class="list-group-item list-group-item-action {{ $slug === $navigationPlace ? 'active' : '' }}"
                                   href="{{route('menu-manager.index',$navigationPlace)}}">
                                    {{ucfirst(str_replace('-',' ',$navigationPlace))}}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card menu-panel-card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Add Routes') }}</h3>
                    </div>
                    <div id="all-routes" class="card-body menu-scroll-panel" data-name="Unnamed">
                        @foreach($allRoutes as $routeName => $routeData)
                            @php
                                $middleware = $routeData->middleware();
                                $parameters = $routeData->signatureParameters();
                                $isMenuable = true;
                            @endphp
                            @foreach($parameters as $parameter)
                                @if(!$parameter->isOptional())
                                    @php($isMenuable = false)
                                    @break
                                @endif
                            @endforeach
                            @if($isMenuable && !empty($middleware) && !in_array('api', $middleware) && !in_array('Barryvdh\Debugbar\Middleware\DebugbarEnabled',$middleware))
                                <?php
                                $route = explode('/{', $routeName)[0];
                                if ($route == '/' || $route == '' || strlen($route) == 2) {
                                    $route = 'Home';
                                } else {
                                    if (strpos($route, '/') == 2) {
                                        $route = substr($route, 3);
                                    }
                                    $route = ucfirst(str_replace('/', ' - ', str_replace('-', ' ', $route)));
                                }
                                ?>
                                <label class="form-check menu-route-check">
                                    <input type="checkbox" class="form-check-input route-check-box" value="{{$routeData->getName()}}">
                                    <span class="form-check-label">{{$route}}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary w-100" id="add-route" type="button">
                            <i class="fa fa-plus me-1"></i>{{ __('Add Route') }}
                        </button>
                    </div>
                </div>

                <div class="card menu-panel-card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Add LINK') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="link-data" class="form-label">{{ __('URL') }}</label>
                            <input type="text" id="link-data" placeholder="{{ __('Enter url') }}" class="form-control">
                        </div>
                        <div class="mb-0">
                            <label for="link-name" class="form-label">{{ __('Menu Item Name') }}</label>
                            <input type="text" data-name="Unnamed" id="link-name" placeholder="{{ __('Enter Menu Item Name') }}" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary w-100" id="add-link" type="button">
                            <i class="fa fa-link me-1"></i>{{ __('Add A custom Link') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card menu-builder-card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-3">
                        <div class="admin-page-heading">
                            <h3 class="admin-page-title">{{ __('Menu ITEMS') }}</h3>
                            <p class="admin-page-subtitle">{{ __('Drag items to reorder and nest navigation entries.') }}</p>
                        </div>
                        <button class="btn btn-primary menu-submit" type="button">
                            <i class="fa fa-floppy-disk me-1"></i>{{ __('Save Menu') }}
                        </button>
                    </div>
                    {{ Form::open(['route'=>['menu-manager.save', $slug], 'method'=>'post','id'=>'menu-form']) }}
                    <div class="card-body menu-builder-body">
                        <div class="menu-tree-wrapper">
                            {!! $menu !!}
                        </div>
                        <button id="form-submit-button" type="submit" style="display:none">{{ __('Save Menu') }}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('backend/vendors/menu_manager/jquery.mjs.nestedSortable.js')}}"></script>
    <script src="{{asset('backend/vendors/menu_manager/adminmenuhandler.js')}}"></script>
@endsection
