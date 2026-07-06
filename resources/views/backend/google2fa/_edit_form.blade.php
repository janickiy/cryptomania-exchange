<div class="alert alert-success d-flex align-items-center gap-2">
    <i class="fa fa-shield"></i>
    <div>{{ __('Google Authentication is Enabled.') }}</div>
</div>

{!! Form::open(['route'=>['profile.google-2fa.destroy'], 'class'=>'validator']) !!}
<input type="hidden" name="base_key" value="{{ base_key() }}">
@method('put')

<p class="text-body-secondary">
    {{ __('If you want to turn off Google 2FA, input your account password and the six-digit code provided by the Google Authenticator app below, then submit.') }}
</p>

<div class="row g-3">
    <div class="col-md-6 {{ $errors->has('password') ? 'has-error' : '' }}">
        <label for="{{ fake_field('password') }}" class="form-label required">{{ __('Current Password') }}</label>
        {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter current password'), 'id' => fake_field('password'),'data-cval-name' => 'The password','data-cval-rules' => 'required|escapeInput']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
    </div>

    <div class="col-md-6 {{ $errors->has('google_app_code') ? 'has-error' : '' }}">
        <label for="google_app_code" class="form-label required">{{ __('Enter G2FA App Code') }}</label>
        {{ Form::text('google_app_code', null, ['class'=>'form-control', 'placeholder' => __('Enter G2FA App Code'), 'id' => 'google_app_code','data-cval-name' => 'The G2FA app code field','data-cval-rules' => 'required|escapeInput|integer']) }}
        <span class="validation-message cval-error" data-cval-error="google_app_code">{{ $errors->first('google_app_code') }}</span>
    </div>
</div>

<div class="alert alert-warning mt-3">
    {{ __('IMPORTANT: When you disable 2FA, The 16 digit code will no longer be valid.') }}
</div>

<div class="form-check mb-3">
    {{ Form::checkbox('back_up', 1, false,['class' => 'form-check-input', 'id' => 'google2fa-disable-confirm', 'data-cval-rules' => 'required|in:1', 'data-cval-name' => 'Checking']) }}
    <label class="form-check-label" for="google2fa-disable-confirm">{{ __('I understand.') }}</label>
    <span class="validation-message cval-error" data-cval-error="back_up">{{ $errors->first('back_up') }}</span>
</div>

{{ Form::submit(__('Disable Google Authentication'), ['class'=>'btn btn-danger form-submission-button']) }}
{!! Form::close() !!}
