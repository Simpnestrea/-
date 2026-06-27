<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang xác thực của Provider (Google/Facebook).
     */
    public function redirectToProvider($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->withErrors(['login_id' => 'Nhà cung cấp đăng nhập không hỗ trợ.']);
        }

        // Kiểm tra xem đã cấu hình Client ID cho provider chưa. Nếu chưa, chạy chế độ mô phỏng (Mock Mode)
        $clientId = config("services.{$provider}.client_id");

        if ($clientId && class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            try {
                return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
            } catch (\Exception $e) {
                // Nếu xảy ra lỗi cấu hình, chuyển sang Mock Mode
            }
        }

        // Chế độ mô phỏng (Mock Mode) - Tự động chuyển đến callback với cờ 'mock'
        return redirect()->route('social.callback', ['provider' => $provider, 'mock' => 'true']);
    }

    /**
     * Nhận phản hồi từ Provider.
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->withErrors(['login_id' => 'Nhà cung cấp đăng nhập không hỗ trợ.']);
        }

        $socialUser = null;

        // Kiểm tra xem có sử dụng chế độ mô phỏng (Mock) không
        if ($request->input('mock') === 'true' || !config("services.{$provider}.client_id")) {
            // Dữ liệu mô phỏng cho Google và Facebook
            if ($provider === 'google') {
                $socialUser = (object)[
                    'id' => 'google_mock_123456789',
                    'name' => 'Nguyễn Hải Nam',
                    'email' => 'hainam.google@gmail.com',
                    'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=150',
                ];
            } else {
                $socialUser = (object)[
                    'id' => 'facebook_mock_987654321',
                    'name' => 'Trần Thị Mai',
                    'email' => 'maitran.facebook@gmail.com',
                    'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
                ];
            }
        } else {
            // Luồng thật dùng Laravel Socialite
            try {
                $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
            } catch (\Exception $e) {
                return redirect()->route('login')->withErrors(['login_id' => 'Đã có lỗi xảy ra khi xác thực qua ' . ucfirst($provider) . ': ' . $e->getMessage()]);
            }
        }

        if (!$socialUser || !$socialUser->email) {
            return redirect()->route('login')->withErrors(['login_id' => 'Không thể lấy thông tin email từ tài khoản mạng xã hội.']);
        }

        // 1. Kiểm tra tài khoản đã liên kết trước đó bằng provider & provider_id
        $user = User::where('provider', $provider)
                    ->where('provider_id', $socialUser->id)
                    ->first();

        // 2. Nếu chưa liên kết, kiểm tra xem email đã tồn tại chưa (liên kết tự động)
        if (!$user) {
            $user = User::where('email', $socialUser->email)->first();
            if ($user) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->id,
                ]);
            }
        }

        // 3. Nếu tìm thấy user (đã đăng ký trước hoặc liên kết email thành công), thực hiện đăng nhập
        if ($user) {
            Auth::login($user, true);
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        // 4. Nếu là user mới hoàn toàn, lưu thông tin MXH vào session và chuyển sang trang hoàn thiện thông tin
        session([
            'social_user' => [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'avatar' => $socialUser->avatar,
                'provider' => $provider,
                'provider_id' => $socialUser->id,
            ]
        ]);

        return redirect()->route('social.complete');
    }

    /**
     * Hiển thị giao diện hoàn thiện thông tin (Tên hiển thị & Username).
     */
    public function showCompleteForm()
    {
        if (!session()->has('social_user')) {
            return redirect()->route('login')->withErrors(['login_id' => 'Vui lòng đăng nhập mạng xã hội trước.']);
        }

        $socialUser = session('social_user');
        return view('social_complete', compact('socialUser'));
    }

    /**
     * Lưu thông tin người dùng mới đăng ký qua mạng xã hội.
     */
    public function completeRegistration(Request $request)
    {
        if (!session()->has('social_user')) {
            return redirect()->route('login')->withErrors(['login_id' => 'Vui lòng đăng nhập mạng xã hội trước.']);
        }

        $socialUser = session('social_user');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9_.]+$/'],
        ], [
            'name.required' => 'Vui lòng nhập tên hiển thị.',
            'username.required' => 'Vui lòng nhập tên đăng nhập (username).',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái không dấu, số, dấu chấm và gạch dưới.',
        ]);

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $socialUser['email'],
            'avatar' => $socialUser['avatar'],
            'provider' => $socialUser['provider'],
            'provider_id' => $socialUser['provider_id'],
            'password' => Hash::make(Str::random(24)), // Tạo mật khẩu ngẫu nhiên bảo mật
        ]);

        // Xóa thông tin tạm trong session
        session()->forget('social_user');

        // Đăng nhập tài khoản vừa tạo
        Auth::login($user, true);

        return redirect()->route('home')->with('success', 'Đăng ký tài khoản và đăng nhập thành công!');
    }
}
