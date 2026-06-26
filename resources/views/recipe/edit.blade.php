<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa công thức - {{ $recipe->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 h-screen flex flex-col overflow-hidden" 
      x-data="{ 
          ingredients: [
              @foreach($recipe->ingredients as $index => $ing)
                  { id: {{ $ing->id }}, quantity: '{{ $ing->pivot->quantity }}', unit: '{{ $ing->pivot->unit }}', name: '{{ addslashes($ing->name) }}' }{{ !$loop->last ? ',' : '' }}
              @endforeach
          ], 
          steps: [
              @foreach($recipe->steps as $index => $step)
                  { id: {{ $step->id }}, content: '{{ addslashes($step->content) }}', image: '{{ $step->image }}', preview: '{{ $step->image }}' }{{ !$loop->last ? ',' : '' }}
              @endforeach
          ] 
      }">

    <form action="{{ route('recipe.update', $recipe) }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
        @csrf
        @method('PUT')
        
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shrink-0">
            <a href="{{ route('kitchen.index') }}" class="text-gray-500 hover:text-orange-500 font-bold text-sm">Hủy</a>
            <div class="font-black text-xl tracking-tight text-gray-900 flex items-center space-x-2">
                <span>Chỉnh sửa công thức</span>
                <span>🍳</span>
            </div>
            <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg font-bold text-sm hover:bg-orange-600 transition shadow-md shadow-orange-500/10">Lưu</button>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            
            @if ($errors->any())
                <div class="max-w-6xl mx-auto mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 text-sm">
                    <div class="font-bold mb-1">Vui lòng kiểm tra lại thông tin:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="max-w-6xl mx-auto bg-white rounded-xl border border-gray-200 p-6 sm:p-10 flex flex-col lg:flex-row gap-10">
                
                <!-- Cột trái: Ảnh đại diện & Nguyên liệu -->
                <div class="w-full lg:w-[360px] shrink-0 space-y-8">
                    
                    <!-- Tải lên ảnh đại diện món ăn -->
                    <label class="block w-full aspect-square bg-gray-50 border-2 border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-400 transition cursor-pointer relative overflow-hidden rounded-xl" 
                           x-data="{ preview: '{{ $recipe->image }}' }">
                        <input type="file" name="image" accept="image/*" class="hidden" @change="const file = $event.target.files[0]; if (file) { preview = URL.createObjectURL(file); }">
                        <template x-if="preview">
                            <img :src="preview" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <template x-if="!preview">
                            <div class="flex flex-col items-center p-4 text-center">
                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-sm font-bold mt-2 text-gray-500">Tải ảnh đại diện món ăn</span>
                                <span class="text-xs text-gray-400 mt-1">Định dạng ảnh JPG, PNG dưới 2MB</span>
                            </div>
                        </template>
                    </label>

                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-gray-900 border-t border-gray-200 pt-6">Nguyên Liệu</h2>

                        <!-- Vòng lặp Nguyên liệu -->
                        <div class="space-y-3">
                            <template x-for="(ing, index) in ingredients" :key="ing.id">
                                <div class="flex items-center space-x-2 group">
                                    <div class="cursor-move text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    <input type="text" :name="`ingredients[${index}][quantity]`" x-model="ing.quantity" placeholder="Lượng (vd: 200)" class="w-20 border border-gray-300 rounded px-2 py-1.5 text-sm outline-none focus:border-orange-500">
                                    <input type="text" :name="`ingredients[${index}][unit]`" x-model="ing.unit" placeholder="Đơn vị (vd: g)" class="w-16 border border-gray-300 rounded px-2 py-1.5 text-sm outline-none focus:border-orange-500">
                                    <input type="text" :name="`ingredients[${index}][name]`" x-model="ing.name" placeholder="Tên (vd: Thịt bò)" class="flex-1 min-w-0 border border-gray-300 rounded px-2 py-1.5 text-sm outline-none focus:border-orange-500" required>
                                    <button type="button" @click="ingredients.splice(index, 1)" class="text-red-400 hover:text-red-650 font-bold px-1 text-lg">×</button>
                                </div>
                            </template>
                        </div>

                        <div class="flex pt-2">
                            <button type="button" @click="ingredients.push({ id: Date.now(), quantity: '', unit: '', name: '' })" class="text-sm font-bold text-orange-500 hover:text-orange-600">+ Thêm nguyên liệu</button>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Thông tin chi tiết & Các bước thực hiện -->
                <div class="flex-1 space-y-8 lg:border-l lg:border-gray-200 lg:pl-10">
                    
                    <!-- Tên món ăn -->
                    <div class="flex items-center space-x-4 border border-gray-300 px-4 py-2 rounded-xl">
                        <label class="font-bold text-gray-700 whitespace-nowrap" for="title">Tên món:</label>
                        <input type="text" name="title" id="title" class="flex-1 border-none bg-transparent outline-none py-1 text-gray-900 font-medium placeholder-gray-400" placeholder="Ví dụ: Bún chả Hà Nội..." value="{{ old('title', $recipe->title) }}" required>
                    </div>

                    <!-- Dropdown chọn danh mục & Độ khó -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-4 border border-gray-300 px-4 py-2 rounded-xl">
                            <label class="font-bold text-gray-700 whitespace-nowrap" for="category_id">Danh mục:</label>
                            <select name="category_id" id="category_id" class="flex-1 bg-transparent border-none outline-none py-1 text-gray-900 font-medium">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $cat->id === $recipe->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center space-x-4 border border-gray-300 px-4 py-2 rounded-xl">
                            <label class="font-bold text-gray-700 whitespace-nowrap" for="difficulty">Độ khó:</label>
                            <select name="difficulty" id="difficulty" class="flex-1 bg-transparent border-none outline-none py-1 text-gray-900 font-medium">
                                <option value="dễ" {{ $recipe->difficulty === 'dễ' ? 'selected' : '' }}>Dễ</option>
                                <option value="trung bình" {{ $recipe->difficulty === 'trung bình' ? 'selected' : '' }}>Trung bình</option>
                                <option value="khó" {{ $recipe->difficulty === 'khó' ? 'selected' : '' }}>Khó</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Thiết lập giá bán (Premium) -->
                    <div class="bg-amber-50/50 border border-amber-200/60 p-5 rounded-2xl space-y-4" x-data="{ isPremium: {{ $recipe->is_premium ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <h3 class="font-bold text-gray-900 flex items-center gap-1.5 text-sm sm:text-base">
                                    <span>👑</span> Đặt làm công thức trả phí (Premium)
                                </h3>
                                <p class="text-xs text-gray-500">Người dùng Premium sẽ được xem miễn phí, người dùng thường cần trả tiền để mua.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_premium" value="1" x-model="isPremium" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                            </label>
                        </div>
                        
                        <div x-show="isPremium" x-transition class="space-y-2" style="display: none;">
                            <div class="flex items-center space-x-3 border border-gray-300 px-4 py-2.5 rounded-xl bg-white">
                                <label class="font-bold text-gray-700 text-sm whitespace-nowrap" for="price">Giá bán (đ):</label>
                                <input type="number" name="price" id="price" class="flex-1 border-none bg-transparent outline-none py-1 text-gray-900 font-bold placeholder-gray-400 text-sm" placeholder="Ví dụ: 50000" value="{{ old('price', $recipe->price) }}" min="1000" step="1000">
                                <span class="text-xs text-gray-400 font-bold">VND</span>
                            </div>
                            <p class="text-[11px] text-amber-600 font-semibold bg-amber-50/70 py-2 px-3.5 rounded-xl border border-amber-200/50 flex items-center gap-1.5">
                                <span>⚠️</span>
                                <span>Doanh thu bán công thức sẽ tự động khấu trừ <strong>5% thuế</strong> hệ thống (chỉ <strong>1%</strong> đối với tài khoản <strong>Premium</strong>).</span>
                            </p>
                        </div>
                    </div>

                    <!-- Mô tả công thức -->
                    <textarea name="description" class="w-full h-32 border border-gray-300 p-4 text-gray-700 outline-none focus:border-orange-500 resize-none rounded-xl" placeholder="Mô tả ngắn gọn về món ăn của bạn (câu chuyện, cảm xúc, hương vị...)...">{{ old('description', $recipe->description) }}</textarea>

                    <!-- Phần Các bước thực hiện -->
                    <div class="space-y-6 border-t border-gray-200 pt-8">
                        <h2 class="text-xl font-bold text-gray-900">Các bước thực hiện</h2>
                        
                        <!-- Thời gian nấu -->
                        <div class="flex items-center space-x-3 mb-6">
                            <span class="text-sm font-medium text-gray-600">Thời gian nấu (phút):</span>
                            <input type="number" name="time_to_cook" id="time_to_cook" placeholder="30" min="1" class="border border-gray-300 rounded px-3 py-1 text-sm w-24 outline-none focus:border-orange-500" value="{{ old('time_to_cook', $recipe->time_to_cook) }}" required>
                            <span class="text-sm font-medium text-gray-500">phút</span>
                        </div>

                        <!-- Vòng lặp các bước làm -->
                        <div class="space-y-8">
                            <template x-for="(step, index) in steps" :key="step.id">
                                <div class="flex items-start space-x-3 bg-gray-50/50 p-4 rounded-xl border border-gray-100" x-data="{ stepPreview: step.preview }">
                                    <div class="w-6 h-6 rounded-full bg-gray-700 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-1.5" x-text="index + 1"></div>
                                    <div class="cursor-move text-gray-400 mt-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-start space-x-2">
                                            <textarea :name="`steps[${index}][content]`" x-model="step.content" placeholder="Mô tả cách thực hiện bước này..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-orange-500 h-20 resize-none" required></textarea>
                                            <button type="button" @click="steps.splice(index, 1)" class="text-red-400 hover:text-red-655 font-bold px-1 text-lg">×</button>
                                        </div>
                                        
                                        <!-- Old Image Path keeper -->
                                        <input type="hidden" :name="`steps[${index}][old_image]`" :value="step.image">

                                        <div class="flex items-center space-x-4">
                                            <label class="w-32 h-24 bg-white border border-gray-300 rounded-lg flex flex-col items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-400 cursor-pointer overflow-hidden relative">
                                                <input type="file" :name="`steps[${index}][image]`" accept="image/*" class="hidden" @change="const file = $event.target.files[0]; if (file) { stepPreview = URL.createObjectURL(file); }">
                                                <template x-if="stepPreview">
                                                    <img :src="stepPreview" class="absolute inset-0 w-full h-full object-cover">
                                                </template>
                                                <template x-if="!stepPreview">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                        </svg>
                                                        <span class="text-[10px] font-bold mt-1">Thêm ảnh</span>
                                                    </div>
                                                </template>
                                            </label>
                                            <span class="text-xs text-gray-400">Hình ảnh minh họa cho bước nấu (tùy chọn)</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="pt-4 text-center sm:text-left sm:pl-16">
                            <button type="button" @click="steps.push({ id: Date.now(), content: '', image: '', preview: '' })" class="text-sm font-bold text-orange-500 hover:text-orange-600">+ Thêm bước làm</button>
                        </div>
                    </div>

                    <!-- Mẹo / Bí quyết nấu ăn -->
                    <div class="border-t border-gray-200 pt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="tips">Bí quyết để làm cho món ăn ngon của bạn là gì?</label>
                        <textarea name="tips" id="tips" class="w-full h-20 border border-gray-300 p-3 text-gray-700 outline-none focus:border-orange-500 resize-none rounded-xl" placeholder="Chia sẻ các mẹo nhỏ, lưu ý khi nấu món này...">{{ old('tips', $recipe->tips) }}</textarea>
                    </div>

                </div>
            </div>
        </main>
    </form>

</body>
</html>
