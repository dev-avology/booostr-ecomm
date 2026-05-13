<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
        <?php if(Auth::user()->role_id == 1): ?>
        <a href="<?php echo e(url('/admin/dashboard')); ?>"><img src="/uploads/booostr-logo-long-top-header.png" height="40"/></a>
        <?php elseif(Auth::user()->role_id == 2): ?>
        <a href="<?php echo e(url('/')); ?>"><?php echo e(Config::get('app.name')); ?></a>
        <?php elseif(Auth::user()->role_id == 3): ?>
          <?php if(!empty(tenant()->logo)): ?>
              <?php
                  $url = env("WP_API_URL");
                  $url = ($url != '') ? $url.'/logo-tenant?tenant='.tenant()->club_id : "https://staging3.booostr.co/wp-json/store-api/v1/logo-tenant";
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                  ));
                  $logo = curl_exec($curl);
                  curl_close($curl);
                  $result = json_decode($logo, true);
              ?>
            <a href="#"><img src="<?php echo e($result['data'] ?? ''); ?>" style="max-width: 80px;"/></a>
          <?php else: ?>
            <a href="<?php echo e(url('/')); ?>"><?php echo e(Config::get('app.name')); ?></a>
          <?php endif; ?>
        <?php elseif(Auth::user()->role_id == 5): ?>
        <a href="<?php echo e(url('/')); ?>"><?php echo e(Config::get('app.name')); ?></a>
        <?php endif; ?>
            
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="<?php echo e(url('login')); ?>"><?php echo e(Str::limit(env('APP_NAME'), $limit = 1)); ?></a>
        </div>
        <ul class="sidebar-menu">
            <?php if(Auth::user()->role_id == 1): ?>

              <?php echo $__env->make('admin.adminmenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(Auth::user()->role_id == 2): ?>

              <?php echo $__env->make('merchant.merchantmenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(Auth::user()->role_id == 3): ?>

              <?php echo $__env->make('seller.sellermenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php elseif(Auth::user()->role_id == 5): ?>

              <?php echo $__env->make('rider.ridermenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </ul>
        <?php if(Auth::user()->role_id == 3): ?>
        <!-- <div class=" mb-4 p-3 hide-sidebar-mini">
            <a href="<?php echo e(url('seller/site-settings')); ?>" class="btn btn-primary btn-lg btn-block btn-icon-split">
              <i class="fas fa-cog"></i> <?php echo e(__('App Settings')); ?>

            </a>
          </div>  -->
        <?php endif; ?>  
    </aside>
</div>
<?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/layouts/backend/partials/sidebar.blade.php ENDPATH**/ ?>