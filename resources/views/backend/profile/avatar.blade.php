<div class="card card-primary card-outline profile-sidebar-card">
    <div class="card-body text-center">
        <img src="{{ get_avatar($user->avatar) }}" alt="{{ __('Profile Image') }}"
             class="profile-user-img img-fluid rounded-circle shadow-sm">
        <h4 class="profile-username mt-3 mb-1">{{ $user->userInfo->full_name }}</h4>
        @if(isset($user->userRoleManagement))
            <p class="text-body-secondary mb-0">{{ $user->userRoleManagement->role_name }}</p>
        @endif
    </div>

    <div class="list-group list-group-flush profile-stat-list">
        @if(has_permission($profileRouteInfo['walletRouteName']))
            <a href="{{ $profileRouteInfo['walletRoute'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fa fa-wallet me-2 text-primary"></i>{{ __('Wallets') }}</span>
                <span class="badge text-bg-success rounded-pill">{{ $profileRouteInfo['totalWallets'] }}</span>
            </a>
        @endif

        @if(has_permission($profileRouteInfo['openOrderRouteName']))
            <a href="{{ $profileRouteInfo['openOrderRoute'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fa fa-list-check me-2 text-primary"></i>{{ __('Open Orders') }}</span>
                <span class="badge text-bg-success rounded-pill">{{ $profileRouteInfo['totalOpenOrders'] }}</span>
            </a>
        @endif

        @if(has_permission($profileRouteInfo['tradeHistoryRouteName']))
            <a href="{{ $profileRouteInfo['tradeHistoryRoute'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <span><i class="fa fa-chart-line me-2 text-primary"></i>{{ __('Trade History') }}</span>
                <span class="badge text-bg-success rounded-pill">{{ $profileRouteInfo['totalTrades'] }}</span>
            </a>
        @endif
    </div>
</div>
