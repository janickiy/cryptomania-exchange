<input type="hidden" value="{{ base_key() }}" name="base_key">

<section class="user-form-section">
    <div class="user-form-section-header">
        <h4 class="user-form-section-title">{{ __('Account Status') }}</h4>
    </div>

    <div class="user-form-grid">
        <div class="user-form-field {{ $errors->has('is_email_verified') ? 'has-error' : '' }}">
            <label for="{{ fake_field('is_email_verified') }}" class="form-label required">{{ __('Email Status') }}</label>
            {{ Form::select(fake_field('is_email_verified'), email_status(), $user->is_email_verified, ['class' => 'form-control','id' => fake_field('is_email_verified'),'placeholder' => __('Select Status'),'data-cval-name' => 'The email status field','data-cval-rules' => 'required|in:'.array_to_string(email_status())]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_email_verified') }}">{{ $errors->first('is_email_verified') }}</span>
        </div>

        <div class="user-form-field {{ $errors->has('is_active') ? 'has-error' : '' }}">
            <label for="{{ fake_field('is_active') }}" class="form-label required">{{ __('Account Status') }}</label>
            {{ Form::select(fake_field('is_active'), account_status(), $user->is_active, ['class' => 'form-control','id' => fake_field('is_active'),'placeholder' => __('Select Status'),'data-cval-name' => 'The account status field','data-cval-rules' => 'required|in:'.array_to_string(account_status())]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_active') }}">{{ $errors->first('is_active') }}</span>
        </div>

        <div class="user-form-field {{ $errors->has('is_financial_active') ? 'has-error' : '' }}">
            <label for="{{ fake_field('is_financial_active') }}" class="form-label required">{{ __('Financial Status') }}</label>
            {{ Form::select(fake_field('is_financial_active'), financial_status(), $user->is_financial_active, ['class' => 'form-control','id' => fake_field('is_financial_active'),'placeholder' => __('Select Status'),'data-cval-name' => 'The financial status field','data-cval-rules' => 'required|in:'.array_to_string(financial_status())]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_financial_active') }}">{{ $errors->first('is_financial_active') }}</span>
        </div>

        <div class="user-form-field {{ $errors->has('is_accessible_under_maintenance') ? 'has-error' : '' }}">
            <label for="{{ fake_field('is_accessible_under_maintenance') }}" class="form-label required">{{ __('Maintenance Access Status') }}</label>
            {{ Form::select(fake_field('is_accessible_under_maintenance'), maintenance_accessible_status(), $user->is_accessible_under_maintenance, ['class' => 'form-control','id' => fake_field('is_accessible_under_maintenance'),'placeholder' => __('Select Status'),'data-cval-name' => 'The maintenance access status field','data-cval-rules' => 'required|in:'.array_to_string(maintenance_accessible_status())]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_accessible_under_maintenance') }}">{{ $errors->first('is_accessible_under_maintenance') }}</span>
        </div>
    </div>
</section>

<div class="user-form-actions">
    {{ Form::submit(__('Update Status'),['class'=>'btn btn-primary form-submission-button']) }}
    {{ Form::reset(__('Reset Status'),['class'=>'btn btn-outline-secondary']) }}
</div>
