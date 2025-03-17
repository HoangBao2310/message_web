<!-- Modal Profile -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">{{ __('messages.profile_info') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Ảnh Bìa -->
                    <div class="form-group">
                        <label for="cover_image" class="cover-image-label">
                            <img id="coverImagePreview" src="{{ Auth::user()->cover_image ? asset(Auth::user()->cover_image) :  asset('assets/images/logo/uocmo.jpg')  }}" class="rounded border border-primary" width="100%" height="190px" alt="Ảnh Bìa">
                        </label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control mt-2" style="display: none;" onchange="previewImage(event)">
                    </div>

                    <!-- Ảnh Đại Diện -->
                    <div class="form-group text-left mt-n5">
                        <label for="avatar"> <!-- Thêm label để kích hoạt chọn tệp -->
                            <img id="avatarPreview" src="{{ asset(Auth::user()->avatar) }}" class="rounded-circle" width="100" height="100" alt="Avatar">
                        </label>
                        <input type="file" name="avatar" id="avatar" class="form-control mt-2" style="display: none;" onchange="previewImage(event)">
                    </div>
                                        
                    <!-- Name -->
                    <div class="form-group">
                        <label>{{ __('messages.name')}}</label>
                        <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}">
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label>{{ __('messages.email')}}</label>
                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label>{{ __('messages.phone')}}</label>
                        <input type="tel" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label>{{ __('messages.dob')}}</label>
                        <input type="date" name="dob" class="form-control" value="{{ Auth::user()->dob }}">
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender">{{ __('messages.gender')}}</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>{{ __('messages.female')}}</option>
                            <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>{{ __('messages.male')}}</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label>{{ __('messages.description')}}</label>
                        <textarea name="description" class="form-control" rows="3">{{ Auth::user()->description }}</textarea>
                    </div>

                    <!-- Updated At (Hidden Field) -->
                    <input type="hidden" name="updated_at" value="{{ Auth::user()->updated_at }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close')}}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

