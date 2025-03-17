<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        // Lấy chế độ từ session hoặc mặc định là 'light' nếu chưa có
        $theme = session('theme', 'light');
        
        return view('home', compact('theme'));
    }

    public function changeTheme(Request $request)
    {
        // Lưu chế độ sáng/tối vào session
        session(['theme' => $request->input('theme')]);

        return redirect()->route('home');
    }
}
