<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoàn tất đăng ký - Foodball</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-800 flex h-screen overflow-hidden">

    <div class="hidden md:block md:w-1/2 lg:w-3/5 relative">
        <img src="https://images.unsplash.com/photo-1506084868230-bb9d95c24759?auto=format&fit=crop&q=80&w=1200" alt="Cooking ingredients background" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
        <div class="absolute top-1/2 left-12 -translate-y-1/2 text-white max-w-lg">
            <h2 class="text-5xl font-black mb-4 leading-tight">Chỉ một bước nữa<br>để bắt đầu! 🍳</h2>
            <p class="text-lg font-medium opacity-90">Bổ sung Tên hiển thị và Tên đăng nhập của bạn để cá nhân hóa căn bếp riêng.</p>
        </div>
    </div>

    <div class="w-full md:w-1/2 lg:w-2/5 flex flex-col justify-center px-8 sm:px-16 relative overflow-y-auto bg-white">
        <a href="{{ route('home') }}" class="absolute top-8 left-8 text-gray-400 hover:text-orange-500 transition flex items-center space-x-2 text-sm font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Trang chủ</span>
        </a>

        <div class="w-full max-w-md mx-auto mt-12 py-8">
            <div class="flex items-center space-x-2 mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                    <line x1="6" y1="17" x2="18" y2="17"/>
                </svg>
                <span class="text-3xl font-black tracking-tighter text-gray-900 uppercase">Foodball</span>
            </div>

            <div class="flex items-center space-x-3 mb-6 bg-orange-50 p-4 rounded-xl border border-orange-100">
                @if($socialUser['avatar'])
                    <img src="{{ $socialUser['avatar'] }}" alt="Social Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-orange-200">
                @else
                    <div class="w-12 h-12 rounded-full bg-orange-200 text-orange-700 flex items-center justify-center font-bold">
                        {{ strtoupper(substr($socialUser['name'], 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Liên kết thành công!</h3>
                    <p class="text-xs text-gray-500">Tiếp tục bằng tài khoản {{ ucfirst($socialUser['provider']) }}</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-2 text-gray-900">Hoàn tất hồ sơ ✨</h2>
            <p class="text-gray-500 mb-6 text-sm">Vui lòng cung cấp các thông tin hiển thị của bạn dưới đây.</p>

            <form action="{{ route('social.complete.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-1">Địa chỉ Email (Không thể thay đổi)</label>
                    <input type="email" value="{{ $socialUser['email'] }}" class="w-full px-4 py-3 rounded-lg border border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tên hiển thị</label>
                    <input type="text" name="name" value="{{ old('name', $socialUser['name']) }}" class="w-full px-4 py-3 rounded-lg border @error('name') border-red-500 @else border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @enderror outline-none transition" placeholder="Ví dụ: Hải Nam Nguyễn" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tên đăng nhập (Username)</label>
                    <input type="text" name="username" value="{{ old('username', strstr($socialUser['email'], '@', true)) }}" class="w-full px-4 py-3 rounded-lg border @error('username') border-red-500 @else border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @enderror outline-none transition" placeholder="Ví dụ: hainam99" required>
                    <p class="text-gray-400 text-xs mt-1">Chỉ chứa chữ không dấu, số, dấu chấm và gạch dưới.</p>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-lg hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                        Hoàn Tất & Khám Phá
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-gray-500">
                Nhầm tài khoản? <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-orange-500 font-bold hover:underline">Hủy liên kết</a>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>

</body>
</html>
