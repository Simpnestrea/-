<?php $__env->startSection('title', 'Thống Kê Bếp - Foodball'); ?>
<?php $__env->startSection('header_title', 'Thống Kê Bếp'); ?>

<?php $__env->startSection('content'); ?>
    <div class="flex-1 max-w-5xl mx-auto w-full px-6 py-12 space-y-8 relative">
        <!-- Nút quay lại -->
        <div>
            <button onclick="history.back()" class="inline-flex items-center space-x-2 text-gray-500 hover:text-orange-500 font-bold transition bg-white border border-gray-200 hover:bg-orange-50 px-4 py-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại</span>
            </button>
        </div>
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-gray-900">Thống Kê Bếp 📊</h1>
            <select onchange="window.location.href = '?period=' + this.value" class="bg-white border border-gray-200 rounded-xl px-4 py-2 font-medium text-gray-600 outline-none focus:border-orange-500 cursor-pointer shadow-sm">
                <option value="30" <?php echo e($period === '30' ? 'selected' : ''); ?>>30 ngày qua</option>
                <option value="7" <?php echo e($period === '7' ? 'selected' : ''); ?>>7 ngày qua</option>
                <option value="all" <?php echo e($period === 'all' ? 'selected' : ''); ?>>Tất cả thời gian</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition">
                <div class="w-14 h-14 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center text-2xl">👁️</div>
                <div>
                    <div class="text-3xl font-black"><?php echo e(number_format($totalViews)); ?></div>
                    <div class="text-sm font-semibold text-gray-500">Lượt Xem Công Thức</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition">
                <div class="w-14 h-14 bg-red-100 text-red-500 rounded-full flex items-center justify-center text-2xl">❤️</div>
                <div>
                    <div class="text-3xl font-black"><?php echo e(number_format($likesCount)); ?></div>
                    <div class="text-sm font-semibold text-gray-500">Lượt Thích Mới</div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 hover:shadow-md transition">
                <div class="w-14 h-14 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-2xl">🔖</div>
                <div>
                    <div class="text-3xl font-black"><?php echo e(number_format($savesCount)); ?></div>
                    <div class="text-sm font-semibold text-gray-500">Lượt Lưu Công Thức</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mt-8">
            <h2 class="text-xl font-bold mb-6">Món Ăn Nổi Bật Của Bạn</h2>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $topRecipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-orange-50/50 transition">
                        <div class="flex items-center space-x-4">
                            <?php if($recipe->image): ?>
                                <img src="<?php echo e(str_contains($recipe->image, 'http') ? $recipe->image : Storage::url($recipe->image)); ?>" class="w-12 h-12 rounded-lg object-cover shadow-sm">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                                    <?php echo e(substr($recipe->title, 0, 1)); ?>

                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="<?php echo e(route('recipe.detail', $recipe->slug)); ?>" class="font-bold text-gray-900 hover:text-orange-500 transition block">
                                    <?php echo e($recipe->title); ?>

                                </a>
                                <p class="text-sm text-gray-500">Đăng ngày <?php echo e($recipe->created_at->format('d/m/Y')); ?> • <?php echo e(number_format($recipe->views_count)); ?> lượt xem</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-orange-600 flex items-center justify-end gap-1.5 text-sm">
                                <span>+<?php echo e($recipe->likes_in_period); ?> ❤️</span>
                                <span>+<?php echo e($recipe->saves_in_period); ?> 🔖</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                <?php if($period === '7'): ?>
                                    trong 7 ngày qua
                                <?php elseif($period === '30'): ?>
                                    trong 30 ngày qua
                                <?php else: ?>
                                    tất cả thời gian
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-gray-500 italic bg-gray-50 rounded-xl">
                        Bạn chưa viết công thức nấu ăn nào. Hãy tạo món đầu tiên để theo dõi số liệu thống kê!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\--main\resources\views/stats.blade.php ENDPATH**/ ?>