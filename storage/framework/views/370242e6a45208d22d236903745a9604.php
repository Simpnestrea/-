<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Foodball</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-800 flex h-screen overflow-hidden">

    <div class="hidden md:block md:w-1/2 lg:w-3/5 relative">
        <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&q=80&w=1200" alt="Food background" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
        <div class="absolute top-1/2 left-12 -translate-y-1/2 text-white max-w-lg">
            <h1 class="text-5xl font-black mb-4 leading-tight">Cùng nấu ăn,<br>cùng sẻ chia.</h1>
            <p class="text-lg font-medium opacity-90">Hàng ngàn công thức nấu ăn ngon đang chờ bạn khám phá và đóng góp.</p>
        </div>
    </div>

    <div class="w-full md:w-1/2 lg:w-2/5 flex flex-col justify-center px-8 sm:px-16 relative overflow-y-auto bg-white">
        <a href="<?php echo e(route('home')); ?>" class="absolute top-8 left-8 text-gray-400 hover:text-orange-500 transition flex items-center space-x-2 text-sm font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Trang chủ</span>
        </a>

        <div class="w-full max-w-md mx-auto mt-12 md:mt-0">
            <div class="flex items-center space-x-2 mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                    <line x1="6" y1="17" x2="18" y2="17"/>
                </svg>
                <span class="text-3xl font-black tracking-tighter text-gray-900 uppercase">Foodball</span>
            </div>

            <h2 class="text-2xl font-bold mb-2 text-gray-900">Chào mừng trở lại! 👋</h2>
            <p class="text-gray-500 mb-8 text-sm">Vui lòng đăng nhập vào tài khoản của bạn.</p>

            <form action="<?php echo e(route('login.post')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email hoặc Tên đăng nhập</label>
                    <input type="text" name="login_id" value="<?php echo e(old('login_id')); ?>" class="w-full px-4 py-3 rounded-lg border <?php $__errorArgs = ['login_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> outline-none transition" placeholder="Ví dụ: you@example.com hoặc your_username" required>
                    <?php $__errorArgs = ['login_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-bold text-gray-700">Mật khẩu</label>
                        <a href="#" class="text-sm text-orange-500 hover:underline font-semibold">Quên mật khẩu?</a>
                    </div>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition" placeholder="••••••••" required>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-lg hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                    Đăng Nhập
                </button>
            </form>

            <div class="relative flex py-5 items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink mx-4 text-gray-400 text-xs font-semibold uppercase">Hoặc tiếp tục bằng</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <a href="<?php echo e(route('social.redirect', ['provider' => 'google'])); ?>" class="flex items-center justify-center space-x-2 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 py-3 rounded-lg font-semibold text-gray-700 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M12.24 10.285V14.4h6.887c-.648 2.41-2.519 4.114-5.136 4.114A5.69 5.69 0 0 1 8.3 12.825a5.69 5.69 0 0 1 5.69-5.69c1.47 0 2.8.544 3.824 1.433l3.202-3.202C18.9 3.264 16.63 2 13.99 2 8.474 2 4 6.474 4 12s4.474 10 9.99 10c5.787 0 9.63-4.07 9.63-9.78 0-.663-.06-1.295-.17-1.935H12.24Z"/>
                    </svg>
                    <span>Google</span>
                </a>
                <a href="<?php echo e(route('social.redirect', ['provider' => 'facebook'])); ?>" class="flex items-center justify-center space-x-2 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 py-3 rounded-lg font-semibold text-gray-700 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </a>
            </div>

            <div class="mt-8 text-center text-sm text-gray-600">
                Bạn chưa có tài khoản? 
                <a href="<?php echo e(route('register')); ?>" class="text-orange-500 font-bold hover:underline">Tạo tài khoản mới</a>
            </div>
        </div>
    </div>

</body>
</html><?php /**PATH E:\--main\resources\views/login.blade.php ENDPATH**/ ?>