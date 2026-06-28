@extends('layouts.admin')

@section('title', 'Quản lý Thành viên - Foodball')
@section('page_title', 'Quản lý Thành viên')

@section('content')
<div class="space-y-6" x-data="usersManager()">

    <!-- Search and Actions Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form action="{{ route('admin.users') }}" method="GET" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="query" value="{{ $query }}" placeholder="Tìm theo tên, username, email..." 
                    class="w-full py-2.5 pl-10 pr-4 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10"
                    {{ $query ? 'autofocus' : '' }}>
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                @if($query)
                    <a href="{{ route('admin.users') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 hover:text-slate-600">Xóa tìm kiếm</a>
                @endif
            </div>
            
            <select name="admin_role" class="py-2.5 pl-4 pr-10 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                <option value="">Tất cả vai trò</option>
                <option value="admin" {{ ($adminRole ?? '') === 'admin' ? 'selected' : '' }}>Chỉ Admin</option>
                <option value="user" {{ ($adminRole ?? '') === 'user' ? 'selected' : '' }}>Chỉ Người dùng</option>
            </select>

            <select name="role" class="py-2.5 pl-4 pr-10 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                <option value="">Tất cả cấp bậc</option>
                <option value="beginner" {{ ($role ?? '') === 'beginner' ? 'selected' : '' }}>🔰 Người mới (Beginner)</option>
                <option value="homecook" {{ ($role ?? '') === 'homecook' ? 'selected' : '' }}>🍳 Đầu bếp tại gia (Home Cook)</option>
                <option value="prochef" {{ ($role ?? '') === 'prochef' ? 'selected' : '' }}>👨‍🍳 Chuyên nghiệp (Pro Chef)</option>
                <option value="masterchef" {{ ($role ?? '') === 'masterchef' ? 'selected' : '' }}>👑 Siêu đầu bếp (Master Chef)</option>
            </select>

            <select name="status" class="py-2.5 pl-4 pr-10 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                <option value="">Tất cả gói</option>
                <option value="premium" {{ ($status ?? '') === 'premium' ? 'selected' : '' }}>Gói Premium 👑</option>
                <option value="free" {{ ($status ?? '') === 'free' ? 'selected' : '' }}>Gói Thường</option>
            </select>

            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-orange-500/20 text-sm whitespace-nowrap">Lọc</button>
        </form>
        
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider shrink-0 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
            Hiển thị {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} / {{ $users->total() }} thành viên
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Thành viên</th>
                        <th class="px-6 py-4">Thông tin liên lạc</th>
                        <th class="px-6 py-4">Vai trò / Gói</th>
                        <th class="px-6 py-4 text-center">Công thức</th>
                        <th class="px-6 py-4 text-center">Bình luận</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/30 transition-all" id="user-row-{{ $user->id }}">
                            
                            <!-- Avatar & Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200/80 shrink-0">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=f97316&background=fff7ed" alt="Avatar" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 hover:text-orange-500 cursor-pointer" @click="showDetail({{ $user->id }})">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-400">&#64;{{ $user->username ?? 'social_user' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email & Joined Date -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-700 font-medium">{{ $user->email }}</div>
                                <div class="text-[10px] text-slate-400">Tham gia: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Không rõ' }}</div>
                            </td>

                            <!-- Status Indicators -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1.5">
                                    <div class="flex flex-wrap gap-1.5 badge-container">
                                        @if($user->is_admin)
                                            <span class="admin-badge bg-red-50 text-red-700 border border-red-100 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wide">Admin</span>
                                        @else
                                            <span class="user-badge bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide">User</span>
                                        @endif

                                        @if($user->is_premium)
                                            <span class="premium-badge bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wide">👑 Premium</span>
                                        @else
                                            <span class="free-badge bg-slate-100 text-slate-400 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide">Free</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Culinary Role Selector -->
                                    <div class="relative w-36">
                                        <select @change="changeRole({{ $user->id }}, $event)" 
                                            class="role-select block w-full py-1 px-2.5 text-[11px] rounded-lg border border-slate-200 bg-slate-50 font-bold text-slate-700 transition focus:bg-white focus:border-orange-500 focus:outline-none cursor-pointer">
                                            <option value="beginner" {{ $user->role === 'beginner' ? 'selected' : '' }}>🔰 Beginner</option>
                                            <option value="homecook" {{ $user->role === 'homecook' ? 'selected' : '' }}>🍳 Home Cook</option>
                                            <option value="prochef" {{ $user->role === 'prochef' ? 'selected' : '' }}>👨‍🍳 Pro Chef</option>
                                            <option value="masterchef" {{ $user->role === 'masterchef' ? 'selected' : '' }}>👑 Master Chef</option>
                                        </select>
                                    </div>
                                </div>
                            </td>

                            <!-- Recipes count -->
                            <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-slate-700 font-mono">
                                {{ $user->recipes_count }}
                            </td>

                            <!-- Comments count -->
                            <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-slate-700 font-mono">
                                {{ $user->comments_count }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    
                                    <!-- View detail button -->
                                    <button type="button" @click="showDetail({{ $user->id }})" class="font-bold text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition">
                                        Xem chi tiết
                                    </button>

                                    <!-- Toggle Premium Action -->
                                    <form action="{{ route('admin.users.toggle-premium', $user) }}" method="POST" class="m-0 inline" @submit.prevent="togglePremium({{ $user->id }}, $event)">
                                        @csrf
                                        <button type="submit" class="premium-btn font-bold text-xs px-2.5 py-1.5 rounded-lg border transition-all
                                            {{ $user->is_premium 
                                                ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' 
                                                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800' }}">
                                            {{ $user->is_premium ? 'Hủy Premium' : 'Gán Premium' }}
                                        </button>
                                    </form>

                                    <!-- Toggle Admin Action -->
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="m-0 inline" @submit.prevent="toggleAdmin({{ $user->id }}, $event, '{{ addslashes($user->name) }}')">
                                            @csrf
                                            <button type="submit" class="admin-btn font-bold text-xs px-2.5 py-1.5 rounded-lg border transition-all
                                                {{ $user->is_admin 
                                                    ? 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' 
                                                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800' }}">
                                                {{ $user->is_admin ? 'Hạ quyền Admin' : 'Thăng Admin' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold italic px-2.5 py-1.5">Tôi</span>
                                    @endif

                                    <!-- Delete Action -->
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="m-0 inline" @submit.prevent="deleteUser({{ $user->id }}, $event)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-white hover:bg-red-50 text-slate-400 hover:text-red-600 border border-slate-200 hover:border-red-200 p-1.5 rounded-lg transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">Không tìm thấy thành viên nào phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->appends(['query' => $query, 'admin_role' => $adminRole ?? '', 'role' => $role ?? '', 'status' => $status ?? ''])->links() }}
            </div>
        @endif
    </div>

    <!-- User Detail Modal -->
    <div x-show="detailModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="detailModal = false"></div>

        <!-- Content Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 transition-all max-h-[85vh] flex flex-col overflow-hidden">
                <!-- Close Button -->
                <button type="button" @click="detailModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition z-10 bg-white/80 p-1.5 rounded-full border border-slate-100 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Modal Loading State -->
                <div x-show="loadingDetail" class="py-12 flex flex-col items-center justify-center space-y-4">
                    <div class="w-12 h-12 rounded-full border-4 border-slate-100 border-t-orange-500 animate-spin"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Đang tải thông tin...</span>
                </div>

                <!-- Modal Content -->
                <div x-show="!loadingDetail && userDetail" class="space-y-6 flex-1 overflow-y-auto custom-scrollbar pr-1">
                    <template x-if="userDetail">
                        <div>
                            <!-- User Card Header -->
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 pb-6 border-b border-slate-100">
                                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                    <img :src="userDetail.user.avatar ? (userDetail.user.avatar.startsWith('http') ? userDetail.user.avatar : '/storage/' + userDetail.user.avatar) : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userDetail.user.name) + '&color=f97316&background=fff7ed'" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 text-center sm:text-left space-y-2">
                                    <div>
                                        <h4 class="text-lg font-black text-slate-900 leading-tight" x-text="userDetail.user.name"></h4>
                                        <p class="text-xs text-slate-400" x-text="'@' + (userDetail.user.username || 'social_user')"></p>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5">
                                        <span x-show="userDetail.user.is_admin" class="bg-red-50 text-red-700 border border-red-100 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">Admin</span>
                                        <span x-show="!userDetail.user.is_admin" class="bg-slate-100 text-slate-500 border border-slate-200 text-[9px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider">User</span>
                                        <span x-show="userDetail.user.is_premium" class="bg-amber-50 text-amber-700 border border-amber-100 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">👑 Premium</span>
                                        <span x-show="!userDetail.user.is_premium" class="bg-slate-100 text-slate-400 text-[9px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider">Free</span>
                                        
                                        <!-- Culinary Role Badge -->
                                        <span x-show="userDetail.user.role === 'beginner'" class="bg-slate-50 text-slate-600 border border-slate-200 text-[9px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider">🔰 Beginner</span>
                                        <span x-show="userDetail.user.role === 'homecook'" class="bg-blue-50 text-blue-700 border border-blue-100 text-[9px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider">🍳 Home Cook</span>
                                        <span x-show="userDetail.user.role === 'prochef'" class="bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider">👨‍🍳 Pro Chef</span>
                                        <span x-show="userDetail.user.role === 'masterchef'" class="bg-orange-50 text-orange-700 border border-orange-100 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">👑 Master Chef</span>
                                    </div>
                                    <p class="text-xs text-slate-500 italic" x-text="userDetail.user.bio || 'Chưa cập nhật tiểu sử.'"></p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center shrink-0 min-w-[140px]">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Số dư ví</span>
                                    <span class="text-base font-black text-slate-900 font-mono block" x-text="new Intl.NumberFormat('vi-VN').format(userDetail.user.balance) + ' đ'"></span>
                                </div>
                            </div>

                            <!-- Activity Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 py-6 border-b border-slate-100">
                                <div class="bg-orange-50/40 border border-orange-100/60 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-450 uppercase tracking-widest block">Công thức đã đăng</span>
                                        <span class="text-2xl font-black text-orange-600 font-mono mt-1 block" x-text="userDetail.user.recipes_count"></span>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-orange-100/50 text-orange-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                </div>
                                <div class="bg-blue-50/40 border border-blue-100/60 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-450 uppercase tracking-widest block">Bình luận đóng góp</span>
                                        <span class="text-2xl font-black text-blue-600 font-mono mt-1 block" x-text="userDetail.user.comments_count"></span>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-blue-100/50 text-blue-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs for Sub-contents: Recipes vs Comments -->
                            <div class="pt-6" x-data="{ currentTab: 'recipes' }">
                                <div class="flex items-center space-x-4 border-b border-slate-100 pb-3">
                                    <button @click="currentTab = 'recipes'" 
                                            class="text-xs font-bold uppercase tracking-wider pb-2 border-b-2 transition"
                                            :class="currentTab === 'recipes' ? 'border-orange-500 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                        Công thức nấu ăn mới
                                    </button>
                                    <button @click="currentTab = 'comments'" 
                                            class="text-xs font-bold uppercase tracking-wider pb-2 border-b-2 transition"
                                            :class="currentTab === 'comments' ? 'border-orange-500 text-slate-900' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                        Bình luận gần đây
                                    </button>
                                </div>

                                <!-- Recipes List Tab -->
                                <div x-show="currentTab === 'recipes'" class="pt-4 space-y-3">
                                    <template x-if="userDetail.recent_recipes.length === 0">
                                        <p class="text-xs text-slate-400 italic text-center py-4">Chưa đăng công thức nấu ăn nào.</p>
                                    </template>
                                    <template x-for="recipe in userDetail.recent_recipes" :key="recipe.id">
                                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/60 transition">
                                            <div class="flex items-center space-x-3 truncate">
                                                <img :src="recipe.image || 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=120'" class="w-10 h-10 rounded-lg object-cover bg-slate-200 shadow-sm shrink-0">
                                                <div class="truncate">
                                                    <span class="text-sm font-bold text-slate-800 truncate block" x-text="recipe.title"></span>
                                                    <span class="text-[10px] text-slate-450 font-semibold uppercase" x-text="recipe.category ? recipe.category.name : 'Chưa phân loại'"></span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-sm font-extrabold text-slate-800 block" x-text="new Intl.NumberFormat('vi-VN').format(recipe.views_count)"></span>
                                                <span class="text-[8px] font-bold text-slate-400 block uppercase">Lượt xem</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Comments List Tab -->
                                <div x-show="currentTab === 'comments'" class="pt-4 space-y-3">
                                    <template x-if="userDetail.recent_comments.length === 0">
                                        <p class="text-xs text-slate-400 italic text-center py-4">Chưa đăng bình luận nào.</p>
                                    </template>
                                    <template x-for="comment in userDetail.recent_comments" :key="comment.id">
                                        <div class="p-3 bg-slate-50 rounded-xl space-y-1">
                                            <p class="text-xs text-slate-700 italic font-medium" x-text="'&quot;' + comment.content + '&quot;'"></p>
                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                                <span>Bài viết: <strong class="text-slate-500" x-text="comment.recipe ? comment.recipe.title : 'Đã xóa'"></strong></span>
                                                <span x-text="new Date(comment.created_at).toLocaleDateString('vi-VN')"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Reusable Beautiful Confirmation Modal -->
    <div x-show="confirmModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-transition>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="confirmModal = false"></div>

        <!-- Content Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 transition-all text-center">
                <!-- Warning/Question Icon -->
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-orange-50 text-orange-500 mb-4 border border-orange-100">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <h3 class="text-base font-extrabold text-slate-900 mb-1" x-text="confirmTitle"></h3>
                <p class="text-xs text-slate-500 font-medium mb-6" x-text="confirmMessage"></p>
                
                <div class="flex items-center justify-center gap-3">
                    <button @click="confirmModal = false" class="px-4 py-2 border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition">
                        Hủy bỏ
                    </button>
                    <button @click="executeConfirm()" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl transition shadow-md shadow-orange-500/10">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function usersManager() {
        return {
            detailModal: false,
            loadingDetail: false,
            userDetail: null,
            
            confirmModal: false,
            confirmTitle: '',
            confirmMessage: '',
            confirmCallback: null,
            
            triggerConfirm(title, message, callback) {
                this.confirmTitle = title;
                this.confirmMessage = message;
                this.confirmCallback = callback;
                this.confirmModal = true;
            },
            
            executeConfirm() {
                this.confirmModal = false;
                if (typeof this.confirmCallback === 'function') {
                    this.confirmCallback();
                }
            },
            
            showDetail(userId) {
                this.detailModal = true;
                this.loadingDetail = true;
                this.userDetail = null;
                
                fetch(`/admin/users/${userId}/detail`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async res => {
                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.user) {
                        this.userDetail = data;
                    } else {
                        throw new Error('Dữ liệu không hợp lệ');
                    }
                    this.loadingDetail = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loadingDetail = false;
                    this.detailModal = false;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Không thể tải chi tiết: ' + err.message, type: 'error' } }));
                });
            },
            
            changeRole(userId, event) {
                const newRole = event.target.value;
                
                fetch(`/admin/users/${userId}/change-role`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ role: newRole })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else if (data.error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.error, type: 'error' } }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra khi cập nhật cấp bậc', type: 'error' } }));
                });
            },
            
            togglePremium(userId, event) {
                const form = event.target.closest('form');
                const url = form.action;
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const badgeContainer = document.querySelector(`#user-row-${userId} .badge-container`);
                        const premiumBtn = document.querySelector(`#user-row-${userId} .premium-btn`);
                        
                        if (data.is_premium) {
                            const premiumBadge = document.createElement('span');
                            premiumBadge.className = 'premium-badge bg-amber-50 text-amber-700 border border-amber-100 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wide';
                            premiumBadge.innerHTML = '👑 Premium';
                            
                            const freeBadge = badgeContainer.querySelector('.free-badge');
                            if (freeBadge) freeBadge.remove();
                            badgeContainer.appendChild(premiumBadge);
                            
                            premiumBtn.innerHTML = 'Hủy Premium';
                            premiumBtn.className = 'premium-btn font-bold text-xs px-2.5 py-1.5 rounded-lg border transition-all bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100';
                        } else {
                            const freeBadge = document.createElement('span');
                            freeBadge.className = 'free-badge bg-slate-100 text-slate-400 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide';
                            freeBadge.innerHTML = 'Free';
                            
                            const premiumBadge = badgeContainer.querySelector('.premium-badge');
                            if (premiumBadge) premiumBadge.remove();
                            badgeContainer.appendChild(freeBadge);
                            
                            premiumBtn.innerHTML = 'Gán Premium';
                            premiumBtn.className = 'premium-btn font-bold text-xs px-2.5 py-1.5 rounded-lg border transition-all bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800';
                        }
                        
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                });
            },

            toggleAdmin(userId, event, name) {
                const form = event.target.closest('form');
                const url = form.action;
                const btn = form.querySelector('button');
                const isDemoting = btn ? btn.innerText.includes('Hạ') : false;
                const actionText = isDemoting ? 'Hạ cấp quyền Admin' : 'Thăng chức Admin';

                this.triggerConfirm(
                    `${actionText}?`,
                    `Bạn có chắc chắn muốn thực hiện hành động này đối với thành viên ${name}?`,
                    () => {
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const badgeContainer = document.querySelector(`#user-row-${userId} .badge-container`);
                                const adminBtn = document.querySelector(`#user-row-${userId} .admin-btn`);
                                
                                if (data.is_admin) {
                                    const adminBadge = document.createElement('span');
                                    adminBadge.className = 'admin-badge bg-red-50 text-red-700 border border-red-100 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wide';
                                    adminBadge.innerHTML = 'Admin';
                                    
                                    const userBadge = badgeContainer.querySelector('.user-badge');
                                    if (userBadge) userBadge.remove();
                                    badgeContainer.insertBefore(adminBadge, badgeContainer.firstChild);
                                    
                                    adminBtn.innerHTML = 'Hạ quyền Admin';
                                    adminBtn.className = 'admin-btn font-bold text-xs px-2.5 py-1.5 rounded-lg border transition-all bg-red-50 text-red-700 border-red-200 hover:bg-red-100';
                                } else {
                                    const userBadge = document.createElement('span');
                                    userBadge.className = 'user-badge bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide';
                                    userBadge.innerHTML = 'User';
                                    
                                    const adminBadge = badgeContainer.querySelector('.admin-badge');
                                    if (adminBadge) adminBadge.remove();
                                    badgeContainer.insertBefore(userBadge, badgeContainer.firstChild);
                                    
                                    adminBtn.innerHTML = 'Thăng Admin';
                                    adminBtn.className = 'admin-btn font-bold text-xs px-2.5 py-1.5 rounded-lg border transition-all bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800';
                                }
                                
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            } else if (data.error) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.error, type: 'error' } }));
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                        });
                    }
                );
            },

            deleteUser(userId, event) {
                const form = event.target.closest('form');
                const url = form.action;

                this.triggerConfirm(
                    'Xóa vĩnh viễn tài khoản?',
                    'CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn tài khoản và toàn bộ công thức, bình luận của họ. Bạn có chắc chắn?',
                    () => {
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const row = document.getElementById(`user-row-${userId}`);
                                if (row) row.remove();
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                            } else if (data.error) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.error, type: 'error' } }));
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                        });
                    }
                );
            }
        };
    }
</script>
@endpush
@endsection

