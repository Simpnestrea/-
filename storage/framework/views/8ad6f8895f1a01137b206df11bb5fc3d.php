<?php $__env->startSection('title', 'Kho Món Ngon - Foodball'); ?>
<?php $__env->startSection('header_title', 'Kho Món Ngon'); ?>

<?php $__env->startSection('content'); ?>
    <div class="flex-1 max-w-5xl mx-auto w-full px-6 py-10 space-y-10 relative">
        <!-- Nút quay lại -->
        <div>
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-gray-500 hover:text-orange-500 font-bold transition bg-white border border-gray-200 hover:bg-orange-50 px-4 py-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại</span>
            </button>
        </div>

        <!-- Banner thông tin bếp -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <?php if(auth()->user()->avatar): ?>
                    <img src="<?php echo e(str_contains(auth()->user()->avatar, 'http') ? auth()->user()->avatar : Storage::url(auth()->user()->avatar)); ?>" class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover">
                <?php else: ?>
                    <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center text-4xl border-4 border-white shadow-md">👨‍🍳</div>
                <?php endif; ?>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-black mb-1">Bếp của <?php echo e(auth()->user()->name); ?></h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border
                            <?php if(auth()->user()->role === 'beginner'): ?> bg-slate-100 text-slate-700 border-slate-200
                            <?php elseif(auth()->user()->role === 'homecook'): ?> bg-blue-50 text-blue-700 border-blue-100
                            <?php elseif(auth()->user()->role === 'prochef'): ?> bg-emerald-50 text-emerald-700 border-emerald-100
                            <?php elseif(auth()->user()->role === 'masterchef'): ?> bg-orange-50 text-orange-700 border-orange-100
                            <?php else: ?> bg-slate-100 text-slate-700 border-slate-200
                            <?php endif; ?>">
                            <?php echo e(auth()->user()->role_label); ?>

                        </span>
                    </div>
                    <p class="text-gray-500 font-medium mt-1">
                        Thành viên từ <?php echo e(auth()->user()->created_at ? 'Tháng ' . auth()->user()->created_at->format('m, Y') : 'Tháng 5, 2026'); ?>

                    </p>
                </div>
            </div>
            <a href="<?php echo e(route('recipe.create')); ?>" class="bg-orange-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-orange-600 transition shadow-md flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Viết Món Mới</span>
            </a>
        </div>

        <!-- Các thẻ thống kê -->
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <div id="mine-count" class="text-3xl font-black text-gray-900 mb-1"><?php echo e($myRecipesCount); ?></div>
                <div class="text-sm font-semibold text-gray-500 uppercase">Công Thức Đã Viết</div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <div id="saved-count" class="text-3xl font-black text-gray-900 mb-1"><?php echo e($savedRecipesCount); ?></div>
                <div class="text-sm font-semibold text-gray-500 uppercase">Đã Lưu</div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <div class="text-3xl font-black text-gray-900 mb-1"><?php echo e($likesReceivedCount); ?></div>
                <div class="text-sm font-semibold text-gray-500 uppercase">Lượt Thích Nhận Được</div>
            </div>
        </div>

        <!-- Rank Progress Card -->
        <?php if(auth()->user()->role === 'masterchef'): ?>
            <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500 p-6 rounded-2xl text-white shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black flex items-center gap-2">
                        <span>🏆</span> Cấp bậc Danh dự: Siêu đầu bếp (Master Chef)
                    </h3>
                    <p class="text-xs text-orange-100 mt-0.5">Bạn đã đạt danh hiệu ẩm thực cao quý nhất của Foodball, được cấp tay bởi Ban quản trị!</p>
                </div>
                <div class="text-3xl animate-bounce">👑</div>
            </div>
        <?php elseif(auth()->user()->role === 'prochef'): ?>
            <div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 p-6 rounded-3xl text-white shadow-lg relative overflow-hidden group">
                <!-- Background decorative elements -->
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-xl group-hover:scale-110 transition duration-700"></div>
                <div class="absolute -left-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-lg"></div>

                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2.5">
                            <span class="text-3xl animate-pulse">🔥</span>
                            <div>
                                <h3 class="text-lg font-black tracking-wide flex items-center gap-2">
                                    Bạn đã đạt cấp: <span class="underline decoration-wavy decoration-yellow-300 font-extrabold text-yellow-300">Đầu bếp Chuyên nghiệp (Pro Chef)</span>
                                </h3>
                                <p class="text-xs text-emerald-100 font-medium">Đây là cấp bậc ẩm thực cao nhất được nâng cấp tự động qua hệ thống!</p>
                            </div>
                        </div>
                        <div class="border-t border-white/20 pt-3">
                            <p class="text-sm font-bold text-yellow-200">👑 Lộ trình thăng tiến thành Siêu đầu bếp (Master Chef):</p>
                            <p class="text-xs text-slate-100 mt-1 font-medium leading-relaxed">
                                Cấp bậc **Master Chef** tôn vinh những cống hiến vượt bậc và không nâng cấp tự động. Ban quản trị sẽ phê duyệt khi bạn đạt các thành tựu sau:
                            </p>
                            <ul class="list-disc list-inside text-xs text-slate-100 mt-2 space-y-1.5 font-medium pl-1">
                                <li>Đạt giải thưởng hoặc lọt vào danh sách đề cử trong các **Cuộc thi nấu ăn trực tuyến** của cộng đồng Foodball.</li>
                                <li>Có ít nhất **30 công thức nấu ăn chất lượng cao** (được trình bày chi tiết, khoa học và thẩm mỹ).</li>
                                <li>Đạt mốc **500 lượt thích tổng cộng** từ những thành viên khác trên các công thức đã chia sẻ.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-white/15 backdrop-blur-md border border-white/20 p-4 rounded-2xl text-center shrink-0 flex flex-col items-center justify-center min-w-[150px]">
                        <span class="text-xs font-bold uppercase tracking-wider text-yellow-200">Đạt thành tựu</span>
                        <span class="text-3xl font-black mt-1 font-mono">100%</span>
                        <span class="text-[10px] text-emerald-100 mt-1.5">Sẵn sàng xét duyệt</span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
                $currentRole = auth()->user()->role;
                $targetRoleName = $currentRole === 'beginner' ? 'Đầu bếp tại gia (Home Cook)' : 'Đầu bếp chuyên nghiệp (Pro Chef)';
                $targetRecipes = $currentRole === 'beginner' ? 3 : 10;
                $targetLikes = $currentRole === 'beginner' ? 5 : 20;
                
                $recipeProgress = min(100, ($myRecipesCount / $targetRecipes) * 100);
                $likeProgress = min(100, ($likesReceivedCount / $targetLikes) * 100);
                $overallProgress = ($recipeProgress + $likeProgress) / 2;
            ?>
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-6 rounded-2xl border border-orange-100 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-extrabold text-orange-850 flex items-center gap-1.5">
                            <span>🚀</span> Lộ trình thăng cấp tiếp theo: <span class="underline font-black"><?php echo e($targetRoleName); ?></span>
                        </h3>
                        <p class="text-xs text-orange-600 mt-0.5 font-medium">Viết thêm công thức chất lượng và thu hút lượt thích từ cộng đồng để thăng cấp.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black text-orange-700 font-mono bg-orange-100/50 px-2.5 py-1 rounded-lg"><?php echo e(round($overallProgress)); ?>% Hoàn thành</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recipe progress -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-bold text-gray-700">
                            <span>Đăng công thức (<?php echo e($myRecipesCount); ?> / <?php echo e($targetRecipes); ?>)</span>
                            <span class="font-mono"><?php echo e(round($recipeProgress)); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-orange-500 h-full rounded-full transition-all duration-500" style="width: <?php echo e($recipeProgress); ?>%"></div>
                        </div>
                    </div>
                    
                    <!-- Like progress -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-bold text-gray-700">
                            <span>Lượt thích nhận được (<?php echo e($likesReceivedCount); ?> / <?php echo e($targetLikes); ?>)</span>
                            <span class="font-mono"><?php echo e(round($likeProgress)); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: <?php echo e($likeProgress); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabs và Nội dung -->
        <div x-data="{ 
            tab: 'saved', 
            searchQuery: '',
            removeAccents(str) {
                if (!str) return '';
                return str.normalize('NFD')
                          .replace(/[\u0300-\u036f]/g, '')
                          .replace(/đ/g, 'd')
                          .replace(/Đ/g, 'd')
                          .toLowerCase();
            },
            hasVisibleSaved() {
                if (!this.searchQuery) return true;
                const query = this.removeAccents(this.searchQuery);
                const titles = [
                    <?php $__currentLoopData = $savedRecipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    this.removeAccents('<?php echo e(addslashes($r->title)); ?>'),
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ];
                return titles.some(t => t.includes(query));
            },
            hasVisibleMine() {
                if (!this.searchQuery) return true;
                const query = this.removeAccents(this.searchQuery);
                const titles = [
                    <?php $__currentLoopData = $myRecipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    this.removeAccents('<?php echo e(addslashes($r->title)); ?>'),
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ];
                return titles.some(t => t.includes(query));
            },
            unsaveRecipe(recipeId, event) {
                if(!confirm('Xác nhận bỏ lưu món ăn này khỏi thư viện?')) return;
                
                fetch(`/recipe/${recipeId}/save`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const btn = event.target.closest('button');
                        const card = btn ? btn.closest('.recipe-card') : null;
                        if (card) {
                            card.remove();
                            const countEl = document.getElementById('saved-count');
                            if (countEl) {
                                const current = parseInt(countEl.innerText);
                                countEl.innerText = Math.max(0, current - 1);
                            }
                        } else {
                            window.location.reload();
                        }
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    }
                })
                .catch(err => {
                    console.error(err);
                });
            },
            deleteRecipe(recipeId, event) {
                if(!confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa vĩnh viễn công thức này?')) return;
                
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
                        const card = event.target.closest('.recipe-card');
                        if (card) {
                            card.remove();
                            const countEl = document.getElementById('mine-count');
                            if (countEl) {
                                const current = parseInt(countEl.innerText);
                                countEl.innerText = Math.max(0, current - 1);
                            }
                        } else {
                            window.location.reload();
                        }
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else if (data.message) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'error' } }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                });
            }
        }" class="space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 pb-2">
                <div class="flex space-x-4">
                    <button @click="tab = 'saved'; searchQuery = ''" :class="tab === 'saved' ? 'border-orange-500 text-orange-500' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-2 py-4 font-bold border-b-2 transition">Đã Lưu</button>
                    <button @click="tab = 'mine'; searchQuery = ''" :class="tab === 'mine' ? 'border-orange-500 text-orange-500' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-2 py-4 font-bold border-b-2 transition">Món Của Tôi</button>
                </div>
                
                <!-- Search Input inside Kitchen -->
                <div class="relative w-full sm:w-72">
                    <input type="text" x-model="searchQuery" placeholder="Tìm kiếm món ăn..." 
                        class="w-full py-2 pl-9 pr-4 rounded-xl border border-gray-200 outline-none text-sm transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 bg-gray-50 focus:bg-white font-medium">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Tab: Đã Lưu -->
            <div x-show="tab === 'saved'" class="space-y-6">
                <div x-show="hasVisibleSaved()" class="grid md:grid-cols-3 gap-6">
                    <?php $__empty_1 = true; $__currentLoopData = $savedRecipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $savedRecipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div x-show="!searchQuery || removeAccents('<?php echo e(addslashes($savedRecipe->title)); ?>').includes(removeAccents(searchQuery))" 
                             class="recipe-card bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition group relative flex flex-col justify-between">
                            
                            <a href="<?php echo e(route('recipe.detail', $savedRecipe->slug)); ?>" class="block">
                                <div class="w-full h-40 overflow-hidden bg-gray-50 flex items-center justify-center relative">
                                    <?php if($savedRecipe->image): ?>
                                        <img src="<?php echo e($savedRecipe->image); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    <?php else: ?>
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <?php endif; ?>
                                    <span class="absolute top-2 left-2 bg-black/60 text-white text-xs font-bold px-2 py-1 rounded">🔖 Đã Lưu</span>

                                    <!-- Unsave Button -->
                                    <button @click.stop.prevent="unsaveRecipe(<?php echo e($savedRecipe->id); ?>, $event)" 
                                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold p-2 rounded-xl shadow-md transition-all duration-200 opacity-0 group-hover:opacity-100 focus:opacity-100" 
                                        title="Hủy lưu món ăn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-1 line-clamp-1 text-gray-900 group-hover:text-orange-500 transition"><?php echo e($savedRecipe->title); ?></h3>
                                    <p class="text-gray-500 text-sm">Bởi <?php echo e($savedRecipe->user->name); ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-full bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center p-8 text-center text-gray-500 min-h-[240px]">
                            <div class="text-4xl mb-3">🔖</div>
                            <p class="font-medium text-sm">Lưu thêm nhiều món ngon để dễ dàng tìm lại nhé.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Search empty result fallback -->
                <div x-show="!hasVisibleSaved()" style="display: none;" class="bg-gray-50 border border-gray-150 rounded-2xl flex flex-col items-center justify-center py-16 text-center w-full">
                    <div class="text-4xl mb-2">🔍</div>
                    <h4 class="text-base font-bold text-gray-950">Không tìm thấy món ăn nào phù hợp</h4>
                    <p class="text-xs text-gray-500 mt-1">Vui lòng thử tìm kiếm bằng từ khóa khác.</p>
                </div>
            </div>

            <!-- Tab: Món Của Tôi -->
            <div x-show="tab === 'mine'" style="display: none;" class="space-y-6">
                <div x-show="hasVisibleMine()" class="grid md:grid-cols-3 gap-6">
                    <?php $__empty_1 = true; $__currentLoopData = $myRecipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $myRecipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div x-show="!searchQuery || removeAccents('<?php echo e(addslashes($myRecipe->title)); ?>').includes(removeAccents(searchQuery))" 
                             class="recipe-card bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition group flex flex-col justify-between">
                            
                            <a href="<?php echo e(route('recipe.detail', $myRecipe->slug)); ?>" class="block">
                                <div class="w-full h-40 overflow-hidden bg-gray-50 flex items-center justify-center relative">
                                    <?php if($myRecipe->image): ?>
                                        <img src="<?php echo e($myRecipe->image); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    <?php else: ?>
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <?php endif; ?>
                                    <span class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded">🍳 Món Của Tôi</span>
                                </div>
                                <div class="p-4 pb-2">
                                    <h3 class="font-bold text-lg mb-1 line-clamp-1 text-gray-900 group-hover:text-orange-500 transition"><?php echo e($myRecipe->title); ?></h3>
                                    <p class="text-gray-500 text-sm">Bởi Bạn</p>
                                </div>
                            </a>

                            <!-- Actions footer inside card -->
                            <div class="px-4 pb-4 pt-2 flex items-center gap-2 border-t border-gray-50 bg-gray-50/50">
                                <a href="<?php echo e(route('recipe.edit', $myRecipe)); ?>" 
                                   class="flex-1 text-center py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-gray-900 text-xs font-bold rounded-lg transition-all border border-gray-200">
                                    Sửa món
                                </a>
                                <form action="<?php echo e(route('recipe.destroy', $myRecipe)); ?>" method="POST" class="m-0 flex-1" @submit.prevent="deleteRecipe(<?php echo e($myRecipe->id); ?>, $event)">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="w-full py-1.5 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 text-xs font-bold rounded-lg transition-all border border-red-100">
                                        Xóa món
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-full bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center py-20 text-center w-full">
                            <div class="text-6xl mb-4">🍳</div>
                            <h3 class="text-xl font-bold mb-2 text-gray-900">Chia sẻ công thức đầu tiên của bạn</h3>
                            <p class="text-gray-500 mb-6">Mọi người đang chờ đợi những món ngon từ bếp của bạn!</p>
                            <a href="<?php echo e(route('recipe.create')); ?>" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-6 rounded-full transition shadow-md shadow-orange-500/20">Viết Công Thức</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Search empty result fallback -->
                <div x-show="!hasVisibleMine()" style="display: none;" class="bg-gray-50 border border-gray-150 rounded-2xl flex flex-col items-center justify-center py-16 text-center w-full">
                    <div class="text-4xl mb-2">🔍</div>
                    <h4 class="text-base font-bold text-gray-950">Không tìm thấy món ăn nào phù hợp</h4>
                    <p class="text-xs text-gray-500 mt-1">Vui lòng thử tìm kiếm bằng từ khóa khác.</p>
                </div>
            </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\--main\resources\views/kitchen/index.blade.php ENDPATH**/ ?>