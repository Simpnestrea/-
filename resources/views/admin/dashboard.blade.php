@extends('layouts.admin')

@section('title', 'Tổng quan Quản trị - Foodball')
@section('page_title', 'Tổng quan Quản trị')

@section('content')
<div class="space-y-6" x-data="dashboardGreeting()">

    <!-- Premium Greeting Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-orange-950 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Floating shapes -->
        <div class="absolute right-0 top-0 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
        <div class="absolute right-1/4 bottom-0 w-60 h-60 bg-amber-500/5 rounded-full blur-3xl -mb-20"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-1 bg-white/10 px-3 py-1 rounded-full text-xs font-semibold text-orange-300">
                    <span>⚡</span> <span>Hệ thống hoạt động ổn định</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight">
                    <span x-text="greeting"></span>, <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-300">{{ auth()->user()->name }}</span>!
                </h2>
                <p class="text-slate-300 text-xs md:text-sm max-w-xl font-medium">
                    Chào mừng trở lại bảng điều khiển Foodball. Hệ thống của bạn đang quản lý <span class="text-white font-bold">{{ $stats['total_users'] }}</span> thành viên và đã có <span class="text-white font-bold">{{ number_format($stats['total_revenue'], 0, ',', '.') }} đ</span> doanh thu từ bán công thức.
                </p>
            </div>
            <div class="text-left md:text-right shrink-0 bg-white/5 border border-white/10 p-4 rounded-2xl backdrop-blur-md">
                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Thời gian máy chủ</span>
                <span class="text-xl md:text-2xl font-black text-white block font-mono" x-text="timeString"></span>
                <span class="text-xs font-semibold text-orange-400 block" x-text="dateString"></span>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1: Users -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-200">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Thành viên</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-slate-900 font-mono">{{ $stats['total_users'] }}</span>
                </div>
                <p class="text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-100 rounded-full px-2 py-0.5 inline-block">
                    👑 {{ $stats['premium_users'] }} Premium
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <!-- Card 2: Recipes -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-200">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Công thức món ăn</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-slate-900 font-mono">{{ $stats['total_recipes'] }}</span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium">Trung bình {{ $stats['total_users'] > 0 ? round($stats['total_recipes'] / $stats['total_users'], 1) : 0 }} món/thành viên</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>

        <!-- Card 3: Revenue -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-200">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Doanh thu mua công thức</p>
                <div class="flex items-baseline space-x-1">
                    <span class="text-2xl font-black text-slate-900 font-mono">{{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-slate-400">đ</span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium">Tổng tiền ví hệ thống: {{ number_format($stats['total_wallet_balance'], 0, ',', '.') }} đ</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1M10 11h4"></path></svg>
            </div>
        </div>

        <!-- Card 4: Reports -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition duration-200">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tố cáo bình luận</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-slate-900 font-mono">{{ $stats['total_reports'] }}</span>
                </div>
                @if($stats['total_reports'] > 0)
                    <p class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 rounded-full px-2 py-0.5 inline-block animate-pulse">
                        ⚠️ Cần xử lý gấp!
                    </p>
                @else
                    <p class="text-[10px] text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5 inline-block font-semibold">
                        ✓ Đã sạch sẽ
                    </p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>

    </div>

    <!-- Charts Grid Section -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Chart 1: User Growth (Line Chart) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    Thống kê thành viên mới (15 ngày qua)
                </h3>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Premium Ratio -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Gói tài khoản
                </h3>
            </div>
            <div class="h-64 w-full relative flex items-center justify-center">
                <canvas id="userRatioChart"></canvas>
            </div>
        </div>

        <!-- Chart 3: Culinary Roles Distribution -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    Cấp bậc ẩm thực
                </h3>
            </div>
            <div class="h-64 w-full relative flex items-center justify-center">
                <canvas id="userRolesChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Middle Content Grid: Top Recipes and Recent Users -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Column 1 & 2: Top Recipes (takes 2 cols on lg) -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden lg:col-span-2 flex flex-col justify-between">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <span>🔥</span> Món ăn xem nhiều nhất
                </h3>
                <a href="{{ route('admin.recipes') }}" class="text-xs font-bold text-orange-500 hover:text-orange-600 transition flex items-center gap-0.5">
                    Xem tất cả <span>→</span>
                </a>
            </div>
            <div class="divide-y divide-slate-50 flex-1">
                @forelse($topRecipes as $recipe)
                    <div class="px-6 py-4 flex items-center space-x-4 hover:bg-slate-50/50 transition">
                        <img src="{{ $recipe->image ?? 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=120' }}" class="w-12 h-12 rounded-xl object-cover bg-slate-100 border border-slate-200/60 shrink-0 shadow-sm">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-slate-800 truncate">{{ $recipe->title }}</h4>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                <span>Tác giả: <strong class="text-slate-700 font-semibold">{{ $recipe->user->name ?? 'Ẩn danh' }}</strong></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>Danh mục: <strong class="text-slate-700 font-semibold">{{ $recipe->category->name ?? 'Không rõ' }}</strong></span>
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-lg font-black text-slate-900 font-mono">{{ number_format($recipe->views_count) }}</span>
                            <span class="text-[9px] block text-slate-400 font-bold tracking-wider">LƯỢT XEM</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Chưa có công thức nấu ăn nào.</div>
                @endforelse
            </div>
        </div>

        <!-- Column 3: Category Distribution -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <span>📂</span> Phân bổ công thức theo Danh mục
                </h3>
            </div>
            <div class="p-6 flex-1 flex flex-col justify-center">
                <div class="h-44 w-full relative mb-4">
                    <canvas id="categoryDistributionChart"></canvas>
                </div>
                <div class="text-[10px] text-slate-400 text-center font-medium">Chỉ thống kê 6 danh mục lớn nhất</div>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Latest Comment Reports -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-data="dismissibleReports()">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <span>🚨</span> Báo cáo tố cáo vi phạm mới nhất
            </h3>
            <a href="{{ route('admin.reports') }}" class="text-xs font-bold text-orange-500 hover:text-orange-600 transition flex items-center gap-0.5">
                Xem tất cả <span>→</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-3.5">Người báo cáo</th>
                        <th class="px-6 py-3.5">Bình luận bị báo cáo</th>
                        <th class="px-6 py-3.5">Lý do</th>
                        <th class="px-6 py-3.5">Bài viết</th>
                        <th class="px-6 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentReports as $report)
                        <tr class="hover:bg-slate-50/30 transition-all duration-200" id="report-row-{{ $report->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $report->user->name ?? 'Thành viên ẩn danh' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $report->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($report->comment)
                                    <div class="text-slate-700 max-w-xs truncate font-medium" title="{{ $report->comment->content }}">"{{ $report->comment->content }}"</div>
                                    <div class="text-[10px] text-slate-400">Tác giả: {{ $report->comment->user->name ?? 'Không rõ' }}</div>
                                @else
                                    <span class="text-red-500 italic text-xs">Bình luận đã bị xóa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-red-50 text-red-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-red-100">
                                    {{ $report->reason }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->comment && $report->comment->recipe)
                                    <a href="{{ route('recipe.detail', $report->comment->recipe->slug) }}" target="_blank" class="text-orange-500 font-semibold hover:underline block max-w-[150px] truncate">
                                        {{ $report->comment->recipe->title }}
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="dismiss({{ $report->id }})" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                        Bỏ qua
                                    </button>
                                    @if($report->comment)
                                        <button @click="deleteComment({{ $report->id }})" class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition">
                                            Xóa bình luận
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 text-sm">Chưa có báo cáo vi phạm nào gần đây.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function dashboardGreeting() {
        return {
            greeting: 'Chào bạn',
            timeString: '00:00:00',
            dateString: '',
            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
            },
            updateTime() {
                const now = new Date();
                const hours = now.getHours();
                
                if (hours < 12) {
                    this.greeting = 'Chào buổi sáng';
                } else if (hours < 18) {
                    this.greeting = 'Chào buổi chiều';
                } else {
                    this.greeting = 'Chào buổi tối';
                }
                
                this.timeString = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                this.dateString = now.toLocaleDateString('vi-VN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }
        }
    }

    function dismissibleReports() {
        return {
            dismiss(reportId) {
                if(!confirm('Xác nhận bỏ qua báo cáo này?')) return;
                fetch(`/admin/reports/${reportId}/dismiss`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const row = document.getElementById(`report-row-${reportId}`);
                        if(row) row.remove();
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                    }
                });
            },
            deleteComment(reportId) {
                if(!confirm('Xác nhận xóa bình luận vi phạm này?')) return;
                fetch(`/admin/reports/${reportId}/comment`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const row = document.getElementById(`report-row-${reportId}`);
                        if(row) row.remove();
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'success' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Có lỗi xảy ra', type: 'error' } }));
                    }
                });
            }
        }
    }

    // Chart.js Initializations
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. User Growth Chart
        const ctxUser = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthGradient = ctxUser.createLinearGradient(0, 0, 0, 200);
        userGrowthGradient.addColorStop(0, 'rgba(249, 115, 22, 0.4)');
        userGrowthGradient.addColorStop(1, 'rgba(249, 115, 22, 0)');

        new Chart(ctxUser, {
            type: 'line',
            data: {
                labels: @json($chartUserGrowth['labels']),
                datasets: [{
                    label: 'Thành viên mới',
                    data: @json($chartUserGrowth['data']),
                    borderColor: '#f97316',
                    borderWidth: 3,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: userGrowthGradient
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'semibold' }, color: '#64748b' }
                    },
                    y: {
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b', stepSize: 1 }
                    }
                }
            }
        });

        // 2. User Ratio Chart (Doughnut representing Free/Premium ratio)
        const ctxRatio = document.getElementById('userRatioChart').getContext('2d');
        new Chart(ctxRatio, {
            type: 'doughnut',
            data: {
                labels: ['Thành viên Premium', 'Thành viên Thường'],
                datasets: [{
                    data: [@json($userRatio['premium']), @json($userRatio['free'])],
                    backgroundColor: ['#eab308', '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: 'bold' },
                            padding: 15,
                            color: '#334155'
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // 2.2 User Culinary Roles Chart (Doughnut representing beginner, homecook, prochef, masterchef)
        const ctxRoles = document.getElementById('userRolesChart').getContext('2d');
        new Chart(ctxRoles, {
            type: 'doughnut',
            data: {
                labels: ['Người mới', 'Đầu bếp tại gia', 'Chuyên nghiệp', 'Siêu đầu bếp'],
                datasets: [{
                    data: [
                        @json($roleStats['beginner'] ?? 0),
                        @json($roleStats['homecook'] ?? 0),
                        @json($roleStats['prochef'] ?? 0),
                        @json($roleStats['masterchef'] ?? 0)
                    ],
                    backgroundColor: ['#94a3b8', '#3b82f6', '#10b981', '#f97316'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: 'bold' },
                            padding: 15,
                            color: '#334155'
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // 3. Category Distribution Chart (Bar Chart or Doughnut)
        const ctxCat = document.getElementById('categoryDistributionChart').getContext('2d');
        const categories = @json($categoriesData);
        const catLabels = categories.map(c => c.name);
        const catCounts = categories.map(c => c.count);

        new Chart(ctxCat, {
            type: 'polarArea',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catCounts,
                    backgroundColor: [
                        'rgba(249, 115, 22, 0.75)',
                        'rgba(234, 179, 8, 0.75)',
                        'rgba(59, 130, 246, 0.75)',
                        'rgba(16, 185, 129, 0.75)',
                        'rgba(168, 85, 247, 0.75)',
                        'rgba(236, 72, 153, 0.75)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        grid: { color: '#f1f5f9' },
                        ticks: { display: false }
                    }
                }
            }
        });

    });
</script>
@endpush

