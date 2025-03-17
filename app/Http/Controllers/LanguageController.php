<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanguage($lang)
    {
        // Kiểm tra xem ngôn ngữ có hợp lệ không
        if (in_array($lang, ['en', 'vi'])) {
            session(['locale' => $lang]); // Lưu ngôn ngữ vào session
            return response()->json(['success' => true]); // Trả về phản hồi JSON
        }

        return response()->json(['success' => false, 'message' => 'Invalid language'], 400); // Trả về lỗi nếu ngôn ngữ không hợp lệ
    }
}
