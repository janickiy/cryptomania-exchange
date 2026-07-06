<input type="hidden" name="base_key" value="{{ base_key() }}">

<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h4 class="admin-form-section-title">{{ __('Currency Details') }}</h4>
    </div>

    <div class="admin-form-grid">
        <div class="admin-form-field {{ $errors->has('item') ? 'has-error' : '' }}">
            <label for="{{ fake_field('item') }}" class="form-label required">{{ __('Item') }}</label>
            {{ Form::text(fake_field('item'), old('item', $stockItem->item), ['class'=>'form-control', 'id' => fake_field('item'), 'data-cval-name' => 'The item field', 'data-cval-rules' => 'required|alpha|escapeInput|max:255', 'placeholder' => __('ex: USD')]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('item') }}">{{ $errors->first('item') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('item_name') ? 'has-error' : '' }}">
            <label for="{{ fake_field('item_name') }}" class="form-label required">{{ __('Item Name') }}</label>
            {{ Form::text(fake_field('item_name'), old('item_name', $stockItem->item_name), ['class'=>'form-control', 'id' => fake_field('item_name'), 'data-cval-name' => 'The item name field', 'data-cval-rules' => 'required|escapeInput|max:255', 'placeholder' => __('ex: United States Dollar')]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('item_name') }}">{{ $errors->first('item_name') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('item_type') ? 'has-error' : '' }}">
            <label for="{{ fake_field('item_type') }}" class="form-label required">{{ __('Item Type') }}</label>
            {{ Form::select(fake_field('item_type'), stock_item_types(), old('item_type', $stockItem->item_type), ['class' => 'form-control', 'id' => fake_field('item_type'), 'placeholder' => __('Select Stock Item Type'), 'data-cval-name' => 'The item type field', 'data-cval-rules' => 'required|in:' . array_to_string(stock_item_types()), 'v-on:change' => 'changeItemType']) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('item_type') }}">{{ $errors->first('item_type') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('item_emoji') ? 'has-error' : '' }}">
            <label for="{{ fake_field('item_emoji') }}" class="form-label required">{{ __('Item Emoji') }}</label>
            <div class="fileinput fileinput-new stock-emoji-input" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    @if(!is_null($stockItem->item_emoji))
                        <img src="{{ get_item_emoji($stockItem->item_emoji) }}" alt="{{ __('Item Emoji') }}">
                    @else
                        <i class="fa fa-money text-success"></i>
                    @endif
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail"></div>
                <div class="stock-file-actions">
                    <span class="btn btn-outline-secondary btn-file">
                        <span class="fileinput-new">{{ __('Select Emoji') }}</span>
                        <span class="fileinput-exists">{{ __('Change') }}</span>
                        {{ Form::file('item_emoji', ['class' => '', 'id' => fake_field('item_emoji'), 'data-cval-name' => 'The item emoji field', 'data-cval-rules' => 'files:jpg,png,jpeg|max:1024']) }}
                    </span>
                    <a href="javascript:;" class="btn btn-outline-danger fileinput-exists" data-dismiss="fileinput">{{ __('Remove') }}</a>
                </div>
            </div>
            <p class="form-text">{{ __('Upload item emoji 100x100 and less than or equal 1MB.') }}</p>
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('item_emoji') }}">{{ $errors->first('item_emoji') }}</span>
        </div>
    </div>
</div>

<div class="admin-form-section">
    <div class="admin-form-section-header">
        <h4 class="admin-form-section-title">{{ __('Availability') }}</h4>
    </div>

    <div class="admin-form-grid">
        <div class="admin-form-field {{ $errors->has('is_active') ? 'has-error' : '' }}">
            <label for="{{ fake_field('is_active') }}" class="form-label required">{{ __('Active Status') }}</label>
            <div class="cm-switch stock-switch">
                {{ Form::radio(fake_field('is_active'), ACTIVE_STATUS_ACTIVE, old('is_active', $stockItem->is_active) == ACTIVE_STATUS_ACTIVE, ['id' => fake_field('is_active') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The active status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                <label for="{{ fake_field('is_active') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                {{ Form::radio(fake_field('is_active'), ACTIVE_STATUS_INACTIVE, old('is_active', $stockItem->is_active) == ACTIVE_STATUS_INACTIVE, ['id' => fake_field('is_active') . '-inactive', 'class' => 'cm-switch-input']) }}
                <label for="{{ fake_field('is_active') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
            </div>
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_active') }}">{{ $errors->first('is_active') }}</span>
        </div>

        <div class="admin-form-field {{ $errors->has('is_ico') ? 'has-error' : '' }}">
            <label for="{{ fake_field('is_ico') }}" class="form-label required">{{ __('Is ICO') }}</label>
            <div class="cm-switch stock-switch">
                {{ Form::radio(fake_field('is_ico'), ACTIVE_STATUS_ACTIVE, old('is_ico', $stockItem->is_ico) == ACTIVE_STATUS_ACTIVE, ['id' => fake_field('is_ico') . '-yes', 'class' => 'cm-switch-input', 'data-cval-name' => 'The ICO field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status()), 'v-model' => 'hideIcoOptionFields']) }}
                <label for="{{ fake_field('is_ico') }}-yes" class="cm-switch-label">{{ __('Yes') }}</label>

                {{ Form::radio(fake_field('is_ico'), ACTIVE_STATUS_INACTIVE, old('is_ico', $stockItem->is_ico) == ACTIVE_STATUS_INACTIVE, ['id' => fake_field('is_ico') . '-no', 'class' => 'cm-switch-input', 'v-model' => 'hideIcoOptionFields']) }}
                <label for="{{ fake_field('is_ico') }}-no" class="cm-switch-label">{{ __('No') }}</label>
            </div>
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_ico') }}">{{ $errors->first('is_ico') }}</span>
        </div>
    </div>
</div>

<div v-if="hideIcoOptionFields == 0">
    <div class="admin-form-section">
        <div class="admin-form-section-header">
            <h4 class="admin-form-section-title">{{ __('Exchange Settings') }}</h4>
        </div>

        <div class="admin-form-grid">
            <div class="admin-form-field {{ $errors->has('exchange_status') ? 'has-error' : '' }}">
                <label for="{{ fake_field('exchange_status') }}" class="form-label required">{{ __('Exchange Status') }}</label>
                <div class="cm-switch stock-switch">
                    {{ Form::radio(fake_field('exchange_status'), ACTIVE_STATUS_ACTIVE, old('exchange_status', $stockItem->exchange_status) == ACTIVE_STATUS_ACTIVE, ['id' => fake_field('exchange_status') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The exchange status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                    <label for="{{ fake_field('exchange_status') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                    {{ Form::radio(fake_field('exchange_status'), ACTIVE_STATUS_INACTIVE, old('exchange_status', $stockItem->exchange_status) == ACTIVE_STATUS_INACTIVE, ['id' => fake_field('exchange_status') . '-inactive', 'class' => 'cm-switch-input']) }}
                    <label for="{{ fake_field('exchange_status') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('exchange_status') }}">{{ $errors->first('exchange_status') }}</span>
            </div>

            <div v-if="showOptionalFields" class="admin-form-field {{ $errors->has('api_service') ? 'has-error' : '' }}">
                <label for="api-services" class="form-label required">{{ __('API Service') }}</label>
                <select class="form-control" id="api-services" data-cval-name="{{ __('The API service field') }}" data-cval-rules="require" name="{{ fake_field('api_service') }}">
                    <option value="">{{ __('Select API Service') }}</option>
                    <option
                        v-for="(api, index) in apis"
                        v-bind:value="index"
                        v-text="api"
                        @if(!is_null(old('api_service', $stockItem->api_service)))
                            :selected="index == @json(old('api_service', $stockItem->api_service))"
                        @endif
                    ></option>
                </select>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('api_service') }}">{{ $errors->first('api_service') }}</span>
            </div>
        </div>
    </div>

    <div v-if="showOptionalFields" class="admin-form-section">
        <div class="admin-form-section-header">
            <h4 class="admin-form-section-title">{{ __('Deposit And Withdrawal') }}</h4>
        </div>

        <div class="admin-form-grid">
            <div class="admin-form-field {{ $errors->has('deposit_status') ? 'has-error' : '' }}">
                <label for="{{ fake_field('deposit_status') }}" class="form-label required">{{ __('Deposit Status') }}</label>
                <div class="cm-switch stock-switch">
                    {{ Form::radio(fake_field('deposit_status'), ACTIVE_STATUS_ACTIVE, old('deposit_status', $stockItem->deposit_status) == ACTIVE_STATUS_ACTIVE, ['id' => fake_field('deposit_status') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The deposit status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                    <label for="{{ fake_field('deposit_status') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                    {{ Form::radio(fake_field('deposit_status'), ACTIVE_STATUS_INACTIVE, old('deposit_status', $stockItem->deposit_status) == ACTIVE_STATUS_INACTIVE, ['id' => fake_field('deposit_status') . '-inactive', 'class' => 'cm-switch-input']) }}
                    <label for="{{ fake_field('deposit_status') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('deposit_status') }}">{{ $errors->first('deposit_status') }}</span>
            </div>

            <div class="admin-form-field {{ $errors->has('deposit_fee') ? 'has-error' : '' }}">
                <label for="{{ fake_field('deposit_fee') }}" class="form-label required">{{ __('Deposit Fee') }}</label>
                <div class="input-group">
                    {{ Form::text(fake_field('deposit_fee'), old('deposit_fee', $stockItem->deposit_fee), ['class'=>'form-control', 'id' => fake_field('deposit_fee'), 'data-cval-name' => 'The deposit fee field', 'data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99', 'placeholder' => __('ex: 0.01')]) }}
                    <span class="input-group-text"><i class="fa fa-percent"></i></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('deposit_fee') }}">{{ $errors->first('deposit_fee') }}</span>
            </div>

            <div class="admin-form-field {{ $errors->has('withdrawal_status') ? 'has-error' : '' }}">
                <label for="{{ fake_field('withdrawal_status') }}" class="form-label required">{{ __('Withdrawal Status') }}</label>
                <div class="cm-switch stock-switch">
                    {{ Form::radio(fake_field('withdrawal_status'), ACTIVE_STATUS_ACTIVE, old('withdrawal_status', $stockItem->withdrawal_status) == ACTIVE_STATUS_ACTIVE, ['id' => fake_field('withdrawal_status') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The withdrawal status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                    <label for="{{ fake_field('withdrawal_status') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                    {{ Form::radio(fake_field('withdrawal_status'), ACTIVE_STATUS_INACTIVE, old('withdrawal_status', $stockItem->withdrawal_status) == ACTIVE_STATUS_INACTIVE, ['id' => fake_field('withdrawal_status') . '-inactive', 'class' => 'cm-switch-input']) }}
                    <label for="{{ fake_field('withdrawal_status') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('withdrawal_status') }}">{{ $errors->first('withdrawal_status') }}</span>
            </div>

            <div class="admin-form-field {{ $errors->has('withdrawal_fee') ? 'has-error' : '' }}">
                <label for="{{ fake_field('withdrawal_fee') }}" class="form-label required">{{ __('Withdrawal Fee') }}</label>
                <div class="input-group">
                    {{ Form::text(fake_field('withdrawal_fee'), old('withdrawal_fee', $stockItem->withdrawal_fee), ['class'=>'form-control', 'id' => fake_field('withdrawal_fee'), 'data-cval-name' => 'The withdrawal fee field', 'data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99', 'placeholder' => __('ex: 0.01')]) }}
                    <span class="input-group-text"><i class="fa fa-percent"></i></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('withdrawal_fee') }}">{{ $errors->first('withdrawal_fee') }}</span>
            </div>

            <div class="admin-form-field {{ $errors->has('minimum_withdrawal_amount') ? 'has-error' : '' }}">
                <label for="{{ fake_field('minimum_withdrawal_amount') }}" class="form-label required">{{ __('Minimum Withdrawal Amount') }}</label>
                {{ Form::text(fake_field('minimum_withdrawal_amount'), old('minimum_withdrawal_amount', $stockItem->minimum_withdrawal_amount), ['class'=>'form-control', 'id' => fake_field('minimum_withdrawal_amount'), 'data-cval-name' => 'The minimum withdrawal amount field', 'data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99999999', 'placeholder' => __('ex: 25')]) }}
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('minimum_withdrawal_amount') }}">{{ $errors->first('minimum_withdrawal_amount') }}</span>
            </div>

            <div class="admin-form-field {{ $errors->has('daily_withdrawal_limit') ? 'has-error' : '' }}">
                <label for="{{ fake_field('daily_withdrawal_limit') }}" class="form-label required">{{ __('Daily Withdrawal Limit') }}</label>
                {{ Form::text(fake_field('daily_withdrawal_limit'), old('daily_withdrawal_limit', $stockItem->daily_withdrawal_limit), ['class'=>'form-control', 'id' => fake_field('daily_withdrawal_limit'), 'data-cval-name' => 'The daily withdrawal limit field', 'data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99999999', 'placeholder' => __('ex: 25')]) }}
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('daily_withdrawal_limit') }}">{{ $errors->first('daily_withdrawal_limit') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="admin-form-actions">
    {{ Form::submit(__('Update'), ['class'=>'btn btn-primary form-submission-button']) }}
    {{ Form::reset(__('Reset'), ['class'=>'btn btn-outline-secondary']) }}
</div>
