@extends('layouts.app')

@section('content')

        <div class="px-10 pb-10 pt-6 max-w-5xl mx-auto w-full space-y-8 flex-1">
            
            <div class="text-center space-y-3" x-data="{
                query: '',
                results: [],
                showDropdown: false,
                async fetchSuggestions() {
                    if (this.query.trim().length < 1) {
                        this.results = [];
                        this.showDropdown = false;
                        return;
                    }
                    try {
                        const response = await fetch(`/api/search-suggestions?query=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.results = data;
                        this.showDropdown = this.results.length > 0;
                    } catch (error) {
                        console.error('Error fetching suggestions:', error);
                    }
                }
            }">
                <div class="relative w-full max-w-2xl mx-auto z-50">
                    <form action="{{ route('search') }}" method="GET" class="relative flex w-full items-center group">
                        <div class="absolute left-6 text-gray-400 group-focus-within:text-orange-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="query" x-model="query" @input.debounce.250ms="fetchSuggestions()" @focus="if(results.length > 0) showDropdown = true" placeholder="Tìm tên món hay nguyên liệu..." class="w-full py-4 pl-16 pr-36 rounded-full border border-gray-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] outline-none focus:ring-4 focus:ring-orange-500/20 focus:border-orange-400 text-gray-700 text-lg transition-all bg-white hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
                        <button type="submit" class="absolute right-2 top-2 bottom-2 bg-orange-500 text-white px-8 rounded-full font-bold hover:bg-orange-600 transition shadow-md shadow-orange-500/30 text-base flex items-center gap-2">
                            <span>Tìm Kiếm</span>
                        </button>
                    </form>

                    <!-- Dropdown Menu gợi ý -->
                    <div x-show="showDropdown" 
                         @click.outside="showDropdown = false"
                         class="absolute left-0 right-0 top-full mt-2 bg-white rounded-3xl border border-gray-150 shadow-xl overflow-hidden z-50 text-left" 
                         style="display: none;"
                         x-transition>
                        <div class="px-5 py-3 border-b border-gray-100 text-[11px] font-black text-gray-400 uppercase tracking-wider bg-gray-50/50">
                            ✨ Món ăn gợi ý
                        </div>
                        <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto custom-scrollbar">
                            <template x-for="recipe in results" :key="recipe.id">
                                <li>
                                    <a :href="`/recipe/${recipe.slug}`" class="flex items-center gap-4 px-5 py-3 hover:bg-orange-50/50 transition duration-150 group">
                                        <img :src="recipe.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100'" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=100';" 
                                             class="w-11 h-11 rounded-xl object-cover border border-gray-100 shrink-0 shadow-sm">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-orange-500 transition truncate" x-text="recipe.title"></p>
                                        </div>
                                        <span class="text-xs font-bold text-orange-500 opacity-0 group-hover:opacity-100 transition duration-150 shrink-0 flex items-center gap-1">
                                            Xem ngay <span>→</span>
                                        </span>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2 flex-wrap text-sm text-gray-400 pt-1">
                    <span>Gợi ý:</span>
                    @foreach(['Phở Bò', 'Bánh Mì', 'Gỏi Cuốn', 'Cơm Tấm', 'Lẩu'] as $suggest)
                        <a href="{{ route('search', ['query' => $suggest]) }}" class="bg-gray-100 hover:bg-orange-100 hover:text-orange-600 text-gray-500 px-3 py-1 rounded-full transition font-medium">{{ $suggest }}</a>
                    @endforeach
                </div>
            </div>

            <div x-data="{ 
                activeSlide: {{ $featuredRecipes->first()->id ?? 1 }}, 
                slides: {{ \Illuminate\Support\Js::from($featuredRecipes->map(fn($r) => [
                    'id' => $r->id, 
                    'title' => $r->title, 
                    'desc' => \Illuminate\Support\Str::limit($r->description, 50), 
                    'bg' => $r->image, 
                    'hashtag' => '#'.($r->category->slug ?? 'recipe'),
                    'slug' => $r->slug
                ])) }}
            }" class="relative w-full h-80 rounded-[30px] overflow-hidden shadow-sm bg-gray-50 group">
                <template x-for="slide in slides" :key="slide.id">
                    <div x-show="activeSlide === slide.id" x-transition class="absolute inset-0 flex items-center px-16 bg-cover bg-center" :style="`background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('${slide.bg}');`">
                        <div class="space-y-4 max-w-md z-10 text-white">
                            <h2 class="text-4xl font-black leading-tight" x-text="slide.title"></h2>
                            <p class="text-gray-100 font-medium" x-text="slide.desc"></p>
                            <p class="text-orange-400 font-bold" x-text="slide.hashtag"></p>
                            <a :href="`/recipe/${slide.slug}`" class="inline-block mt-4 bg-orange-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-orange-600 transition">Khám phá ngay</a>
                        </div>
                    </div>
                </template>
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2.5 z-10">
                    <template x-for="slide in slides" :key="slide.id">
                        <button @click="activeSlide = slide.id" :class="activeSlide === slide.id ? 'bg-orange-500 w-10' : 'bg-gray-300 w-3'" class="h-3 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>

            <section class="space-y-8">
                <div class="flex justify-between items-end border-b-2 border-gray-100 pb-3">
                    <h2 class="text-2xl font-black uppercase tracking-tight text-gray-900">Từ Khóa Thịnh Hành</h2>
                    <span class="text-sm text-gray-400">Cập nhật lúc: {{ now()->format('H:i') }}</span>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach($categories as $category)
                        <a href="{{ route('search', ['type' => 'category', 'category' => $category->slug]) }}" class="group relative rounded-xl overflow-hidden aspect-video border border-gray-100 hover:shadow-xl transition-all shadow-sm">
                            <img src="{{ $category->image }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 z-10 text-white font-bold">{{ $category->name }}</div>
                        </a>
                    @endforeach
                </div>
            </section>

            @if(isset($premiumRecipes) && $premiumRecipes->isNotEmpty())
            <section class="space-y-5">
                <div class="flex items-center justify-between border-b-2 border-gray-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-2xl font-black uppercase tracking-tight text-gray-900">👑 Bí Quyết Master Chef (Premium)</h2>
                        <span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Độc quyền</span>
                    </div>
                    <a href="{{ route('premium') }}" class="text-sm font-bold text-orange-500 hover:underline whitespace-nowrap">Đăng ký Premium →</a>
                </div>

                <div class="relative group/carousel">
                    {{-- Nút trái --}}
                    <button onclick="document.getElementById('carousel-premium').scrollBy({left: -340, behavior: 'smooth'})"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover/carousel:opacity-100 transition hover:bg-orange-50 hover:border-orange-300">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    {{-- Scroll container --}}
                    <div id="carousel-premium" class="flex gap-5 overflow-x-auto pb-3 scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach($premiumRecipes as $recipe)
                            <a href="{{ route('recipe.detail', $recipe->slug) }}"
                               class="group/card flex-none w-56 bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all flex flex-col relative">
                                <div class="h-36 relative overflow-hidden shrink-0">
                                    <img src="{{ $recipe->image }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800';" class="absolute inset-0 w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500" alt="{{ $recipe->title }}">
                                    <div class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-lg shadow-sm">👑 {{ $recipe->category->name ?? 'Premium' }}</div>
                                    <div class="absolute bottom-2 right-2 bg-black/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-lg shadow-sm font-mono">{{ number_format($recipe->price, 0, ',', '.') }} đ</div>
                                </div>
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-bold text-sm text-gray-900 mb-1 group-hover/card:text-orange-500 transition line-clamp-2 leading-snug">{{ $recipe->title }}</h3>
                                    <p class="text-xs text-gray-400 line-clamp-2 flex-1 mb-3">{{ Str::limit($recipe->description, 60) }}</p>
                                    <div class="flex items-center justify-between text-[11px] text-gray-400 font-medium border-t border-gray-50 pt-2">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $recipe->time_to_cook }}p
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                            {{ ucfirst($recipe->difficulty) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Nút phải --}}
                    <button onclick="document.getElementById('carousel-premium').scrollBy({left: 340, behavior: 'smooth'})"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover/carousel:opacity-100 transition hover:bg-orange-50 hover:border-orange-300">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </section>
            @endif

            {{-- COMPONENT: Recipe Row Carousel --}}

            @foreach([
                ['title' => '🌟 Món Ngon Nổi Bật', 'id' => 'carousel-famous', 'recipes' => $famousRecipes, 'link_query' => 'Món ăn'],
                ['title' => '😊 Dành Cho Người Mới', 'id' => 'carousel-easy', 'recipes' => $easyRecipes, 'link_query' => 'dễ'],
                ['title' => '⚡ Nấu Nhanh Dưới 30 Phút', 'id' => 'carousel-quick', 'recipes' => $quickRecipes, 'link_query' => 'Món Nước'],
            ] as $section)
            <section class="space-y-5">
                <div class="flex items-center justify-between border-b-2 border-gray-100 pb-3">
                    <h2 class="text-2xl font-black uppercase tracking-tight text-gray-900">{{ $section['title'] }}</h2>
                    <a href="{{ route('search', ['query' => $section['link_query']]) }}" class="text-sm font-bold text-orange-500 hover:underline whitespace-nowrap">Xem tất cả →</a>
                </div>

                <div class="relative group/carousel">
                    {{-- Nút trái --}}
                    <button onclick="document.getElementById('{{ $section['id'] }}').scrollBy({left: -340, behavior: 'smooth'})"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover/carousel:opacity-100 transition hover:bg-orange-50 hover:border-orange-300">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    {{-- Scroll container --}}
                    <div id="{{ $section['id'] }}" class="flex gap-5 overflow-x-auto pb-3 scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
                        @forelse($section['recipes'] as $recipe)
                            <a href="{{ route('recipe.detail', $recipe->slug) }}"
                               class="group/card flex-none w-56 bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all flex flex-col">
                                <div class="h-36 relative overflow-hidden shrink-0">
                                    <img src="{{ $recipe->image }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800';" class="absolute inset-0 w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500" alt="{{ $recipe->title }}">
                                    <div class="absolute top-2 left-2 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg shadow-sm">{{ $recipe->category->name ?? 'Nổi Bật' }}</div>
                                </div>
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-bold text-sm text-gray-900 mb-1 group-hover/card:text-orange-500 transition line-clamp-2 leading-snug">{{ $recipe->title }}</h3>
                                    <p class="text-xs text-gray-400 line-clamp-2 flex-1 mb-3">{{ Str::limit($recipe->description, 60) }}</p>
                                    <div class="flex items-center justify-between text-[11px] text-gray-400 font-medium border-t border-gray-50 pt-2">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $recipe->time_to_cook }}p
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $recipe->difficulty === 'dễ' ? 'bg-green-100 text-green-700' : ($recipe->difficulty === 'khó' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                            {{ ucfirst($recipe->difficulty) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="w-full py-10 text-center text-gray-400 text-sm">Chưa có món ăn nào trong mục này.</div>
                        @endforelse
                    </div>

                    {{-- Nút phải --}}
                    <button onclick="document.getElementById('{{ $section['id'] }}').scrollBy({left: 340, behavior: 'smooth'})"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover/carousel:opacity-100 transition hover:bg-orange-50 hover:border-orange-300">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </section>
            @endforeach

            <div class="bg-black text-white rounded-[30px] p-10 flex flex-col md:flex-row items-center justify-between border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                <div class="space-y-2">
                    <h3 class="text-3xl font-black uppercase text-orange-400">Gói Premium 👑</h3>
                    <p class="font-bold text-gray-200">Mở khóa 1000+ công thức độc quyền từ các siêu đầu bếp.</p>
                </div>
                <a href="{{ route('premium') }}" class="mt-6 md:mt-0 bg-white text-black px-10 py-4 rounded-full font-black hover:bg-orange-500 hover:text-white transition uppercase text-sm border-2 border-black">
                    Bắt Đầu ngay
                </a>
            </div>

        </div>

        <footer class="bg-white border-t border-gray-200 pt-16 relative overflow-hidden shrink-0 mt-8">
            <div class="max-w-5xl mx-auto px-10 pb-32 grid grid-cols-1 md:grid-cols-3 gap-12 relative z-10">
                
                <div class="md:col-span-3 space-y-4">
                    <h3 class="font-black text-xl text-gray-900">Về Foodball</h3>
                    <p class="text-gray-600 leading-relaxed max-w-4xl">
                        Sứ mệnh của Foodball là <strong class="text-gray-900">làm cho việc vào bếp vui hơn mỗi ngày</strong>, vì chúng tôi tin rằng nấu nướng là chìa khoá cho một cuộc sống hạnh phúc hơn và khoẻ mạnh hơn cho con người, cộng đồng, và hành tinh này. Chúng tôi muốn hỗ trợ các đầu bếp gia đình trên toàn thế giới để họ có thể <strong class="text-gray-900">giúp đỡ nhau</strong> qua việc chia sẻ các món ngon và kinh nghiệm nấu ăn của mình.
                        <br><a href="#" class="text-orange-500 hover:underline font-bold mt-2 inline-block">Đăng ký gói Premium</a> để truy cập các chức năng và quyền lợi dành riêng khác!
                    </p>
                </div>

                <div class="md:col-span-3 space-y-4">
                    <h3 class="font-black text-xl text-gray-900">Cộng Đồng Foodball</h3>
                    <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-600">
                        <a href="#" class="hover:text-orange-500 hover:underline">🇺🇸 United States</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇬🇧 United Kingdom</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇪🇸 España</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇦🇷 Argentina</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇲🇽 México</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇻🇳 Việt Nam</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇹🇭 ไทย</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">🇮🇩 Indonesia</a>
                        <button class="bg-gray-100 px-2 py-0.5 rounded text-xs font-bold hover:bg-gray-200">Xem Tất Cả</button>
                    </div>
                </div>

                <div class="md:col-span-3 space-y-4">
                    <h3 class="font-black text-xl text-gray-900">Tìm Hiểu Thêm</h3>
                    <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm font-bold text-gray-700">
                        <a href="#" class="hover:text-orange-500 hover:underline">Gói Foodball Premium</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">Sự Nghiệp</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">Góp Ý</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">Bài Viết</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">Điều Khoản Dịch Vụ</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">Hướng Dẫn Cộng Đồng</a>
                        <a href="#" class="hover:text-orange-500 hover:underline">Chính Sách Bảo Mật</a>
                    </div>
                </div>

                <div class="md:col-span-3 text-center text-sm text-gray-400 mt-8">
                    Bản quyền của © Foodball Inc. All Rights Reserved
                </div>
            </div>

            <div class="absolute bottom-[-20px] left-0 w-full flex justify-around text-8xl opacity-90 select-none overflow-hidden h-28 items-end pointer-events-none">
                <span>🥕</span><span>🍅</span><span>🥦</span><span>🍋</span><span>🌶️</span><span>🥕</span><span>🍅</span><span>🥦</span>
            </div>
        </footer>

@endsection