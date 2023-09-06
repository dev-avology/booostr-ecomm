<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
        @if(Auth::user()->role_id == 1)
        <a href="{{ url('/admin/dashboard') }}"><img src="/uploads/booostr-logo-long-top-header.png" height="40"/></a>
        @elseif (Auth::user()->role_id == 2)
        <a href="{{ url('/') }}">{{ Config::get('app.name') }}</a>
        @elseif (Auth::user()->role_id == 3)
          @if(!empty(tenant()->logo))
            <a href="#"><img src="{{env('WP_URL')}}{{tenant()->logo}}" style="max-width: 80px;"/></a>
          @else
            <a href="{{ url('/') }}">{{ Config::get('app.name') }}</a>
          @endif
        @elseif (Auth::user()->role_id == 5)
        <a href="{{ url('/') }}">{{ Config::get('app.name') }}</a>
        @endif
            
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('login') }}">{{ Str::limit(env('APP_NAME'), $limit = 1) }}</a>
        </div>
        <ul class="sidebar-menu">
            @if(Auth::user()->role_id == 1)

              @include('admin.adminmenu')
            @elseif (Auth::user()->role_id == 2)

              @include('merchant.merchantmenu')
            @elseif (Auth::user()->role_id == 3)

              @include('seller.sellermenu')
            @elseif (Auth::user()->role_id == 5)

              @include('rider.ridermenu')
            @endif
        </ul>
        @if(Auth::user()->role_id == 3)
        <!-- <div class=" mb-4 p-3 hide-sidebar-mini">
            <a href="{{ url('seller/site-settings') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
              <i class="fas fa-cog"></i> {{ __('App Settings') }}
            </a>
          </div>  -->
        @endif  
    </aside>
</div>
