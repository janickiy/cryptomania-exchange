<input type="hidden" name="base_key" value="{{ base_key() }}">

<section class="admin-form-section">
    <div class="admin-form-section-header">
        <h4 class="admin-form-section-title">{{ __('Notice Content') }}</h4>
    </div>
    <div class="admin-form-grid">
        <div class="admin-form-field admin-form-field-wide {{ $errors->has('title') ? 'has-error' : '' }}">
            <label for="{{ fake_field('title') }}" class="form-label required">{{ __('Title') }}</label>
            {{ Form::text(fake_field('title'), old('title', isset($systemNotice) ? $systemNotice->title : null), ['class'=>'form-control', 'id' => fake_field('title'), 'data-cval-name' => 'The title field', 'data-cval-rules' => 'required']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('title') }}">{{ $errors->first('title') }}</span>
        </div>

        <div class="admin-form-field admin-form-field-wide {{ $errors->has('description') ? 'has-error' : '' }}">
            <label for="{{ fake_field('description') }}" class="form-label required">{{ __('Description') }}</label>
            {{ Form::textarea(fake_field('description'), old('description', isset($systemNotice) ? $systemNotice->description : null), ['class'=>'form-control', 'id' => fake_field('description'), 'rows' => 5, 'data-cval-name' => 'The description field', 'data-cval-rules' => 'required']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('description') }}">{{ $errors->first('description') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('type') ? 'has-error' : '' }}">
            <label for="{{ fake_field('type') }}" class="form-label required">{{ __('Type') }}</label>
            {{ Form::select(fake_field('type'), $types, old('type', isset($systemNotice) ? $systemNotice->type : null), ['class'=>'form-control', 'placeholder'=> __('Select type'), 'id' => fake_field('type'), 'data-cval-name' => 'The type field', 'data-cval-rules' => 'required']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('type') }}">{{ $errors->first('type') }}</span>
        </div>
    </div>
</section>

<section class="admin-form-section">
    <div class="admin-form-section-header">
        <h4 class="admin-form-section-title">{{ __('Schedule') }}</h4>
    </div>
    <div class="admin-form-grid">
        <div class="admin-form-field {{ $errors->has('start_at') ? 'has-error' : '' }}">
            <label for="start_time" class="form-label required">{{ __('Start Time') }}</label>
            {{ Form::text(fake_field('start_at'), old('start_at', isset($systemNotice) ? $systemNotice->start_at : null), ['class'=>'form-control', 'id' => 'start_time', 'data-cval-name' => 'The start time field', 'data-cval-rules' => 'date']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('start_at') }}">{{ $errors->first('start_at') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('end_at') ? 'has-error' : '' }}">
            <label for="end_time" class="form-label required">{{ __('End Time') }}</label>
            {{ Form::text(fake_field('end_at'), old('end_at', isset($systemNotice) ? $systemNotice->end_at : null), ['class'=>'form-control', 'id' => 'end_time', 'data-cval-name' => 'The end time field', 'data-cval-rules' => 'data']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('end_at') }}">{{ $errors->first('end_at') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('status') ? 'has-error' : '' }}">
            <label for="{{ fake_field('status') }}" class="form-label required">{{ __('Status') }}</label>
            {{ Form::select(fake_field('status'), active_status(), old('status', isset($systemNotice) ? $systemNotice->status : null), ['class'=>'form-control', 'id' => fake_field('status'), 'data-cval-name' => 'The status field', 'data-cval-rules' => 'required|in:'.array_to_string(active_status())]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('status') }}">{{ $errors->first('status') }}</span>
        </div>
    </div>
</section>

<div class="admin-form-actions">
    {{ Form::submit($buttonText, ['class'=>'btn btn-primary form-submission-button']) }}
    {{ Form::reset(__('Reset'), ['class'=>'btn btn-outline-secondary']) }}
</div>
