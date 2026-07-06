@php($parameters = ['journalType' => null])

@if(isset($userId))
    @php($parameters['id'] = $userId)
@endif

<ul class="nav nav-pills report-category-tabs">
    <li class="nav-item">
        <a class="nav-link {{ is_current_route($routeName, 'active', ['journalType' => null]) }}" href="{{ route($routeName, $parameters) }}">{{ __('All') }}</a>
    </li>

    @foreach(config('commonconfig.journal_type') as $key => $value)
        @php($parameters['journalType'] = $key)
        <li class="nav-item">
            <a title="{{ \Illuminate\Support\Str::title(str_replace('-',' ', $key)) }}"
               class="nav-link {{ is_current_route($routeName, 'active', ['journalType' => $key]) }}"
               href="{{ route($routeName, $parameters) }}">
                {{ \Illuminate\Support\Str::title(str_replace('-',' ', $key)) }}
            </a>
        </li>
    @endforeach
</ul>
