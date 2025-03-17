@extends('layouts.main')

@push('styles-main')
    <style>

    </style>
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
                                                    <h4 class="text-center color-coins-refund">Tạo tài khoản</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">

                                            <a href="{{ route('google.login') }}" class="btn-165 mb-3 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 262">
                                                    <path fill="#4285F4"
                                                        d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027">
                                                    </path>
                                                    <path fill="#34A853"
                                                        d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1">
                                                    </path>
                                                    <path fill="#FBBC05"
                                                        d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782">
                                                    </path>
                                                    <path fill="#EB4335"
                                                        d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251">
                                                    </path>
                                                </svg>
                                                <span>Đăng nhập với Goole</span>
                                            </a>

                                            <a href="{{ route('google.login') }}?choose_account=true" class="btn-165 mb-3 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 262">
                                                    <path fill="#4285F4"
                                                        d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027">
                                                    </path>
                                                    <path fill="#34A853"
                                                        d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1">
                                                    </path>
                                                    <path fill="#FBBC05"
                                                        d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782">
                                                    </path>
                                                    <path fill="#EB4335"
                                                        d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251">
                                                    </path>
                                                </svg>
                                                <span>Đăng nhập bằng tài khoản khác</span>
                                            </a>
                                            
                                        </div>

                                        <form id="registerForm">
                                            <div class="row gy-3 gx-0 overflow-hidden">
                                                <div class="col-12 form-email">
                                                    <div class="form-floating mb-3">
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email" id="email" placeholder="name@example.com"
                                                            value="{{ old('email') }}" required>
                                                        <label for="email" class="form-label">Nhập email của bạn</label>
                                                    </div>
                                                </div>

                                                <div id="otpPasswordContainer" class="overflow-hidden text-center">

                                                </div>

                                                <div class="box-button col-12">
                                                    <button
                                                        class="w-100 btn btn-lg border-coins-refund-2 color-coins-refund"
                                                        type="submit" id="btn-send">Tiếp tục</button>
                                                </div>

                                            </div>
                                        </form>
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-center mt-5">
                                                    <span>Bạn đã có tài khoản? <a href="{{ route('login') }}"
                                                            class="link-secondary text-decoration-none color-coins-refund">Đăng
                                                            nhập</a></span>

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
            $('#registerForm').on('submit', function(e) {
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
                    url: '{{ route('register') }}',
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

                            $('#otpPasswordContainer').html(`
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
                        <div class="col-12">
                            <div class="form-floating mb-3 position-relative">
                                <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password" required>
                                <label for="password" class="form-label">Mật khẩu</label>
                                <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer" id="togglePassword"></i>
                            </div>
                            <div class="form-floating mb-3 position-relative">
                                <input type="text" class="form-control" name="name" id="name" value="" placeholder="Name" required>
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="form-floating mb-3 position-relative">
                                <input type="text" class="form-control" name="phone" id="phone" value="" placeholder="Phone" required>
                                <label for="phone" class="form-label">Phone</label>
                            </div>
                            <div class="form-floating mb-3 position-relative">
                                <input type="date" class="form-control" name="dob" id="dob" value="" placeholder="Date of Birth" required>
                                <label for="dob" class="form-label">Date of Birth</label>
                            </div>
                            <div class="form-floating mb-3 position-relative">
                                <select class="form-control" name="gender" id="gender" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <label for="gender" class="form-label">Gender</label>
                            </div>
                        </div>
                    `);

                            $('.box-button').html(`
                        <button class="w-100 btn btn-lg border-coins-refund-2 color-coins-refund" type="button" id="submitOtpPassword">Xác nhận</button>
                    `);

                            $('#submitOtpPassword').on('click', function() {
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');
                                const passwordInput = $('#password');
                                const nameInput = $('#name');
                                const phoneInput = $('#phone');
                                const dobInput = $('#dob');
                                const genderInput = $('#gender');

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });
                                const password = passwordInput.val();
                                const name = nameInput.val();
                                const phone = phoneInput.val();
                                const dob = dobInput.val();
                                const gender = genderInput.val();

                                removeInvalidFeedback(passwordInput);
                                input_otp.find('.invalid-otp').remove();
                                removeInvalidFeedback(emailInput);
                                removeInvalidFeedback(nameInput);
                                removeInvalidFeedback(phoneInput);
                                removeInvalidFeedback(dobInput);
                                removeInvalidFeedback(genderInput);


                                $.ajax({
                                    url: '{{ route('register') }}',
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    data: JSON.stringify({
                                        email: email,
                                        otp: otp,
                                        password: password,
                                        name: name,
                                        phone: phone,
                                        dob: dob,
                                        gender: gender
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
                                            showToast(response.message,
                                                'error');
                                        }
                                    },
                                    error: function(xhr) {
                                        const response = xhr.responseJSON;

                                        if (response && response.status === 'error') {
                                            if (response.message.email) {
                                                response.message.email.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    emailInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.otp) {
                                                input_otp.append(`<div class="invalid-otp text-danger fs-7">${response.message.otp[0]}</div>`);
                                            }
                                            if (response.message.password) {
                                                response.message.password.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    passwordInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.name) {
                                                response.message.name.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    nameInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.phone) {
                                                response.message.phone.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    phoneInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.dob) {
                                                response.message.dob.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    dobInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.gender) {
                                                response.message.gender.forEach(error => {
                                                    const invalidFeedback = $('<div class="invalid-feedback"></div>').text(error);
                                                    genderInput.addClass('is-invalid').parent().append(invalidFeedback);
                                                });
                                            }
                                        } else {
                                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
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

                        console.log(response);


                        if (response && response.message && response.message.email) {
                            response.message.email.forEach(error => {
                                const invalidFeedback = $(
                                    '<div class="invalid-feedback"></div>').text(
                                    error);
                                emailInput.addClass('is-invalid').parent().append(
                                    invalidFeedback);
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


        function removeInvalidFeedback(input) {
            const oldInvalidFeedback = input.parent().find('.invalid-feedback');
            input.removeClass('is-invalid');
            if (oldInvalidFeedback.length) {
                oldInvalidFeedback.remove();
            }
        }
    </script>
@endpush
