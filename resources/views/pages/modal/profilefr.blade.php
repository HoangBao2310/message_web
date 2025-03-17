<!-- Modal Thông Tin Bạn Bè -->
<div class="modal fade" id="friendInfoModal-{{ $friend->id }}" tabindex="-1" aria-labelledby="friendInfoModalLabel-{{ $friend->id }}" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="friendInfoModalLabel-{{ $friend->id }}">{{ __('messages.friend_info') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Ảnh Bìa -->
                <div class="text-center mb-4">
                    <img src="{{ $friend->cover_image ?? 'assets/images/logo/uocmo.jpg' }}" class="img-fluid rounded" alt="Cover Image" style="max-height: 200px; width: 100%; object-fit: cover;">
                </div>
                <!-- Ảnh Đại Diện Bạn Bè -->
                <div class="text-center mb-4" style="margin-top: -60px;">
                    <img src="{{ $friend->avatar }}" class="rounded-circle border border-3 border-light" width="100" height="100" alt="Avatar">
                </div>
                <div class="text-center">
                    <h6 class="fw-bold">{{ $friend->name }}</h6>
                </div>
                <hr>
                <div class="row text-start">
                    <!-- Gmail -->
                    <div class="col-4 mb-3">
                        <p class="mb-0 text-primary"><strong>{{ __('messages.email') }}:</strong></p>
                    </div>
                    <div class="col-8 mb-3">
                        <p class="mb-0"><strong>{{ $friend->email}}</strong></p>
                    </div>
                    <!-- Giới Tính -->
                    <div class="col-4 mb-3">
                        <p class="mb-0 text-primary"><strong>{{ __('messages.gender') }}: </strong></p>
                    </div>
                    <div class="col-8 mb-3">
                        <p class="mb-0"><strong>{{ $friend->gender == 'female' ? __('messages.female') : ($friend->gender == 'male' ? __('messages.male') : __('')) }}</strong></p>
                    </div>

                    <!-- Ngày Sinh -->
                    <div class="col-4 mb-3">
                        <label class="fw-bold text-primary">{{ __('messages.dob') }}: </label>
                    </div>
                    <div class="col-8 mb-3">
                        <label class="fw-bold">
                            {{ $friend->dob ? \Carbon\Carbon::parse($friend->dob)->format('d/m/Y') : '' }}
                        </label>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="col-4 mb-3">
                        <label class="fw-bold text-primary">{{ __('messages.phone') }}:</label>
                    </div>
                    <div class="col-8 mb-3">
                        <label class="fw-bold">{{ $friend->phone ?? '' }}</label>
                    </div>
                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label class="fw-bold text-primary">{{ __('messages.description') }}:</label>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">{{ $friend->description ?? '' }}</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
            </div>
        </div>
    </div>
</div>