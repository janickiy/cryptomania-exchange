<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fa fa-bars"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
                @php
                    $userNotifications = get_user_specific_notice();
                @endphp
                <li class="nav-item dropdown notifications-menu">
                    <a href="#" class="nav-link" data-bs-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="badge text-bg-warning navbar-badge">{{ $userNotifications['count_unread'] }}</span>
                    </a>
                    @if(!$userNotifications['list']->isEmpty())
                        <ul class="dropdown-menu">
                            <li class="dropdown-header text-bold">{{ __('You have :count notifications',['count' => $userNotifications['count_unread']]) }}</li>
                            <li>
                                <ul class="list-unstyled mb-0">
                                    @foreach($userNotifications['list'] as $notification)
                                    <li>
                                        <a class="dropdown-item"><i class="fa fa-bell text-warning"></i><span class="ms-2">{{ \Illuminate\Support\Str::limit($notification->data, 50) }}</span></a>
                                    </li>
                                        @endforeach
                                </ul>
                            </li>
                            <li><a class="dropdown-item text-center" href="{{ route('notices.index') }}">View all</a></li>
                        </ul>
                    @endif
                </li>
                <li class="nav-item user user-menu">
                    <a href="{{ route('profile.index') }}" class="nav-link">
                        <img src="{{ get_avatar(Auth::user()->avatar) }}" class="user-image rounded-circle shadow" alt="User Image">
                        <span class="d-none d-md-inline ms-1">{{ Auth::user()->userInfo->full_name }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"><i class="fa fa-sign-out"></i></a>
                </li>
        </ul>
    </div>
</nav>
