<div class="row g-4 google2fa-setup">
    <div class="col-lg-5">
        <div class="google2fa-qr-card">
            <figure class="text-center mb-0">
                <img src="{{ $inlineUrl }}" alt="{{ __('QR CODE') }}" class="google2fa-qr-image">
                <figcaption class="mt-3">
                    <span class="text-body-secondary">{{__('16-Digit Key')}}:</span>
                    <strong class="d-block mt-1">{{ $secretKey }}</strong>
                </figcaption>
            </figure>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            {{ __('NOTE: This code changes each time you enable 2FA. If you disable 2FA this code will no longer be valid.') }}
        </div>
    </div>

    <div class="col-lg-7">
        {!! Form::open(['route'=>['profile.google-2fa.store', $secretKey], 'class'=>'validator']) !!}
            <input type="hidden" name="base_key" value="{{ base_key() }}">
            @method('put')

            <div class="mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                <div class="form-control-plaintext profile-readonly-value">{{ $user->email }}</div>
            </div>

            <div class="mb-3 {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="{{ fake_field('password') }}" class="form-label required">{{ __('Current Password') }}</label>
                {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter current password'), 'id' => fake_field('password'),'data-cval-name' => 'The password','data-cval-rules' => 'required|escapeInput']) }}
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
            </div>

            <div class="mb-3 {{ $errors->has('google_app_code') ? 'has-error' : '' }}">
                <label for="google_app_code" class="form-label required">{{ __('Enter G2FA App Code') }}</label>
                {{ Form::text('google_app_code', null, ['class'=>'form-control', 'placeholder' => __('Enter G2FA App Code'), 'id' => 'google_app_code','data-cval-name' => 'The G2FA app code field','data-cval-rules' => 'required|escapeInput|integer']) }}
                <span class="validation-message cval-error" data-cval-error="google_app_code">{{ $errors->first('google_app_code') }}</span>
            </div>

            <p class="text-body-secondary">
                {{ __('Before turning on 2FA, write down or print a copy of your 16-digit key and put it in a safe place. If your phone gets lost, stolen, or erased, you will need this key to get back into your account!') }}
            </p>

            <div class="form-check mb-3">
                {{ Form::checkbox('back_up', 1, false,['class' => 'form-check-input', 'id' => 'google2fa-back-up', 'data-cval-rules' => 'required|in:1', 'data-cval-name' => 'Checking']) }}
                <label class="form-check-label" for="google2fa-back-up">{{ __('I have backed up my 16-digit key.') }}</label>
                <span class="validation-message cval-error" data-cval-error="back_up">{{ $errors->first('back_up') }}</span>
            </div>

            {{ Form::submit(__('Set Google Authentication'), ['class'=>'btn btn-primary form-submission-button']) }}
        {!! Form::close() !!}
    </div>
</div>
