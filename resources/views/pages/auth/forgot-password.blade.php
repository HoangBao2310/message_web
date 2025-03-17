@extends('layouts.main')

@push('styles')
@endpush

@section('content-main')
@include('components.toast')
    <section class=" p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-11">
                    <div class="border-light shadow-sm rounded">
                        <div class="g-0">
                            <div class="col-12 d-flex align-items-center justify-content-center rounded">
                                <div class="col-12 col-lg-11 col-xl-10">
                                    <div class="card-body p-3 p-md-4 p-xl-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-5">
                                                    <div class="text-center mb-4">
                                                        <a href="{{ route('home') }}">
                                                            <img class="logohoanxu"
                                                                src="{{ asset('assets/images/logo/logochat.png') }}"
                                                                alt="logohoanxu">
                                                        </a>
                                                    </div>
                                                    <h4 class="text-center color-coins-refund">{{ __('messages.haveYouForgottenYourPassword') }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <form id="forgotForm">
                                            <div class="row gy-3 gx-0 overflow-hidden">
                                                <div class="col-12 form-email">
                                                    <div class="form-floating mb-3">
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email" id="email" placeholder="name@example.com"
                                                            value="{{ old('email') }}" required>
                                                        <label for="email" class="form-label">{{ __('messages.enterYourEmail') }}</label>
                                                    </div>
                                                </div>

                                                <div id="otpContainer" class="overflow-hidden text-center">

                                                </div>

                                                <div id="passwordContainer" ></div>

                                                <div class="box-button col-12">
                                                    <button
                                                        class="w-100 btn btn-lg border-coins-refund-2 color-coins-refund"
                                                        type="submit" id="btn-send">{{ __('messages.continue') }}</button>
                                                </div>

                                            </div>
                                        </form>
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-center mt-5">
                                                    <span>{{ __('messages.rememberedPassword') }} <a href="{{ route('login') }}" class="link-secondary text-decoration-none color-coins-refund">{{ __('messages.login') }}</a></span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts-main')
    <script>
        $(document).ready(function() {
            $('#forgotForm').on('submit', function(e) {
                e.preventDefault();
                const emailInput = $('#email');
                const email = emailInput.val();
                const submitButton = $('#btn-send');

                // Xóa thông báo lỗi cũ nếu tồn tại
                const oldInvalidFeedback = emailInput.parent().find('.invalid-feedback');
                emailInput.removeClass('is-invalid');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }

                // Thay đổi nút submit thành trạng thái loading
                submitButton.prop('disabled', true);
                submitButton.html('<span class="loading-spinner"></span> Đang xử lý...');

                $.ajax({
                    url: '{{ route('forgot.password') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        email: email
                    }),
                    success: function(response) {

                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            submitButton.remove();

                            $('.form-email').remove();

                            $('#otpContainer').html(`
                        <span class="text-center mb-1">${response.message}</span>
                        <div class="otp-container justify-content-center mb-3" id="input-otp">
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <br>
                        </div>
                    `);

                            $('.box-button').html(`
                        <button class="w-100 btn btn-lg border-coins-refund-2 color-coins-refund" type="button" id="submitOtp">Tiếp tục</button>
                    `);

                            $('#submitOtp').on('click', function() {
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });

                                input_otp.find('.invalid-otp').remove();

                                const oldInvalidFeedbackEmail = emailInput.parent().find('.invalid-feedback');
                                emailInput.removeClass('is-invalid');
                                if (oldInvalidFeedbackEmail.length) {
                                    oldInvalidFeedbackEmail.remove();
                                }

                                $.ajax({
                                    url: '{{ route('forgot.password') }}',
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    data: JSON.stringify({
                                        email: email,
                                        otp: otp,
                                    }),
                                    success: function(response) {
                                        
                                        if (response.status === 'success') {
                                            showToast(response.message,
                                                'success');
                                                $('#submitOtp').remove();
                                                $('#otpContainer').remove();

                                                $('#passwordContainer').html(`
                                                <span class="text-center mb-1">${response.message}</span>
                                                <div class="col-12">
                                                    <div class="form-floating mb-3 position-relative">
                                                        <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password" required>
                                                        <label for="password" class="form-label">Mật khẩu</label>
                                                        <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword"></i>
                                                    </div>
                                                </div>
                                            `);

                                                    $('.box-button').html(`
                                                <button class="w-100 btn btn-lg border-coins-refund-2 color-coins-refund" type="button" id="submitPassword">Xác nhận</button>
                                            `);

                                            $('#submitPassword').on('click', function() {
                                                const passwordInput = $('#password');
                                                const password = passwordInput.val();

                                                const oldInvalidFeedback = passwordInput.parent().find('.invalid-feedback');
                                                passwordInput.removeClass('is-invalid');
                                                    if (oldInvalidFeedback.length) {
                                                    oldInvalidFeedback.remove();
                                                }

                                                $.ajax({
                                                    url: '{{ route('forgot.password') }}',
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    data: JSON.stringify({
                                                        email: email,
                                                        otp: otp,
                                                        password: password
                                                    }),
                                                    success: function(response) {
                                                        if (response.status === 'success') {
                                                            showToast(response.message,
                                                                'success');
                                                            saveToast(response.message,
                                                                response.status);
                                                            window.location.href = response
                                                                .url;
                                                        } else {
                                                            showToast(response.message, 'error');
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        const response = xhr.responseJSON;
                                                        console.log('Error2:', response);

                                                        if (response && response.status === 'error') {
                                                            if (response.message.password) {
                                                                response.message.password.forEach(error => {
                                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                                    passwordInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                                });
                                                            }
                                                        } else {
                                                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                                                        }
                                                    }
                                                });
                                            });


                                        } else {
                                            showToast(response.message,
                                                'error');
                                        }
                                    },
                                    error: function(xhr) {
                                        const response = xhr.responseJSON;
                                        console.log('Error2:', response);

                                        if (response && response.status ===
                                            'error') {
                                            if (response.message.email) {
                                                response.message.email.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    emailInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.otp) {
                                                input_otp.append(`<div class="invalid-otp text-danger fs-7">${response.message.otp[0]}</div>`);
                                            }
                                        } else {
                                            showToast(
                                                'Đã xảy ra lỗi, vui lòng thử lại.',
                                                'error');
                                        }
                                    }
                                });
                            });

                        } else {
                            showToast(response.message, 'error');
                            submitButton.prop('disabled', false);
                            submitButton.html('Tiếp tục');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
console.log('Error1:', response);

                        if (response && response.message && response.message.email) {
                            response.message.email.forEach(error => {
                                const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                emailInput.addClass('is-invalid').parent().append(invalidFeedback);
                            });
                        } else {
                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                        }
                        submitButton.prop('disabled', false);
                        submitButton.html('Tiếp tục');
                    }
                });
            });
        });
    </script>
@endpush