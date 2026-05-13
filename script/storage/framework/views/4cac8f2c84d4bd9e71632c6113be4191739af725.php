<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  
  <title><?php echo e(isset(Auth::user()->name) ? ucwords(str_replace("-"," ", Auth::user()->name)) : config('app.name')); ?> | <?php echo e(__('Booostr Store Management Tool')); ?></title>

  <!-- Favicon icon -->
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <link rel="icon" type="image/png" href="<?php echo e(asset('uploads/favicon.jpg')); ?>"/>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/bootstrap.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/fontawesome.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/selectric.css')); ?>">
  <?php echo $__env->yieldContent('style'); ?>
  <?php echo $__env->yieldPushContent('css'); ?>
  <!-- Template CSS -->
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/style.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/components.css')); ?>">


  
  <link rel="stylesheet" href="<?php echo e(asset('seller/css/common.css')); ?>">

</head>

<body>

<div id="app">
  <div class="main-wrapper">
      <!--- Header Section ---->
    <?php echo $__env->make('layouts.backend.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <!--- Sidebar Section --->
    <?php echo $__env->make('layouts.backend.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <!--- Main Content --->
    <div class="main-content  main-wrapper-1">
      <section class="section">
        <?php echo $__env->yieldContent('head'); ?>
      </section>
       <?php echo $__env->yieldContent('content'); ?>
    </div>

    <?php echo $__env->yieldContent('modal'); ?>

     <!--- Footer Section --->
    
    </div>
</div>


<input type="hidden" class="placeholder_image" value="<?php echo e(asset('admin/img/img/placeholder.png')); ?>">

<?php if(tenant('push_notification') == 'on' && env('FMC_SERVER_API_KEY') != null): ?>
<input type="hidden" id="apiKey" value="<?php echo e(env('FMC_CLIENT_API_KEY')); ?>">
<input type="hidden" id="authDomain" value="<?php echo e(env('FMC_AUTH_DOMAIN')); ?>">
<input type="hidden" id="projectId" value="<?php echo e(env('FMC_PROJECT_ID')); ?>">
<input type="hidden" id="storageBucket" value="<?php echo e(env('FMC_STORAGE_BUCKET')); ?>">
<input type="hidden" id="messagingSenderId" value="<?php echo e(env('FMC_MESSAGING_SENDER_ID')); ?>">
<input type="hidden" id="appId" value="<?php echo e(env('FMC_APP_ID')); ?>">
<input type="hidden" id="measurementId" value="<?php echo e(env('FMC_MEASUREMENT_ID')); ?>">
<?php endif; ?>

<input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>">
<input type="hidden" id="site_url" value="<?php echo e(url('/')); ?>">

<!-- General JS Scripts -->
<script src="<?php echo e(asset('admin/assets/js/jquery-3.5.1.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/jquery.inputmask.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/general/general.js')); ?>"></script>

<script src="<?php echo e(asset('admin/assets/js/popper.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/jquery.nicescroll.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/sweetalert2.all.min.js')); ?>"></script>

<!-- Template JS File -->
<script src="<?php echo e(asset('admin/assets/js/scripts.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/custom.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/form.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/jquery.selectric.min.js')); ?>"></script>

<?php if(tenant('push_notification') == 'on' && env('FMC_SERVER_API_KEY') != null): ?>

<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js"></script>
<script src="<?php echo e(asset('firebasestatus.js')); ?>"></script>
<?php endif; ?>
<?php echo $__env->yieldContent('script'); ?>
<?php echo $__env->yieldPushContent('script'); ?>
<script src="<?php echo e(asset('admin/js/main.js')); ?>"></script>

</body>
</html>
<?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/layouts/backend/app.blade.php ENDPATH**/ ?>