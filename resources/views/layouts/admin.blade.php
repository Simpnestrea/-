<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Foodball')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Outfit', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(229, 231, 235, 0.2); border-radius: 10px; }
        .glass-header {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex h-screen overflow-hidden">

    <!-- Admin Sidebar -->
    <aside class="w-64 border-r border-slate-800 flex flex-col flex-shrink-0 bg-[#0F172A] text-white z-20 shadow-2xl relative">
        <!-- Accent Glow -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500"></div>

        <a href="{{ route('admin.dashboard') }}" class="p-6 flex items-center space-x-3 group block border-b border-slate-800/80">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-105 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <span class="text-lg font-black tracking-wider uppercase text-white">FOODBALL <span class="text-orange-500 text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-500/10 border border-orange-500/20 ml-0.5">CMS</span></span>
        </a>

        <nav class="flex-1 overflow-y-auto custom-scrollbar px-4 py-6 space-y-1.5">
            
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/20 font-bold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                <span>Tổng quan</span>
            </a>

            <a href="{{ route('admin.users') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.users') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/20 font-bold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Thành viên</span>
            </a>

            <a href="{{ route('admin.recipes') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.recipes') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/20 font-bold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Công thức món ăn</span>
            </a>

            <a href="{{ route('admin.categories') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.categories') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/20 font-bold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Danh mục</span>
            </a>

            <a href="{{ route('admin.reports') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.reports') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/20 font-bold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <div class="flex-1 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Tố cáo bình luận</span>
                    </div>
                    @php
                        $pendingReportsCount = \App\Models\CommentReport::count();
                    @endphp
                    @if($pendingReportsCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-bounce">{{ $pendingReportsCount }}</span>
                    @endif
                </div>
            </a>

            <div class="pt-6 border-t border-slate-800/60 my-4">
                <a href="{{ route('home') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl font-semibold text-slate-400 hover:bg-slate-800/50 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Quay lại Trang chủ</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-[#0B0F19] flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-sm shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate text-white leading-tight">{{ auth()->user()->name }}</p>
                    <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Admin Panel</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-red-400 hover:bg-slate-800/50 transition" title="Đăng xuất">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto bg-slate-50 relative">
        
        <!-- Header -->
        <header class="flex items-center justify-between px-8 py-4.5 sticky top-0 glass-header z-10 border-b border-slate-200/80 shrink-0 shadow-sm">
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">@yield('page_title', 'Admin Dashboard')</h1>
                <p class="text-[11px] text-slate-500 font-medium">Hệ thống quản trị Foodball CMS v2.5</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200/60 flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Hệ thống Online
                </span>
            </div>
        </header>

        <!-- Session Message Toast Triggers -->
        @if(session('success'))
            <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes(session('success')) }}', type: 'success' } }))"></div>
        @endif

        @if(session('error'))
            <div x-data x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ addslashes(session('error')) }}', type: 'error' } }))"></div>
        @endif

        <!-- Main Body -->
        <div class="px-8 py-6 flex-1">
            @yield('content')
        </div>

    </main>

    <!-- Stackable Toast Alerts -->
    <div x-data="toastComponent()"
         @toast.window="add($event.detail)"
         class="fixed bottom-6 right-6 z-[9999] flex flex-col space-y-3 max-w-sm w-full">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-4 opacity-0 scale-95"
                 x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                 x-transition:leave-end="translate-y-4 opacity-0 scale-95"
                 class="p-4 rounded-2xl shadow-2xl border flex items-start space-x-3 backdrop-blur-md transition-all duration-200"
                 :class="{
                     'bg-emerald-50/95 border-emerald-200 text-emerald-900': toast.type === 'success',
                     'bg-red-50/95 border-red-200 text-red-900': toast.type === 'error',
                     'bg-blue-50/95 border-blue-200 text-blue-900': toast.type === 'info',
                     'bg-amber-50/95 border-amber-200 text-amber-900': toast.type === 'warning'
                 }">
                <!-- Icon -->
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </template>
                </div>
                <!-- Body -->
                <div class="flex-1">
                    <p class="text-xs font-semibold" x-text="toast.message"></p>
                </div>
                <!-- Close -->
                <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-700 transition shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>
    </div>

    <script>
        function toastComponent() {
            return {
                toasts: [],
                add(detail) {
                    const id = Date.now() + Math.random().toString(36).substr(2, 9);
                    const message = typeof detail === 'string' ? detail : detail.message;
                    const type = detail.type || 'success';
                    this.toasts.push({ id, message, type, show: true });
                    setTimeout(() => {
                        this.remove(id);
                    }, 4000);
                },
                remove(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].show = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>

