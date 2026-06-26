@extends('layouts.app')

@php
    // Popular ingredient images mapping to make the UI stunning
    $ingredientImages = [
        'Xương ống bò' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400',
        'Thịt bò thăn/gầu' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400',
        'Thịt nạc vai heo' => 'https://images.unsplash.com/photo-1602489114888-46aa350f6853?w=400',
        'Tôm sú tươi' => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=400',
        'Thịt ba chỉ' => 'https://images.unsplash.com/photo-1602489114888-46aa350f6853?w=400',
        'Trứng vịt' => 'https://images.unsplash.com/photo-1582722411494-598322652b44?w=400',
        'Hành tây khô' => 'https://images.unsplash.com/photo-1508747703725-719ae25b3cfb?w=400',
        'Bột mì' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400',
        'Tỏi phi thơm' => 'https://images.unsplash.com/photo-1608797178974-15b35a61d121?w=400',
        'Hẹ và giá đỗ' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=400',
        'Bún tươi sợi nhỏ' => 'https://images.unsplash.com/photo-1618040996337-56904b7850b9?w=400',
        'Gan heo' => 'https://images.unsplash.com/photo-1602489114888-46aa350f6853?w=400'
    ];
@endphp

@section('title', 'Tìm kiếm Công thức - Foodball')
@section('header_title', 'Tìm Kiếm')

@section('header_middle')
    <form action="{{ route('search') }}" method="GET" class="relative flex w-full max-w-xl mx-auto items-center group">
        <input type="hidden" name="type" value="{{ $type }}">
        
        @if($type === 'category')
            <input type="hidden" name="category" value="{{ request('category') }}">
        @elseif($type === 'ingredient')
            <input type="hidden" name="ingredient" value="{{ request('ingredient') }}">
        @elseif($type === 'time')
            <input type="hidden" name="time" value="{{ request('time') }}">
        @endif

        <div class="absolute left-4 text-gray-400 group-focus-within:text-orange-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        <input type="text" name="query" value="{{ $query }}" 
            placeholder="@if($type === 'category')Tìm món ngon trong danh mục...@elseif($type === 'ingredient')Nhập nguyên liệu cần tìm (vd: tôm, tỏi)...@elseif($type === 'author')Nhập tên tác giả cần tìm...@elseif($type === 'time')Tìm tên món ăn theo thời gian nấu...@elseTìm tên món ăn (vd: phở bò, cơm tấm)...@endif" 
            class="w-full py-2.5 pl-12 pr-24 rounded-full border border-gray-200 shadow-sm outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 text-gray-700 transition-all bg-gray-50 hover:bg-white focus:bg-white">
        
        @if($query || request('category') || request('ingredient') || request('time'))
            <a href="{{ route('search', ['type' => $type]) }}" class="absolute right-4 text-gray-400 hover:text-red-500 font-bold text-xs transition-colors">Xóa bộ lọc</a>
        @endif
    </form>
@endsection

@section('content')
    <div class="flex-1 max-w-6xl mx-auto w-full px-6 py-10 space-y-8 relative">
        <!-- Nút quay lại -->
        <div>
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-gray-500 hover:text-orange-500 font-bold transition bg-white border border-gray-200 hover:bg-orange-50 px-4 py-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại</span>
            </button>
        </div>

        <!-- Hệ thống Tab điều hướng tìm kiếm -->
        <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap gap-1.5">
            <a href="{{ route('search', ['type' => 'category', 'query' => $query]) }}" 
                class="flex items-center space-x-2 px-5 py-3 rounded-xl font-bold transition-all duration-300 {{ $type === 'category' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}">
                <span>📂</span>
                <span>Theo Danh mục</span>
            </a>
            <a href="{{ route('search', ['type' => 'ingredient', 'query' => $query]) }}" 
                class="flex items-center space-x-2 px-5 py-3 rounded-xl font-bold transition-all duration-300 {{ $type === 'ingredient' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}">
                <span>🥬</span>
                <span>Theo Nguyên liệu</span>
            </a>
            <a href="{{ route('search', ['type' => 'dish', 'query' => $query]) }}" 
                class="flex items-center space-x-2 px-5 py-3 rounded-xl font-bold transition-all duration-300 {{ $type === 'dish' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}">
                <span>🍲</span>
                <span>Theo Món ăn</span>
            </a>
            <a href="{{ route('search', ['type' => 'author', 'query' => $query]) }}" 
                class="flex items-center space-x-2 px-5 py-3 rounded-xl font-bold transition-all duration-300 {{ $type === 'author' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}">
                <span>👨‍🍳</span>
                <span>Theo Tác giả</span>
            </a>
            <a href="{{ route('search', ['type' => 'time', 'query' => $query]) }}" 
                class="flex items-center space-x-2 px-5 py-3 rounded-xl font-bold transition-all duration-300 {{ $type === 'time' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 hover:bg-orange-50 hover:text-orange-500' }}">
                <span>⏱️</span>
                <span>Theo Thời gian nấu</span>
            </a>
        </div>

        <!-- Vùng chọn Trực quan (Illustration Selector) -->
        
        <!-- 1. Theo Danh mục -->
        @if($type === 'category')
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span>📂</span> Danh mục món ăn
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    @foreach($categories as $cat)
                        @php
                            $isSelected = request('category') === $cat->slug;
                        @endphp
                        <a href="{{ route('search', ['type' => 'category', 'category' => $isSelected ? '' : $cat->slug, 'query' => $query]) }}" 
                           class="relative rounded-2xl overflow-hidden aspect-video border group hover:shadow-md transition-all {{ $isSelected ? 'border-orange-500 ring-4 ring-orange-500/20 scale-[0.98]' : 'border-gray-100' }}">
                            <img src="{{ $cat->image }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t {{ $isSelected ? 'from-orange-600/90 via-orange-600/50' : 'from-black/75 via-black/25' }} to-transparent"></div>
                            <div class="absolute bottom-3 left-3 right-3 z-10 text-white flex flex-col">
                                <span class="font-black text-xs sm:text-sm uppercase tracking-wide leading-tight">{{ $cat->name }}</span>
                                @if($isSelected)
                                    <span class="text-[10px] text-orange-200 font-bold flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Đang chọn
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 2. Theo Nguyên liệu -->
        @if($type === 'ingredient')
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span>🥬</span> Lọc nhanh nguyên liệu phổ biến
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                    @foreach($popularIngredients as $ing)
                        @php
                            $isSelected = request('ingredient') === $ing->name;
                            // Match custom illustration image or fallback
                            $imgUrl = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400';
                            foreach($ingredientImages as $key => $val) {
                                if(mb_stripos($ing->name, $key) !== false || mb_stripos($key, $ing->name) !== false) {
                                    $imgUrl = $val;
                                    break;
                                }
                            }
                        @endphp
                        <a href="{{ route('search', ['type' => 'ingredient', 'ingredient' => $isSelected ? '' : $ing->name, 'query' => $query]) }}" 
                           class="flex flex-col items-center p-4 bg-gray-50/50 rounded-2xl border text-center group hover:bg-white hover:shadow-md transition-all relative overflow-hidden {{ $isSelected ? 'border-orange-500 bg-white ring-4 ring-orange-500/10 scale-[0.98]' : 'border-gray-100' }}">
                            <div class="w-14 h-14 rounded-full overflow-hidden mb-3 bg-white border border-gray-100 group-hover:scale-105 transition-transform duration-300 shrink-0 shadow-sm">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover" alt="{{ $ing->name }}">
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-gray-800 group-hover:text-orange-500 transition line-clamp-1 w-full">{{ $ing->name }}</span>
                            <span class="text-[10px] text-gray-400 mt-0.5 font-medium">{{ $ing->recipes_count }} công thức</span>
                            
                            @if($isSelected)
                                <div class="absolute top-2 right-2 w-4 h-4 bg-orange-500 rounded-full flex items-center justify-center text-white text-[9px] font-bold shadow-sm">
                                    ✓
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Theo Món ăn gợi ý -->
        @if($type === 'dish')
            <div class="bg-orange-50 border border-orange-100 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="space-y-1 text-center md:text-left">
                    <h2 class="text-lg font-black text-orange-800 flex items-center justify-center md:justify-start gap-2">
                        <span>🍲</span> Tìm kiếm theo món ăn
                    </h2>
                    <p class="text-sm text-orange-600 font-medium">Tìm kiếm món ăn yêu thích bằng thanh tìm kiếm ở góc trên màn hình.</p>
                </div>
                <div class="flex items-center justify-center gap-2 flex-wrap text-xs sm:text-sm">
                    <span class="text-gray-400 font-bold">Gợi ý từ khóa:</span>
                    @foreach(['Phở Bò', 'Bánh Mì', 'Gỏi Cuốn', 'Cơm Tấm', 'Hủ Tiếu Khô'] as $suggest)
                        <a href="{{ route('search', ['type' => 'dish', 'query' => $suggest]) }}" 
                           class="bg-white hover:bg-orange-500 hover:text-white hover:border-orange-500 text-gray-600 px-3.5 py-1.5 rounded-full transition font-semibold shadow-sm border border-gray-200">
                            {{ $suggest }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 4. Theo Thời gian nấu -->
        @if($type === 'time')
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span>⏱️</span> Thời gian chế biến món ăn
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $timePresets = [
                            [
                                'key' => 'quick',
                                'title' => '⚡ Nhanh siêu tốc',
                                'desc' => 'Dưới 15 phút',
                                'desc_full' => 'Các món chế biến nhanh gọn, tiết kiệm thời gian tối đa.',
                                'image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=400',
                            ],
                            [
                                'key' => 'medium',
                                'title' => '⏱️ Tiện lợi',
                                'desc' => '15 - 30 phút',
                                'desc_full' => 'Bữa cơm hàng ngày nhẹ nhàng với món xào, món canh.',
                                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400',
                            ],
                            [
                                'key' => 'long',
                                'title' => '🍲 Đậm đà',
                                'desc' => '30 - 60 phút',
                                'desc_full' => 'Các món kho mặn, cá chiên sốt thơm ngon cho gia đình.',
                                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400',
                            ],
                            [
                                'key' => 'slow',
                                'title' => '⏳ Ninh & Hầm',
                                'desc' => 'Trên 60 phút',
                                'desc_full' => 'Hầm xương lấy nước dùng ngọt đậm hoặc món tiềm bổ dưỡng.',
                                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400',
                            ]
                        ];
                    @endphp
                    @foreach($timePresets as $preset)
                        @php
                            $isSelected = request('time') === $preset['key'];
                        @endphp
                        <a href="{{ route('search', ['type' => 'time', 'time' => $isSelected ? '' : $preset['key'], 'query' => $query]) }}" 
                           class="flex flex-col bg-gray-50/50 rounded-2xl overflow-hidden border group hover:bg-white hover:shadow-md transition-all relative {{ $isSelected ? 'border-orange-500 bg-white ring-4 ring-orange-500/10 scale-[0.98]' : 'border-gray-100' }}">
                            <div class="h-28 relative overflow-hidden shrink-0">
                                <img src="{{ $preset['image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t {{ $isSelected ? 'from-orange-600/90' : 'from-black/60' }} to-transparent"></div>
                                <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full text-[11px] font-bold shadow-sm">
                                    <span class="text-gray-800">{{ $preset['desc'] }}</span>
                                </div>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-sm text-gray-900 group-hover:text-orange-500 transition leading-tight">{{ $preset['title'] }}</h3>
                                <p class="text-[11px] text-gray-400 mt-1.5 leading-relaxed flex-1">{{ $preset['desc_full'] }}</p>
                                @if($isSelected)
                                    <span class="text-[10px] text-orange-500 font-bold flex items-center gap-1 mt-3 pt-2 border-t border-gray-100 shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Đang chọn mốc này
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 5. Theo Tác giả -->
        @if($type === 'author')
            <div class="bg-orange-50 border border-orange-100 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="space-y-1 text-center md:text-left">
                    <h2 class="text-lg font-black text-orange-800 flex items-center justify-center md:justify-start gap-2">
                        <span>👨‍🍳</span> Tìm kiếm theo tác giả
                    </h2>
                    <p class="text-sm text-orange-600 font-medium">Nhập tên hoặc tên đăng nhập của tác giả/đầu bếp vào ô tìm kiếm phía trên.</p>
                </div>
                <div class="flex items-center justify-center gap-2 flex-wrap text-xs sm:text-sm">
                    <span class="text-gray-400 font-bold">Gợi ý từ khóa:</span>
                    @foreach(['Kiệt', 'Test User'] as $suggest)
                        <a href="{{ route('search', ['type' => 'author', 'query' => $suggest]) }}" 
                           class="bg-white hover:bg-orange-500 hover:text-white hover:border-orange-500 text-gray-600 px-3.5 py-1.5 rounded-full transition font-semibold shadow-sm border border-gray-200">
                            {{ $suggest }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tiêu đề Kết quả Tìm kiếm -->
        <div>
            @php
                $filterLabel = '';
                if ($type === 'category' && request('category')) {
                    $selectedCat = $categories->firstWhere('slug', request('category'));
                    $filterLabel = 'Danh mục: ' . ($selectedCat ? $selectedCat->name : request('category'));
                } elseif ($type === 'ingredient' && request('ingredient')) {
                    $filterLabel = 'Nguyên liệu: ' . request('ingredient');
                } elseif ($type === 'time' && request('time')) {
                    $presets = ['quick' => 'Dưới 15 phút', 'medium' => '15 - 30 phút', 'long' => '30 - 60 phút', 'slow' => 'Trên 60 phút'];
                    $filterLabel = 'Thời gian: ' . ($presets[request('time')] ?? request('time'));
                }
            @endphp
            
            <h1 class="text-2xl font-black text-gray-900 mb-1.5 tracking-tight">
                @if($filterLabel)
                    Đang xem bộ lọc: <span class="text-orange-500">"{{ $filterLabel }}"</span>
                    @if($query) + Từ khóa: <span class="text-orange-500">"{{ $query }}"</span> @endif
                @else
                    Kết quả tìm kiếm cho: <span class="text-orange-500">"{{ $query ?: 'Tất cả công thức' }}"</span>
                @endif
            </h1>
            <p class="text-gray-400 text-sm font-semibold">Tìm thấy {{ $recipes->count() }} công thức nấu ăn.</p>
        </div>

        <!-- Danh sách kết quả món ăn -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($recipes as $recipe)
            <a href="{{ route('recipe.detail', $recipe->slug) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition duration-300 group cursor-pointer flex flex-col h-full">
                <div class="relative h-44 overflow-hidden shrink-0">
                    <img src="{{ $recipe->image }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $recipe->title }}">
                    <div class="absolute top-3 left-3 bg-orange-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $recipe->category->name ?? 'Nổi bật' }}
                    </div>
                </div>
                <div class="p-5 flex flex-col flex-1 space-y-2">
                    <h3 class="font-bold text-base text-gray-900 group-hover:text-orange-500 transition line-clamp-2 leading-snug">{{ $recipe->title }}</h3>
                    <p class="text-gray-400 text-xs line-clamp-2 flex-1 leading-relaxed">{{ $recipe->description }}</p>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50 text-[11px] text-gray-400 shrink-0">
                        <div class="flex items-center space-x-2">
                            @if($recipe->user->avatar)
                                <img src="{{ str_contains($recipe->user->avatar, 'http') ? $recipe->user->avatar : Storage::url($recipe->user->avatar) }}" class="w-6 h-6 rounded-full object-cover">
                            @else
                                <div class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-[10px]">
                                    {{ substr($recipe->user->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="font-bold text-gray-600 truncate max-w-[80px]">{{ $recipe->user->name }}</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <span class="flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $recipe->time_to_cook }}p
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide
                                {{ $recipe->difficulty === 'dễ' ? 'bg-green-50 text-green-700 border border-green-200/50' : ($recipe->difficulty === 'khó' ? 'bg-red-50 text-red-700 border border-red-200/50' : 'bg-yellow-50 text-yellow-700 border border-yellow-200/50') }}">
                                {{ $recipe->difficulty }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-20 text-center text-gray-500 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="text-6xl mb-4 animate-bounce">🔍</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Không tìm thấy món ăn nào phù hợp</h3>
                <p class="text-sm text-gray-400 max-w-md mx-auto">Bạn hãy thử thay đổi bộ lọc, chuyển sang tab khác hoặc tìm kiếm bằng từ khóa khác nhé!</p>
                <div class="mt-6">
                    <a href="{{ route('search', ['type' => $type]) }}" class="inline-flex items-center space-x-2 bg-orange-500 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-orange-600 transition shadow-md shadow-orange-500/20 text-sm">
                        <span>Xóa tất cả bộ lọc</span>
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
@endsection
