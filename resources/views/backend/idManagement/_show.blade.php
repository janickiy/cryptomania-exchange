<div class="images" v-viewer>
    <div class="row g-3">
        <div class="col-md-{{ $user->id_type == ID_PASSPORT ? '12' : '6' }}">
            <div class="id-document-preview">
                <div class="id-document-preview-header">
                    <h5 class="mb-0">{{ __('ID Card') }} {{ $user->id_type == ID_PASSPORT ? '' : __('Front') }}</h5>
                </div>
                <img src="{{ get_id_image($user->id_card_front) }}" alt="{{ __('ID Card Front') }}" class="id-document-image">
            </div>
        </div>
        @if($user->id_type != ID_PASSPORT)
            <div class="col-md-6">
                <div class="id-document-preview">
                    <div class="id-document-preview-header">
                        <h5 class="mb-0">{{ __('ID Card Back ') }}</h5>
                    </div>
                    <img src="{{ get_id_image($user->id_card_back) }}" alt="{{ __('ID Card Back') }}" class="id-document-image">
                </div>
            </div>
        @endif
    </div>
</div>
