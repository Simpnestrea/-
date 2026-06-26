@extends('layouts.admin')

@section('title', 'Quản lý Danh mục - Foodball')
@section('page_title', 'Quản lý Danh mục')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ 
    editMode: false,
    editId: null,
    editName: '',
    editImageUrl: '',
    addUrl: '',
    addFilePreview: '',
    editFilePreview: '',
    searchQuery: '',
    removeAccents(str) {
        if (!str) return '';
        return str.normalize('NFD')
                  .replace(/[\u0300-\u036f]/g, '')
                  .replace(/đ/g, 'd')
                  .replace(/Đ/g, 'd')
                  .toLowerCase();
    },
    openEditModal(category) {
        this.editMode = true;
        this.editId = category.id;
        this.editName = category.name;
        this.editImageUrl = category.image || '';
        this.editFilePreview = '';
    },
    handleFileChange(event, mode) {
        const file = event.target.files[0];
        if (file) {
            const previewUrl = URL.createObjectURL(file);
            if (mode === 'add') {
                this.addFilePreview = previewUrl;
            } else {
                this.editFilePreview = previewUrl;
            }
        }
    },
    hasNoVisibleCategories() {
        if (!this.searchQuery) return false;
        const query = this.removeAccents(this.searchQuery);
        const names = [
            @foreach($categories as $category)
            this.removeAccents('{{ addslashes($category->name) }}'),
            @endforeach
        ];
        return !names.some(n => n.includes(query));
    }
}">

    <!-- Left Column: Add New Category -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm h-fit">
        <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
            <span>➕</span> Thêm Danh mục mới
        </h3>
        
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label for="name" class="text-xs font-bold text-slate-500 uppercase">Tên danh mục</label>
                <input type="text" name="name" id="name" required placeholder="Ví dụ: Món Kho, Bánh Ngọt..."
                    class="w-full py-2.5 px-4 rounded-xl border border-slate-200 outline-none text-sm transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
            </div>

            <div class="space-y-1">
                <label for="image_file" class="text-xs font-bold text-slate-500 uppercase block">Tải ảnh lên</label>
                <input type="file" name="image_file" id="image_file" accept="image/*" @change="handleFileChange($event, 'add')"
                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
            </div>

            <div class="relative py-2 flex items-center justify-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-100"></div>
                </div>
                <span class="relative bg-white px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hoặc dùng URL ảnh</span>
            </div>

            <div class="space-y-1">
                <label for="image_url" class="text-xs font-bold text-slate-500 uppercase">Địa chỉ liên kết ảnh (URL)</label>
                <input type="url" name="image_url" id="image_url" placeholder="https://example.com/image.jpg" x-model="addUrl"
                    class="w-full py-2.5 px-4 rounded-xl border border-slate-200 outline-none text-sm transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
            </div>

            <!-- Dynamic Preview Box -->
            <div class="mt-4 p-2 bg-slate-50 rounded-xl border border-slate-100 flex flex-col items-center justify-center space-y-1.5" x-show="addFilePreview || addUrl">
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Xem trước hình ảnh</span>
                <img :src="addFilePreview || addUrl" class="max-h-32 rounded-lg object-cover bg-slate-100 border border-slate-200/50">
            </div>

            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl transition shadow-lg shadow-orange-500/20 text-sm">
                Tạo Danh mục
            </button>
        </form>
    </div>

    <!-- Right Column: Category List (takes 2 cols on lg) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden lg:col-span-2">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <span>📂</span> Danh sách Danh mục ẩm thực
            </h3>
            <div class="relative w-full sm:w-64">
                <input type="text" x-model="searchQuery" placeholder="Tìm danh mục..." 
                    class="w-full py-2 pl-9 pr-4 rounded-xl border border-slate-200 outline-none text-xs transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 bg-slate-50 focus:bg-white font-medium">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Hình ảnh</th>
                        <th class="px-6 py-4">Tên danh mục</th>
                        <th class="px-6 py-4">Đường dẫn (Slug)</th>
                        <th class="px-6 py-4 text-center">Số công thức</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($categories as $category)
                        <tr x-show="!searchQuery || removeAccents('{{ addslashes($category->name) }}').includes(removeAccents(searchQuery))" 
                            class="hover:bg-slate-50/30 transition-all">
                            
                            <!-- Image -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ $category->image ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=120' }}" 
                                    class="w-12 h-12 rounded-xl object-cover bg-slate-100 border border-slate-200 shrink-0">
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $category->name }}
                            </td>

                            <!-- Slug -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-400">
                                {{ $category->slug }}
                            </td>

                            <!-- Recipes count -->
                            <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-slate-705 font-mono">
                                {{ $category->recipes_count }} món
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    
                                    <!-- Edit Trigger Button -->
                                    <button @click="openEditModal({ id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', image: '{{ $category->image }}' })" 
                                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                        Chỉnh sửa
                                    </button>

                                    <!-- Delete Category Form -->
                                    <form action="{{ route('admin.categories.delete', $category) }}" method="POST" class="m-0 inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục \"{{ $category->name }}\"? Tất cả công thức thuộc danh mục này sẽ chuyển về trạng thái Chưa phân loại.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-500 font-bold px-3 py-1.5 rounded-lg text-xs transition border border-red-200">
                                            Xóa
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">Chưa có danh mục nào được tạo.</td>
                        </tr>
                    @endforelse
                    <tr x-show="hasNoVisibleCategories()" style="display: none;">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                            Không tìm thấy danh mục nào khớp với từ khóa.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Category Modal (AlpineJS Managed) -->
    <div x-show="editMode" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="editMode = false"></div>

        <!-- Content Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 transition-all">
                
                <!-- Close Button -->
                <button @click="editMode = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                    <span>✏️</span> Chỉnh sửa Danh mục
                </h3>

                <!-- Dynamic Action Form -->
                <form :action="`/admin/categories/${editId}`" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-1">
                        <label for="edit_name" class="text-xs font-bold text-slate-500 uppercase">Tên danh mục</label>
                        <input type="text" name="name" id="edit_name" required x-model="editName"
                            class="w-full py-2.5 px-4 rounded-xl border border-slate-200 outline-none text-sm transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                    </div>

                    <div class="space-y-1">
                        <label for="edit_image_file" class="text-xs font-bold text-slate-500 uppercase block">Thay đổi ảnh đại diện (Tải lên file)</label>
                        <input type="file" name="image_file" id="edit_image_file" accept="image/*" @change="handleFileChange($event, 'edit')"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>

                    <div class="relative py-2 flex items-center justify-center">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-100"></div>
                        </div>
                        <span class="relative bg-white px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hoặc thay đổi bằng URL</span>
                    </div>

                    <div class="space-y-1">
                        <label for="edit_image_url" class="text-xs font-bold text-slate-500 uppercase">Địa chỉ liên kết ảnh (URL)</label>
                        <input type="url" name="image_url" id="edit_image_url" x-model="editImageUrl" placeholder="https://example.com/image.jpg"
                            class="w-full py-2.5 px-4 rounded-xl border border-slate-200 outline-none text-sm transition focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                    </div>

                    <!-- Dynamic Edit Preview Box -->
                    <div class="mt-4 p-2 bg-slate-50 rounded-xl border border-slate-100 flex flex-col items-center justify-center space-y-1.5" x-show="editFilePreview || editImageUrl">
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Xem trước hình ảnh thay đổi</span>
                        <img :src="editFilePreview || editImageUrl" class="max-h-32 rounded-lg object-cover bg-slate-100 border border-slate-200/50">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2">
                        <button type="button" @click="editMode = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-sm transition">
                            Hủy
                        </button>
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-2.5 rounded-xl transition shadow-lg shadow-orange-500/20 text-sm">
                            Cập nhật danh mục
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
@endsection

