

<?php $__env->startSection('content'); ?>
<div class="card"  >
	<div class="card-body">
		<div class="row mb-30">
			<div class="col-lg-6">
				<h4><?php echo e(__('Admins')); ?></h4>
			</div>
			<div class="col-lg-6">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.create')): ?>
				<div class="add-new-btn">
					<a href="<?php echo e(route('admin.admin.create')); ?>" class="btn btn-primary float-right"><?php echo e(__('Add New')); ?></a>
                </div>
                <?php endif; ?>
			</div>
		</div>
		<br>
		<div class="card-action-filter">
			<form method="post" class="ajaxform_with_reload" action="<?php echo e(route('admin.admins.destroy')); ?>">
				<?php echo csrf_field(); ?>
				<div class="row">
					<div class="col-lg-6">
						<div class="d-flex">
							<div class="single-filter">
								<div class="form-group">
									<select class="form-control selectric" name="status">
                                        <option disabled selected><?php echo e(__('Select Action')); ?></option>
										<option value="1"><?php echo e(__('Active')); ?></option>
										<option value="0"><?php echo e(__('Deactivate')); ?></option>
									</select>
								</div>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.edit')): ?>
							<div class="single-filter">
								<button type="submit" class="btn btn-primary btn-lg ml-2"><?php echo e(__('Apply')); ?></button>
                            </div>
                            <?php endif; ?>
						</div>
					</div>
					<div class="col-lg-6">

					</div>
				</div>
			</div>
			<div class="table-responsive custom-table">
				<table class="table">
					<thead>
						<tr>
							<th>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input checkAll" id="selectAll">
									<label class="custom-control-label checkAll" for="selectAll"></label>
								</div>
							</th>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Email')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                          
                            <th><?php echo e(__('Role')); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck<?php echo e($row->id); ?>" value="<?php echo e($row->id); ?>">
									<label class="custom-control-label" for="customCheck<?php echo e($row->id); ?>"></label>
								</div>
							</td>
							<td>
                                <?php echo e($row->name); ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.edit')): ?>
								<div class="hover">
									<a href="<?php echo e(route('admin.admin.edit',$row->id)); ?>"><?php echo e(__('Edit')); ?></a>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                               <?php echo e($row->email); ?>

                            </td>
                            <td>
                            <?php if($row->status==1): ?>
                            <span class="badge badge-success"><?php echo e(__('Active')); ?></span>
                            <?php else: ?>
                            <span class="badge badge-danger"><?php echo e(__('Deactive')); ?></span>
                            <?php endif; ?>
                            </td>
                             
                           <td>
                        	<?php $__currentLoopData = $row->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <span class="badge badge-primary"><?php echo e($r->name); ?></span> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</form>
			</table>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/admin/admin/index.blade.php ENDPATH**/ ?>