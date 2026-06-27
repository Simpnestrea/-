<?php $__env->startSection('title', 'Quản lý Tố cáo - Foodball'); ?>
<?php $__env->startSection('page_title', 'Quản lý Tố cáo'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="reportsManager()">

    <!-- Search and Actions Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form action="<?php echo e(route('admin.reports')); ?>" method="GET" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="query" value="<?php echo e($query ?? ''); ?>" placeholder="Tìm theo lý do, người tố cáo, nội dung, tác giả..." 
                    class="w-full py-2.5 pl-10 pr-4 rounded-xl border border-slate-200 outline-none text-sm transition-all focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10"
                    oninput="clearTimeout(window.searchTimeout); window.searchTimeout = setTimeout(() => { this.form.submit(); }, 600)"
                    <?php echo e(($query ?? '') ? 'autofocus' : ''); ?>

                    onfocus="var val=this.value; this.value=''; this.value=val;">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <?php if($query ?? ''): ?>
                    <a href="<?php echo e(route('admin.reports')); ?>" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 hover:text-slate-600">Xóa tìm kiếm</a>
                <?php endif; ?>
            </div>
        </form>
        
        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider shrink-0 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
            Hiển thị <?php echo e($reports->firstItem() ?? 0); ?> - <?php echo e($reports->lastItem() ?? 0); ?> / <?php echo e($reports->total()); ?> báo cáo
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Người tố cáo</th>
                        <th class="px-6 py-4">Bình luận bị báo cáo</th>
                        <th class="px-6 py-4">Tác giả bình luận</th>
                        <th class="px-6 py-4">Lý do vi phạm</th>
                        <th class="px-6 py-4">Từ công thức</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/30 transition-all duration-200" id="report-row-<?php echo e($report->id); ?>">
                            
                            <!-- Reporter Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800"><?php echo e($report->user->name ?? 'Ẩn danh'); ?></div>
                                <div class="text-[10px] text-slate-400">Gửi: <?php echo e($report->created_at ? $report->created_at->diffForHumans() : 'Không rõ'); ?></div>
                            </td>

                            <!-- Comment Content -->
                            <td class="px-6 py-4">
                                <?php if($report->comment): ?>
                                    <div class="text-slate-700 font-medium italic max-w-sm line-clamp-2" title="<?php echo e($report->comment->content); ?>">
                                        "<?php echo e($report->comment->content); ?>"
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">Đăng ngày: <?php echo e($report->comment->created_at ? $report->comment->created_at->format('d/m/Y H:i') : ''); ?></div>
                                <?php else: ?>
                                    <span class="text-red-500 italic text-xs">Bình luận này đã bị xóa trước đó</span>
                                <?php endif; ?>
                            </td>

                            <!-- Comment Author -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($report->comment && $report->comment->user): ?>
                                    <div class="font-bold text-slate-800"><?php echo e($report->comment->user->name); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo e($report->comment->user->email); ?></div>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Không rõ tác giả</span>
                                <?php endif; ?>
                            </td>

                            <!-- Reason -->
                            <td class="px-6 py-4">
                                <span class="bg-red-50 text-red-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-red-100 inline-block">
                                    <?php echo e($report->reason); ?>

                                </span>
                            </td>

                            <!-- Recipe Name -->
                            <td class="px-6 py-4">
                                <?php if($report->comment && $report->comment->recipe): ?>
                                    <a href="<?php echo e(route('recipe.detail', $report->comment->recipe->slug)); ?>" target="_blank" class="text-orange-500 font-semibold hover:underline line-clamp-1 max-w-[150px]">
                                        <?php echo e($report->comment->recipe->title); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400 italic text-xs">Công thức đã bị xóa</span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    
                                    <!-- Dismiss Report Form -->
                                    <button type="button" @click="dismiss(<?php echo e($report->id); ?>)" class="bg-white hover:bg-slate-50 text-slate-650 hover:text-slate-800 border border-slate-200 font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                        Bỏ qua báo cáo
                                    </button>

                                    <!-- Delete Comment Form -->
                                    <?php if($report->comment): ?>
                                        <button type="button" @click="deleteComment(<?php echo e($report->id); ?>)" class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-md shadow-red-500/10">
                                            Xóa bình luận vi phạm
                                        </button>
                                    <?php else: ?>
                                        <!-- Clean up orphan report if comment was already deleted -->
                                        <button type="button" @click="dismiss(<?php echo e($report->id); ?>)" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                            Dọn dẹp bản ghi
                                        </button>
                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm font-semibold">Tuyệt vời! Hiện tại không có báo cáo vi phạm nào chưa xử lý.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($reports->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                <?php echo e($reports->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function reportsManager() {
        return {
            dismiss(reportId) {
                if(!confirm('Bạn có chắc chắn muốn BỎ QUA báo cáo tố cáo này?')) return;
                fetch(`/admin/reports/${reportId}/dismiss`, {
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
                        const row = document.getElementById(`report-row-${reportId}`);
                        if (row) row.remove();
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                    }
                })
                .catch(err => {
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                });
            },
            deleteComment(reportId) {
                if(!confirm('Bạn có chắc chắn muốn XÓA bình luận này khỏi hệ thống không?')) return;
                fetch(`/admin/reports/${reportId}/comment`, {
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
                        const row = document.getElementById(`report-row-${reportId}`);
                        if (row) row.remove();
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\--main\resources\views/admin/reports.blade.php ENDPATH**/ ?>