<input type="hidden" value="{{ base_key() }}" name="base_key">

<section class="user-form-section">
    <div class="user-form-section-header">
        <h4 class="user-form-section-title">{{ __('Profile Details') }}</h4>
    </div>

    <div class="user-form-grid">
        <div class="user-form-field {{ $errors->has('first_name') ? 'has-error' : '' }}">
            <label for="{{ fake_field('first_name') }}" class="form-label required">{{ __('First Name') }}</label>
            {{ Form::text(fake_field('first_name'), old('first_name', $user->userInfo->first_name), ['class'=>'form-control', 'id' => fake_field('first_name'),'data-cval-name' => 'The first name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('first_name') }}">{{ $errors->first('first_name') }}</span>
        </div>

        <div class="user-form-field {{ $errors->has('last_name') ? 'has-error' : '' }}">
            <label for="{{ fake_field('last_name') }}" class="form-label required">{{ __('Last Name') }}</label>
            {{ Form::text(fake_field('last_name'), old('last_name', $user->userInfo->last_name), ['class'=>'form-control', 'id' => fake_field('last_name'),'data-cval-name' => 'The last name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('last_name') }}">{{ $errors->first('last_name') }}</span>
        </div>

        <div class="user-form-field">
            <span class="form-label required">{{ __('Email') }}</span>
            <div class="user-readonly-value">{{ $user->email }}</div>
        </div>

        <div class="user-form-field">
            <span class="form-label required">{{ __('Username') }}</span>
            <div class="user-readonly-value">{{ $user->username }}</div>
        </div>

        <div class="user-form-field user-form-field-wide {{ $errors->has('address') ? 'has-error' : '' }}">
            <label for="{{ fake_field('address') }}" class="form-label">{{ __('Address') }}</label>
            {{ Form::textarea(fake_field('address'), old('address', $user->userInfo->address), ['class'=>'form-control', 'id' => fake_field('address'), 'rows'=>3,'data-cval-name' => 'The address field','data-cval-rules' => 'escapeText']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('address') }}">{{ $errors->first('address') }}</span>
        </div>
    </div>
</section>

<section class="user-form-section">
    <div class="user-form-section-header">
        <h4 class="user-form-section-title">{{ __('Access Settings') }}</h4>
    </div>

    <div class="user-form-grid">
        <div class="user-form-field {{ $errors->has('user_role_management_id') ? 'has-error' : '' }}">
            <label for="{{ fake_field('user_role_management_id') }}" class="form-label required">{{ __('User Role') }}</label>
            @if(!in_array($user->id, config('commonconfig.fixed_users')) && $user->id != Auth::user()->id)
                {{ Form::select(fake_field('user_role_management_id'), $userRoleManagements, old('user_role_management_id', $user->user_role_management_id),['class' => 'form-control','id' => fake_field('user_role_management_id'),'placeholder' => __('Select Role'),'data-cval-name' => 'The user role field','data-cval-rules' => 'required|in:'.array_to_string($userRoleManagements->toArray())]) }}
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('user_role_management_id') }}">{{ $errors->first('user_role_management_id') }}</span>
            @else
                <div class="user-readonly-value">{{ $userRoleManagements[$user->user_role_management_id] }}</div>
            @endif
        </div>
    </div>
</section>

<div class="user-form-actions">
    {{ Form::submit(__('Update Information'),['class'=>'btn btn-primary form-submission-button']) }}
    {{ Form::reset(__('Reset Information'),['class'=>'btn btn-outline-secondary reset-button']) }}
</div>
