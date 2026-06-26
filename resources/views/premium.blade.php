@extends('layouts.app')

@section('title', 'Gói Premium - Foodball')
@section('header_title', 'Gói Premium')

@section('content')
    <div class="flex-1 max-w-5xl mx-auto w-full px-6 py-12 relative">
        <!-- Nút quay lại -->
        <div class="mb-6">
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-gray-500 hover:text-orange-500 font-bold transition bg-white border border-gray-200 hover:bg-orange-50 px-4 py-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-200" role="alert">
                <span class="font-bold">Thành công!</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200" role="alert">
                <span class="font-bold">Thất bại!</span> {{ session('error') }}
            </div>
        @endif

        <div class="bg-gradient-to-br from-orange-500 to-yellow-400 rounded-3xl p-12 text-white text-center shadow-xl shadow-orange-500/20 mb-12 relative overflow-hidden">
            <!-- Họa tiết trang trí nền -->
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <h1 class="text-5xl font-black mb-6">Trải Nghiệm Nấu Ăn Đỉnh Cao 👑</h1>
            <p class="text-xl font-medium opacity-90 max-w-2xl mx-auto mb-8">Mở khóa tất cả tính năng, hàng ngàn công thức độc quyền từ siêu đầu bếp và loại bỏ hoàn toàn quảng cáo.</p>
            
            @guest
                <a href="{{ route('login') }}" class="inline-block bg-white text-orange-500 px-10 py-4 rounded-full font-black text-lg hover:bg-gray-50 transition shadow-lg hover:scale-105 duration-300">
                    Đăng nhập để đăng ký Premium - 100k/tháng
                </a>
            @else
                @if(auth()->user()->is_premium)
                    <div class="inline-flex items-center space-x-2 bg-white/20 border border-white/30 px-8 py-3.5 rounded-full font-black text-lg text-white">
                        <span>Đã kích hoạt gói Premium 👑</span>
                    </div>
                @else
                    <form action="{{ route('premium.buy') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="bg-white text-orange-500 px-10 py-4 rounded-full font-black text-lg hover:bg-gray-50 transition shadow-lg hover:scale-105 duration-300">
                            Đăng Ký Ngay - Chỉ 100k/tháng
                        </button>
                    </form>
                @endif
            @endguest
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <div class="text-4xl mb-4">📖</div>
                <h3 class="text-xl font-bold mb-2">Công Thức Độc Quyền</h3>
                <p class="text-gray-500 text-sm">Truy cập không giới hạn vào kho tàng công thức bí truyền từ các chuyên gia.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <div class="text-4xl mb-4">🚫</div>
                <h3 class="text-xl font-bold mb-2">Không Quảng Cáo</h3>
                <p class="text-gray-500 text-sm">Trải nghiệm ứng dụng mượt mà, không bị gián đoạn bởi bất kỳ quảng cáo nào.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <div class="text-4xl mb-4">📱</div>
                <h3 class="text-xl font-bold mb-2">Lưu Ngoại Tuyến</h3>
                <p class="text-gray-500 text-sm">Tải công thức về thiết bị để nấu ăn bất cứ lúc nào, kể cả khi không có internet.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-orange-200 bg-orange-50/20 text-center hover:shadow-md transition relative">
                <div class="absolute top-3 right-3 bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">Mới</div>
                <div class="text-4xl mb-4">💸</div>
                <h3 class="text-xl font-bold mb-2 text-orange-600">Ưu Đãi Thuế</h3>
                <p class="text-gray-500 text-sm">Phí thuế bán công thức chỉ còn <strong class="text-gray-800">1%</strong> thay vì 5% như người dùng thường.</p>
            </div>
        </div>
    </div>
@endsection
