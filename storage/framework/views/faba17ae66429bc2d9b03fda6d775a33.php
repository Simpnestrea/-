<?php $__env->startSection('title', 'Ví của tôi - Foodball'); ?>
<?php $__env->startSection('header_title', 'Ví của tôi'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex-1 w-full max-w-7xl mx-auto p-6 space-y-8 bg-white shadow-sm border border-gray-100 rounded-2xl my-6 mx-auto relative">
    
    <!-- Thông báo kết quả -->
    <?php if(session('success')): ?>
        <div class="p-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-200" role="alert">
            <span class="font-bold">Thành công!</span> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="p-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200" role="alert">
            <span class="font-bold">Lỗi!</span> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="p-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-200" role="alert">
            <span class="font-bold">Lỗi!</span> <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Cột 1: Thẻ số dư & Nạp tiền (Chiếm 1/3) -->
        <div class="space-y-6">
            <!-- Thẻ số dư phong cách Premium -->
            <div class="bg-gradient-to-br from-orange-500 via-amber-500 to-yellow-500 text-white rounded-3xl p-6 shadow-xl shadow-orange-500/20 relative overflow-hidden h-52 flex flex-col justify-between">
                <!-- Họa tiết trang trí nền -->
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                
                <div class="flex justify-between items-start z-10">
                    <span class="text-xs font-black tracking-widest uppercase opacity-80">Foodball Member Card</span>
                    <span class="text-xl">👑</span>
                </div>
                
                <div class="z-10">
                    <span class="text-xs opacity-75 font-semibold">Số dư khả dụng</span>
                    <h2 class="text-3xl font-black mt-1 font-mono tracking-tight"><?php echo e(number_format($user->balance, 0, ',', '.')); ?> đ</h2>
                </div>
                
                <div class="flex justify-between items-end z-10">
                    <div>
                        <span class="text-[10px] opacity-75 uppercase block leading-none">Chủ ví</span>
                        <span class="text-sm font-bold tracking-wide"><?php echo e($user->name); ?></span>
                    </div>
                    <div class="w-10 h-6 bg-white/20 rounded-md backdrop-blur-sm flex items-center justify-center text-[10px] font-black uppercase">
                        VND
                    </div>
                </div>
            </div>

            <!-- Widget Nạp tiền ảo -->
            <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6 space-y-4 relative" x-data="{ step: 'input', amount: 100000, loading: false }">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Nạp tiền vào ví (Thử nghiệm)</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Mô phỏng nạp tiền ảo để kiểm tra chức năng mua công thức nấu ăn.</p>
                </div>

                <!-- BƯỚC 1: Nhập số tiền (Luôn hiển thị) -->
                <div class="space-y-4">
                    <!-- Nhập số tiền -->
                    <div class="space-y-2">
                        <label for="custom_amount" class="text-xs font-bold text-gray-500">Số tiền nạp (VND)</label>
                        <div class="relative flex items-center">
                            <input type="number" id="custom_amount" x-model.number="amount" min="10000" max="10000000" step="10000" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 outline-none focus:border-orange-500 font-bold text-gray-900 pr-12 text-sm" required>
                            <span class="absolute right-4 font-bold text-xs text-gray-400">VND</span>
                        </div>
                    </div>

                    <!-- Các mốc nạp nhanh -->
                    <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                        <button type="button" @click="amount = 50000" class="border border-gray-300 hover:border-orange-500 hover:bg-orange-50 text-gray-700 hover:text-orange-600 py-2.5 rounded-xl transition">
                            + 50.000 đ
                        </button>
                        <button type="button" @click="amount = 100000" class="border border-gray-300 hover:border-orange-500 hover:bg-orange-50 text-gray-700 hover:text-orange-600 py-2.5 rounded-xl transition">
                            + 100.000 đ
                        </button>
                        <button type="button" @click="amount = 200000" class="border border-gray-300 hover:border-orange-500 hover:bg-orange-50 text-gray-700 hover:text-orange-600 py-2.5 rounded-xl transition">
                            + 200.000 đ
                        </button>
                        <button type="button" @click="amount = 500000" class="border border-gray-300 hover:border-orange-500 hover:bg-orange-50 text-gray-700 hover:text-orange-600 py-2.5 rounded-xl transition">
                            + 500.000 đ
                        </button>
                    </div>

                    <button type="button" @click="step = 'qr'" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-black/10 text-sm">
                        Xác nhận nạp tiền
                    </button>
                </div>

                <!-- MODAL POPUP: BƯỚC 2 & BƯỚC 3 -->
                <div x-show="step !== 'input'" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                    <!-- Modal Content -->
                    <div @click.away="if(!loading && step === 'qr') step = 'input'" class="bg-white rounded-3xl p-8 max-w-sm w-full space-y-4 relative shadow-2xl overflow-hidden">
                        
                        <!-- BƯỚC 2: Quét mã QR -->
                        <div x-show="step === 'qr'" x-transition class="space-y-4 text-center">
                            <p class="text-sm font-bold text-gray-800">Quét mã QR để thanh toán <br> <span class="text-orange-600 text-xl" x-text="new Intl.NumberFormat('vi-VN').format(amount) + ' đ'"></span></p>
                            
                            <div class="relative w-48 h-48 mx-auto rounded-xl overflow-hidden border border-gray-200 bg-white flex items-center justify-center shadow-sm p-2">
                                <!-- Hiển thị QR. -->
                                <img src="<?php echo e(asset('qr.jpg')); ?>" alt="QR Code" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Demo';">
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" @click="step = 'input'" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition text-sm" :disabled="loading">
                                    Hủy bỏ
                                </button>
                                <button type="button" @click="
                                    loading = true; 
                                    fetch('<?php echo e(route('wallet.deposit')); ?>', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                        },
                                        body: JSON.stringify({ amount: amount })
                                    }).then(() => {
                                        setTimeout(() => { loading = false; step = 'success'; }, 1000);
                                    }).catch(() => {
                                        loading = false;
                                        alert('Có lỗi xảy ra, vui lòng thử lại.');
                                    })
                                " class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-orange-500/30 text-sm" :disabled="loading">
                                    Xác nhận
                                </button>
                            </div>
                        </div>

                        <!-- BƯỚC 3: Thành công -->
                        <div x-show="step === 'success'" x-transition class="space-y-4 text-center py-4" style="display: none;">
                            <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-xl">Giao dịch thành công!</h3>
                            <p class="text-sm text-gray-500">Đã nạp <span class="font-bold text-gray-900" x-text="new Intl.NumberFormat('vi-VN').format(amount) + ' đ'"></span> vào ví của bạn.</p>
                            
                            <button type="button" @click="window.location.reload()" class="w-full mt-6 border-2 border-gray-200 hover:border-gray-300 text-gray-800 font-bold py-3 rounded-xl transition text-sm">
                                Đóng
                            </button>
                        </div>

                        <!-- Màn hình loading che lên (Fake loading) -->
                        <div x-show="loading" x-transition class="absolute inset-0 bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center z-20 m-0" style="display: none;">
                            <div class="w-10 h-10 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                            <p class="mt-3 text-sm font-bold text-gray-700">Đang tra cứu giao dịch...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột 2 & 3: Lịch sử giao dịch (Chiếm 2/3) -->
        <div class="lg:col-span-2 space-y-6" x-data="{ tab: 'purchases' }">
            <!-- Tabs Menu -->
            <div class="flex border-b border-gray-200">
                <button @click="tab = 'purchases'" :class="tab === 'purchases' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="flex-1 text-center py-4 border-b-2 font-bold text-sm transition">
                    🛍️ Lịch sử mua hàng (<?php echo e($purchases->count()); ?>)
                </button>
                <button @click="tab = 'earnings'" :class="tab === 'earnings' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="flex-1 text-center py-4 border-b-2 font-bold text-sm transition">
                    📈 Lịch sử thu nhập (<?php echo e($earnings->count()); ?>)
                </button>
            </div>

            <!-- Tab Content 1: Lịch sử mua hàng -->
            <div x-show="tab === 'purchases'" class="space-y-4" x-transition>
                <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Tên công thức</th>
                                <th class="px-6 py-4">Tác giả</th>
                                <th class="px-6 py-4 text-right">Chi phí</th>
                                <th class="px-6 py-4 text-right">Ngày mua / Hạn mở</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 text-sm text-gray-600">
                            <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        <div class="flex flex-col">
                                            <a href="<?php echo e(route('recipe.detail', $item->slug)); ?>" class="hover:text-orange-500 transition">
                                                <?php echo e($item->title); ?>

                                            </a>
                                            <?php if($item->pivot->price == 0): ?>
                                                <?php if(\Carbon\Carbon::parse($item->pivot->created_at)->addHours(24)->isPast()): ?>
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md mt-1 w-max">
                                                        ⏳ Đã hết hạn (24h)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md mt-1 w-max border border-emerald-250/50">
                                                        ⚡ Đang mở khóa (Ad)
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md mt-1 w-max border border-amber-250/50">
                                                    👑 Đã mua vĩnh viễn
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-gray-500">
                                        <?php echo e($item->user->name ?? 'Không rõ'); ?>

                                    </td>
                                    <td class="px-6 py-4 font-bold text-right text-red-500 font-mono">
                                        <?php if($item->pivot->price > 0): ?>
                                            -<?php echo e(number_format($item->pivot->price, 0, ',', '.')); ?> đ
                                        <?php else: ?>
                                            <span class="text-gray-450 font-bold text-xs">Mở bằng Ad 📺</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs font-medium">
                                        <?php if($item->pivot->price > 0): ?>
                                            <span class="text-amber-500 font-bold">♾️ Vĩnh viễn</span>
                                        <?php else: ?>
                                            <span class="text-gray-400">
                                                Hết hạn: <?php echo e(\Carbon\Carbon::parse($item->pivot->created_at)->addHours(24)->format('H:i d/m/Y')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                        Bạn chưa mua công thức nấu ăn nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content 2: Lịch sử thu nhập -->
            <div x-show="tab === 'earnings'" class="space-y-4" x-transition style="display: none;">
                <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Tên công thức</th>
                                <th class="px-6 py-4">Người mua</th>
                                <th class="px-6 py-4 text-right">Doanh thu</th>
                                <th class="px-6 py-4 text-right">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 text-sm text-gray-600">
                            <?php $__empty_1 = true; $__currentLoopData = $earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        <a href="<?php echo e(route('recipe.detail', $item->recipe_slug)); ?>" class="hover:text-orange-500 transition">
                                            <?php echo e($item->recipe_title); ?>

                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-gray-500">
                                        <?php echo e($item->buyer_name); ?>

                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <?php
                                            $taxRate = $user->is_premium ? 0.01 : 0.05;
                                            $taxAmount = $item->price * $taxRate;
                                            $netEarnings = $item->price - $taxAmount;
                                        ?>
                                        <div class="flex flex-col items-end">
                                            <span class="font-bold text-emerald-600 font-mono">
                                                +<?php echo e(number_format($netEarnings, 0, ',', '.')); ?> đ
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-semibold mt-0.5">
                                                (Đã trừ <?php echo e($taxRate * 100); ?>% thuế: <?php echo e(number_format($taxAmount, 0, ',', '.')); ?> đ)
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs text-gray-400 font-medium">
                                        <?php echo e(\Carbon\Carbon::parse($item->created_at)->format('H:i d/m/Y')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                        Chưa có thành viên nào mua công thức nấu ăn của bạn.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\--main\resources\views/wallet/index.blade.php ENDPATH**/ ?>