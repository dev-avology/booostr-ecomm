@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Customer Dashboard Home View
    Features:
      - 2-Column Responsive Dashboard Layout with brand blue
      - Purchases & Pending Orders Summary Cards with correct redirections
      - Recent Orders Table with accurate payment/order status badges
      - User welcome greeting with Booostr meta username resolution
    =================================================================================
--}}
@php
    $userMeta = !empty(Auth::user()->meta) ? json_decode(Auth::user()->meta, true) : [];
    $displayName = !empty($userMeta['username']) ? $userMeta['username'] : Auth::user()->name;
@endphp

<!-- Start Summary Cards -->
<div class="details-lists mb-4">
	<div class="row g-3">
		<div class="col-lg-6 col-md-6 col-12">
			<!-- Total Orders / Purchases Card -->
			<div class="dash-stat-card dash-stat-primary">
				<div class="dash-stat-icon">
					<i class="icofont-shopping-cart"></i>
				</div>
				<div class="dash-stat-body">
					<div>
						<h3 class="dash-stat-number">{{ $total_orders }}</h3>
						<span class="dash-stat-label">{{ __('Purchases / Total Orders') }}</span>
					</div>
					<a href="{{ url('/customer/orders') }}" class="dash-stat-btn">
						{{ __('View All') }} &rarr;
					</a>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-12">
			<!-- Pending Orders Card -->
			<div class="dash-stat-card dash-stat-dark">
				<div class="dash-stat-icon">
					<i class="icofont-sand-clock"></i>
				</div>
				<div class="dash-stat-body">
					<div>
						<h3 class="dash-stat-number">{{ $total_pending_orders }}</h3>
						<span class="dash-stat-label">{{ __('Pending Orders') }}</span>
					</div>
					@php
						$pendingLink = url('/customer/orders');
						if (!empty($pendingStatusIds) && count($pendingStatusIds) === 1) {
							$pendingLink = url('/customer/orders') . '?status=' . $pendingStatusIds[0];
						}
					@endphp
					<a href="{{ $pendingLink }}" class="dash-stat-btn dash-stat-btn-dark">
						{{ __('Check Status') }} &rarr;
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Summary Cards -->

<div class="row">
	<div class="col-lg-7 col-md-12 col-12 mb-4">
		<!-- Start Recent Orders -->
		<div class="dash-recent-card">
			<div class="dash-recent-header">
				<h3 class="dash-recent-title">
					<i class="icofont-history me-2"></i>{{ __('Recent Purchases') }}
				</h3>
				<a href="{{ url('/customer/orders') }}" class="dash-view-all-link">{{ __('View Purchase History') }}</a>
			</div>

			@if(($orders ?? collect())->count() > 0)
			<div class="table-responsive">
				<table class="table align-middle mb-0 dash-orders-table">
					<thead>
						<tr>
							<th>{{ __('Order') }}</th>
							<th class="text-center">{{ __('Status') }}</th>
							<th class="text-center">{{ __('Payment') }}</th>
							<th>{{ __('Date') }}</th>
							<th class="text-end">{{ __('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($orders as $row)
						@php
							// Order Status styling
							$statusName = $row->orderstatus->name ?? __('Pending');
							$statusLower = strtolower($statusName);
							$orderStatusClass = 'order-badge-pending';

							if (strpos($statusLower, 'complete') !== false || strpos($statusLower, 'deliver') !== false) {
								$orderStatusClass = 'order-badge-completed';
							} elseif (strpos($statusLower, 'cancel') !== false || strpos($statusLower, 'fail') !== false) {
								$orderStatusClass = 'order-badge-cancelled';
							} elseif (strpos($statusLower, 'process') !== false) {
								$orderStatusClass = 'order-badge-processing';
							}

							// Payment Status styling (1-to-1 matching like orders_table.blade.php)
							if ($row->payment_status == 1) {
								$paymentText = __('Paid');
								$paymentClass = 'payment-badge-paid';
								$paymentIcon = 'icofont-check-circled';
							} elseif ($row->payment_status == 5) {
								$paymentText = __('Refunded');
								$paymentClass = 'payment-badge-refunded';
								$paymentIcon = 'icofont-reply';
							} elseif ($row->payment_status == 4) {
								$paymentText = __('Processing');
								$paymentClass = 'payment-badge-processing';
								$paymentIcon = 'icofont-refresh';
							} elseif ($row->payment_status == 2) {
								$paymentText = __('Pending');
								$paymentClass = 'payment-badge-pending';
								$paymentIcon = 'icofont-clock-time';
							} elseif ($row->payment_status == 3) {
								$paymentText = __('Incomplete');
								$paymentClass = 'payment-badge-incomplete';
								$paymentIcon = 'icofont-exclamation-circle';
							} elseif ($row->payment_status == 0) {
								$paymentText = __('Cancelled');
								$paymentClass = 'payment-badge-cancelled';
								$paymentIcon = 'icofont-close-circled';
							} else {
								$paymentText = __('Pending');
								$paymentClass = 'payment-badge-pending';
								$paymentIcon = 'icofont-clock-time';
							}

							// Total Items
							$totalQty = $row->orderitems->sum('qty');
							if (!$totalQty || $totalQty < 1) {
								$totalQty = $row->orderitems_count ?? 1;
							}
						@endphp
						<tr class="dash-order-row">
							<td>
								<a href="{{ url('/customer/order/'.$row->id) }}" class="dash-invoice-link">
									#{{ $row->invoice_no }}
								</a>
								<div class="dash-order-price">
									<span class="render_currency">{{ number_format($row->total, 2) }}</span>
									<span class="dash-item-count">· {{ $totalQty }} {{ $totalQty == 1 ? __('item') : __('items') }}</span>
								</div>
							</td>
							<td class="text-center">
								<span class="badge order-status-badge {{ $orderStatusClass }}">
									{{ $statusName }}
								</span>
							</td>
							<td class="text-center">
								<span class="badge payment-status-badge {{ $paymentClass }}">
									<i class="{{ $paymentIcon }} me-1"></i>{{ $paymentText }}
								</span>
							</td>
							<td>
								<div class="dash-order-date">{{ $row->created_at->format('M d, Y') }}</div>
								<div class="dash-order-ago">{{ $row->created_at->diffForHumans() }}</div>
							</td>
							<td class="text-end">
								<a href="{{ url('/customer/order/'.$row->id) }}" class="dash-detail-btn">
									<i class="icofont-eye me-1"></i>{{ __('View') }}
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			@else
			<div class="dash-empty-state">
				<i class="icofont-shopping-cart"></i>
				<p>{{ __('No purchases found yet.') }}</p>
				<a href="{{ url('/products') }}" class="dash-browse-btn">{{ __('Browse Products') }}</a>
			</div>
			@endif
		</div>
		<!-- End Recent Orders -->
	</div>

	<div class="col-lg-5 col-md-12 col-12">
		<!-- Start Welcome Card -->
		<div class="dash-welcome-card">
			<div class="dash-welcome-header">
				<div class="dash-welcome-avatar">
					{{ strtoupper(substr($displayName, 0, 1)) }}
				</div>
				<div>
					<h3 class="dash-welcome-title">{{ __('Welcome') }}</h3>
					<p class="dash-welcome-name mb-0">
						{{ __('Hello') }}, <strong>{{ $displayName }}</strong>
					</p>
				</div>
			</div>

			<p class="dash-welcome-text">
				{{ __('From your account dashboard you can view your') }} 
				<a href="{{ url('/customer/orders') }}">{{ __('recent purchases') }}</a>, 
				{{ __('manage your') }} 
				<a href="{{ url('/customer/edit-profile') }}">{{ __('profile & addresses') }}</a>, 
				{{ __('and update your') }} 
				<a href="{{ url('/customer/change-password') }}">{{ __('account password') }}</a>.
			</p>

			<div class="dash-quick-links">
				<a href="{{ url('/customer/orders') }}" class="dash-quick-link">
					<i class="icofont-shopping-cart"></i>
					<span>{{ __('Purchase History') }}</span>
				</a>
				<a href="{{ url('/customer/edit-profile') }}" class="dash-quick-link">
					<i class="icofont-ui-user"></i>
					<span>{{ __('Edit Profile') }}</span>
				</a>
				<a href="{{ url('/customer/change-password') }}" class="dash-quick-link">
					<i class="icofont-key"></i>
					<span>{{ __('Change Password') }}</span>
				</a>
			</div>
		</div>
		<!-- End Welcome Card -->
	</div>
</div>

<style>
	/* ======================= Stat Cards ======================= */
	.dash-stat-card {
		position: relative;
		padding: 28px 24px 28px 100px;
		border-radius: 10px;
		border: none;
		background-image: none;
		overflow: hidden;
	}

	.dash-stat-primary {
		background: linear-gradient(135deg, #528CD7 0%, #3a6db8 100%);
	}

	.dash-stat-dark {
		background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
	}

	.dash-stat-icon {
		position: absolute;
		left: 24px;
		top: 50%;
		transform: translateY(-50%);
		height: 56px;
		width: 56px;
		line-height: 56px;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.2);
		text-align: center;
	}

	.dash-stat-icon i {
		font-size: 26px;
		color: #ffffff;
	}

	.dash-stat-body {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		flex-wrap: wrap;
	}

	.dash-stat-number {
		font-size: 30px;
		font-weight: 700;
		color: #ffffff !important;
		margin: 0;
		line-height: 1.1;
	}

	.dash-stat-label {
		color: rgba(255, 255, 255, 0.85);
		font-size: 13px;
		font-weight: 500;
		display: block;
		margin-top: 4px;
	}

	.dash-stat-btn {
		display: inline-flex;
		align-items: center;
		padding: 7px 18px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
		text-decoration: none !important;
		white-space: nowrap;
		background: #ffffff;
		color: #528CD7 !important;
		transition: all 0.2s ease;
		box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
	}

	.dash-stat-btn:hover {
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
	}

	.dash-stat-btn-dark {
		color: #2c3e50 !important;
	}

	/* ======================= Recent Orders Card ======================= */
	.dash-recent-card {
		background: #ffffff;
		border: 1px solid #e2e8f0;
		border-radius: 10px;
		overflow: hidden;
	}

	.dash-recent-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 18px 24px;
		border-bottom: 1px solid #f1f5f9;
	}

	.dash-recent-title {
		font-size: 16px;
		font-weight: 600;
		color: #1e293b;
		margin: 0;
	}

	.dash-recent-title i {
		color: #528CD7;
	}

	.dash-view-all-link {
		font-size: 13px;
		color: #528CD7 !important;
		text-decoration: none !important;
		font-weight: 500;
		transition: color 0.2s ease;
	}

	.dash-view-all-link:hover {
		color: #3a6db8 !important;
		text-decoration: underline !important;
	}

	/* Table */
	.dash-orders-table thead th {
		background: #f8fafc;
		border-bottom: 2px solid #e2e8f0;
		font-size: 12px;
		font-weight: 600;
		color: #64748b;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		padding: 12px 16px;
	}

	.dash-orders-table tbody td {
		padding: 14px 16px;
		font-size: 14px;
		border-bottom: 1px solid #f1f5f9;
		vertical-align: middle;
	}

	.dash-order-row:hover {
		background-color: #f8fafc;
	}

	.dash-invoice-link {
		color: #528CD7 !important;
		font-size: 15px;
		font-weight: 700;
		text-decoration: none !important;
		transition: color 0.2s ease;
	}

	.dash-invoice-link:hover {
		color: #3a6db8 !important;
		text-decoration: underline !important;
	}

	.dash-order-price {
		margin-top: 3px;
		font-size: 13px;
		color: #334155;
		font-weight: 600;
	}

	.dash-item-count {
		color: #94a3b8;
		font-weight: 400;
		margin-left: 2px;
	}

	.dash-order-date {
		font-size: 13px;
		color: #334155;
		font-weight: 500;
	}

	.dash-order-ago {
		font-size: 12px;
		color: #94a3b8;
	}

	.dash-detail-btn {
		display: inline-flex;
		align-items: center;
		padding: 5px 12px;
		border: 1.5px solid #528CD7;
		border-radius: 6px;
		font-size: 12px;
		font-weight: 600;
		color: #528CD7 !important;
		text-decoration: none !important;
		transition: all 0.2s ease;
		background: transparent;
	}

	.dash-detail-btn:hover {
		background: #528CD7;
		color: #ffffff !important;
	}

	/* Badges (same as orders_table) */
	.order-status-badge {
		font-size: 12px;
		font-weight: 600;
		padding: 5px 10px;
		border-radius: 20px;
		display: inline-block;
	}

	.order-badge-pending {
		background-color: #fffbeb;
		color: #b45309;
		border: 1px solid #fde68a;
	}

	.order-badge-processing {
		background-color: #eff6ff;
		color: #1d4ed8;
		border: 1px solid #bfdbfe;
	}

	.order-badge-completed {
		background-color: #ecfdf5;
		color: #047857;
		border: 1px solid #a7f3d0;
	}

	.order-badge-cancelled {
		background-color: #fef2f2;
		color: #b91c1c;
		border: 1px solid #fecaca;
	}

	.payment-status-badge {
		font-size: 12px;
		font-weight: 600;
		padding: 5px 10px;
		border-radius: 20px;
		display: inline-flex;
		align-items: center;
	}

	.payment-badge-paid {
		background-color: #ecfdf5;
		color: #047857;
		border: 1px solid #a7f3d0;
	}

	.payment-badge-refunded {
		background-color: #fef2f2;
		color: #dc2626;
		border: 1px solid #fecaca;
	}

	.payment-badge-processing {
		background-color: #eff6ff;
		color: #1d4ed8;
		border: 1px solid #bfdbfe;
	}

	.payment-badge-pending {
		background-color: #fffbeb;
		color: #b45309;
		border: 1px solid #fde68a;
	}

	.payment-badge-cancelled {
		background-color: #f1f5f9;
		color: #64748b;
		border: 1px solid #cbd5e1;
	}

	.payment-badge-incomplete {
		background-color: #fff7ed;
		color: #c2410c;
		border: 1px solid #ffedd5;
	}

	/* Empty State */
	.dash-empty-state {
		text-align: center;
		padding: 48px 24px;
	}

	.dash-empty-state i {
		font-size: 48px;
		color: #cbd5e1;
		display: block;
		margin-bottom: 12px;
	}

	.dash-empty-state p {
		color: #94a3b8;
		font-size: 14px;
		margin-bottom: 16px;
	}

	.dash-browse-btn {
		display: inline-flex;
		align-items: center;
		padding: 8px 20px;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 600;
		color: #ffffff !important;
		background: #528CD7;
		text-decoration: none !important;
		transition: background 0.2s ease;
	}

	.dash-browse-btn:hover {
		background: #3a6db8;
	}

	/* ======================= Welcome Card ======================= */
	.dash-welcome-card {
		background: #ffffff;
		border: 1px solid #e2e8f0;
		border-radius: 10px;
		padding: 24px;
	}

	.dash-welcome-header {
		display: flex;
		align-items: center;
		gap: 14px;
		margin-bottom: 16px;
		padding-bottom: 16px;
		border-bottom: 1px solid #f1f5f9;
	}

	.dash-welcome-avatar {
		height: 48px;
		width: 48px;
		border-radius: 50%;
		background: linear-gradient(135deg, #528CD7 0%, #3a6db8 100%);
		color: #ffffff;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 20px;
		font-weight: 700;
		flex-shrink: 0;
	}

	.dash-welcome-title {
		font-size: 18px;
		font-weight: 600;
		color: #1e293b;
		margin: 0 0 2px 0;
	}

	.dash-welcome-name {
		font-size: 14px;
		color: #64748b;
	}

	.dash-welcome-text {
		font-size: 14px;
		line-height: 1.7;
		color: #64748b;
		margin-bottom: 20px;
	}

	.dash-welcome-text a {
		color: #528CD7 !important;
		font-weight: 600;
		text-decoration: none !important;
	}

	.dash-welcome-text a:hover {
		text-decoration: underline !important;
	}

	/* Quick Links */
	.dash-quick-links {
		display: flex;
		flex-direction: column;
		gap: 8px;
	}

	.dash-quick-link {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px 16px;
		border: 1px solid #f1f5f9;
		border-radius: 8px;
		text-decoration: none !important;
		transition: all 0.2s ease;
		color: #334155 !important;
		font-size: 14px;
		font-weight: 500;
	}

	.dash-quick-link:hover {
		border-color: #528CD7;
		background: #f0f5fd;
		color: #528CD7 !important;
	}

	.dash-quick-link i {
		font-size: 18px;
		color: #528CD7;
		width: 20px;
		text-align: center;
	}

	/* ======================= Responsive ======================= */
	@media (max-width: 768px) {
		.dash-stat-card {
			padding: 24px 20px 24px 80px;
		}

		.dash-stat-icon {
			left: 16px;
			height: 48px;
			width: 48px;
			line-height: 48px;
		}

		.dash-stat-icon i {
			font-size: 22px;
		}

		.dash-stat-number {
			font-size: 24px;
		}

		.dash-recent-header {
			flex-direction: column;
			align-items: flex-start;
			gap: 8px;
		}

		.dash-orders-table thead {
			display: none;
		}

		.dash-orders-table,
		.dash-orders-table tbody,
		.dash-orders-table tr,
		.dash-orders-table td {
			display: block;
			width: 100%;
		}

		.dash-orders-table tr {
			border-bottom: 1px solid #e2e8f0;
			padding: 12px 0;
		}

		.dash-orders-table td {
			text-align: left !important;
			padding: 6px 16px !important;
			border: none !important;
		}

		.dash-orders-table td:before {
			content: attr(data-label);
			font-weight: 600;
			font-size: 11px;
			color: #94a3b8;
			text-transform: uppercase;
			display: block;
			margin-bottom: 2px;
		}
	}
</style>
@endsection
