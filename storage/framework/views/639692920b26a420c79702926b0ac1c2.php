<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Newsly Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'tajawal': ['Tajawal', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen flex items-center justify-center p-4">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23334155\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="relative w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-2xl shadow-lg shadow-sky-500/30 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Newsly</h1>
            <p class="text-slate-400 mt-2">لوحة تحكم المدير</p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-slate-800/50 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
            <h2 class="text-xl font-bold text-white mb-6">تسجيل الدخول</h2>
            
            <?php if(isset($errors) && $errors->any()): ?>
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-xl text-red-400 text-sm">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
            
            <form action="<?php echo e(route('admin.login.submit')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-300 mb-2">البريد الإلكتروني</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="<?php echo e(old('email')); ?>"
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all"
                        placeholder="admin@newsly.app"
                        required
                    >
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-300 mb-2">كلمة المرور</label>
                    <input 
                        type="password" 
                        name="password"
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all"
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 text-slate-400 text-sm cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-sky-500 focus:ring-sky-500/20">
                        <span>تذكرني</span>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-l from-sky-600 to-sky-500 text-white py-3 rounded-xl font-medium transition-all hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5">
                    دخول
                </button>
            </form>
        </div>
        
        <p class="text-center text-slate-500 text-sm mt-6">
            Newsly Admin Panel &copy; <?php echo e(date('Y')); ?>

        </p>
    </div>
</body>
</html>



<?php /**PATH D:\Newsly\backend\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>