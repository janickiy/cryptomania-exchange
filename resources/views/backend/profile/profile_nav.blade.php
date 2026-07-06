<ul class="nav nav-tabs profile-tabs">
    <li class="nav-item">
        <a class="nav-link {{ is_current_route(['profile.index','profile.edit']) }}" href="{{ route('profile.index') }}">
            <i class="fa fa-user me-1"></i>{{ __('Profile') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ is_current_route('profile.change-password') }}" href="{{ route('profile.change-password') }}">
            <i class="fa fa-lock me-1"></i>{{ __('Change Password') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ is_current_route('profile.avatar.edit') }}" href="{{ route('profile.avatar.edit') }}">
            <i class="fa fa-image me-1"></i>{{ __('Change Avatar') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ is_current_route('trader.upload-id.index') }}" href="{{ route('trader.upload-id.index') }}">
            <i class="fa fa-id-card me-1"></i>{{ __('Upload ID') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ is_current_route('profile.google-2fa.create') }}" href="{{ route('profile.google-2fa.create') }}">
            <i class="fa fa-shield me-1"></i>{{ __('Google Authentication') }}
        </a>
    </li>
    @if(admin_settings('referral'))
        <li class="nav-item">
            <a class="nav-link {{ is_current_route('profile.referral') }}" href="{{ route('profile.referral') }}">
                <i class="fa fa-link me-1"></i>{{ __('Referral Link') }}
            </a>
        </li>
    @endif
</ul>
