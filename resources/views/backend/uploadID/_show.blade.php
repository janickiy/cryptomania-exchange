<div class="id-status-summary">
    <div>
        <div class="text-body-secondary small text-uppercase">{{ __('ID Type') }}</div>
        <h4 class="mb-0">{{ id_type($user->userInfo->id_type) }}</h4>
    </div>
    <span class="badge text-bg-{{ config('commonconfig.id_status.' . $user->userInfo->is_id_verified . '.color_class') }}">
        {{ id_status($user->userInfo->is_id_verified) }}
    </span>
</div>

@if($user->userInfo->is_id_verified == ID_STATUS_PENDING)
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="fa fa-clock-o"></i>
        <div>{{ __('Your ID verification request is being reviewed. It will take maximum 3 business day to approve / decline your request.') }}</div>
    </div>
@endif

@include('backend.idManagement._show', ['user' => $user->userInfo])
