<?php $__env->startSection('title', 'Quản lý Công thức - Foodball'); ?>
<?php $__env->startSection('page_title', 'Quản lý Công thức'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="recipesManager()">

    <!-- Search and Actions Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form action="<?php echo e(route('admin.recipes')); ?>" method="GET" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="query" value="<?php echo e($query); ?>" placeholder="Tìm tên công thức, người đăng..." 
                    class="w-full py-2.5 pl-10 pr-4 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10"
                    oninput="clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => { this.form.submit(); }, 600)"
                    <?php echo e($query ? 'autofocus' : ''); ?>

                    onfocus="var val=this.value; this.value=''; this.value=val;">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <?php if($query): ?>
                    <a href="<?php echo e(route('admin.recipes')); ?>" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 hover:text-slate-600">Xóa tìm kiếm</a>
                <?php endif; ?>
            </div>

            <!-- Category Filter -->
            <select name="category_id" onchange="this.form.submit()" class="py-2.5 pl-4 pr-10 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                <option value="">Tất cả danh mục</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(($categoryId ?? '') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <!-- Difficulty Filter -->
            <select name="difficulty" onchange="this.form.submit()" class="py-2.5 pl-4 pr-10 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                <option value="">Tất cả độ khó</option>
                <option value="dễ" <?php echo e(($difficulty ?? '') === 'dễ' ? 'selected' : ''); ?>>Dễ</option>
                <option value="trung bình" <?php echo e(($difficulty ?? '') === 'trung bình' ? 'selected' : ''); ?>>Trung bình</option>
                <option value="khó" <?php echo e(($difficulty ?? '') === 'khó' ? 'selected' : ''); ?>>Khó</option>
            </select>

            <!-- Premium/Free Filter -->
            <select name="premium_status" onchange="this.form.submit()" class="py-2.5 pl-4 pr-10 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10">
                <option value="">Tất cả loại bài</option>
                <option value="premium" <?php echo e(($premiumStatus ?? '') === 'premium' ? 'selected' : ''); ?>>Bài Premium</option>
                <option value="free" <?php echo e(($premiumStatus ?? '') === 'free' ? 'selected' : ''); ?>>Bài Thường</option>
            </select>
        </form>
        
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider shrink-0 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
            Hiển thị <?php echo e($recipes->firstItem() ?? 0); ?> - <?php echo e($recipes->lastItem() ?? 0); ?> / <?php echo e($recipes->total()); ?> công thức
        </div>
    </div>

    <!-- Recipes Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Món ăn</th>
                        <th class="px-6 py-4">Người đăng</th>
                        <th class="px-6 py-4">Danh mục</th>
                        <th class="px-6 py-4 text-center">Độ khó / Thời gian</th>
                        <th class="px-6 py-4 text-center">Lượt xem</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $recipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/30 transition-all" id="recipe-row-<?php echo e($recipe->id); ?>">
                            
                            <!-- Recipe Image & Title -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 max-w-sm">
                                    <img src="<?php echo e($recipe->image ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=120'); ?>" 
                                        class="w-12 h-12 rounded-xl object-cover bg-slate-100 shrink-0 border border-slate-200/80 shadow-sm">
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 hover:text-orange-500 transition line-clamp-1 cursor-pointer" title="<?php echo e($recipe->title); ?>" @click="showDetail(<?php echo e($recipe->id); ?>)">
                                            <?php echo e($recipe->title); ?>

                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <?php if($recipe->is_premium): ?>
                                                <span class="bg-amber-50 text-amber-600 border border-amber-100 text-[8px] font-black px-1 rounded uppercase tracking-wider">Premium</span>
                                            <?php endif; ?>
                                            <span class="text-[10px] text-slate-400">Đăng lúc: <?php echo e($recipe->created_at ? $recipe->created_at->format('d/m/Y H:i') : 'Không rõ'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Author -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($recipe->user): ?>
                                    <div class="font-bold text-slate-800"><?php echo e($recipe->user->name); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo e($recipe->user->email); ?></div>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Thành viên đã xóa</span>
                                <?php endif; ?>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-slate-100 text-slate-700 border border-slate-200/60 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    <?php echo e($recipe->category->name ?? 'Chưa phân loại'); ?>

                                </span>
                            </td>

                            <!-- Difficulty & Time -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        <?php echo e($recipe->difficulty === 'dễ' ? 'bg-green-50 text-green-700 border border-green-100' : ($recipe->difficulty === 'khó' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-yellow-50 text-yellow-700 border border-yellow-100')); ?>">
                                        <?php echo e($recipe->difficulty); ?>

                                    </span>
                                    <span class="text-xs text-slate-500 font-semibold"><?php echo e($recipe->time_to_cook); ?> phút nấu</span>
                                </div>
                            </td>

                            <!-- Views -->
                            <td class="px-6 py-4 whitespace-nowrap text-center font-black text-slate-800 font-mono">
                                <?php echo e(number_format($recipe->views_count)); ?>

                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    
                                    <!-- View frontend button -->
                                    <button @click="showDetail(<?php echo e($recipe->id); ?>)" class="bg-slate-50 hover:bg-slate-100 text-slate-750 font-bold px-3 py-1.5 rounded-lg border border-slate-200 text-xs transition">
                                        Xem chi tiết
                                    </button>

                                    <!-- Delete Recipe Form -->
                                    <form action="<?php echo e(route('admin.recipes.delete', $recipe)); ?>" method="POST" class="m-0 inline" @submit.prevent="deleteRecipe(<?php echo e($recipe->id); ?>, $event)">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                            Xóa
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">Không tìm thấy công thức nấu ăn nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($recipes->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <?php echo e($recipes->appends(['query' => $query, 'category_id' => $categoryId ?? '', 'difficulty' => $difficulty ?? '', 'premium_status' => $premiumStatus ?? ''])->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Recipe Quick View Modal -->
    <div x-show="detailModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="detailModal = false"></div>

        <!-- Content Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-3xl rounded-3xl bg-white p-6 shadow-2xl border border-slate-100 transition-all max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Close Button -->
                <button @click="detailModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition z-10 bg-white/80 p-1.5 rounded-full border border-slate-100 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Modal Loading State -->
                <div x-show="loadingDetail" class="py-16 flex flex-col items-center justify-center space-y-4">
                    <div class="w-12 h-12 rounded-full border-4 border-slate-100 border-t-orange-500 animate-spin"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Đang tải công thức...</span>
                </div>

                <!-- Modal Content -->
                <div x-show="!loadingDetail && recipeDetail" class="space-y-6 flex-1 overflow-y-auto custom-scrollbar pr-1">
                    <template x-if="recipeDetail">
                        <div class="space-y-6">
                            <!-- Hero Cover & Title -->
                            <div class="relative h-56 rounded-2xl overflow-hidden border border-slate-100 shadow-sm shrink-0">
                                <img :src="recipeDetail.image || 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800'" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/20 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <span class="text-[9px] font-black uppercase bg-orange-500 px-2 py-0.5 rounded tracking-widest text-white inline-block mb-1" x-text="recipeDetail.category ? recipeDetail.category.name : 'Chưa phân loại'"></span>
                                    <h3 class="text-xl font-extrabold tracking-tight leading-snug line-clamp-2" x-text="recipeDetail.title"></h3>
                                </div>
                            </div>

                            <!-- Meta Info Columns -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100/80">
                                <div class="text-center sm:border-r border-slate-200/60 last:border-0 py-1">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Người đăng</span>
                                    <span class="text-xs font-extrabold text-slate-800 block mt-0.5" x-text="recipeDetail.user ? recipeDetail.user.name : 'Ẩn danh'"></span>
                                </div>
                                <div class="text-center sm:border-r border-slate-200/60 last:border-0 py-1">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Độ khó</span>
                                    <span class="text-xs font-black uppercase block mt-0.5" 
                                          :class="{
                                              'text-green-600': recipeDetail.difficulty === 'dễ',
                                              'text-yellow-600': recipeDetail.difficulty === 'trung bình',
                                              'text-red-600': recipeDetail.difficulty === 'khó'
                                          }"
                                          x-text="recipeDetail.difficulty"></span>
                                </div>
                                <div class="text-center sm:border-r border-slate-200/60 last:border-0 py-1">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Thời gian nấu</span>
                                    <span class="text-xs font-extrabold text-slate-800 block mt-0.5" x-text="recipeDetail.time_to_cook + ' phút'"></span>
                                </div>
                                <div class="text-center last:border-0 py-1">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Loại bài</span>
                                    <span x-show="recipeDetail.is_premium" class="text-xs font-extrabold text-amber-600 block mt-0.5" x-text="'Premium (' + new Intl.NumberFormat('vi-VN').format(recipeDetail.price) + ' đ)'"></span>
                                    <span x-show="!recipeDetail.is_premium" class="text-xs font-extrabold text-emerald-600 block mt-0.5">Miễn phí</span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-1.5">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400">Giới thiệu món ăn</h4>
                                <p class="text-xs text-slate-650 leading-relaxed font-medium" x-text="recipeDetail.description || 'Chưa có phần giới thiệu.'"></p>
                            </div>

                            <!-- Ingredients list & Steps list Split -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Ingredients -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 pb-2">Nguyên liệu</h4>
                                    <div class="space-y-2">
                                        <template x-if="recipeDetail.ingredients.length === 0">
                                            <p class="text-xs text-slate-400 italic">Chưa nhập nguyên liệu.</p>
                                        </template>
                                        <template x-for="ing in recipeDetail.ingredients" :key="ing.id">
                                            <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-50 last:border-0 font-medium">
                                                <span class="text-slate-800 font-bold" x-text="ing.name"></span>
                                                <span class="text-slate-500 font-semibold" x-text="(ing.pivot.quantity || '') + ' ' + (ing.pivot.unit || '')"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Directions / Steps -->
                                <div class="md:col-span-2 space-y-3">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 pb-2">Các bước thực hiện</h4>
                                    <div class="space-y-3">
                                        <template x-if="recipeDetail.steps.length === 0">
                                            <p class="text-xs text-slate-400 italic">Chưa nhập các bước làm.</p>
                                        </template>
                                        <template x-for="step in recipeDetail.steps" :key="step.id">
                                            <div class="flex items-start space-x-3 p-3 bg-slate-50 rounded-xl">
                                                <div class="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-sm" x-text="step.order"></div>
                                                <div class="space-y-1.5 flex-1 min-w-0">
                                                    <p class="text-xs text-slate-800 font-bold leading-relaxed" x-text="step.instruction"></p>
                                                    <template x-if="step.image">
                                                        <img :src="step.image.startsWith('http') ? step.image : '/storage/' + step.image" class="max-h-36 rounded-lg object-cover bg-slate-200 mt-2">
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips -->
                            <template x-if="recipeDetail.tips">
                                <div class="bg-amber-50/50 border border-amber-100 p-4 rounded-2xl space-y-1.5">
                                    <h4 class="text-xs font-black uppercase tracking-widest text-amber-700 flex items-center gap-1.5">
                                        <span>💡</span> Mẹo vặt từ đầu bếp
                                    </h4>
                                    <p class="text-xs text-amber-900 leading-relaxed font-semibold" x-text="recipeDetail.tips"></p>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function recipesManager() {
        return {
            detailModal: false,
            loadingDetail: false,
            recipeDetail: null,

            showDetail(recipeId) {
                this.detailModal = true;
                this.loadingDetail = true;
                this.recipeDetail = null;

                fetch(`/admin/recipes/${recipeId}/detail`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.recipeDetail = data;
                    this.loadingDetail = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loadingDetail = false;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Không thể tải chi tiết công thức', type: 'error' } }));
                });
            },

            deleteRecipe(recipeId, event) {
                if(!confirm('Bạn có chắc chắn muốn xóa công thức này?')) return;
                const form = event.target.closest('form');
                const url = form.action;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`recipe-row-${recipeId}`);
                        if (row) row.remove();
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Xóa thất bại', type: 'error' } }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                });
            }
        };
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\--main\resources\views/admin/recipes.blade.php ENDPATH**/ ?>