<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
      <title><?php echo e(ucwords(Config::get('app.name'))); ?> | <?php echo e(__('Booostr Store Management Tool')); ?></title>
      <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
      <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('uploads/favicon.jpg')); ?>">
      <!-- General CSS Files -->
      <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/bootstrap.min.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/fontawesome.min.css')); ?>">
      <!-- Template CSS -->
      <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/style.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/components.css')); ?>">
   </head>
   <body>
      <div id="app">
      <section class="section">
         <div class="container mt-5">
         <div class="row">
         <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
               <?php if(tenant('id') != null): ?>
               <img src="<?php echo e(asset('uploads/'.tenant('uid').'/logo.png?v='.tenant('cache_version'))); ?>" alt="">
               <?php else: ?>
               <img src="<?php echo e(asset('uploads/logo.png')); ?>" alt="" class="shadow-light">
               <?php endif; ?>
            </div>
            <div class="card card-primary">
               <?php echo $__env->yieldContent('content'); ?>
            </div>
      </section>
      </div>
      <!-- General JS Scripts -->
      <script src="<?php echo e(asset('admin/assets/js/jquery-3.5.1.min.js')); ?>"></script>
      <script src="<?php echo e(asset('admin/assets/js/popper.min.js')); ?>"></script>
      <script src="<?php echo e(asset('admin/assets/js/bootstrap.min.js')); ?>"></script>
      <!-- Template JS File -->
      <script src="<?php echo e(asset('admin/assets/js/scripts.js')); ?>"></script>
   </body>
</html>
<?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/auth/main.blade.php ENDPATH**/ ?>