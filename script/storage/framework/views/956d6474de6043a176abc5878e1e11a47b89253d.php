<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <ul class="navbar-nav mr-3">
      <li><a href="#" data-toggle="sidebar" class="nav-link collapse_btn nav-link-lg"><i class="fas fa-bars"></i></a></li>

    </ul>
    <div class="search-element"></div>
  </form>
  <ul class="navbar-nav navbar-right">
    <?php if(Auth::User()->role_id == 3): ?>
    <!-- <li><a target="_blank" href="<?php echo e(url('/')); ?>" class="btn btn-white view-demo"><i class="fas fa-eye"></i> View Site</a></li> -->
    <?php endif; ?>
    <?php if(Auth()->user()->role_id == 3 && url()->current() == url('/seller/dashboard')): ?>
    <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i class="far fa-bell"></i></a>
      <div class="dropdown-menu dropdown-list dropdown-menu-right">
        <div class="dropdown-header"><?php echo e(__('Notifications')); ?></div>
        <div class="dropdown-list-content dropdown-list-icons"></div>
        <div class="dropdown-footer text-center">
          <a href="<?php echo e(route('seller.order.index')); ?>"><?php echo e(__('View All ')); ?><i class="fas fa-chevron-right"></i></a>
        </div>
      </div>
    </li>
    <?php endif; ?>
    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">

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

        <!-- <img alt="image" src='https://ui-avatars.com/api/?name=<?php echo e(Auth()->user()->name); ?>' class="rounded-circle profile-widget-picture "> -->
        <img alt="image" src="<?php echo e($result['data'] ?? ''); ?>" class="rounded-circle profile-widget-picture ">

      <div class="d-sm-none d-lg-inline-block"><?php echo e(__('Hi')); ?>, <?php echo e(ucwords(str_replace("-", " ", Auth::user()->name))); ?></div></a>
      <div class="dropdown-menu dropdown-menu-right">


        <a href="<?php echo e(url('/mysettings')); ?>" class="dropdown-item has-icon">
          <i class="far fa-user"></i> <?php echo e(__('Profile')); ?>

        </a>


        <div class="dropdown-divider"></div>
        <a href="/close-store-maneger" class="dropdown-item has-icon text-danger" onclick="event.preventDefault();
        document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> <?php echo e(__('Close Store Manager')); ?>

        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
          <?php echo csrf_field(); ?>
        </form>
    </div>
  </li>
</ul>
</nav>
<?php /**PATH D:\wamp64\www\booostr-ecomm\script\resources\views/layouts/backend/partials/header.blade.php ENDPATH**/ ?>