
<?php $__env->startSection('content'); ?>
<div class="card-header">
  <h4><?php echo e(__('Login')); ?></h4>
</div>
<div class="card-body">

  <?php if(Session::has('error')): ?>
  <div class="alert alert-danger d-flex align-items-center" role="alert">
   <?php echo e(Session::get('error')); ?>

 </div>
 <?php endif; ?>
 <form method="POST" id="ajaxform" class="needs-validation" action="<?php echo e(route('login')); ?>">
  <?php echo csrf_field(); ?>
  <div class="form-group">
    <label for="email"><?php echo e(__('E-Mail Address')); ?></label>
    <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus >
    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="invalid-feedback">
      <?php echo e($message); ?>

    </div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
  <div class="form-group">
    <div class="d-block">
      <label for="password" class="control-label"><?php echo e(__('Password')); ?></label>
      <?php if(Route::has('password.request')): ?>
      <div class="float-right">
        <a href="<?php echo e(route('password.request')); ?>" class="text-small">
          <?php echo e(__('Forgot Password?')); ?>

        </a>
      </div>
      <?php endif; ?>
    </div>
    <input id="password" type="password" class="form-control  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required autocomplete="current-password">
    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
    <div class="invalid-feedback">
      <?php echo e($message); ?>

    </div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <div class="form-group">
      <div class="custom-control custom-checkbox">
       <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" <?php echo e(old('remember') ? 'checked' : ''); ?>>
       <label class="custom-control-label" for="remember-me"><?php echo e(__('Remember Me')); ?></label>
     </div>
   </div>
   <div class="form-group">
    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
      <?php echo e(__('Login')); ?>

    </button>
  </div>
</form>
<div class="simple-footer">
  <?php echo e(__('Copyright')); ?> &copy; <?php echo e(Config::get('app.name')); ?> <?php echo e(date('Y')); ?>

</div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('auth.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/auth/login.blade.php ENDPATH**/ ?>