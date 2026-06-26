@extends('layouts.app')

@section('title', 'Tương Tác - Foodball')
@section('header_title', 'Tương Tác')

@section('content')
    <div class="flex-1 max-w-4xl mx-auto w-full px-6 py-12 space-y-6 relative">
        <!-- Nút quay lại -->
        <div>
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-gray-500 hover:text-orange-500 font-bold transition bg-white border border-gray-200 hover:bg-orange-50 px-4 py-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại</span>
            </button>
        </div>
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                Tương Tác 
                <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full">{{ $interactions->count() }} hoạt động</span>
            </h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
            @forelse($interactions as $item)
                @php
                    $isComment = $item['type'] === 'comment';
                    $isLike = $item['type'] === 'like';
                    $isSave = $item['type'] === 'save';
                @endphp

                <!-- Mỗi tương tác bọc trong một link dẫn đến công thức -->
                <a href="{{ route('recipe.detail', $item['recipe']->slug) }}" class="p-6 flex space-x-4 hover:bg-orange-50/20 transition cursor-pointer {{ $isComment ? 'bg-orange-50/10' : 'bg-white' }} block">
                    <div class="relative shrink-0">
                        @if($item['user']->avatar)
                            <img src="{{ str_contains($item['user']->avatar, 'http') ? $item['user']->avatar : Storage::url($item['user']->avatar) }}" class="w-12 h-12 rounded-full border border-gray-200 object-cover shadow-sm">
                        @else
                            <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg border border-gray-200 shadow-sm shrink-0">
                                {{ substr($item['user']->name, 0, 1) }}
                            </div>
                        @endif

                        <!-- Badge Icon cho từng loại tương tác ở góc avatar -->
                        @if($isComment)
                            <div class="absolute -bottom-1 -right-1 bg-red-500 text-white text-[9px] flex items-center justify-center w-5 h-5 rounded-full border-2 border-white">💬</div>
                        @elseif($isLike)
                            <div class="absolute -bottom-1 -right-1 bg-blue-500 text-white text-[9px] flex items-center justify-center w-5 h-5 rounded-full border-2 border-white">👍</div>
                        @elseif($isSave)
                            <div class="absolute -bottom-1 -right-1 bg-emerald-500 text-white text-[9px] flex items-center justify-center w-5 h-5 rounded-full border-2 border-white">🔖</div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-800">
                            <strong class="font-bold text-gray-900">{{ $item['user']->name }}</strong> 
                            @if($isComment)
                                đã bình luận về công thức <strong class="font-bold text-orange-600 hover:underline">{{ $item['recipe']->title }}</strong> của bạn.
                            @elseif($isLike)
                                đã thích công thức <strong class="font-bold text-orange-600 hover:underline">{{ $item['recipe']->title }}</strong>.
                            @elseif($isSave)
                                đã lưu công thức <strong class="font-bold text-orange-600 hover:underline">{{ $item['recipe']->title }}</strong> vào thư viện.
                            @endif
                        </p>
                        
                        @if($isComment && !empty($item['content']))
                            <p class="text-gray-600 text-sm mt-2 bg-gray-50 border border-gray-100 p-3 rounded-xl italic">
                                "{{ $item['content'] }}"
                            </p>
                        @endif
                        
                        <div class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $item['created_at']->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v2.472M21 16v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 16H4"></path></svg>
                    <p class="font-medium text-lg text-gray-600">Chưa có tương tác nào</p>
                    <p class="text-gray-400 text-sm mt-1">Khi có người bình luận, thích hoặc lưu công thức của bạn, thông báo sẽ xuất hiện ở đây.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
