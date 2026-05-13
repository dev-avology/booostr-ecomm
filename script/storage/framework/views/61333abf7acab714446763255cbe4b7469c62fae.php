<?php $__env->startSection('title','Dashboard'); ?>

<?php $__env->startSection('style'); ?>
<style>
    span.risk-badge-text img {
        margin-left: 12px;
    }
    .input-group-btn button.btn.btn-primary.btn-icon {
    padding-top: 8px;
    padding-bottom: 8px;

}

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<section class="section">
    <div class="section-header row">
        <div class="col-sm-12">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link  <?php echo e($request_status == null ? 'active' : ''); ?> " href="<?php echo e(route('seller.order.index')); ?>">All</a>
                </li>
                <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="nav-item">
                <a class="nav-link  <?php echo e($request_status == $row->id ? 'active' : ''); ?>" href="<?php echo e(url('seller/order?status='.$row->id)); ?>"><?php echo e($row->name); ?> <span class="badge badge-secondary"><?php echo e($row->orderstatus_count); ?></span></a>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</section>

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

 <div class="card">
    <div class="card-header">
        <h4><?php echo e(__('Orders')); ?></h4>
        <form class="card-header-form">
            <div class="input-group">
                <input type="text" name="src" value="<?php echo e($request->src ?? ''); ?>" class="form-control" required=""  placeholder="Order Id" />
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <button class="btn btn-sm btn-primary  ml-1" type="button" data-toggle="modal" data-target="#searchmodal">
            <i class="fe fe-sliders mr-1"></i> <?php echo e(__('Filter')); ?> <span class="badge badge-primary ml-1 d-none">0</span>
        </button>
    </div>
    <div class="card-body">
        <form method="post" action="<?php echo e(route('seller.order.multipledelete')); ?>" class="" id="bulkActionForm">
            <?php echo csrf_field(); ?>
            <div class="float-left">
                <?php if(count($orders) > 0): ?>

                <div class="input-group mb-1">
                     <select class="form-control selectric" name="method">
                        <option disabled selected>Select Capture/Fulfillment</option>
                        <option value="capture_authorized">Capture Authorized Payments</option>
                        <option value="complete_fulfillment">Complete Fulfillment</option>
                        <option value="cancel_order">Cancel Order</option>
                        <option value="mark_pending">Mark As Pending</option>
                        <option value="delete">Delete Permanently</option>
                    </select>

                    <div class="input-group-append">                                            
                        <button class="btn btn-primary basicbtn" type="submit"><?php echo e(__('Submit')); ?></button>
                    </div>
                </div>
                <?php endif; ?>
            </div>  
            <div class="float-right">
                <?php if(count($request->all()) > 0): ?>
                <?php echo e($orders->appends($request->all())->links('vendor.pagination.bootstrap-4')); ?>

                <?php else: ?>
                <?php echo e($orders->links('vendor.pagination.bootstrap-4')); ?>

                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-nowrap card-table text-center">
                    <thead>
                        <tr>
                            <th class="text-left" ><div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checkAll" id="selectAll">
                            <label class="custom-control-label checkAll" for="selectAll"></label>
                            </div></th>
                            <th class="text-left" ><?php echo e(__('Order')); ?></th>
                            <th ><?php echo e(__('Date')); ?></th>
                            <th><?php echo e(__('Customer')); ?></th>
                            <th class="text-left" ><?php echo e(__('Order Channel')); ?></th>
                            <th class="text-right"><?php echo e(__('total')); ?></th>
                            <th><?php echo e(__('Payment')); ?></th>
                            <th><?php echo e(__('Fulfillment')); ?></th>
                            <th class="text-right"><?php echo e(__('Type')); ?></th>
                            <th class="text-right"><?php echo e(__('Item(s)')); ?></th>
                            <th class="text-right"><?php echo e(__('Print')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="list font-size-base rowlink" data-link="row">
                        <?php $__currentLoopData = $orders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php 
                        // All product type IDs
                            $p_types = $product_type->pluck('id')->all();

                            // Gather category IDs from order items that match product types
                            $selected_product_type = collect($row->orderitems ?? [])
                                ->flatMap(fn($item) => $item->term->termcategories->pluck('category_id'))
                                ->filter(fn($id) => in_array($id, $p_types))
                                ->unique()
                                ->values()
                                ->all();

                            // Default order type
                            $order_type = 'Goods';
                            $count = count($selected_product_type);

                            if ($count === 1) {
                                // Find the product type model by ID
                                $pt = $product_type->firstWhere('id', $selected_product_type[0]);

                                // Decide order type by slug
                                if ($pt && $pt->slug === 'digital_product') {
                                    $order_type = 'Digital';
                                } elseif ($pt && $pt->slug === 'physical_product') {
                                    $order_type = 'Goods';
                                }
                            } elseif ($count > 1) {
                                $order_type = 'Mixed';
                            }

                        $ordermeta=json_decode($row->ordermeta->value ?? ''); 
                    ?>
                        <tr>
                            <td  class="text-left">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck<?php echo e($row->id); ?>" value="<?php echo e($row->id); ?>">
                                    <label class="custom-control-label" for="customCheck<?php echo e($row->id); ?>"></label>
                                </div>
                            </td>
                            <td class="text-left">
                                <a href="<?php echo e(route('seller.order.show',$row->id)); ?>"><?php echo e($row->invoice_no); ?></a> 
                             
                                <span class="risk-badge">
                                  <?php if($row->risk_level == 'normal'): ?>
                                    <span class="risk-badge-text"  data-toggle="tooltip" data-placement="left" title="Normal"><img src="<?php echo e(asset('uploads/security-1.png')); ?>" alt="Low"></span>
                                  <?php elseif($row->risk_level == 'elevated'): ?>
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Medium"><img src="<?php echo e(asset('uploads/security-3.png')); ?>" alt="Medium"></span>
                                  <?php elseif($row->risk_level == 'highest'): ?>
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Highest"><img src="<?php echo e(asset('uploads/security-2.png')); ?>" alt="Highest"></span>
                                  <?php elseif($row->risk_level == 'unknown'): ?>
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Unknown"><img src="<?php echo e(asset('uploads/shiled-5.png')); ?>" alt="Unknown"></span>
                                  <?php elseif($row->risk_level == 'not_assessed'): ?>
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Not Assessed"><img src="<?php echo e(asset('uploads/shiled-4.png')); ?>" alt="Not Assessed"></span>
                                  <?php endif; ?>
                                </span>
                            </td>

                            <td><a href="<?php echo e(route('seller.order.show',$row->id)); ?>"><?php echo e($row->created_at->format('d-F-Y')); ?></a></td>

                            <td>
                               <?php if($row->user_id == null && $row->order_from == 1  ): ?>
                                 <?php echo e(($ordermeta != '' ) ? $ordermeta->name : $row->user->name); ?> 
                                <?php elseif($row->user_id !== null): ?>
                                 <a href="<?php echo e(route('seller.user.show',$row->user_id)); ?>"><?php echo e(($ordermeta != '' ) ? $ordermeta->name : $row->user->name); ?></a>
                                <?php else: ?> 
                                 <?php echo e(__('Guest User')); ?>

                                <?php endif; ?> 
                            </td>

                            <?php if($row->order_from == 4 || $row->order_from == 5): ?>

                            <td class="text-left">
                                POS(point of sale)
                            </td>

                            <?php elseif($row->order_from == 0): ?>
                            <td class="text-left">
                                POS(Web)
                            </td>
                            <?php else: ?>

                            <td class="text-left">
                                ECOM(online)
                            </td>

                            <?php endif; ?>


                            <td ><?php echo e(currency_formate($row->total)); ?></td>
                            <td>
                                <?php if($row->payment_status==2): ?>
                                <span class="badge badge-warning"><?php echo e(__('Pending')); ?></span>
                                <?php elseif($row->payment_status==1): ?>

                                     <?php if($row->order_from == 4 || ($row->order_from == 0 && $row->getway->name !== 'cash')): ?>
                                        <span class="badge badge-success"><?php echo e(__('CC Complete')); ?></span>
                                     <?php elseif($row->order_from == 5 || ($row->order_from == 0 && $row->getway->name == 'cash')): ?>
                                        <span class="badge badge-success"><?php echo e(__('Cash Complete')); ?></span>
                                     <?php else: ?>
                                        <span class="badge badge-success"><?php echo e(__('Complete')); ?></span>
                                     <?php endif; ?>


                                <?php elseif($row->payment_status==0): ?>
                                <span class="badge badge-danger"><?php echo e(__('Cancel')); ?></span> 
                                <?php elseif($row->payment_status==3): ?>
                                <span class="badge badge-danger"><?php echo e(__('Incomplete')); ?></span> 
                                <?php elseif($row->payment_status==4): ?>
    							<span class="badge badge-danger"><?php echo e(__('Authorized')); ?></span>
                                <?php elseif($row->payment_status==5): ?>
                                <span class="badge badge-warning"><?php echo e(__('Refunded')); ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($row->order_from == 4 || $row->order_from == 5): ?>
                               

                                <span class="badge badge-success text-white" style="background-color:#028a74">POS (In Person)</span>

                                <?php elseif($row->order_from == 0): ?>

                                <span class="badge badge-success text-white" style="background-color:#028a74">POS (Web)</span>

                                <?php else: ?>

                                <span class="badge <?php echo e($row->orderstatus == null ? 'badge-warning' :''); ?> text-white" style="background-color: <?php echo e($row->orderstatus->slug); ?>"><?php echo e($row->orderstatus->name ?? ''); ?></span>

                                <?php endif; ?> 


                            </td>
                            
                            <td> <?php echo e($order_type); ?> </td>
                            <td><?php echo e($row->orderitems_count); ?></td>
                            <td>
                                <a target="_blank" href="<?php echo e(route('seller.order.print',$row->id)); ?>" class="btn btn-primary">Print</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                   
                </table>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="searchmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="card-header-title"><?php echo e(__('Filters')); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
            <div class="modal-body">
                <div class="form-group row mb-4">
                    <label class="col-sm-7"><?php echo e(__('Payment Status')); ?></label>
                    <div class="col-sm-5">
                        <select class="form-control selectric" name="payment_status" id="payment_status">
                            <option value="2"><?php echo e(__('Pending')); ?></option>
                            <option value="1" ><?php echo e(__('Complete')); ?></option>
                            <option value="3" ><?php echo e(__('Incomplete')); ?></option>
                            <option value="0" ><?php echo e(__('Cancel')); ?></option>
                           
                        </select>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-7"><?php echo e(__('Fulfillment status')); ?></label>
                    <div class="col-sm-5">
                        <select class="form-control selectric" name="status" id="status" >
                          <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($row->id); ?>" <?php echo e($request_status == $row->id ? 'selected' : ''); ?>><?php echo e($row->name); ?></option>
                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-3"><?php echo e(__('Starting date')); ?></label>
                    <div class="col-sm-9">
                        <input type="date" name="start" class="form-control" value="<?php echo e($request->start); ?>" />
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-3"><?php echo e(__('Ending date')); ?></label>
                    <div class="col-sm-9">
                        <input type="date" name="end" class="form-control" value="<?php echo e($request->end); ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo e(route('seller.order.index')); ?>" class="btn btn-secondary"><?php echo e(__('Clear Filter')); ?></a>
                <button type="submit" class="btn btn-primary"><?php echo e(__('Filter')); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
<input type="hidden" id="payment" value="<?php echo e($request->payment_status ?? ''); ?>">
<input type="hidden" id="order_status" value="<?php echo e($request->status ?? ''); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<!--<script src="<?php echo e(asset('assets/js/form.js')); ?>"></script>-->
<!--<script src="<?php echo e(asset('assets/js/order_index.js')); ?>"></script>-->
<?php $__env->stopPush(); ?>
<!-- jQuery (must be before your custom JS) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Submit handler for the bulk form
    $('#bulkActionForm').on('submit', function(e){
        e.preventDefault(); // stop page refresh

        let method = $(this).find('select[name="method"]').val();
        if(method === 'capture_authorized'){
            $('#captureModal').modal('show');
        } else if (method === 'complete_fulfillment'){ 
            $('#fulfillModal').modal('show'); 
        } else if (method === 'cancel_order'){ 
            $('#cancelModal').modal('show'); 
        } else if (method === 'mark_pending'){ 
            $('#pendingModal').modal('show'); 
        } else if (method === 'delete'){ 
            $('#deleteModal').modal('show'); 
        } else {
            sendAjax($(this)); // other actions
        }
    });

    // Proceed in Capture modal
    $('#proceedCapture').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#captureModal').modal('hide');
    });

    // Proceed in Fulfill modal
    $('#proceedFulfill').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#fulfillModal').modal('hide');
    });

    // Proceed in Cancel modal
    $('#proceedCancel').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#cancelModal').modal('hide');
    });

    // Proceed in Pending modal
    $('#proceedPending').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#pendingModal').modal('hide');
    });

    // Proceed in Delete modal
    $('#proceedDelete').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#deleteModal').modal('hide');
    });

    function sendAjax(form){
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(res){
                alert(res.message);
                location.reload(); // reload page
            },
            error: function(err){
                alert(err.responseJSON?.error || 'Something went wrong!');
            }
        });
    }
});

</script>

<!-- Capture Authorized Payments Modal -->
<div class="modal fade" id="captureModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Fraud Protection</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          Bulk capturing order payments will only capture authorized payments for <strong>low risk</strong> orders.<br>
          If you have selected any other risk level, those orders will be skipped, and you will need to manually capture those higher-risk orders individually.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="proceedCapture">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Complete Fulfillment Modal -->
<div class="modal fade" id="fulfillModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Limitations</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          Choosing bulk <strong>Complete Order Fulfillment</strong> will only complete digital orders that:
          <br><br>
          - Payment status is <strong>Complete (Paid)<br>
          - Do not require shipping/tracking information<br><br>
          Any order with authorized payment status, refunded payment status, or the order contains physical goods that need to be shipped will need to be managed and/or completed manually.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="proceedFulfill">Proceed</button>
      </div>
    </div>
  </div>
</div>


<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Cancel Order Confirmation</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This will cancel the selected orders and process refunds where applicable.<br>
          This action cannot be undone for refunded orders.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="proceedCancel">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Mark As Pending Modal -->
<div class="modal fade" id="pendingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Mark As Pending Confirmation</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This will mark the selected orders as pending.<br>
          This will update the fulfillment status for the orders.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" id="proceedPending">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Permanently Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Delete Confirmation</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This will permanently delete uncaptured orders only.<br>
          Captured or refunded orders will be skipped.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="proceedDelete">Proceed</button>
      </div>
    </div>
  </div>
</div>



<?php echo $__env->make('layouts.backend.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/seller/order/index.blade.php ENDPATH**/ ?>