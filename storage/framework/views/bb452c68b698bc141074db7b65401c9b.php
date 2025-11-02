<?php $__env->startSection("title", __('common.subscription')); ?>
<?php $__env->startSection("page-title", __('common.subscription')); ?>

<?php $__env->startSection("sidebar"); ?>
    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="nav-link <?php echo e(request()->routeIs('crm.dashboard.*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <?php echo e(__('common.dashboard')); ?>

    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <?php echo e(__('common.calendar')); ?>

    </a>
    <a href="<?php echo e(route("crm.workouts.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <?php echo e(__('common.workouts')); ?>

    </a>
    <a href="<?php echo e(route("crm.trainer.athletes")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <?php echo e(__('common.clients')); ?>

    </a>
    <a href="<?php echo e(route('crm.trainer.subscription')); ?>" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        <?php echo e(__('common.subscription')); ?>

    </a>
    <a href="<?php echo e(route('crm.trainer.settings')); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <?php echo e(__('common.settings')); ?>

    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("mobile-menu"); ?>
    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.dashboard.*') ? 'active' : ''); ?>">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <?php echo e(__('common.dashboard')); ?>

    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?>">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <?php echo e(__('common.calendar')); ?>

    </a>
    <a href="<?php echo e(route("crm.workouts.index")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <?php echo e(__('common.workouts')); ?>

    </a>
    <a href="<?php echo e(route("crm.trainer.athletes")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <?php echo e(__('common.clients')); ?>

    </a>
    <a href="<?php echo e(route('crm.trainer.subscription')); ?>" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        <?php echo e(__('common.subscription')); ?>

    </a>
    <a href="<?php echo e(route('crm.trainer.settings')); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <?php echo e(__('common.settings')); ?>

    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>
<div class="space-y-6">
    <?php if($currentSubscription): ?>
        <!-- Текущий план -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.current_plan')); ?></h3>
                <span class="px-3 py-1 
                    <?php if($currentSubscription->status === 'trial'): ?> bg-blue-100 text-blue-800
                    <?php elseif($currentSubscription->status === 'active'): ?> bg-green-100 text-green-800
                    <?php elseif($currentSubscription->status === 'expired'): ?> bg-red-100 text-red-800
                    <?php else: ?> bg-gray-100 text-gray-800
                    <?php endif; ?>
                    text-sm font-medium rounded-full">
                    <?php if($currentSubscription->status === 'trial'): ?>
                        <?php echo e(__('common.trial')); ?>

                    <?php elseif($currentSubscription->status === 'active'): ?>
                        <?php echo e(__('common.subscription_active')); ?>

                    <?php elseif($currentSubscription->status === 'expired'): ?>
                        <?php echo e(__('common.subscription_expired')); ?>

                    <?php else: ?>
                        <?php echo e(ucfirst($currentSubscription->status)); ?>

                    <?php endif; ?>
                </span>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">
                        <?php echo e($currentSubscription->plan->name ?? __('common.not_specified')); ?>

                    </div>
                    <div class="text-gray-600 mt-1"><?php echo e(__('common.subscription_plan')); ?></div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        <?php if($currentSubscription->currency): ?>
                            <?php echo e($currentSubscription->currency->format($currentSubscription->price)); ?>

                        <?php else: ?>
                            <?php echo e(number_format($currentSubscription->price, 2)); ?> <?php echo e($currentSubscription->currency_code); ?>

                        <?php endif; ?>
                    </div>
                    <div class="text-gray-600 mt-1"><?php echo e(__('common.per_month')); ?></div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900">
                        <?php echo e($currentSubscription->expires_date->format('d.m.Y')); ?>

                    </div>
                    <div class="text-gray-600 mt-1"><?php echo e(__('common.valid_until')); ?></div>
                </div>
            </div>
            
            <?php if($currentSubscription->plan && $currentSubscription->plan->description): ?>
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="text-sm text-gray-600">
                        <h4 class="font-medium text-gray-900 mb-3"><?php echo e(__('common.description')); ?></h4>
                        <p><?php echo e($currentSubscription->plan->description); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <?php if($currentSubscription->is_trial): ?>
                    <p class="text-sm text-gray-600 mb-3"><?php echo e(__('common.trial_period')); ?>: <?php echo e($currentSubscription->trial_days); ?> <?php echo e(__('common.days')); ?></p>
                <?php endif; ?>
                <?php if($currentSubscription->status === 'trial' || $currentSubscription->status === 'active'): ?>
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        <?php echo e(__('common.extend_subscription')); ?>

                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Нет подписки -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo e(__('common.no_subscription')); ?></h3>
            <p class="text-gray-600 mb-6"><?php echo e(__('common.no_subscription')); ?></p>
        </div>
    <?php endif; ?>

    <?php if($subscriptionHistory && $subscriptionHistory->count() > 0): ?>
        <!-- История подписок -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6"><?php echo e(__('common.subscription_history')); ?></h3>
            
            <div class="space-y-4">
                <?php $__currentLoopData = $subscriptionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between py-4 <?php echo e(!$loop->last ? 'border-b border-gray-100' : ''); ?>">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4
                                <?php if($subscription->status === 'trial'): ?> bg-blue-100
                                <?php elseif($subscription->status === 'active'): ?> bg-green-100
                                <?php elseif($subscription->status === 'expired'): ?> bg-red-100
                                <?php else: ?> bg-gray-100
                                <?php endif; ?>">
                                <?php if($subscription->status === 'trial'): ?>
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php elseif($subscription->status === 'active' || $subscription->status === 'expired'): ?>
                                    <svg class="w-5 h-5 <?php echo e($subscription->status === 'active' ? 'text-green-600' : 'text-red-600'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">
                                    <?php if($subscription->is_trial): ?>
                                        <?php echo e(__('common.trial')); ?> <?php echo e(__('common.subscription')); ?>

                                    <?php else: ?>
                                        <?php echo e($subscription->plan->name ?? __('common.subscription')); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <?php echo e($subscription->start_date->format('d.m.Y')); ?> - <?php echo e($subscription->expires_date->format('d.m.Y')); ?>

                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-gray-900">
                                <?php if($subscription->currency): ?>
                                    <?php echo e($subscription->currency->format($subscription->price)); ?>

                                <?php else: ?>
                                    <?php echo e(number_format($subscription->price, 2)); ?> <?php echo e($subscription->currency_code); ?>

                                <?php endif; ?>
                            </div>
                            <div class="text-sm 
                                <?php if($subscription->status === 'active' || $subscription->status === 'trial'): ?> text-green-600
                                <?php elseif($subscription->status === 'expired'): ?> text-red-600
                                <?php else: ?> text-gray-600
                                <?php endif; ?>">
                                <?php if($subscription->status === 'trial'): ?>
                                    <?php echo e(__('common.trial')); ?>

                                <?php elseif($subscription->status === 'active'): ?>
                                    <?php echo e(__('common.subscription_active')); ?>

                                <?php elseif($subscription->status === 'expired'): ?>
                                    <?php echo e(__('common.subscription_expired')); ?>

                                <?php else: ?>
                                    <?php echo e(ucfirst($subscription->status)); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("crm.layouts.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/crm/trainer/subscription/index.blade.php ENDPATH**/ ?>