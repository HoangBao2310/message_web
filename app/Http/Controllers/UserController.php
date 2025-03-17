<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\OTPUpdateUserMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(20);

        return view('admin.pages.users.index', compact('users'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($request->has('otp')) {
            $otp = $request->otp;

            if (!password_verify($otp, $user->key_reset_password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['Mã OTP không chính xác để thay đổi mật khẩu']],
                ], 422);
            }

            if ($request->has('password') && $request->has('password_confirmation')) {
                try {
                    $request->validate([
                        'password' => 'required|min:6|confirmed',
                    ], [
                        'password.required' => 'Hãy nhập mật khẩu mới',
                        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                        'password.confirmed' => 'Mật khẩu xác nhận không khớp',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->errors()
                    ], 422);
                }

                $user->password = bcrypt($request->password);
                $user->key_reset_password = null;

                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật mật khẩu thành công',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
            ], 200);
        }

        $otp = $this->generateRandomOTP();
        if ($user->reset_password_at != null) {
            $resetPasswordAt = Carbon::parse($user->reset_password_at);
            if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã được gửi đến Email của bạn, hoặc thử lại sau 3 phút',
                ], 200);
            }
        }

        $user->key_reset_password = bcrypt($otp);
        $user->reset_password_at = now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'password'));
        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mã OTP thành công, vui lòng kiểm tra Email của bạn',
        ], 200);
    }

    public function updateBankAccount(Request $request)
    {
        $user = Auth::user();

        if ($request->has('otp')) {
            $otp = $request->otp;

            if (!password_verify($otp, $user->key_change_bank)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['Mã OTP không chính xác để thay đổi thông tin ngân hàng']],
                ], 422);
            }

            if ($request->has('bank_id') && $request->has('account_number') && $request->has('account_name')) {
                try {
                    $request->validate([
                        'bank_id' => 'required|exists:banks,id',
                        'account_number' => 'required',
                        'account_name' => 'required',
                    ], [
                        'bank_id.required' => 'Hãy chọn ngân hàng',
                        'bank_id.exists' => 'Ngân hàng không tồn tại',
                        'account_number.required' => 'Hãy nhập số tài khoản',
                        'account_name.required' => 'Hãy nhập tên chủ tài khoản',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->errors()
                    ], 422);
                }

                $user->key_change_bank = null;

                $data = [
                    'bank_id' => $request->input('bank_id'),
                    'account_number' => $request->input('account_number'),
                    'account_name' => $request->input('account_name'),
                ];

                $bank_account = $user->bankAccount()->updateOrCreate(
                    ['user_id' => $user->id], // Điều kiện để kiểm tra sự tồn tại
                    $data // Dữ liệu cần cập nhật hoặc tạo mới
                );

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật thông tin ngân hàng thành công',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
            ], 200);
        }

        $otp = $this->generateRandomOTP();
        if ($user->change_bank_at != null) {
            $changeBankAt = Carbon::parse($user->change_bank_at);
            if (!$changeBankAt->lt(Carbon::now()->subMinutes(3))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã được gửi đến Email của bạn, hoặc thử lại sau 3 phút',
                ], 200);
            }
        }

        $user->key_change_bank = bcrypt($otp);
        $user->change_bank_at = now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'Banking'));

        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mã OTP thành công, vui lòng kiểm tra Email của bạn',
        ], 200);
    }

    public function userProfile()
    {
        $user = Auth::user();
        $banks = Bank::all();

        $bank_account = $user->bankAccount()->first();

        return view('pages.information.index', compact('user', 'banks', 'bank_account'));
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            ], [
                'avatar.required' => 'Hãy chọn ảnh avatar',
                'avatar.image' => 'Avatar phải là ảnh',
                'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg, gif, svg',
                'avatar.max' => 'Dung lượng avatar không được vượt quá 4MB'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $imageBackup = $user->avatar;
            $imageName = $user->id . time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('uploads/images/avatar/'), $imageName);
            $imagePath = 'uploads/images/avatar/' . $imageName;

            $user->avatar = $imagePath;
            $user->save();

            DB::commit();

            if (isset($imageBackup) && File::exists($imageBackup)) {
                File::delete($imageBackup);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật avatar thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }

    public function updateNameOrPhone(Request $request)
    {

        if ($request->has('name')) {
            try {
                $request->validate([
                    'name' => 'required|string|min:3|max:255',
                ], [
                    'name.required' => 'Hãy nhập tên',
                    'name.string' => 'Tên phải là chuỗi',
                    'name.min' => 'Tên phải có ít nhất 3 ký tự',
                    'name.max' => 'Tên không được vượt quá 255 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user-profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->name = $request->name;
                $user->save();
                return redirect()->route('user-profile')->with('success', 'Cập nhật tên thành công');
            } catch (\Exception $e) {
                return redirect()->route('user-profile')->with('error', 'Cập nhật tên thất bại');
            }
        } elseif ($request->has('phone')) {

            try {
                $request->validate([
                    'phone' => 'required|string|min:10|max:10',
                ], [
                    'phone.required' => 'Hãy nhập số điện thoại',
                    'phone.string' => 'Số điện thoại phải là chuỗi',
                    'phone.min' => 'Số điện thoại phải có ít nhất 10 ký tự',
                    'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('user-profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->phone = $request->phone;
                $user->save();
                return redirect()->route('user-profile')->with('success', 'Cập nhật số điện thoại thành công');
            } catch (\Exception $e) {
                return redirect()->route('user-profile')->with('error', 'Cập nhật số điện thoại thất bại');
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }
    }
   // Hàm updateUser
public function update(Request $request)
{
    // Xác thực dữ liệu đầu vào
    $validator = Validator::make($request->all(), [
        'avatar' => 'image|nullable|max:2048',
        'cover_image' => 'image|nullable|max:2048',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        'gender' => 'required|string|in:male,female',
        'phone' => 'nullable|string|regex:/^[0-9]+$/|digits_between:10,15|max:15', // Chỉ cho phép số
        'dob' => 'nullable|date|before_or_equal:today', // Thêm quy tắc cho ngày sinh
        'description' => 'nullable|string|max:1000', // Thêm quy tắc cho mô tả
    ], [
        'avatar.required' =>  __('messages.avatarrequired'),
        'avatar.image' =>  __('messages.avatarimage'),
        'avatar.mimes' =>  __('messages.avatarmimes'),
        'avatar.max' =>  __('messages.avatarmax'),
        'cover_image.image' =>  __('messages.cover_imageimage'),
        'cover_image.mimes' =>  __('messages.cover_imagemimes'),
        'cover_image.max' =>  __('messages.cover_imagemax'),
        'phone.max' =>  __('messages.phonemax'),
        'phone.regex' =>  __('messages.phoneregex'), 
        'phone.digits_between' =>  __('messages.phonedigits_between'),
        'dob.date' =>  __('messages.dobdate'),
        'dob.before_or_equal' =>  __('messages.dobbefore_or_equal'), // Thông báo lỗi cho ngày sinh
        'description.max' =>  __('messages.descriptionmax'),
    ]);

    if ($validator->fails()) {
        // In ra các lỗi cụ thể
        $errors = $validator->errors()->all();
        return redirect()->back()->withErrors($validator)->withInput()->with('error', implode(', ', $errors));
    }

    $user = Auth::user();

    // Kiểm tra nếu dữ liệu đã bị thay đổi ở nơi khác
    // if ($request->input('updated_at') != $user->updated_at->toDateTimeString()) {
    //     return redirect()->back()->withErrors(['user' => 'Dữ liệu đã được cập nhật ở nơi khác. Vui lòng tải lại trang.'])
    //         ->with('error',  __('messages.Datahasbeenupdatedelsewhere'));
    // }

    // Xử lý tải lên avatar
   
    if ($request->hasFile('avatar')) {
        $fileName = uniqid() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->move(public_path('uploads/images/avatars'), $fileName);
        $user->avatar = 'uploads/images/avatars/' . $fileName;
    }
    // Xử lý tải lên ảnh bìa
    if ($request->hasFile('cover_image')) {
        if ($user->cover_image && file_exists(public_path($user->cover_image))) {
            unlink(public_path($user->cover_image));
        }

        $coverFileName = uniqid() . '.' . $request->file('cover_image')->getClientOriginalExtension();
        $request->file('cover_image')->move(public_path('uploads/images/avatars'), $coverFileName);
        $user->cover_image = 'uploads/images/avatars/' . $coverFileName; // Giả sử bạn lưu trữ ảnh bìa cùng thư mục với ảnh đại diện
    }

    // Cập nhật thông tin người dùng
    $user->name = $request->name;
    $user->gender = $request->gender;
    $user->phone = $request->phone; // Cập nhật số điện thoại
    $user->dob = $request->dob; // Cập nhật ngày sinh
    $user->description = $request->description; // Cập nhật mô tả

    // Lưu thông tin
    $user->save();

    return redirect()->back()->with('success',  __('messages.successfully') );
}

}
