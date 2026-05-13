<?php $__env->startSection('title','Products'); ?>

<?php $__env->startSection('head'); ?>
<?php echo $__env->make('layouts.backend.partials.headersection',['title'=>'Products','button_name'=> 'Create New','button_link'=> url('seller/product/create')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.storenotification','data' => []]); ?>
<?php $component->withName('storenotification'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

<?php if(Session::has('error')): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><?php echo e(Session::get('error')); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="card card-primary">
    <div class="card-body">
        <div class="row mb-2">
            <!-- <div class="col-lg-12">
                <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#import"><?php echo e(__('Bulk Import')); ?></a>
            </div> -->
        </div>
        <br>
        <div class="float-right">
            <form>
                <div class="input-group mb-2">
                    <?php
                        $per_page_options = [10, 20, 50, 100, 200, 500];
                        $selected_per_page = request()->get('per_page', 10);
                    ?>

                    <select class="form-control" name="per_page" id="per_page" onchange="this.form.submit()">
                        <?php $__currentLoopData = $per_page_options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $per): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($per); ?>" <?php echo e($selected_per_page == $per ? 'selected' : ''); ?>>
                                Per page <?php echo e($per); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </form>
        </div>

        <div class="float-right">
            <form>
                <div class="input-group mb-2">

                    <input type="text" id="src" class="form-control" placeholder="Search..." required="" name="src" autocomplete="off" value="<?php echo e($request->src ?? ''); ?>">
                    <select class="form-control selectric" name="type" id="type">

                        <option value="full_id" <?php if($type == 'full_id'): ?> selected <?php endif; ?>><?php echo e(__('Search By Id')); ?></option>
                        <option value="title" <?php if($type == 'title'): ?> selected <?php endif; ?>><?php echo e(__('Search By Name')); ?></option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <form method="post" action="<?php echo e(route('seller.products.destroys')); ?>" class="ajaxform_with_reload">
            <?php echo csrf_field(); ?>
            <div class="float-left">
                <div class="input-group">
                    <select class="form-control selectric" name="method">
                        <option disabled selected=""><?php echo e(__('Select Action')); ?></option>
                        <option value="1"><?php echo e(__('Publish Now')); ?></option>
                        <option value="0"><?php echo e(__('Draft')); ?></option>

                        <option value="duplicate"><?php echo e(__('Duplicate')); ?></option>
                        <option value="delete" class="text-danger"><?php echo e(__('Delete Permanently')); ?></option>
                       

                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-primary basicbtn" type="submit"><?php echo e(__('Submit')); ?></button>
                    </div>
                </div>

            </div>
            <div class="table-responsive custom-table">
                <table class="table" id="table-2">
                    <thead>
                        <tr>
                            <th class="am-select">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input checkAll" id="selectAll">
                                    <label class="custom-control-label checkAll" for="selectAll"></label>
                                </div>
                            </th>

                            <th><?php echo e(__('Name')); ?></th>
                            <th class="text-right"><i class="far fa-image"></i></th>
                            <th class="text-right"><?php echo e(__('Type')); ?></th>
                            <th class="text-right"><?php echo e(__('Price')); ?></th>
                            <th class="text-right"><?php echo e(__('Status')); ?></th>
                            <th class="text-right"><?php echo e(__('Sales')); ?></th>
                            <th class="text-right"><?php echo e(__('Created At')); ?></th>
                            <th class="text-right"><?php echo e(__('Edit')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr id="data-<?php echo e($row->id); ?>">
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck<?php echo e($row->id); ?>" value="<?php echo e($row->id); ?>">
                                    <label class="custom-control-label" for="customCheck<?php echo e($row->id); ?>"></label>
                                </div>
                            </td>
                                  <td><?php echo e(Str::limit($row->title,20)); ?> (<?php echo e($row->full_id); ?>)</td>
                                  <td class="text-right"><img src="<?php echo e(asset($row->media->value ?? 'uploads/default.png')); ?>" height="50" alt=""></td>
                                  
                                <?php
                                    if ($row->is_variation == 2) {
                                        $productTypeLabel = 'Online Ticket';
                                    } elseif ($row->is_variation == 1) {
                                        $productTypeLabel = 'Variations';
                                    } else {
                                        $productTypeLabel = 'Simple';
                                    }
                                ?>

                                <?php if(isset($row->formType) && !empty($row->formType)): ?>
                                    <td class="text-right">
                                        <span style="display: inline-block;">
                                            <?php echo e($productTypeLabel); ?>

                                            <span style="display: block;font-size:10px;">Linked Form</span>
                                        </span>
                                    </td>
                                <?php else: ?>
                                    <td class="text-right"><?php echo e($productTypeLabel); ?></td>
                                <?php endif; ?>


                                  

                                  <?php
                                    $basePrice = (float) ($row->price?->price ?? 0);
                                    $ticketFee = 0;

                                    if ($row->is_variation == 2) {
                                        $ticketFee = 0.75;
                                        if (isset($row->ticketFeeMeta)) {
                                            $ticketFee = (float) $row->ticketFeeMeta->value;
                                        }
                                    }

                                    $displayPrice = $basePrice + $ticketFee;
                                ?>

                                <td class="text-right" style="<?php echo e($basePrice == 0 ? 'font-weight: bold; color: red;' : ''); ?>">
                                    $<?php echo e(number_format($displayPrice, 2)); ?><?php echo e($row->is_variation == 1 ? '*' : ''); ?>



                                </td>
                                

                                  
                                  <td class="text-right"><span class="badge badge-<?php echo e($row->status == 1 ? 'success' : 'danger'); ?>"><?php echo e($row->status == 1 ? 'Active' : 'Disable'); ?></span></td>
                                  <td><?php echo e($row->orders_count); ?></td>
                                  <td class="text-right"><?php echo e(date('d-m-Y', strtotime($row->created_at))); ?></td>
                                  <td class="text-right">
                                    <a class="text-primary" href="<?php echo e(route('seller.product.edit', $row->id)); ?>"><i class="fa fa-edit"></i></a>
                                    <a class="text-primary" href="<?php echo e(route('seller.product.clone', $row->id)); ?>"><i class="fa fa-clone"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <!--div class="result">&nbsp;</div-->
            </form>
            <?php echo e($posts->appends($request->all())->links('vendor.pagination.bootstrap-4')); ?>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="import" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo e(__('Product Import')); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('seller.product.import')); ?>" method="POST" class="ajaxform_with_reload" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-control">
                            <input type="file" name="file" accept=".csv">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="import_area">
                        <div>
                            <p class="text-left"><a href="<?php echo e(asset('uploads/demo.csv')); ?>"><?php echo e(__('Download Sample')); ?></a>
                            </p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                            <button type="submit" class="btn btn-primary basicbtn"><?php echo e(__('Import')); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<style>
.ui-sortable-helper td{
    padding: 0 25px;
    height: 60px;
    vertical-align: middle;
}
</style>

<script>
  $(function() {
    $("#table-2 tbody").sortable({
      cursor: "move",
      placeholder: "ui-state-highlight",
      update: function (event, ui) {
        var data = $(this).sortable('serialize');
         console.log(data);

         $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

         $.ajax({
            data: data,
            type: 'POST',
            url: 'product-order-update'
        });
      },
      helper: function(e, tr)
      {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index)
        {
            //console.log(index);

      //  Set helper cell sizes to match the original sizes

       $(this).width($originals.eq(index).width());
       $(this).css('padding',$originals.eq(index).css('padding'));
       $(this).css('height',$originals.eq(index).css('height'));
       $(this).css('vertical-align',$originals.eq(index).css('vertical-align'));

        console.log($originals.eq(index).css('padding'),$originals.eq(index).css('height'),$originals.eq(index).css('vertical-align'));

        });
        return $helper;
      }
    }).disableSelection();
  });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.backend.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/seller/product/index.blade.php ENDPATH**/ ?>