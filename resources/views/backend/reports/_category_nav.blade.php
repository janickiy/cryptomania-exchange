@php
    $routeParameters = $routeParameters ?? [];
@endphp

<ul class="nav nav-pills report-category-tabs">
    <li class="nav-item">
        <a class="nav-link {{ is_current_route($routeName, 'active', array_merge($routeParameters, ['categoryType' => null])) }}"
           href="{{ route($routeName, array_merge($routeParameters, ['categoryType' => null])) }}">{{ __('All') }}</a>
    </li>

    @foreach(config('commonconfig.category_slug') as $key => $value)
        <li class="nav-item">
            <a class="nav-link {{ is_current_route($routeName, 'active', array_merge($routeParameters, ['categoryType' => $key])) }}"
               href="{{ route($routeName, array_merge($routeParameters, ['categoryType' => $key])) }}">{{ category_type($value) }}</a>
        </li>
    @endforeach
</ul>
