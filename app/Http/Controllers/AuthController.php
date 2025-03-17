<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\OTPForgotPWMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        if ($request->has('email') && $request->has('otp') && $request->has('password') && $request->has('name') && $request->has('phone') && $request->has('dob') && $request->has('gender')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                    'otp' => 'required',
                    'password' => 'required|min:6',
                    'name' => 'required|max:255',
                    'phone' => 'required|regex:/^0[0-9]{9}$/',
                    'dob' => 'required|date',
                    'gender' => 'required|in:male,female,other',
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                    'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                    'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                    'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                    'name.required' => 'Hãy nhập họ và tên của bạn vào đi',
                    'phone.required' => 'Hãy nhập số điện thoại của bạn vào đi',
                    'phone.regex' => 'Số điện thoại không hợp lệ',
                    'dob.required' => 'Hãy nhập ngày sinh của bạn vào đi',
                    'dob.date' => 'Ngày sinh không hợp lệ',
                    'gender.required' => 'Hãy chọn giới tính của bạn',
                    'gender.in' => 'Giới tính không hợp lệ',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email này không hợp lệ']],
                    ], 422);
                }

                if (!password_verify($request->otp, $user->key_active)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['otp' => ['Mã OTP không chính xác']],
                    ], 422);
                }
                $user->key_active = null;
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->dob = $request->dob;
                $user->gender = $request->gender;
                $user->password = bcrypt($request->password);
                $user->active = 'active';
                $user->save();

                Auth::login($user);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Đăng ký thành công, chào mừng bạn đến với hệ thống',
                    'url' => route('home'),
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
        try {
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'Hãy nhập email của bạn vào đi',
                'email.email' => 'Email bạn nhập không hợp lệ rồi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Trả về lỗi validate dưới dạng JSON
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {

                if ($user->active == 'active') {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Email này đã tồn tại, hãy dùng email khác']],
                    ], 422);
                }

                if (!$user->updated_at->lt(Carbon::now()->subMinutes(1))) {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Bạn chỉ có thể gửi lại sau 1 phút']],
                    ], 422);
                }
            } else {
                $user = new User();
                $user->email = $request->email;
            }

            $randomPassword = Str::random(10);
            $user->password = bcrypt($randomPassword);

            $otp = $this->generateRandomOTP();
            $user->key_active = bcrypt($otp);
            $user->save();

            Mail::to($user->email)->send(new OTPMail($otp));

            return response()->json([
                'status' => 'success',
                'message' => 'Đăng ký thành công, hãy kiểm tra email của bạn để lấy mã OTP',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Hãy nhập email của bạn vào đi',
            'email.email' => 'Email bạn nhập không hợp lệ rồi',
            'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
        ]);

        try {

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            if ($user->active == 'inactive') {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            if (!password_verify($request->password, $user->password) && $request->google_id != null) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác, hãy lấy lại mật khẩu nếu bạn đã đăng nhập bằng google trước đó.',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            Auth::login($user);

            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại sau.' . $e->getMessage());
        }
    }

    public function redirectToGoogle(Request $request)
    {
        if ($request->has('choose_account')) {
            $googleAuth = Socialite::driver('google')->stateless();
            $googleAuth->with(['prompt' => 'select_account']);
            return $googleAuth->redirect();
        }else{
            if (session()->has('google_id')) {
                $user = User::where('google_id', session('google_id'))->first();
            
                if ($user) {
                    Auth::login($user);
                    return redirect()->route('home');
                }
            }

        }

        $googleAuth = Socialite::driver('google')->stateless()->with(['access_type' => 'offline', 'prompt' => 'consent']);

        return $googleAuth->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $imageContents = Http::get($googleUser->avatar)->body();
            $imageName = Str::random(40) . '.jpg';
            $imagePath = public_path('uploads/images/avatars/' . $imageName);

            if (!File::exists(public_path('uploads/images/avatars'))) {
                File::makeDirectory(public_path('uploads/images/avatars'), 0755, true);
            }

            File::put($imagePath, $imageContents);

            $user = User::where('email', $googleUser->email)->first();
            if (!$user) {
                $user = new User();
                $user->email = $googleUser->email;
                $user->name = $googleUser->name;
                $user->avatar = 'uploads/images/avatars/' . $imageName;
                $user->active = 'active';
                $user->password = bcrypt(Str::random(10));
            }
            $user->google_id = $googleUser->id;
            $user->save();

            Auth::login($user);
            // Lưu session thông tin người dùng (nếu muốn)
            session(['google_id' => $googleUser->id]);

            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('login')->with(['error' => 'Có lỗi xảy ra trong quá trình đăng nhập']);
        }
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route(('login'));
    }

    public function forgotPassword(Request $request)
    {
        if ($request->has('email')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == 'inactive') {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Thông tin xác thực không chính xác']],
                    ], 422);
                }



                if ($request->has('email') && $request->has('otp')) {

                    try {
                        $request->validate([
                            'otp' => 'required',
                        ], [
                            'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                        ]);
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $e->errors()
                        ], 422);
                    }

                    if (!password_verify($request->otp, $user->key_reset_password)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => ['otp' => ['Mã OTP không chính xác']],
                        ], 422);
                    }

                    if ($request->has('email') && $request->has('otp') && $request->has('password')) {
                        try {
                            $request->validate([
                                'password' => 'required|min:6',
                            ], [
                                'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                            ]);
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $e->errors()
                            ], 422);
                        }

                        try {

                            $user->key_reset_password = null;
                            $user->password = bcrypt($request->password);
                            $user->save();

                            Auth::login($user);

                            return response()->json([
                                'status' => 'success',
                                'message' => 'Đặt lại mật khẩu thành công',
                                'url' => route('home'),
                            ]);
                        } catch (Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                                'error' => $e->getMessage(),
                            ], 500);
                        }
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Hãy nhập mật khẩu mới của bạn',
                    ], 200);
                }

                if ($user->key_reset_password_at != null) {
                    $resetPasswordAt = Carbon::parse($user->key_reset_password_at);
                    if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                        return response()->json([
                            'status' => 'error',
                            'message' => ['email' => ['Bạn chỉ có thể gửi lại sau 3 phút']],
                        ], 422);
                    }
                }

                $randomOTPForgotPW = $this->generateRandomOTP();
                $user->key_reset_password = bcrypt($randomOTPForgotPW);
                $user->key_reset_password_at = Carbon::now();
                $user->save();

                Mail::to($user->email)->send(new OTPForgotPWMail($randomOTPForgotPW));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Hãy kiểm tra email của bạn để lấy mã OTP',
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }
}
