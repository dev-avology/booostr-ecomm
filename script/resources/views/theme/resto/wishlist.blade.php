@extends('theme.resto.layouts.app')
@section('content')
@php
$autoloaddata=getautoloadquery();
$currency_data=$autoloaddata['currency_data'];
@endphp
@php
    $wishlistDescription = __('View and manage your saved items.');
@endphp
@include('theme.resto.components.page-banner', [
    'title' => __('Wishlist'),
    'description' => $wishlistDescription,
    'breadcrumbs' => [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home'],
        ['url' => '', 'label' => __('Wishlist'), 'icon' => 'icofont-heart'],
    ]
])
		
		
		<!-- Shopping Cart -->
		<div class="shopping-cart section">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<!-- Shopping Summery -->
						<table class="table shopping-summery">
							<thead>
								<tr class="main-hading">
									<th><i class="icofont-price"></i> {{ __('PRODUCT') }}</th>
									<th><i class="icofont-pencil-alt-5"></i> {{ __('NAME') }}</th>
									<th class="text-center"><i class="icofont-money"></i> {{ __('UNIT PRICE') }}</th>
									
									
									<th class="text-center"><i class="icofont-trash"></i>{{ __('Remove') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($contents as $row)
								<tr>
									<td class="image" data-title="No"><img src="{{ asset($row->options->preview) }}" alt="{{ $row->name }}"></td>
									<td class="product-desc" data-title="Description">
										<p class="product-name"><a href="{{ url('/product',$row->options->slug) }}">{{ $row->name }}</a></p>
										
									</td>
									<td class="price" data-title="Price"><span>{{ number_format($row->price,2) }} </span></td>
									
									
									<td class="action" data-title="Remove"><a href="{{ url('/remove-wishlist',$row->rowId) }}" class=""><i class="icofont-trash remove-icon"></i></a></td>
								</tr>
								@endforeach
								
							</tbody>
						</table>
						<!--/ End Shopping Summery -->
					</div>
				</div>
				
			</div>
		</div>
		<!--/ End Shopping Cart -->
@endsection