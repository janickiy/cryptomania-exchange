<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('home')}}">
            <i class="fa fa-home me-1"></i>{{ __('Home') }}
        </a>
    </li>

    @foreach(get_breadcrumbs() as $breadcrumb)
        @if($loop->last || !$breadcrumb['display_url'])
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
        @else
            <li class="breadcrumb-item">
                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
            </li>
        @endif
    @endforeach
</ol>
