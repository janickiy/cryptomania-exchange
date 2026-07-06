<input type="hidden" name="base_key" value="{{ base_key() }}">

<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h4 class="admin-form-section-title">{{ __('Pair Details') }}</h4>
    </div>

    <div class="admin-form-grid">
        <div class="admin-form-field {{ $errors->has('stock_item_id') ? 'has-error' : '' }}">
            <label for="{{ fake_field('stock_item_id') }}" class="form-label required">{{ __('Exchangeable Item') }}</label>
            {{ Form::select(fake_field('stock_item_id'), $stockItems, old('stock_item_id', $stockPair->stock_item_id), ['class' => 'form-control', 'id' => fake_field('stock_item_id'), 'placeholder' => __('Select Exchangeable Item'), 'data-cval-name' => 'The exchangable item field','data-cval-rules' => 'required|in:' . array_to_string($stockItems)]) }}

            <span class="validation-message cval-error" data-cval-error="{{ fake_field('stock_item_id') }}">{{ $errors->first('stock_item_id') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('base_item_id') ? 'has-error' : '' }}">
            <label for="{{ fake_field('base_item_id') }}" class="form-label required">{{ __('Base Item') }}</label>
            {{ Form::select(fake_field('base_item_id'), $stockItems, old('base_item_id', $stockPair->base_item_id),['class' => 'form-control','id' => fake_field('base_item_id'), 'placeholder' => __('Select Base Item'), 'data-cval-name' => 'The base item field','data-cval-rules' => 'required|in:'.array_to_string($stockItems)]) }}

            <span class="validation-message cval-error" data-cval-error="{{ fake_field('base_item_id') }}">{{ $errors->first('base_item_id') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('last_price') ? 'has-error' : '' }}">
            <label for="{{ fake_field('last_price') }}" class="form-label required">{{ __('Last Price') }}</label>
            {{ Form::text(fake_field('last_price'),  old('last_price', $stockPair->last_price), ['class'=>'form-control', 'id' => fake_field('last_price'),'data-cval-name' => 'The last price field','data-cval-rules' => 'required|numeric|escapeInput|between:0.00000001, 99999999999.99999999', 'placeholder' => __('ex: 0.00150000')]) }}

            <span class="validation-message cval-error" data-cval-error="{{ fake_field('last_price') }}">{{ $errors->first('last_price') }}</span>
        </div>
    </div>
</div>

<div class="admin-form-actions">
    {{ Form::submit(__('Update'),['class'=>'btn btn-primary form-submission-button']) }}
    {{ Form::reset(__('Reset'),['class'=>'btn btn-outline-secondary']) }}
    <a href="{{ route('admin.stock-pairs.show', $stockPair->id) }}" class="btn btn-outline-secondary">{{ __('View Stock Pair') }}</a>
</div>
