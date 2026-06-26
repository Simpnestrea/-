<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Foodball - Nấu ăn vui hơn mỗi ngày')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 border-r border-gray-100 flex flex-col flex-shrink-0 bg-white z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <a href="{{ route('home') }}" class="p-6 flex items-center space-x-3 group block">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-500 group-hover:scale-110 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                <line x1="6" y1="17" x2="18" y2="17"/>
            </svg>
            <span class="text-2xl font-black text-gray-900 tracking-tighter uppercase group-hover:text-orange-500 transition">Foodball</span>
        </a>

        <nav class="flex-1 overflow-y-auto custom-scrollbar px-4 py-2 space-y-2" x-data="{ libraryOpen: false }">
            
            <a href="{{ route('search') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('search') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('search') ? 'text-orange-500' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span>Tìm Kiếm Nâng Cao</span>
            </a>

            <a href="{{ route('home') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span :class="{'text-orange-600': '{{ request()->routeIs('home') }}'}">Trang Chủ</span>
            </a>

            <a href="{{ route('premium') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('premium') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                <span>Premium</span>
            </a>

            <a href="{{ route('stats') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('stats') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>Thống Kê Bếp</span>
            </a>

            @auth
            <a href="{{ route('interactions') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('interactions') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span>Tương Tác</span>
                </div>
                @if(isset($unreadInteractionsCount) && $unreadInteractionsCount > 0)
                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $unreadInteractionsCount }}</span>
                @endif
            </a>
            @endauth

            @auth
            <a href="{{ route('kitchen.index') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold transition-colors {{ request()->routeIs('kitchen.index') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('kitchen.index') ? 'text-orange-500' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                <span>Kho Món Ngon</span>
            </a>
            @endauth

            @auth
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold transition-colors text-red-650 hover:bg-red-50 hover:text-red-700">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    <span>Trang Quản Trị</span>
                </a>
                @endif
            @endauth
        </nav>

        <div class="p-6 border-t border-gray-100">
            <a href="{{ route('recipe.create') }}" class="w-full flex items-center justify-center space-x-2 bg-orange-500 text-white px-4 py-3 rounded-xl font-bold hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                <span>+ Viết món mới</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto bg-gray-50/50">
        
        <header class="flex items-center justify-between px-8 py-4 sticky top-0 bg-white/95 backdrop-blur-md z-40 border-b border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.06)] shrink-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 text-gray-500 hover:text-orange-500 transition font-bold lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Trang chủ</span>
                </a>
                <div class="hidden lg:block text-xl font-black text-gray-900 tracking-tighter uppercase">@yield('header_title', '')</div>
            </div>
            
            <div class="flex-1 px-8">
                @yield('header_middle')
            </div>

            @auth
                <div class="flex items-center space-x-4">
                    <!-- Ví tiền -->
                    <a href="{{ route('wallet.index') }}" class="flex items-center space-x-2 bg-amber-50 hover:bg-amber-100 text-amber-700 px-3.5 py-1.5 rounded-full font-bold text-sm border border-amber-200 transition">
                        <!-- Icon ví tiền (SVG) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <path d="M18 12h.01"/>
                        </svg>
                        <span>{{ number_format(auth()->user()->balance, 0, ',', '.') }} đ</span>
                    </a>

                    <!-- Dropdown Người dùng -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none hover:bg-gray-100 px-3 py-1.5 rounded-full transition">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-orange-500/20">
                            @else
                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="font-bold text-gray-700 text-sm hidden sm:block">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Menu dropdown -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             @click.away="open = false"
                             style="display: none;"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-gray-100 mb-1">
                                <p class="text-xs text-gray-400">Tài khoản</p>
                                <p class="font-bold text-gray-800 text-sm truncate">{{ auth()->user()->name }}</p>
                            </div>

                            <a href="{{ route('wallet.index') }}" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Ví của tôi</span>
                            </a>

                            <a href="{{ route('kitchen.index') }}" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span>Kho món ngon</span>
                            </a>

                            <a href="{{ route('stats') }}" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Thống kê bếp</span>
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition font-bold text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center space-x-2">
                    <a href="{{ route('login') }}" class="px-6 py-2 rounded-full font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition text-sm hidden sm:block">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="px-6 py-2 rounded-full font-bold text-white bg-orange-500 hover:bg-orange-600 transition text-sm shadow-lg shadow-orange-500/30">Đăng ký</a>
                </div>
            @endauth
        </header>

        @yield('content')

    </main>

    @stack('scripts')
</body>
</html>
