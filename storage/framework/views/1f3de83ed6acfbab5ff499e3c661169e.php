

<?php $__env->startSection('title', 'تفاصيل المصدر'); ?>

<?php $__env->startSection('header-actions'); ?>
    <div class="flex items-center gap-3">
        <a href="<?php echo e($source->website_url); ?>" target="_blank" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            زيارة الموقع
        </a>
        <a href="<?php echo e(route('admin.sources.edit', $source)); ?>" class="bg-gradient-to-l from-sky-600 to-sky-500 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/30 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            تعديل
        </a>
        <a href="<?php echo e(route('admin.sources.index')); ?>" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">
            رجوع
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Source Header -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
        <div class="flex items-start gap-6">
            <?php if($source->logo): ?>
                <img src="<?php echo e($source->logo); ?>" alt="<?php echo e($source->name_en); ?>" class="w-24 h-24 rounded-2xl object-cover">
            <?php else: ?>
                <div class="w-24 h-24 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl flex items-center justify-center text-white font-bold text-3xl">
                    <?php echo e(substr($source->name_en, 0, 1)); ?>

                </div>
            <?php endif; ?>
            
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-white"><?php echo e($source->name_ar); ?></h1>
                    <?php if($source->is_active): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">نشط</span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400">معطل</span>
                    <?php endif; ?>
                    <?php if($source->is_breaking_source): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">🔴 عاجل</span>
                    <?php endif; ?>
                </div>
                <p class="text-slate-400 text-lg mb-4"><?php echo e($source->name_en); ?></p>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    <?php if($source->country): ?>
                        <span><?php echo e($source->country->flag); ?> <?php echo e($source->country->name_ar); ?></span>
                    <?php endif; ?>
                    <?php if($source->category): ?>
                        <span>•</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: <?php echo e($source->category->color); ?>20; color: <?php echo e($source->category->color); ?>">
                            <?php echo e($source->category->name_ar); ?>

                        </span>
                    <?php endif; ?>
                    <span>•</span>
                    <span><?php echo e($source->language); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">إجمالي المقالات</div>
            <div class="text-3xl font-bold text-white"><?php echo e(number_format($source->articles_count)); ?></div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">آخر جلب</div>
            <div class="text-lg font-bold text-white">
                <?php if($source->last_fetched_at): ?>
                    <?php echo e($source->last_fetched_at->diffForHumans()); ?>

                <?php else: ?>
                    <span class="text-slate-500">-</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">فاصل الجلب</div>
            <div class="text-lg font-bold text-white"><?php echo e($source->fetch_interval_seconds); ?>s</div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">الحالة</div>
            <div class="text-lg font-bold">
                <?php if($source->is_active): ?>
                    <span class="text-emerald-400">✓ نشط</span>
                <?php else: ?>
                    <span class="text-red-400">✗ معطل</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- URLs -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">الروابط</h3>
        <div class="space-y-3">
            <div>
                <label class="block text-sm text-slate-400 mb-1">RSS Feed</label>
                <a href="<?php echo e($source->rss_url); ?>" target="_blank" class="text-sky-400 hover:text-sky-300 break-all">
                    <?php echo e($source->rss_url); ?>

                </a>
            </div>
            
            <?php if($source->website_url): ?>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">الموقع الإلكتروني</label>
                    <a href="<?php echo e($source->website_url); ?>" target="_blank" class="text-sky-400 hover:text-sky-300 break-all">
                        <?php echo e($source->website_url); ?>

                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Articles -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">أحدث المقالات</h3>
                <a href="<?php echo e(route('admin.articles.index', ['source_id' => $source->id])); ?>" class="text-sky-400 hover:text-sky-300 text-sm">
                    عرض الكل →
                </a>
            </div>
        </div>
        
        <div class="divide-y divide-slate-700/50">
            <?php $__empty_1 = true; $__currentLoopData = $recentArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 hover:bg-slate-800/30 transition-colors">
                    <div class="flex gap-4">
                        <?php if($article->image_url): ?>
                            <img src="<?php echo e($article->image_url); ?>" alt="" class="w-20 h-16 object-cover rounded-lg flex-shrink-0">
                        <?php else: ?>
                            <div class="w-20 h-16 bg-slate-700 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex-1 min-w-0">
                            <a href="<?php echo e(route('admin.articles.show', $article)); ?>" class="font-medium text-white hover:text-sky-400 line-clamp-2 mb-1 block">
                                <?php echo e($article->title); ?>

                            </a>
                            <div class="flex items-center gap-2 text-sm text-slate-400">
                                <span><?php echo e($article->published_at?->diffForHumans()); ?></span>
                                <?php if($article->is_breaking): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">عاجل</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 text-center text-slate-400">
                    لا توجد مقالات من هذا المصدر
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">إجراءات سريعة</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <form action="<?php echo e(route('admin.sources.fetch-now', $source)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-sky-600 hover:bg-sky-500 rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    جلب الأخبار الآن
                </button>
            </form>
            
            <form action="<?php echo e(route('admin.sources.toggle-active', $source)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 <?php echo e($source->is_active ? 'bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30' : 'bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30'); ?> rounded-xl transition-all duration-200">
                    <?php if($source->is_active): ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        تعطيل المصدر
                    <?php else: ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        تفعيل المصدر
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Newsly\backend\resources\views/admin/sources/show.blade.php ENDPATH**/ ?>