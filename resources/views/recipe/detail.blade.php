@extends('layouts.app')

@section('title', $recipe->title . ' - Foodball')
@section('header_title', Str::limit($recipe->title, 30))

@push('styles')
<style>
    @media print {
        /* Ẩn sidebar, header, các nút hành động, bình luận và form */
        aside, header, button, form, .border-t, .flex-wrap, a, svg, .rounded-full {
            display: none !important;
        }
        /* Hiển thị bình thường các phần tử chính */
        body, main, .flex-1, .max-w-7xl, .p-6, .border, .rounded-2xl {
            overflow: visible !important;
            height: auto !important;
            background: white !important;
            color: black !important;
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        /* Canh chỉnh kích thước ảnh món ăn khi in */
        .w-full.lg\:w-\[350px\] {
            width: 250px !important;
            height: 250px !important;
        }
        .flex-col.lg\:flex-row {
            flex-direction: column !important;
        }
    }
</style>
@endpush

@section('content')
    @php
        $isLiked = auth()->check() && auth()->user()->likedRecipes()->where('recipe_id', $recipe->id)->exists();
        $likesCount = $recipe->likedByUsers()->count();
        $isSaved = auth()->check() && auth()->user()->savedRecipes()->where('recipe_id', $recipe->id)->exists();
        
        $isPaidRecipe = $recipe->is_premium && $recipe->price > 0;
        $isAuthorized = false;
        if (!$isPaidRecipe) {
            $isAuthorized = true;
        } else {
            $isAuthorized = auth()->check() && (
                auth()->id() === $recipe->user_id ||
                auth()->user()->isAdmin() ||
                auth()->user()->is_premium ||
                auth()->user()->purchasedRecipes()
                    ->where('recipe_user_purchases.recipe_id', $recipe->id)
                    ->where(function ($q) {
                        $q->where('recipe_user_purchases.price', '>', 0)
                          ->orWhere(function ($sub) {
                              $sub->where('recipe_user_purchases.price', 0)
                                  ->where('recipe_user_purchases.created_at', '>=', now()->subHours(24));
                          });
                    })
                    ->exists()
            );
        }
        $lockAfterStep = $recipe->steps->count() > 3 ? 3 : max(1, $recipe->steps->count() - 1);
        $isAuthorMasterChef = $recipe->user && $recipe->user->isMasterChef();
        
        $adUnlocksCount = 0;
        if (auth()->check()) {
            $adUnlocksCount = \Illuminate\Support\Facades\DB::table('recipe_user_purchases')
                ->where('user_id', auth()->id())
                ->where('price', 0)
                ->where('updated_at', '>=', now()->subHours(24))
                ->count();
        }
    @endphp
    <div x-data="{
        recipeLiked: {{ $isLiked ? 'true' : 'false' }},
        recipeLikesCount: {{ $likesCount }},
        recipeSaved: {{ $isSaved ? 'true' : 'false' }},
        async toggleRecipeLike() {
            try {
                const response = await fetch('{{ route('recipe.like', $recipe->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.recipeLiked = data.is_liked;
                    this.recipeLikesCount = data.likes_count;
                }
            } catch (error) {
                console.error('Error liking recipe:', error);
            }
        },
        async toggleRecipeSave() {
            try {
                const response = await fetch('{{ route('recipe.save', $recipe->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.recipeSaved = data.is_saved;
                }
            } catch (error) {
                console.error('Error saving recipe:', error);
            }
        },
        showAdModal: false,
        adSecondsLeft: 15,
        adCompleted: false,
        currentAdVideo: '',
        adVideos: [
            'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
            'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
            'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4'
        ],
        timerId: null,
        startAd() {
            this.currentAdVideo = this.adVideos[Math.floor(Math.random() * this.adVideos.length)];
            this.showAdModal = true;
            this.adSecondsLeft = 15;
            this.adCompleted = false;
            
            this.$nextTick(() => {
                const videoEl = this.$refs.adVideo;
                if (videoEl) {
                    videoEl.currentTime = 0;
                    videoEl.play().catch(e => console.log('Autoplay blocked:', e));
                }
            });

            if (this.timerId) clearInterval(this.timerId);
            this.timerId = setInterval(() => {
                if (this.adSecondsLeft > 0) {
                    this.adSecondsLeft--;
                } else {
                    clearInterval(this.timerId);
                    this.timerId = null;
                    this.adCompleted = true;
                }
            }, 1000);
        },
        async unlockRecipe() {
            try {
                const response = await fetch('{{ route('recipe.unlock-via-ad', $recipe->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.showAdModal = false;
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi mở khóa.');
                }
            } catch (error) {
                console.error('Error unlocking recipe via ad:', error);
                alert('Đã có lỗi xảy ra.');
            }
        }
    }" class="flex-1 w-full max-w-7xl mx-auto p-6 space-y-8 bg-white shadow-sm border border-gray-100 rounded-2xl m-6 relative">
        
        <!-- Nút quay lại -->
        <div>
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-gray-500 hover:text-orange-500 font-bold transition bg-gray-50 hover:bg-orange-50 px-4 py-2 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại</span>
            </button>
        </div>

        <!-- Thông báo kết quả -->
        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-200" role="alert">
                <span class="font-bold">Thành công!</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shadow-[0_4px_12px_rgba(239,68,68,0.05)]" role="alert">
                <div>
                    <span class="font-bold">Lỗi!</span> {{ session('error') }}
                </div>
                @if(str_contains(session('error'), 'nạp thêm tiền') || str_contains(session('error'), 'Số dư') || str_contains(session('error'), 'không đủ'))
                    <a href="{{ route('wallet.index') }}" class="shrink-0 bg-red-600 hover:bg-red-750 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-md shadow-red-500/20">
                        Nạp tiền ngay 💳
                    </a>
                @endif
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200" role="alert">
                <span class="font-bold">Lỗi!</span> {{ $errors->first() }}
            </div>
        @endif

        <!-- Thông báo trạng thái bài viết (Dành cho tác giả/Admin) -->
        @if($recipe->status === 'Pending')
            <div class="p-4 text-sm text-amber-800 rounded-2xl bg-amber-50 border border-amber-200 flex items-center gap-3 shadow-sm" role="alert">
                <span class="text-xl">⏳</span>
                <div>
                    <span class="font-bold block">Bài viết đang chờ duyệt!</span>
                    <span>Công thức này của bạn hiện đang ở trạng thái chờ duyệt. Chỉ có bạn và Admin mới có thể xem được trang này. Vui lòng đợi Admin phê duyệt để hiển thị công khai nhé!</span>
                </div>
            </div>
        @elseif($recipe->status === 'Rejected')
            <div class="p-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-3 shadow-sm" role="alert">
                <span class="text-xl">❌</span>
                <div>
                    <span class="font-bold block">Bài viết đã bị từ chối!</span>
                    <span>Rất tiếc, công thức này không đáp ứng đủ tiêu chuẩn và đã bị Admin từ chối phê duyệt. Vui lòng chỉnh sửa lại nội dung cho phù hợp.</span>
                </div>
            </div>
        @endif

        <div class="border border-gray-300 p-6 rounded-lg flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-[350px] aspect-square bg-gray-50 border border-gray-300 flex items-center justify-center shrink-0 rounded overflow-hidden">
                @if($recipe->image)
                    <img src="{{ $recipe->image }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800';" alt="{{ $recipe->title }}" class="w-full h-full object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800" alt="{{ $recipe->title }}" class="w-full h-full object-cover">
                @endif
            </div>

            <div class="flex-1 space-y-5">
                <h1 class="text-3xl font-black">{{ $recipe->title }}</h1>
                <div class="flex items-center space-x-2 text-orange-500 font-bold">
                    <span>#{{ $recipe->category->name ?? 'Món ăn' }}</span>
                </div>
                
                <div class="flex space-x-3 pt-2">
                    <!-- Form Thích Món Ăn -->
                    @auth
                        <button @click="toggleRecipeLike()" class="border px-4 py-1.5 rounded flex items-center justify-center transition font-bold space-x-1.5 text-sm" :class="recipeLiked ? 'border-red-500 bg-red-50 text-red-500 hover:bg-red-100' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            <span x-text="recipeLiked ? '❤️' : '🤍'"></span>
                            <span>Thích (<span x-text="recipeLikesCount"></span>)</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-1.5 rounded flex items-center justify-center transition font-bold space-x-1.5 text-sm">
                            <span>🤍</span>
                            <span>Thích ({{ $likesCount }})</span>
                        </a>
                    @endauth

                    <!-- Nút cuộn xuống Bình luận -->
                    @php
                        $commentsCount = $recipe->comments()->count();
                    @endphp
                    <button onclick="document.getElementById('comments-section').scrollIntoView({ behavior: 'smooth' })" class="border border-gray-300 px-4 py-1.5 rounded flex items-center justify-center hover:bg-gray-50 text-gray-600 transition font-bold space-x-1.5 text-sm">
                        <span>💬</span>
                        <span>Bình luận ({{ $commentsCount }})</span>
                    </button>
                </div>

                <div class="flex items-center space-x-3 border border-gray-300 rounded-full w-max px-4 py-2 bg-gray-50">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    <span class="font-bold text-gray-700">Được xem {{ number_format($recipe->views_count) }} lần</span>
                </div>

                <div class="flex items-center space-x-3 py-2">
                    @if($recipe->user->avatar)
                        <img src="{{ str_contains($recipe->user->avatar, 'http') ? $recipe->user->avatar : Storage::url($recipe->user->avatar) }}" 
                             onerror="this.outerHTML='<div class=\'w-12 h-12 rounded-full border border-gray-200 bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-lg\'>{{ mb_substr($recipe->user->name, 0, 1) }}</div>'"
                             class="w-12 h-12 rounded-full border border-gray-200 object-cover">
                    @else
                        <div class="w-12 h-12 rounded-full border border-gray-200 bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-lg">
                            {{ mb_substr($recipe->user->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="space-y-1">
                        <div class="font-bold text-gray-800">{{ $recipe->user->name }}</div>
                        <div class="flex items-center space-x-1 text-sm text-gray-500">
                            <span>Đăng ngày {{ $recipe->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 text-gray-700 leading-relaxed">
                    <p>{{ $recipe->description }}</p>
                    @if($recipe->tips)
                        <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 text-sm rounded">
                            <strong class="block mb-1">Mẹo nhỏ:</strong>
                            {{ $recipe->tips }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-6 pt-4 text-sm font-bold text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $recipe->time_to_cook }} phút</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Độ khó: {{ ucfirst($recipe->difficulty) }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-100" x-data="{ copied: false }">
                    <!-- Nút Lưu Món -->
                    @auth
                        <button @click="toggleRecipeSave()" class="border px-4 py-2 rounded text-sm font-bold flex items-center space-x-2 transition" :class="recipeSaved ? 'border-orange-500 bg-orange-50 text-orange-600 hover:bg-orange-100' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            <span>🔖</span>
                            <span x-text="recipeSaved ? 'Đã Lưu Món' : 'Lưu Món'"></span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded text-sm font-bold flex items-center space-x-2 transition">
                            <span>🔖</span>
                            <span>Lưu Món</span>
                        </a>
                    @endauth

                    <!-- Nút Chia sẻ -->
                    <button @click="
                        navigator.clipboard.writeText(window.location.href);
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    " class="border border-gray-300 px-4 py-2 rounded text-sm font-bold flex items-center space-x-2 hover:bg-gray-50 text-gray-600 transition">
                        <span>📤</span>
                        <span x-text="copied ? 'Đã sao chép!' : 'Chia sẻ'">Chia sẻ</span>
                    </button>

                    <!-- Nút In -->
                    <button onclick="window.print()" class="border border-gray-300 px-4 py-2 rounded text-sm font-bold flex items-center space-x-2 hover:bg-gray-50 text-gray-600 transition">
                        <span>🖨️</span> <span>In công thức</span>
                    </button>
                </div>
            </div>

            <div class="w-full lg:w-[350px] shrink-0 border border-gray-300 p-5 flex flex-col rounded bg-white relative max-h-[600px]">
                <h3 class="font-bold text-lg mb-4 text-orange-500 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Nguyên Liệu
                </h3>
                
                <div class="flex-1 overflow-y-auto pr-4 space-y-0 custom-scrollbar">
                    @forelse($recipe->ingredients as $ingredient)
                        <div class="flex items-center justify-between border-b border-dashed border-gray-200 py-3 last:border-0">
                            <span class="font-medium text-gray-800">{{ $ingredient->name }}</span>
                            <span class="text-sm font-bold text-gray-500 bg-gray-50 px-2 py-1 rounded">{{ $ingredient->pivot->quantity }} {{ $ingredient->pivot->unit }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm italic">Chưa có nguyên liệu nào.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="border-t border-gray-200 pt-8 relative">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-gray-900">Hướng dẫn cách làm</h2>
                    <p class="text-gray-400 text-xs sm:text-sm font-semibold mt-0.5">Bấm vào từng bước để đánh dấu hoàn thành khi nấu ăn.</p>
                </div>
                <div x-data="{ showTimer: false, seconds: 0, timerId: null, inputMinutes: 10,
                    startTimer() {
                        if (this.timerId) clearInterval(this.timerId);
                        this.seconds = this.inputMinutes * 60;
                        this.showTimer = true;
                        this.timerId = setInterval(() => {
                            if (this.seconds > 0) {
                                this.seconds--;
                            } else {
                                clearInterval(this.timerId);
                                this.timerId = null;
                                alert('🔔 Hết giờ nấu ăn!');
                            }
                        }, 1000);
                    },
                    stopTimer() {
                        if (this.timerId) {
                            clearInterval(this.timerId);
                            this.timerId = null;
                        }
                    },
                    formatTime() {
                        const m = Math.floor(this.seconds / 60);
                        const s = this.seconds % 60;
                        return `${m}:${s.toString().padStart(2, '0')}`;
                    }
                }" class="bg-gray-50 border border-gray-200 p-3 rounded-2xl flex items-center gap-3 w-full sm:w-auto shadow-sm">
                    <span class="text-sm font-bold text-gray-700">⏱️ Bộ hẹn giờ:</span>
                    <div class="flex items-center gap-1.5" x-show="!showTimer">
                        <input type="number" x-model="inputMinutes" class="w-12 px-2 py-1 text-xs border rounded-lg text-center font-bold" min="1" max="180">
                        <span class="text-xs text-gray-500 font-bold">phút</span>
                        <button @click="startTimer()" class="bg-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-orange-600 transition">Bắt đầu</button>
                    </div>
                    <div class="flex items-center gap-2" x-show="showTimer" style="display: none;">
                        <span class="font-black text-sm text-orange-600 font-mono" x-text="formatTime()"></span>
                        <button @click="stopTimer(); showTimer = false" class="bg-gray-200 hover:bg-gray-300 text-gray-750 text-xs font-bold px-2 py-1 rounded-lg transition">Hủy</button>
                    </div>
                </div>
            </div>
            
            <div x-data="{ completedSteps: [] }" class="relative space-y-8 pl-2">
                <!-- Timeline vertical line -->
                <div class="absolute left-[24px] top-6 bottom-6 w-0.5 bg-gray-200/80 z-0"></div>

                @forelse($recipe->steps as $step)
                    @php
                        // Render **bold** text
                        $content = e($step->content);
                        $content = preg_replace('/\*\*(.+?)\*\*/', '<strong class="text-gray-900 font-extrabold">$1</strong>', $content);
                    @endphp
                    <div class="relative flex gap-6 group z-10 {{ (!$isAuthorized && $step->order > $lockAfterStep) ? 'blur-md pointer-events-none select-none' : '' }}">
                        <!-- Step Indicator -->
                        <div class="flex flex-col items-center">
                            <button 
                                @click="completedSteps.includes({{ $step->order }}) ? completedSteps = completedSteps.filter(s => s !== {{ $step->order }}) : completedSteps.push({{ $step->order }})"
                                class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-black transition-all duration-300 border-2 shrink-0 shadow-sm focus:outline-none"
                                :class="completedSteps.includes({{ $step->order }}) ? 'bg-green-500 border-green-500 text-white scale-110 shadow-lg shadow-green-500/20' : 'bg-white border-orange-500 text-orange-500 hover:bg-orange-50 group-hover:scale-105'"
                            >
                                <span x-show="!completedSteps.includes({{ $step->order }})">{{ $step->order }}</span>
                                <span x-show="completedSteps.includes({{ $step->order }})" style="display: none;">✓</span>
                            </button>
                        </div>
                        
                        <!-- Step Content Card -->
                        <div 
                            @click="completedSteps.includes({{ $step->order }}) ? completedSteps = completedSteps.filter(s => s !== {{ $step->order }}) : completedSteps.push({{ $step->order }})"
                            class="flex-1 p-6 bg-white rounded-3xl border transition-all duration-300 cursor-pointer select-none"
                            :class="completedSteps.includes({{ $step->order }}) ? 'border-green-200 bg-green-50/10 opacity-60' : 'border-gray-150 hover:border-orange-200 hover:bg-orange-50/5 shadow-sm hover:shadow-md'"
                        >
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-wider transition" :class="completedSteps.includes({{ $step->order }}) ? 'text-green-600' : 'text-orange-500'">
                                        Bước {{ $step->order }}
                                    </span>
                                    <span x-show="completedSteps.includes({{ $step->order }})" class="bg-green-100 text-green-700 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wide" style="display: none;">
                                        Đã Xong ✓
                                    </span>
                                </div>
                                <p class="text-gray-700 leading-relaxed text-sm sm:text-base transition" :class="completedSteps.includes({{ $step->order }}) ? 'line-through text-gray-400' : ''">
                                    {!! $content !!}
                                </p>
                                @if($step->image)
                                    <div class="rounded-2xl overflow-hidden border border-gray-100 mt-4 shadow-sm max-h-[300px] flex items-center justify-center">
                                        <img src="{{ $step->image }}" class="w-full h-full object-cover aspect-video hover:scale-[1.02] transition-transform duration-500" alt="Minh họa Bước {{ $step->order }}">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Nhãn khóa / Nút mua và xem quảng cáo chen giữa --}}
                    @if(!$isAuthorized && $step->order == $lockAfterStep)
                        <div class="relative z-30 max-w-xl mx-auto my-8 p-8 bg-white/95 border border-amber-200 shadow-[0_20px_50px_rgba(245,158,11,0.12)] rounded-3xl text-center space-y-6">
                            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-3xl shadow-inner">
                                👑
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-2xl font-black text-gray-950">Bí quyết Master Chef</h3>
                                <p class="text-gray-600 text-sm leading-relaxed font-medium">
                                    @if($isAuthorMasterChef)
                                        Bạn có thể thực hiện món ăn cơ bản qua {{ $lockAfterStep }} bước trên. Để xem trọn vẹn bí quyết chế biến nâng tầm Master Chef từ tác giả <strong class="text-gray-900 font-bold">{{ $recipe->user->name }}</strong>, vui lòng mua trực tiếp công thức này (Không hỗ trợ xem quảng cáo).
                                    @else
                                        Bạn có thể thực hiện món ăn cơ bản qua {{ $lockAfterStep }} bước trên. Để xem trọn vẹn bí quyết chế biến nâng tầm Master Chef từ tác giả <strong class="text-gray-900 font-bold">{{ $recipe->user->name }}</strong>, hãy mua công thức hoặc xem hết quảng cáo 15s.
                                    @endif
                                </p>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
                                @guest
                                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition shadow-lg shadow-orange-500/20 text-sm flex items-center justify-center">
                                        Đăng nhập để mua ({{ number_format($recipe->price, 0, ',', '.') }} đ)
                                    </a>
                                    @if(!$isAuthorMasterChef)
                                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 bg-gray-805 hover:bg-gray-900 text-white font-bold rounded-xl transition text-sm flex items-center justify-center gap-1.5">
                                            <span>Đăng nhập để xem Ad 📺</span>
                                        </a>
                                    @endif
                                @else
                                    <form action="{{ route('recipe.purchase', $recipe->id) }}" method="POST" class="w-full sm:w-auto m-0">
                                        @csrf
                                        <button type="submit" class="w-full px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition shadow-lg shadow-orange-500/20 text-sm">
                                            Mua công thức ({{ number_format($recipe->price, 0, ',', '.') }} đ)
                                        </button>
                                    </form>
                                    
                                    @if(!$isAuthorMasterChef)
                                        @if($adUnlocksCount >= 3)
                                            <button disabled class="w-full sm:w-auto px-6 py-3 bg-gray-250 text-gray-400 font-bold rounded-xl text-sm cursor-not-allowed flex items-center justify-center gap-1.5" title="Đã hết lượt hôm nay">
                                                <span>Xem quảng cáo (15s) 📺 (Hết lượt)</span>
                                            </button>
                                        @else
                                            <button @click="startAd()" class="w-full sm:w-auto px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition shadow-lg shadow-amber-500/20 text-sm flex items-center justify-center gap-1.5">
                                                <span>Xem quảng cáo (15s) 📺</span>
                                            </button>
                                        @endif
                                    @endif
                                @endguest
                            </div>
                            
                            <!-- Cảnh báo/Ghi chú nếu có -->
                            @auth
                                @if($isAuthorMasterChef)
                                    <p class="text-xs text-amber-600 font-bold bg-amber-50 py-2 px-4 rounded-xl border border-amber-200/50">
                                        Công thức độc quyền từ Master Chef không hỗ trợ mở khóa qua quảng cáo.
                                    </p>
                                @elseif($adUnlocksCount >= 3)
                                    <p class="text-xs text-red-650 font-bold bg-red-50 py-2 px-4 rounded-xl border border-red-200/50">
                                        Bạn đã sử dụng hết 3 lượt mở khóa bằng quảng cáo trong 24 giờ qua. Vui lòng nạp thêm tiền để mua hoặc quay lại sau 24h.
                                    </p>
                                @endif
                            @endauth
                        </div>
                    @endif

                @empty
                    <div class="text-gray-500 italic text-center py-10">Chưa có hướng dẫn cho món này.</div>
                @endforelse
            </div>
        </div>

        <!-- Modal Xem Quảng Cáo -->
        <div x-show="showAdModal" 
             x-transition
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
            
            <div class="bg-gray-900 border border-gray-800 rounded-3xl overflow-hidden w-full max-w-2xl shadow-2xl flex flex-col relative" @click.outside="if(adCompleted) { showAdModal = false; }">
                
                <!-- Header quảng cáo -->
                <div class="px-6 py-4 bg-gray-950 border-b border-gray-850 flex items-center justify-between text-white">
                    <span class="text-xs font-black uppercase tracking-wider text-amber-500">📺 Quảng cáo tài trợ</span>
                    <span class="text-xs font-bold text-gray-400 font-mono" x-show="!adCompleted">
                        Còn lại <span class="text-amber-500 font-black text-sm" x-text="adSecondsLeft"></span> giây
                    </span>
                    <span class="text-xs font-black text-green-500" x-show="adCompleted">
                        ✓ Đã hoàn thành quảng cáo
                    </span>
                </div>

                <!-- Trình phát video -->
                <div class="relative bg-black aspect-video flex items-center justify-center">
                    <video x-ref="adVideo" 
                           :src="currentAdVideo" 
                           class="w-full h-full object-contain"
                           preload="auto"
                           playsinline
                           @contextmenu.prevent
                           @ended="adCompleted = true; if(timerId) { clearInterval(timerId); timerId = null; adSecondsLeft = 0; }">
                    </video>
                </div>

                <!-- Footer chứa nút hành động -->
                <div class="px-6 py-4 bg-gray-950 border-t border-gray-850 flex justify-between items-center">
                    <p class="text-[11px] text-gray-500 font-medium">Xem hết quảng cáo để hỗ trợ tác giả và mở khóa công thức.</p>
                    <div>
                        <!-- Nút hoàn thành mở khóa -->
                        <button x-show="adCompleted" 
                                @click="unlockRecipe()" 
                                class="bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-lg shadow-green-500/20 flex items-center gap-1.5">
                            <span>Mở khóa công thức 🔓</span>
                        </button>
                        
                        <!-- Nút thoát quảng cáo giữa chừng -->
                        <button x-show="!adCompleted" 
                                @click="if(confirm('Bạn có chắc muốn thoát? Bạn sẽ không được mở khóa công thức.')) { showAdModal = false; if(timerId) clearInterval(timerId); $refs.adVideo.pause(); }" 
                                class="bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white px-5 py-2.5 rounded-xl text-xs font-bold transition">
                            Thoát
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div id="comments-section" class="border-t border-gray-200 pt-8 space-y-6">
            <h2 class="text-xl font-bold text-gray-900">Bình luận</h2>
            
            @auth
                <form action="{{ route('comment.store', $recipe->id) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    @if(auth()->user()->avatar)
                        <img src="{{ str_contains(auth()->user()->avatar, 'http') ? auth()->user()->avatar : Storage::url(auth()->user()->avatar) }}" 
                             onerror="this.outerHTML='<div class=\'w-12 h-12 rounded-full border border-gray-300 bg-orange-100 text-orange-600 font-bold flex items-center justify-center shrink-0\'>{{ mb_substr(auth()->user()->name, 0, 1) }}</div>'"
                             class="w-12 h-12 rounded-full border border-gray-300 object-cover shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-full border border-gray-300 bg-orange-100 text-orange-600 font-bold flex items-center justify-center shrink-0">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <input type="text" name="content" class="flex-1 border border-gray-300 rounded-xl px-4 py-3 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition" placeholder="Bạn nghĩ gì về món này..." required>
                    <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl font-bold hover:bg-gray-800 transition shrink-0">Gửi</button>
                </form>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 text-center space-y-3">
                    <p class="text-gray-600 font-medium">Bạn cần đăng nhập để viết bình luận cho món ăn này.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-md shadow-orange-500/20 text-sm">
                        Đăng nhập ngay
                    </a>
                </div>
            @endauth

            <div class="space-y-4 pt-6">
                @forelse($recipe->comments as $comment)
                    @php
                        $userReaction = $comment->reactions->first();
                        $hasLiked = $userReaction && $userReaction->type === 'like';
                        $hasDisliked = $userReaction && $userReaction->type === 'dislike';
                    @endphp
                    <div x-data="{
                        likes: {{ $comment->likes_count ?? 0 }},
                        dislikes: {{ $comment->dislikes_count ?? 0 }},
                        userReaction: '{{ $hasLiked ? 'like' : ($hasDisliked ? 'dislike' : '') }}',
                        isEditing: false,
                        commentContent: '{{ str_replace(["'", "\r", "\n"], ["\\'", " ", " "], $comment->content) }}',
                        editContent: '{{ str_replace(["'", "\r", "\n"], ["\\'", " ", " "], $comment->content) }}',
                        isDeleted: false,
                        async react(type) {
                            try {
                                const response = await fetch('{{ route('comment.react', $comment->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ type: type })
                                });
                                const data = await response.json();
                                if (data.success) {
                                    this.likes = data.likes_count;
                                    this.dislikes = data.dislikes_count;
                                    this.userReaction = data.user_reaction || '';
                                }
                            } catch (error) {
                                console.error('Error during reaction:', error);
                            }
                        },
                        async updateComment() {
                            if (!this.editContent.trim()) return;
                            try {
                                const response = await fetch('{{ route('comment.update', $comment->id) }}', {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ content: this.editContent })
                                });
                                const data = await response.json();
                                if (data.success) {
                                    this.commentContent = data.content;
                                    this.isEditing = false;
                                }
                            } catch (error) {
                                console.error('Error updating comment:', error);
                            }
                        },
                        async deleteComment() {
                            if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) return;
                            try {
                                const response = await fetch('{{ route('comment.destroy', $comment->id) }}', {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    this.isDeleted = true;
                                }
                            } catch (error) {
                                console.error('Error deleting comment:', error);
                            }
                        }
                    }" 
                    x-show="!isDeleted"
                    x-transition
                    class="flex gap-4 p-4 bg-gray-50/50 rounded-2xl border border-gray-100 hover:border-orange-100 transition-all">
                        @if($comment->user->avatar)
                            <img src="{{ str_contains($comment->user->avatar, 'http') ? $comment->user->avatar : Storage::url($comment->user->avatar) }}" 
                                 onerror="this.outerHTML='<div class=\'w-10 h-10 rounded-full border border-gray-200 bg-orange-100 text-orange-600 font-bold flex items-center justify-center shrink-0 text-sm\'>{{ mb_substr($comment->user->name, 0, 1) }}</div>'"
                                 class="w-10 h-10 rounded-full border border-gray-200 object-cover shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full border border-gray-200 bg-orange-100 text-orange-600 font-bold flex items-center justify-center shrink-0 text-sm">
                                {{ mb_substr($comment->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <!-- Hiển thị nội dung bình luận -->
                            <div x-show="!isEditing">
                                <p class="text-gray-600 text-sm leading-relaxed" x-text="commentContent"></p>
                            </div>
                            
                            <!-- Form chỉnh sửa bình luận -->
                            <div x-show="isEditing" class="space-y-2 mt-1" style="display: none;">
                                <input type="text" x-model="editContent" @keydown.enter="updateComment()" class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition">
                                <div class="flex space-x-2 text-xs font-bold">
                                    <button @click="updateComment()" class="bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 transition">Lưu</button>
                                    <button @click="isEditing = false; editContent = commentContent" class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition">Hủy</button>
                                </div>
                            </div>
                            
                            <!-- Tương tác bình luận: Thích, Không thích, Sửa/Xóa hoặc Tố cáo -->
                            <div class="flex items-center space-x-6 pt-2 text-xs font-bold text-gray-500">
                                @auth
                                    <button @click="react('like')" class="flex items-center space-x-1 transition" :class="userReaction === 'like' ? 'text-orange-500' : 'hover:text-orange-500 text-gray-500'">
                                        <span>👍 Thích (<span x-text="likes"></span>)</span>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center space-x-1 hover:text-orange-500 transition">
                                        <span>👍 Thích ({{ $comment->likes_count ?? 0 }})</span>
                                    </a>
                                @endauth
 
                                @auth
                                    <button @click="react('dislike')" class="flex items-center space-x-1 transition" :class="userReaction === 'dislike' ? 'text-red-500' : 'hover:text-red-500 text-gray-500'">
                                        <span>👎 Không thích (<span x-text="dislikes"></span>)</span>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center space-x-1 hover:text-red-500 transition">
                                        <span>👎 Không thích ({{ $comment->dislikes_count ?? 0 }})</span>
                                    </a>
                                @endauth
 
                                @auth
                                    @if(auth()->id() === $comment->user_id)
                                        <button @click="isEditing = true" class="flex items-center space-x-1 hover:text-orange-500 transition">
                                            <span>✏️ Sửa</span>
                                        </button>
                                        <button @click="deleteComment()" class="flex items-center space-x-1 hover:text-red-500 transition">
                                            <span>🗑️ Xóa</span>
                                        </button>
                                    @else
                                        <button @click="$dispatch('open-report-modal', { commentId: {{ $comment->id }} })" class="flex items-center space-x-1 hover:text-red-500 transition">
                                            <span>🚩 Tố cáo</span>
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center space-x-1 hover:text-red-500 transition">
                                        <span>🚩 Tố cáo</span>
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 text-sm py-10">Chưa có bình luận nào. Hãy là người đầu tiên!</div>
                @endforelse
            </div>
        </div>

        <!-- Modal Tố cáo bằng Alpine.js -->
        <div x-data="{ open: false, commentId: null, reason: '' }"
             @open-report-modal.window="open = true; commentId = $event.detail.commentId; reason = ''"
             x-show="open"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
             x-transition
             style="display: none;">
            
            <div class="bg-white rounded-2xl p-6 w-full max-w-md border border-gray-100 shadow-2xl space-y-4" @click.outside="open = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span>🚩</span> Tố cáo bình luận
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl leading-none">&times;</button>
                </div>
                
                <form :action="`/comment/${commentId}/report`" method="POST" class="space-y-4 m-0">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn lý do tố cáo:</label>
                        <select name="reason" x-model="reason" class="w-full border border-gray-300 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition" required>
                            <option value="" disabled selected>-- Chọn lý do --</option>
                            <option value="Spam / Quảng cáo">Spam / Quảng cáo</option>
                            <option value="Ngôn từ kích động thù địch / Xúc phạm">Ngôn từ kích động thù địch / Xúc phạm</option>
                            <option value="Nội dung không liên quan đến món ăn">Nội dung không liên quan đến món ăn</option>
                            <option value="Khác">Lý do khác...</option>
                        </select>
                    </div>
                    
                    <div x-show="reason === 'Khác'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chi tiết lý do khác:</label>
                        <textarea name="custom_reason" class="w-full border border-gray-300 rounded-xl px-4 py-3 outline-none focus:border-orange-500 transition" placeholder="Nhập lý do chi tiết..." rows="3"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="open = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition">Hủy</button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition">Gửi tố cáo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection