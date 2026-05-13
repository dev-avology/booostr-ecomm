<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/owl.carousel.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/owl.theme.default.min.css')); ?>">
<?php $__env->stopPush(); ?>
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


<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-stats">
        <div class="card-stats-title"><?php echo e(__('Store Order Statistics')); ?> - <div class="dropdown d-inline">
            <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month" id="orders-month"><?php echo e(Date('F')); ?></a>
            <ul class="dropdown-menu dropdown-menu-sm">
              <li class="dropdown-title"><?php echo e(__('Select Month')); ?></li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='January'): ?> active <?php endif; ?>" data-month="January"><?php echo e(__('January')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='February'): ?> active <?php endif; ?>" data-month="February"><?php echo e(__('February')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='March'): ?> active <?php endif; ?>" data-month="March"><?php echo e(__('March')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='April'): ?> active <?php endif; ?>" data-month="April"><?php echo e(__('April')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='May'): ?> active <?php endif; ?>" data-month="May"><?php echo e(__('May')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='June'): ?> active <?php endif; ?>" data-month="June"><?php echo e(__('June')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='July'): ?> active <?php endif; ?>" data-month="July"><?php echo e(__('July')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='August'): ?> active <?php endif; ?>" data-month="August"><?php echo e(__('August')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='September'): ?> active <?php endif; ?>" data-month="September"><?php echo e(__('September')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='October'): ?> active <?php endif; ?>" data-month="October"><?php echo e(__('October')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='November'): ?> active <?php endif; ?>" data-month="November"><?php echo e(__('November')); ?></a>
              </li>
              <li>
                <a href="#" class="dropdown-item month <?php if(Date('F')=='December'): ?> active <?php endif; ?>" data-month="December"><?php echo e(__('December')); ?></a>
              </li>
            </ul>
          </div>
        </div>
        <div class="card-stats-items">
          <div class="card-stats-item">
            <div class="card-stats-item-count" id="pending_order"></div>
            <div class="card-stats-item-label"><?php echo e(__('Pending')); ?></div>
          </div>
          <div class="card-stats-item">
            <div class="card-stats-item-count" id="completed_order"></div>
            <div class="card-stats-item-label"><?php echo e(__('Completed')); ?></div>
          </div>
          <div class="card-stats-item">
            <div class="card-stats-item-count" id="shipping_order"></div>
            <div class="card-stats-item-label"><?php echo e(__('Processing')); ?></div>
          </div>
        </div>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-archive"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4><?php echo e(__('Total Orders')); ?></h4>
        </div>
        <div class="card-body" id="total_order"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2">
      <div class="card-chart">
        <canvas id="sales_of_earnings_chart" height="80"></canvas>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4><?php echo e(__('Gross Sales')); ?> - <?php echo e(date('Y')); ?>&nbsp;&nbsp;<i class="fas fa-info-circle" id="tooltip-icon"></i></h4>

        </div>
        <div class="card-body" id="sales_of_earnings">
          <img src="<?php echo e(asset('uploads/loader.gif')); ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12">
    <div class="card card-statistic-2 ">
      <div class="card-chart">
        <canvas id="total-sales-chart" height="80"></canvas>
      </div>
      <div class="card-icon shadow-primary bg-primary">
        <i class="fas fa-shopping-bag"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4><?php echo e(__('Fulfilled Orders')); ?> - <?php echo e(date('Y')); ?></h4>
        </div>
        <div class="card-body" id="total_sales">
          <img src="<?php echo e(asset('uploads/loader.gif')); ?>" class="loads">
        </div>
      </div>
    </div>
  </div>

  

  




  
</div>
 <div class="row pending_order_list" id="pending_order_list">
    
  </div>
<div class="row">
  <div class="col-lg-8 col-md-12 col-12 col-sm-12">
    <div class="card card-primary">
      <div class="card-header">
        <h4 class="card-header-title"><?php echo e(__('Sales Performance')); ?>

          <img src="<?php echo e(asset('uploads/loader.gif')); ?>" height="20" id="earning_performance">
        </h4>
        <div class="card-header-action">
          <select class="form-control selectric" id="perfomace">
            <option value="7"><?php echo e(__('Last 7 Days')); ?></option>
            <option value="15"><?php echo e(__('Last 15 Days')); ?></option>
            <option value="30"><?php echo e(__('Last 30 Days')); ?></option>
            <option value="365"><?php echo e(__('Last 365 Days')); ?></option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <canvas id="myChart" height="158"></canvas>
      </div>
    </div>

    <div class="row">
       <div class="col-12 col-md-6">

            <div class="card card-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2"><?php echo e(__('Today\'s Total Sales')); ?></h6>

                            <span class="h2 mb-0" id="today_total_sales"><img src="<?php echo e(asset('uploads/loader.gif')); ?>"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">

            <div class="card card-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2"><?php echo e(__('Today\'s Orders')); ?></h6>

                            <span class="h2 mb-0" id="today_order"><img src="<?php echo e(asset('uploads/loader.gif')); ?>"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="col-6">

            <div class="card card-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2"><?php echo e(__('Yesterday')); ?></h6>

                            <span class="h2 mb-0" id="yesterday_total_sales"><img src="<?php echo e(asset('uploads/loader.gif')); ?>"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

         <div class="col-6">

            <div class="card card-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2"><?php echo e(__('7 days')); ?></h6>

                            <span class="h2 mb-0" id="last_seven_days_total_sales"><img src="<?php echo e(asset('uploads/loader.gif')); ?>"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-6">

            <div class="card card-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">

                            <h6 class="text-uppercase text-muted mb-2"><?php echo e(__('This Month')); ?></h6>

                            <span class="h2 mb-0" id="monthly_total_sales"><img src="<?php echo e(asset('uploads/loader.gif')); ?>"></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="col-6">

            <div class="card card-primary">
               <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">

                        <h6 class="text-uppercase text-muted mb-2"><?php echo e(__('Last Month')); ?></h6>

                        <span class="h2 mb-0" id="last_month_total_sales"><img src="<?php echo e(asset('uploads/loader.gif')); ?>"></span>
                    </div>
                </div> 
            </div>
        </div>
      </div>
    </div>


    <!-- <div class="card card-primary">
      <div class="card-header">
        <h4><?php echo e(__('Top Selling Products')); ?></h4>
      </div>
      <div class="card-body">
        <div class="owl-carousel owl-theme products-carousel top_selling_products">
          
        
        </div>
      </div>
    </div> -->

    <!-- <div class="card card-primary">
      <div class="card-header">
        <h4><?php echo e(__('Top Rated Products')); ?></h4>
      </div>
      <div class="card-body">
        <div class="owl-carousel owl-theme products-carousel max_rated_products">
         
        </div>
      </div>
    </div> -->

    <!-- <div class="card card-primary">
      <div class="card-header">
        <h4><?php echo e(__('Top Customers')); ?></h4>

      </div>
      <div class="card-body">
        <div class="owl-carousel owl-theme products-carousel top_customer">
         
          
        </div>
      </div>
    </div> -->
  </div>
  <div class="col-lg-4">
  
   
    <?php if(tenant('qr_code') == 'on'): ?>
    <!-- <div class="card card-primary">
      <div class="card-header">
        <h4><?php echo e(__('Scan Your Site')); ?></h4>
      </div>
      <div class="card-body">
        <div class="qrcode-area">
          <img id="qrcode_img" src="data:image/png;base64,<?php echo e(DNS2D::getBarcodePNG(url('/'), 'QRCODE',15,15)); ?>" alt="">  
        </div>
        <div class="button mt-3">
          <button class="btn btn-primary w-100 btn-lg downloadPng" ><?php echo e(__('Download')); ?></button>
        </div>
      </div>
    </div> -->
    <?php endif; ?>

    <div class="card gradient-bottom card-primary">
      <div class="card-header">
        <h4><?php echo e(__('Today\'s Pending Orders')); ?></h4>
      </div>
      <div class="card-body top-5-scroll">
        <ul class="list-unstyled list-unstyled-border todays_orders_list">
        
        </ul>
      </div>
      <div class="card-footer pt-3 d-flex justify-content-center">
        <div class="budget-price justify-content-center">
          <div class="budget-price-square bg-primary" data-width="20"></div>
          <div class="budget-price-label"><a href="<?php echo e(route('seller.order.index')); ?>"><?php echo e(__('Orders')); ?></a></div>
        </div>
      </div>
    </div>

    <!-- <div class="card card-primary">
      <div class="card-header">
        <h4><?php echo e(__('Subscription Status')); ?></h4> 
        <div class="card-header-action ">
          <span><?php echo e(__('Expire')); ?>: <?php echo e(\Carbon\Carbon::parse(tenant('will_expire'))->format('d-F-Y')); ?></span>

        </div>
      </div>
      <div class="card-body" >
        <div class="top-5-scroll">
        <ul class="list-unstyled list-unstyled-border subscription_data_list">
        
        </ul>
        </div>
        <div class="button mt-3">
          <button class="btn btn-primary w-100 btn-lg clear_site_cache" ><?php echo e(__('Clear Site Cache')); ?></button>
        </div>
      </div>
    </div> -->

    


  </div>
</div>
<div class="row">
  <div class="col-lg-4">
    
  </div>
</div>

<?php if(tenant('push_notification') == 'on' && env('FMC_SERVER_API_KEY') != null): ?>
<div class="notification-button-area notification_button">
  <button id="btn-nft-enable" class="btn btn-danger btn-xs btn-flat notification_button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M20 17h2v2H2v-2h2v-7a8 8 0 1 1 16 0v7zM9 21h6v2H9v-2z"/></svg></button>
</div>
<?php endif; ?>
  

<input type="hidden" id="dashboard_static" value="<?php echo e(url('/seller/dashboard/static')); ?>">
<input type="hidden" id="dashboard_perfomance" value="<?php echo e(url('/seller/dashboard/perfomance')); ?>">
<input type="hidden" id="deposit_perfomance" value="<?php echo e(url('/seller/dashboard/deposit/perfomance')); ?>">
<input type="hidden" id="dashboard_order_statics" value="<?php echo e(url('/seller/dashboard/order_statics')); ?>">
<input type="hidden" id="gif_url" value="<?php echo e(asset('uploads/loader.gif')); ?>">
<input type="hidden" id="month" value="<?php echo e(date('F')); ?>">
<input type="hidden" id="new_order_link" value="<?php echo e(route('seller.orders.new')); ?>">
<input type="hidden" id="save_token" value="<?php echo e(route('seller.save-token')); ?>">
<input type="hidden" id="currency_settings" value="<?php echo e(get_option('currency_data')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('admin/assets/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/chart.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/jquery.sparkline.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/seller.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.backend.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/seller/dashboard.blade.php ENDPATH**/ ?>