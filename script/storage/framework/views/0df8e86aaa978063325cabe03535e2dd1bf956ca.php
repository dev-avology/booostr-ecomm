<div class="section-header">
	<?php if(isset($prev)): ?>
	<div class="section-header-back">
		<a href="<?php echo e(url($prev)); ?>" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
	</div>
	<?php endif; ?>
  <h1><?php echo e($title); ?></h1>
  <?php if(isset($button_name)): ?>
  <div class="section-header-button">
  	<a href="<?php echo e(url($button_link)); ?>" class="btn btn-primary"><?php echo e($button_name); ?></a>
  </div>
  <?php endif; ?>

  <?php if(isset($risk_level)): ?> 

      <?php if($risk_level == 'highest'): ?>
      <div class="section-header-risk-level">
        <span class="risk-level-text1"><img src="<?php echo e(asset('uploads/shiled.png')); ?>" alt="High"></span>
        <span class="risk-level-text1">High Risk of fraud detected </span>
      </div>
    <?php endif; ?>

  <?php endif; ?>

  <div class="section-header-breadcrumb">
  	  <?php $__currentLoopData = request()->segments(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="breadcrumb-item"><?php echo e($segment); ?></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div><?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/layouts/backend/partials/headersection.blade.php ENDPATH**/ ?>