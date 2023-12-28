@extends('layouts.backend.app')

@section('title', 'Dashboard')

@section('head')
    @include('layouts.backend.partials.headersection', [
        'title' => $info->invoice_no,
        'prev' => url('seller/order'),
    ])
@endsection

@section('content')
    <div class="row" id="order">
        <div class="col-12 col-lg-8">
            <div class="card card-primary">
                <div class="card-body">
                    <ul class="list-group list-group-lg list-group-flush list">
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <strong>{{ __('Product') }}</strong>
                                </div>
                                <div class="col-3 text-right">
                                    <strong>{{ __('Amount') }}</strong>
                                </div>
                                <div class="col-3 text-right">
                                    <strong>{{ __('Total') }}</strong>
                                </div>
                            </div>
                        </li>
                        @php $subtotal = 0; @endphp
                        @foreach ($info->orderitems ?? [] as $row)
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        @php
                                            $variations = json_decode($row->info ?? '');
                                            $options = $variations->options ?? [];
                                        @endphp
                                        <a href="{{ url('/seller/product/' . $row->term->id . '/edit') }}">{{ $row->term->title ?? '' }}
                                            @if ($options == '' && !empty($variations->sku))
                                                ({{ $row->term->full_id }})
                                            @elseif($options == '' && !empty($variations->sku))
                                                ({{ $variations->sku }})
                                            @elseif($options == '')
                                                ({{ $row->term->full_id }})
                                            @endif
                                            <br>
                                        </a>
                                        @foreach ($options ?? [] as $key => $item)
                                            @php $product_options = $item->varition_options; @endphp
                                            @foreach($item->varitions as $sel_val)
                                                @php $cur_opt_name = array_filter($product_options,function ($x) use ($sel_val) {
                                                    return $x->id == $sel_val->pivot->productoption_id;
                                                } );
                                                @endphp

                                            <strong>{{reset($cur_opt_name)->category->name}} : </strong>{{$sel_val->name}}<br>
                                            @endforeach
                                                <hr>
                                            @endforeach
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($row->amount) }} × {{ $row->qty }}
                                    </div>
                                    <div class="col-3 text-right">
                                        @php $subtotal = $subtotal + $row->amount*$row->qty; @endphp
                                        {{ currency_formate($row->amount * $row->qty) }}
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('SubTotal') }}</div>
                                <div class="col-3 text-right"> {{ currency_formate($subtotal) }} </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Order Discount') }}@isset($info->coupon_code) ({{ $info->coupon_code }}) @endisset</div>
                                <div class="col-3 text-right"> - {{ currency_formate($info->discount) }} </div>
                            </div>
                        </li>
                        @if ($info->order_method == 'delivery')
                            @php
                                $shipping_price = $info->shippingwithinfo->shipping_price ?? 0;
                            @endphp
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        {{ $info->shipping_info->shipping_method->name ?? '' }}
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ __('Shipping Fee') }}
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($shipping_price) }}
                                    </div>
                                </div>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Tax') }}</div>
                                <div class="col-3 text-right"> {{ currency_formate($info->tax) }} </div>
                            </div>
                        </li>

                        @php
                            $credit_card_fee = 0;
                            $booster_platform_fee = 0;
                            $shipping_price = $shipping_price ?? 0;
                            if (!empty($ordermeta)) {
                                $credit_card_fee = $ordermeta->credit_card_fee;
                                $booster_platform_fee = $ordermeta->booster_platform_fee;
                            }

                        @endphp
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Order Total') }}</div>
                                {{-- <div class="col-3 text-right">{{ currency_formate($info->total-$credit_card_fee-$booster_platform_fee) }}</div> --}}
                                <div class="col-3 text-right">{{ currency_formate($info->total) }}</div>
                            </div>
                        </li>
                        @php
                            $club_info = tenant_club_info();

                        @endphp

                        <li class="list-group-item">
                            <div class="row align-items-center text-grey">
                                <div class="col-9 text-right">{{ __('Credit Card Processing ') }}</div>

                                <div class="col-3 text-right">{{ currency_formate($credit_card_fee) }}</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center text-grey">
                                <div class="col-9 text-right">{{ __('Booostr Platform Fee') }}
                                    {{ !empty($club_info['is_pro']) ? '(1.75%)' : '(3.5%)' }}</div>
                                <div class="col-3 text-right">{{ currency_formate($booster_platform_fee) }}</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center text-grey">
                                <div class="col-9 text-right">
                                    {{ __('Net Order Total (Order Total - Credit Card Processing and Booostr Platform Fee)') }}
                                </div>

                                <div class="col-3 text-right">
                                    {{ currency_formate($info->total - $credit_card_fee - $booster_platform_fee) }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="text-right">
                        <form method="POST" action="{{ route('seller.order.update', $info->id . '_' . $info->user_id) }}"
                            accept-charset="UTF-8" class="d-inline ajaxform">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
								<!-- @if ($info->order_method == 'delivery')
									@php
										$rider = $info->shippingwithinfo->user_id ?? '';
									@endphp
									<div class="col-sm-4">
									<div class="form-group text-left">
									<label >Select Rider</label>
									<select class="form-control selectric" name="rider">
									<option value=""><b>{{ __('Select Rider') }}</b></option>
									@foreach ($riders as $row)
										<option value="{{ $row->id }}" @if ($rider == $row->id) selected="" @endif>{{ $row->name }} (#{{ $row->id }})</option>
										@endforeach

									</select>
									</div>
									</div>

									@endif -->

								<!-- <div class="col-sm-4">
								<div class="form-group text-left">
									<label>Select Payment Status</label>
									<select class="form-control selectric" name="payment_status" required="">
									<option value=""><b>{{ __('Select Payment Status') }}</b></option>
									<option value="1" @if ($info->payment_status == '1') selected="" @endif>{{ __('Payment Complete') }}</option>
									<option value="2" @if ($info->payment_status == '2') selected="" @endif>{{ __('Payment Pending') }}</option>
									<option value="0" @if ($info->payment_status == '0') selected="" @endif>{{ __('Payment Cancel') }}</option>
									<option value="3" @if ($info->payment_status == '3') selected="" @endif>{{ __('Payment Incomplete') }}</option>
									</select>
								</div>
								</div> -->

                                <div class="col-sm-4">
                                    <div class="form-group text-left">
                                        <label>Select Order Status</label>
                                        <select class="form-control selectric" id="mainSelect" name="status" required="">
                                            <option value=""><b>{{ __('Select Order Status') }}</b></option>
                                            @foreach ($order_status ?? [] as $row)
                                                <option value="{{ $row->id }}"
                                                    @if ($info->status_id == $row->id) selected="" @endif>
                                                    {{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
@php
$shipping_servics = ['FedEx','UPS','US Postal Service'];
@endphp
                                <div class="col-sm-4" id="hiddenChooseTracking" @if($info->shippingwithinfo->shipping_driver == 'local')style="display:none;" @endif>
                                    <div class="form-group text-left">
                                        <label>Select shipping service</label>
                                        <select class="form-control selectric" id="chooseTracking" name="chooseTracking">
                                            <option value="" selected><b>{{ __('Select option') }}</b></option>
                                            <option value="FedEx" @if ($info->shippingwithinfo->shipping_driver == 'FedEx') selected="" @endif>{{ __('FedEx') }}</option>
                                            <option value="UPS" @if ($info->shippingwithinfo->shipping_driver == 'UPS') selected="" @endif><b>{{ __('UPS') }}</b></option>
                                            <option value="US Postal Service" @if ($info->shippingwithinfo->shipping_driver == 'US Postal Service') selected="" @endif><b>{{ __('US Postal Service') }}</b></option>
                                            <option value="Other" @if ($info->shippingwithinfo->shipping_driver != 'local' && !in_array($info->shippingwithinfo->shipping_driver,$shipping_servics)) selected="" @endif><b>{{ __('Other') }}</b></option>
                                        </select>
                                    </div>
                                </div>

								{{-- <div class="col-sm-3" id="hiddenChooseTracking" style="display:none;">
                                    <div class="form-group">
                                        <label style="text-align: left;">Select shipping service</label>
                                        <select class="form-control selectric" id="chooseTracking" name="chooseTracking">
                                            <option value="" selected><b style="text-align: left;">{{ __('Select option') }}</b></option>
                                            <option value="FedEx" @if ($info->shippingwithinfo->shipping_driver == 'FedEx') selected="" @endif>{{ __('FedEx') }}</option>
                                            <option value="UPS" @if ($info->shippingwithinfo->shipping_driver == 'UPS') selected="" @endif><b style="text-align: left;">{{ __('UPS') }}</b></option>
                                            <option value="US Postal Service" @if ($info->shippingwithinfo->shipping_driver == 'US Postal Service') selected="" @endif><b style="text-align: left;">{{ __('US Postal Service') }}</b></option>
                                            <option value="Other" @if ($info->shippingwithinfo->shipping_drivers == 'Other') selected="" @endif><b style="text-align: left;">{{ __('Other') }}</b></option>
                                        </select>
                                    </div>
                                </div> --}}
                                
                                
                                <div class="col-sm-3" id="hide_shipping_service" @if($info->shippingwithinfo->shipping_driver == 'local' || $info->shippingwithinfo->shipping_driver == 'FedEx' || $info->shippingwithinfo->shipping_driver == 'UPS' || $info->shippingwithinfo->shipping_driver == 'US Postal Service')style="display:none;"@endif>
									<div class="form-group text-left">
										<label>Enter shipping service</label>
									<input type="text" class="form-control" name="shipping_service" id="shipping_service" value="{{$info->shippingwithinfo->shipping_driver??''}}">
									</div>
								</div>

								<div class="col-sm-3" id="hide_tacking_number" @if($info->shippingwithinfo->shipping_driver == 'local')style="display:none;"@endif>
									<div class="form-group text-left">
										<label>Enter tracking number</label>
										<input type="text" value="{{$info->shippingwithinfo->tracking_no}}" class="form-control" name="tacking_number" id="tacking_number" id="tacking_number">
									</div>
								</div>

								


                            </div>
                            <div class="form-group">
                                <label class="custom-switch mt-2">
                                    <input type="hidden" name="mail_notify" value="1" class="custom-switch-input">
                                    <!-- <span class="custom-switch-indicator"></span> -->
                                    <!-- <span class="custom-switch-description">{{ __('Notify To Customer') }}</span> -->
                                </label>
                                @if ($info->order_method == 'delivery')
                                    <label class="custom-switch mt-2">
                                        <input type="hidden" name="rider_notify" value="1"
                                            class="custom-switch-input">
                                        <!-- <span class="custom-switch-indicator"></span> -->
                                        <!-- <span class="custom-switch-description">{{ __('Notify To Admin') }}</span> -->
                                    </label>
                                @endif
                            </div>
                    </div>
                    <button type="submit"
                        class="btn btn-primary float-right mt-2 ml-2 basicbtn">{{ __('Save Changes') }}</button>
                    <a href="{{ route('seller.order.edit', $info->id) }}"
                        class="btn btn-primary text-right float-right mt-2">{{ __('Print Invoice') }}</a>
                    </form>
                </div>
            </div>
            @if (!empty($ordermeta))
                <div class="card card-primary">
                    <div class="card-header">
                        <h4 class="card-header-title">{{ __('Shipping details') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-0">{{ __('Customer Name') }}: {{ $ordermeta->name ?? '' }}</p>
                                <p class="mb-0">{{ __('Customer Email') }}: {{ $ordermeta->email ?? '' }}</p>
                                <p class="mb-0">{{ __('Customer Phone') }}: {{ $ordermeta->phone ?? '' }}</p>
                            </div>
                        </div>
                        @if ($info->order_method == 'delivery')
                            @php
                                $shipping_info = json_decode($info->shippingwithinfo->info ?? '');
                                //$location=$info->shippingwithinfo->location->name ?? '';
                                $address = $shipping_info->address ?? '';
                                $shipping_method = $shipping_info->shipping_label ?? '';
                            @endphp
                            {{-- <p class="mb-0">{{ __('Location') }}: {{ $location }}</p> --}}
                            <p class="mb-0">{{ __('Zip Code') }}: {{ $shipping_info->post_code ?? '' }}</p>
                            <p class="mb-0">{{ __('Address') }}: {{ $address }}</p>
                            <p class="mb-0">{{ __('Shipping Method') }}: {{ $shipping_method ?? '' }}</p>
                        @endif
                        @if ($info->order_method == 'delivery' && !empty($info->shippingwithinfo))
                            <div id="map" class="map-canvas"></div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="col-12 col-lg-4">
            <div class="card-grouping">
                <div class="card card-primary">
                    <div class="card-header" style="justify-content: space-between;">
                        <h4>{{ __('Status') }}</h4>

                        @if ($info->payment_status == 4)
                            <div class="capture-btn">
                                <form method="POST" action="{{ route('seller.order.capture', $info->id) }}">
                                    @csrf
                                    <button type="submit" name="capture_payment"
                                        class="btn btn-primary float-right mt-2 text-right">Capture Payment</button>
                                </form>
                            </div>
                        @endif

                        @if ($info->payment_status == 1)
                            <div class="capture-btn">
                                <form method="POST" action="{{ route('seller.order.refund', $info->id) }}">
                                    @csrf
                                    <button type="submit" name="refund_payment"
                                        class="btn btn-primary float-right mt-2 text-right">Cancel Order & Refund
                                        Payment</button>
                                </form>
                            </div>
                        @endif

                    </div>
                    <div class="card-body">
                        <p>{{ __('Payment Status') }}
                            @if ($info->payment_status == 2)
                                <span class="badge badge-warning float-right">{{ __('Pending') }}</span>
                            @elseif($info->payment_status == 1)
                                <span class="badge badge-success float-right">{{ __('Paid') }}</span>
                            @elseif($info->payment_status == 0)
                                <span class="badge badge-danger float-right">{{ __('Cancel') }}</span>
                            @elseif($info->payment_status == 3)
                                <span class="badge badge-danger float-right">{{ __('Incomplete') }}</span>
                            @elseif($info->payment_status == 4)
                                <span class="badge badge-danger float-right">{{ __('Authorized') }}</span>
                            @elseif($info->payment_status == 5)
                                <span class="badge badge-warning float-right">{{ __('Refunded') }}</span>
                            @endif
                        </p>
                        <p>{{ __('Order Status') }}
                            @if ($info->status_id != null)
                                <span class="badge  float-right text-white"
                                    style="background-color: {{ $info->orderstatus->slug ?? '' }}">{{ $info->orderstatus->name ?? '' }}</span>
                            @endif
                        </p>

                        <p>{{ __('Order Type') }}
                            <span class="badge badge-success float-right">{{ $info->order_method }}</span>
                        </p>

                        <p>{{ __('Order Placed') }}
                            <span class="badge badge-success float-right">
                                {{ \Carbon\Carbon::parse($info->placed_at)->format('m/d/Y h:i A') }}
                            </span>
                        </p>

                        @if($info->captured_at != null)
                        <p>{{ __('Order Capture') }}
                            <span class="badge badge-danger float-right">
                            {{ \Carbon\Carbon::parse($info->captured_at)->format('m/d/Y h:i A') }}
                            </span>
                        </p>
                        @endif

                        @if($info->refunded_at != null)

                        <p>{{ __('Order Refund') }}
                            <span class="badge badge-warning float-right">
                                {{ \Carbon\Carbon::parse($info->refunded_at)->format('m/d/Y h:i A') }}
                            </span>
                        </p>
                        @endif
                        
                    </div>
                </div>
                @if (!empty($info->schedule))
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>{{ __('Pre Order Information') }}</h4>
                        </div>
                        <div class="card-body">
                            <p>{{ __('Date Of Order') }}
                                <span class="float-right"><b>{{ $info->schedule->date ?? '' }}</b></span>
                            </p>
                            <p>{{ __('Order Time') }}
                                <span class="float-right"><b>{{ $info->schedule->time ?? '' }}</b></span>
                            </p>
                        </div>
                    </div>
                @endif
                @if ($info->order_method == 'table')
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>{{ __('Table Information') }}</h4>
                        </div>
                        <div class="card-body">
                            @foreach ($info->ordertable ?? [] as $row)
                                <p>{{ __('Table No :') }}
                                    <a href="{{ route('seller.table.edit', $row->id) }}"><span
                                            class="float-right"><b>{{ $row->name }}</b></span></a>
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Payment Mode') }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($info->getway_id != null)
                            <p>{{ __('Transaction Method') }} <span
                                    class="badge  badge-success  float-right">{{ $info->getway->name ?? '' }} </span></p>
                            <p>{{ __('Transaction Id') }} <span
                                    class="float-right">{{ $info->transaction_id ?? '' }}</span></p>
                        @else
                            <p>{{ __('Incomplete Payment') }}</p>
                        @endif
                    </div>
                </div>
                @if (!empty($ordermeta->comment ?? ''))
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-header-title">{{ __('Note') }}</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $ordermeta->comment ?? '' }}</p>
                        </div>
                    </div>
                @endif
                @if ($info->user_id != null)
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-header-title">{{ __('Customer') }}</h4>
                        </div>
                        <div class="card-body">
                            @if ($info->user != null)
                                <a href="{{ route('seller.user.show', $info->user->id) }}">{{ $ordermeta->name ?? $info->user->name }}
                                    (#{{ $info->user->id }})</a>
                            @else
                                {{ __('Guest Customer') }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    @if ($info->order_method == 'delivery' && !empty($info->shippingwithinfo))
        @if (!empty($info->shippingwithinfo->lat) && !empty($info->shippingwithinfo->long))
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key={{ get_option('order_settings', true)->google_api ?? '' }}&libraries=places&sensor=false&callback=initialise">
            </script>
            <script type="text/javascript">
                "use strict";
                var resturent_lat = {{ tenant('lat') ?? 0.0 }};
                var resturent_long = {{ tenant('long') ?? 0.0 }};
                var customer_lat = {{ $info->shippingwithinfo->lat ?? 0.0 }};
                var customer_long = {{ $info->shippingwithinfo->long ?? 0.0 }};
                var resturent_icon = '{{ asset('uploads/resturent.png') }}';
                var user_icon = '{{ asset('uploads/userpin.png') }}';

                var customer_name = '{{ $info->user->name ?? 'customer' }}';
                var resturent_name = '{{ tenant('id') }}';
                var mainUrl = "{{ url('/') }}";


                function initialise() {
                    var map;
                    var resturent = new google.maps.LatLng(resturent_lat, resturent_long);
                    var customer = new google.maps.LatLng(customer_lat, customer_long);
                    var option = {
                        zoom: 10,
                        center: resturent,
                    };
                    map = new google.maps.Map(document.getElementById('map'), option);
                    var display = new google.maps.DirectionsRenderer({
                        polylineOptions: {
                            strokeColor: "rgba(255, 0, 0, 0.5)"
                        }
                    });
                    var services = new google.maps.DirectionsService();
                    display.setMap(map);
                    calculateroute();

                    function calculateroute() {
                        var request = {
                            origin: resturent,
                            destination: customer,
                            travelMode: 'DRIVING'
                        };
                        services.route(request, function(result, status) {

                            if (status == 'OK') {
                                display.setDirections(result);
                                display.setOptions({
                                    suppressMarkers: true
                                });
                                var leg = result.routes[0].legs[0];
                                makeMarker(leg.start_location, resturent_icon, resturent_name);
                                makeMarker(leg.end_location, user_icon, customer_name);
                            }
                        });
                    }

                    function makeMarker(position, icon, title) {
                        new google.maps.Marker({
                            position: position,
                            map: map,
                            icon: icon,
                            title: title
                        });
                    }
                }
            </script>
        @endif
    @endif

	<script type="text/javascript">
		$(document).ready(function() {
			
	
			$("#mainSelect").on("change", function() {
				$("#tacking_number").val('');
			    $("#shipping_service").val('');
				$('#chooseTracking').val('');

				var selectedValue = $(this).val();
				if (selectedValue === "1") {
				 $("#hiddenChooseTracking").show();
				 $("#hide_tacking_number").hide();
				 $("#chooseTracking").attr("required", true);
				} else {
				 $("#tacking_number").val('');
			     $("#shipping_service").val('');

				 $("#hiddenChooseTracking").hide();
				 $("#hide_tacking_number").hide();
				 $("#hide_shipping_service").hide();
	
				 $("#chooseTracking").attr("required", false);
				 $("#tacking_number").attr("required", false);
				 $("#shipping_service").attr("required", false);
				}
			});
	
			$("#chooseTracking").on("change", function() {
				$("#tacking_number").val('');
			    $("#shipping_service").val('');

				var selectedValue = $(this).val();
				if (selectedValue === "FedEx" || selectedValue === "UPS" || selectedValue === "US Postal Service") {
				   $("#hide_tacking_number").show();
				   $("#hide_shipping_service").hide();
	
				   $("#tacking_number").attr("required", true);
				   $("#shipping_service").attr("required", false);
	
				} else if(selectedValue === "Other") {
				   $("#hide_tacking_number").show();
				   $("#hide_shipping_service").show();
	
				   $("#tacking_number").attr("required", true);
				   $("#shipping_service").attr("required", true);
	
				} else {
					$("#hide_tacking_number").hide();
					$("#hide_shipping_service").hide();
	
					$("#tacking_number").attr("required", false);
					$("#shipping_service").attr("required", false);
				}
			});
	});
	</script>

@endpush
