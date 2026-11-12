@extends('theme.resto.layouts.app')

@section('content')
<!-- Start Order Confirm Area -->
<div class="order-confirm py-5" style="background-color: #f9f9f9; min-height: 65vh;">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-12">
				<div class="confirm-content text-center">
					<i class="icofont-check-circled" style="color: var(--primary); font-size: 55px; background: #fff; width: 85px; height: 85px; line-height: 85px; border-radius: 50%; box-shadow: 0px 4px 15px rgba(0,0,0,0.08); display: inline-block; margin-bottom: 25px;"></i>
					
					<h1 style="font-size: 28px; font-weight: 700; color: #081828; margin-bottom: 10px;">{{ __('Your order has been placed successfully!') }}</h1>
					
					<p class="text-muted mb-4" style="font-size: 15px; padding: 0;">
						{{ __('Thank you for your purchase from') }} <strong style="color: #081828;">{{ $club_info['club_name'] ?? 'our store' }}</strong>.
					</p>

					@if($order)
					<!-- Order Details Card (Resto Theme Style) -->
					<div class="order-summary-card text-left mx-auto mb-4 p-4" style="max-width: 480px; background: #ffffff; border-radius: 8px; box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.06); border: 1px solid #ebebeb;">
						<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
							<span class="text-muted" style="font-size: 14px;">{{ __('Order Number') }}:</span>
							<strong style="color: #081828; font-size: 15px;">{{ $order->invoice_no }}</strong>
						</div>
						<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
							<span class="text-muted" style="font-size: 14px;">{{ __('Order Date') }}:</span>
							<strong style="color: #081828; font-size: 15px;">{{ $order->created_at ? $order->created_at->format('m/d/Y') : date('m/d/Y') }}</strong>
						</div>
						<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
							<span class="text-muted" style="font-size: 14px;">{{ __('Total Items') }}:</span>
							<strong style="color: #081828; font-size: 15px;">{{ $order->orderitems ? $order->orderitems->sum('qty') : 1 }}</strong>
						</div>
						<div class="d-flex justify-content-between align-items-center pt-3">
							<span class="text-muted" style="font-size: 15px; font-weight: 600;">{{ __('Total Amount') }}:</span>
							<strong style="color: var(--primary); font-size: 20px; font-weight: 700;">${{ number_format($order->total ?? 0, 2) }}</strong>
						</div>
					</div>
					@else
					<h4 class="mb-4" style="color: #081828;">{{ __('Order No') }}: {{ $orderno }}</h4>
					@endif

					<p class="text-muted px-2" style="font-size: 14px; max-width: 620px; margin: 0 auto 25px; line-height: 1.6;">
						{{ __('A confirmation receipt has been sent to your email. If you have any questions about your order, please contact us at') }} 
						@if(!empty($club_info['club_email']))
							<a href="mailto:{{ $club_info['club_email'] }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">{{ $club_info['club_email'] }}</a>.
						@else
							{{ __('our organization email.') }}
						@endif
					</p>

					<div class="button mt-3">
						<a href="{{ url('/') }}" class="btn primary animate" style="background: var(--primary); color: #ffffff; padding: 13px 32px; border-radius: 5px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; text-decoration: none; border: none; display: inline-block;">
							{{ __('Go To Store Home') }}
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Order Confirm Area -->
@endsection
