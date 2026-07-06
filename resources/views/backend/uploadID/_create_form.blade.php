<div>
    {{ Form::open(['route'=>['trader.upload-id.store'], 'class'=>'validator', 'enctype'=>'multipart/form-data']) }}
        <input type="hidden" name="base_key" value="{{ base_key() }}">
        <transition name="fade" mode="out-in">
            <section key="1" v-if="step == 1">
                <h4 class="profile-form-section-title">{{ __('Select ID Type') }}</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <button type="button" class="id-type-card text-start" @click="nextStep({{ ID_PASSPORT }})">
                            <span class="id-type-icon text-bg-info"><i class="fa fa-address-book-o"></i></span>
                            <span class="id-type-title">{{ __('PASPORT') }}</span>
                            <span class="id-type-text">{{ __('Single document image') }}</span>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="id-type-card text-start" @click="nextStep({{ ID_NID }})">
                            <span class="id-type-icon text-bg-success"><i class="fa fa-id-card-o"></i></span>
                            <span class="id-type-title">{{ __('NID CARD') }}</span>
                            <span class="id-type-text">{{ __('Front and back images') }}</span>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="id-type-card text-start" @click="nextStep({{ ID_DRIVER_LICENSE }})">
                            <span class="id-type-icon text-bg-warning"><i class="fa fa-truck"></i></span>
                            <span class="id-type-title">{{ __("DRIVING LICENSE") }}</span>
                            <span class="id-type-text">{{ __('Front and back images') }}</span>
                        </button>
                    </div>
                </div>
            </section>

            <section key="2" v-if="step == 2">
                <input type="hidden" name="{{ fake_field('id_type') }}" v-model="idType">

                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                    <h4 class="profile-form-section-title mb-0">{{ __('Upload ID') }}</h4>
                    <button type="button" class="btn btn-outline-secondary" @click.prevent="previousStep">
                        <i class="fa fa-arrow-left me-1"></i>{{ __('Back') }}
                    </button>
                </div>

                <div class="row g-3">
                    <div :class="idType == {{ ID_PASSPORT }} ? 'col-12' : 'col-md-6'">
                        <div class="id-upload-card {{ $errors->has('id_card_front') ? 'has-error' : '' }}">
                            <div class="id-upload-icon"><i class="fa fa-address-book-o"></i></div>
                            <label for="{{ fake_field('id_card_front') }}" class="form-label required">
                                {{ __('Upload') }} <span v-if="idType != {{ ID_PASSPORT }}">{{ __('Front') }}</span>
                            </label>
                            {{ Form::file(fake_field('id_card_front'), ['class' => 'form-control','id' => fake_field('id_card_front'),'data-cval-name' => 'The ID card front','data-cval-rules' => 'required|files:jpg,png,jpeg|max:2048']) }}
                            <div class="form-text">{{ __('Upload scan copy of ID card front less than or equal 2MB.') }}</div>
                            <span class="validation-message cval-error" data-cval-error="{{ fake_field('id_card_front') }}">{{ $errors->first('id_card_front') }}</span>
                        </div>
                    </div>

                    <div class="col-md-6" v-if="idType != {{ ID_PASSPORT }}">
                        <div class="id-upload-card {{ $errors->has('id_card_back') ? 'has-error' : '' }}">
                            <div class="id-upload-icon"><i class="fa fa-id-card-o"></i></div>
                            <label for="{{ fake_field('id_card_back') }}" class="form-label required">{{ __('Upload Back') }}</label>
                            {{ Form::file(fake_field('id_card_back'), ['class' => 'form-control','id' => fake_field('id_card_back'),'data-cval-name' => 'The ID card back','data-cval-rules' => 'files:jpg,png,jpeg|max:2048']) }}
                            <div class="form-text">{{ __('Upload scan copy of ID card back less than or equal 2MB.') }}</div>
                            <span class="validation-message cval-error" data-cval-error="{{ fake_field('id_card_back') }}">{{ $errors->first('id_card_back') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary form-submission-button">{{ __('Submit ID') }}</button>
                </div>
            </section>
        </transition>
    {{ Form::close() }}
</div>
