@extends('layouts.backend.app')

@section('title','Dashboard')

@section('style')
<style>
    span.risk-badge-text img {
        margin-left: 12px;
    }

.order-list-pagination {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

.order-list-pagination .pagination {
    justify-content: center;
    margin-bottom: 0;
}

.order-list-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.order-list-card-header .order-list-card-title {
    margin-bottom: 0;
}

.order-list-header-tools {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

.order-list-search-form {
    margin: 0;
    width: 300px;
    max-width: 100%;
}

.order-list-search-field {
    position: relative;
    display: flex;
    align-items: center;
    height: 38px;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.order-list-search-field:focus-within {
    border-color: #00aeef;
    box-shadow: 0 0 0 0.15rem rgba(0, 174, 239, 0.12);
}

.order-list-search-input {
    width: 100%;
    height: 100%;
    border: 0;
    background: transparent;
    padding: 0 44px 0 12px;
    border-radius: 6px;
    box-shadow: none;
    font-size: 14px;
    line-height: 1.4;
}

.order-list-search-input:focus {
    outline: none;
    box-shadow: none;
}

.order-list-search-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    bottom: 4px;
    width: 34px;
    padding: 0;
    border: 0;
    border-radius: 4px;
    background: #00aeef;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: none;
}

.order-list-search-btn:hover,
.order-list-search-btn:focus {
    background: #0099d6;
    color: #fff;
    outline: none;
    box-shadow: none;
}

.order-list-filter-btn {
    height: 38px;
    padding: 0 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    box-shadow: none;
}

.order-list-filter-btn .fe {
    font-size: 15px;
    line-height: 1;
}

.order-list-per-page-form {
    margin: 0;
}

.order-list-per-page-select {
    height: 38px;
    min-width: 132px;
    padding: 0 12px;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    background: #fff;
    font-size: 14px;
    font-weight: 500;
    color: #34395e;
    box-shadow: none;
    cursor: pointer;
}

.order-list-per-page-select:focus {
    border-color: #00aeef;
    outline: none;
    box-shadow: 0 0 0 0.15rem rgba(0, 174, 239, 0.12);
}

@media (max-width: 767px) {
    .order-list-header-tools {
        width: 100%;
        margin-left: 0;
    }

    .order-list-search-form {
        flex: 1;
        width: auto;
        min-width: 0;
    }
}

</style>
@endsection

@section('content')

<section class="section">
    <div class="section-header row">
        <div class="col-sm-12">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link  {{ $request_status == null ? 'active' : '' }} " href="{{ route('seller.order.index') }}">All</a>
                </li>
                @foreach($status as $row)
                <li class="nav-item">
                <a class="nav-link  {{ $request_status == $row->id ? 'active' : '' }}" href="{{ url('seller/order?status='.$row->id) }}">{{$row->name}} <span class="badge badge-secondary">{{$row->orderstatus_count}}</span></a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<x-storenotification></x-storenotification>

 <div class="card">
    <div class="card-header order-list-card-header">
        <h4 class="order-list-card-title">{{ __('Orders') }}</h4>
        <div class="order-list-header-tools">
            <form class="order-list-search-form">
                <div class="order-list-search-field">
                    <input type="text" name="src" value="{{ $request->src ?? '' }}" class="form-control order-list-search-input" required="" placeholder="{{ __('Search by Order # or Customer Name') }}" />
                    <button type="submit" class="order-list-search-btn" aria-label="{{ __('Search orders') }}">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <button class="btn btn-primary order-list-filter-btn" type="button" data-toggle="modal" data-target="#searchmodal">
                <i class="fe fe-sliders"></i>
                <span>{{ __('Filter') }}</span>
                <span class="badge badge-light ml-1 d-none">0</span>
            </button>
            <form class="order-list-per-page-form" method="get" action="{{ route('seller.order.index') }}">
                @foreach(collect(request()->query())->except(['per_page', 'page']) as $queryKey => $queryValue)
                    @if(!is_array($queryValue))
                        <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                    @endif
                @endforeach
                <select class="form-control order-list-per-page-select" name="per_page" id="order_per_page" onchange="this.form.submit()">
                    @foreach($per_page_options ?? [10, 20, 30, 50, 100, 200, 500] as $per)
                        <option value="{{ $per }}" {{ ($selected_per_page ?? 30) == $per ? 'selected' : '' }}>
                            {{ __('Per page') }} {{ $per }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('seller.order.multipledelete') }}" class="" id="bulkActionForm">
            @csrf
            <div class="float-left">
                @if(count($orders) > 0)

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
                        <button class="btn btn-primary basicbtn" type="submit">{{ __('Submit') }}</button>
                    </div>
                </div>
                @endif
            </div>
            <div class="clearfix mb-3"></div>
            <div class="table-responsive">
                <table class="table table-hover table-nowrap card-table text-center">
                    <thead>
                        <tr>
                            <th class="text-left" ><div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checkAll" id="selectAll">
                            <label class="custom-control-label checkAll" for="selectAll"></label>
                            </div></th>
                            <th class="text-left" >{{ __('Order') }}</th>
                            <th >{{ __('Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th class="text-left" >{{ __('Order Channel') }}</th>
                            <th class="text-right">{{ __('total') }}</th>
                            <th>{{ __('Payment') }}</th>
                            <th>{{ __('Fulfillment') }}</th>
                            <th class="text-right">{{ __('Type') }}</th>
                            <th class="text-right">{{ __('Item(s)') }}</th>
                            <th class="text-right">{{ __('Print') }}</th>
                        </tr>
                    </thead>
                    <tbody class="list font-size-base rowlink" data-link="row">
                        @foreach($orders ?? [] as $key => $row)
                        @php 
                        // All product type IDs
                            $p_types = $product_type->pluck('id')->all();

                            // Gather category IDs from order items that match product types
                            $selected_product_type = collect($row->orderitems ?? [])
                                ->flatMap(function ($item) {
                                    if (!$item->term) {
                                        return [];
                                    }

                                    return $item->term->termcategories->pluck('category_id');
                                })
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
                                } elseif ($pt && $pt->slug === 'online_ticketing') {
                                    $order_type = 'Tickets';
                                }
                            } elseif ($count > 1) {
                                $order_type = 'Mixed';
                            }

                        $ordermeta = json_decode(optional($row->ordermeta)->value ?? '');
                        $customerName = optional($ordermeta)->name ?? optional($row->user)->name ?? __('Guest User');
                        $gatewayName = optional($row->getway)->name;
                    @endphp
                        <tr>
                            <td  class="text-left">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $row->id }}" value="{{ $row->id }}">
                                    <label class="custom-control-label" for="customCheck{{ $row->id }}"></label>
                                </div>
                            </td>
                            <td class="text-left">
                                <a href="{{ route('seller.order.show',$row->id) }}">{{ $row->invoice_no }}</a> 
                             
                                <span class="risk-badge">
                                  @if($row->risk_level == 'normal')
                                    <span class="risk-badge-text"  data-toggle="tooltip" data-placement="left" title="Normal"><img src="{{ asset('uploads/security-1.png') }}" alt="Low"></span>
                                  @elseif($row->risk_level == 'elevated')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Medium"><img src="{{ asset('uploads/security-3.png') }}" alt="Medium"></span>
                                  @elseif($row->risk_level == 'highest')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Highest"><img src="{{ asset('uploads/security-2.png') }}" alt="Highest"></span>
                                  @elseif($row->risk_level == 'unknown')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Unknown"><img src="{{ asset('uploads/shiled-5.png') }}" alt="Unknown"></span>
                                  @elseif($row->risk_level == 'not_assessed')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Not Assessed"><img src="{{ asset('uploads/shiled-4.png') }}" alt="Not Assessed"></span>
                                  @endif
                                </span>
                            </td>

                            <td><a href="{{ route('seller.order.show',$row->id) }}">{{ $row->created_at->format('d-F-Y') }}</a></td>

                            <td>
                               @if($row->user_id == null && $row->order_from == 1)
                                 {{ $customerName }}
                                @elseif($row->user_id !== null)
                                 <a href="{{ route('seller.user.show',$row->user_id) }}">{{ $customerName }}</a>
                                @else 
                                 {{ __('Guest User') }}
                                @endif 
                            </td>

                            @if($row->order_from == 4 || $row->order_from == 5)

                            <td class="text-left">
                                POS(point of sale)
                            </td>

                            @elseif ($row->order_from == 0)
                            <td class="text-left">
                                POS(Web)
                            </td>
                            @else

                            <td class="text-left">
                                ECOM(online)
                            </td>

                            @endif


                            <td >{{ currency_formate($row->total) }}</td>
                            <td>
                                @if($row->payment_status==2)
                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @elseif($row->payment_status==1)

                                     @if($row->order_from == 4 || ($row->order_from == 0 && $gatewayName !== 'cash'))
                                        <span class="badge badge-success">{{ __('CC Complete') }}</span>
                                     @elseif($row->order_from == 5 || ($row->order_from == 0 && $gatewayName === 'cash'))
                                        <span class="badge badge-success">{{ __('Cash Complete') }}</span>
                                     @else
                                        <span class="badge badge-success">{{ __('Complete') }}</span>
                                     @endif


                                @elseif($row->payment_status==0)
                                <span class="badge badge-danger">{{ __('Cancel') }}</span> 
                                @elseif($row->payment_status==3)
                                <span class="badge badge-danger">{{ __('Incomplete') }}</span> 
                                @elseif($row->payment_status==4)
    							<span class="badge badge-danger">{{ __('Authorized') }}</span>
                                @elseif($row->payment_status==5)
                                <span class="badge badge-warning">{{ __('Refunded') }}</span>
                                @endif
                            </td>

                            <td>
                                @if($row->order_from == 4 || $row->order_from == 5)
                               

                                <span class="badge badge-success text-white" style="background-color:#028a74">POS (In Person)</span>

                                @elseif($row->order_from == 0)

                                <span class="badge badge-success text-white" style="background-color:#028a74">POS (Web)</span>

                                @else

                                <span class="badge {{ $row->orderstatus == null ? 'badge-warning' :'' }} text-white" style="background-color: {{ optional($row->orderstatus)->slug ?? '#ffc107' }}">{{ optional($row->orderstatus)->name ?? '' }}</span>

                                @endif 


                            </td>
                            {{-- <td>{{ $row->order_method }}</td> --}}
                            <td> {{$order_type}} </td>
                            <td>{{ $row->orderitems_count }}</td>
                            <td>
                                <a target="_blank" href="{{ route('seller.order.print',$row->id) }}" class="btn btn-primary">Print</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                   
                </table>
            </div>
            <div class="order-list-pagination">
                @if(count($request->all()) > 0)
                {{ $orders->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
                @else
                {{ $orders->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="searchmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="card-header-title">{{ __('Filters') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
            <div class="modal-body">
                <div class="form-group row mb-4">
                    <label class="col-sm-7">{{ __('Payment Status') }}</label>
                    <div class="col-sm-5">
                        <select class="form-control selectric" name="payment_status" id="payment_status">
                            <option value="2">{{ __('Pending') }}</option>
                            <option value="1" >{{ __('Complete') }}</option>
                            <option value="3" >{{ __('Incomplete') }}</option>
                            <option value="0" >{{ __('Cancel') }}</option>
                           
                        </select>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-7">{{ __('Fulfillment status') }}</label>
                    <div class="col-sm-5">
                        <select class="form-control selectric" name="status" id="status" >
                          @foreach($status as $row)
                            <option value="{{ $row->id }}" {{ $request_status == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                           @endforeach
                        </select>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-3">{{ __('Starting date') }}</label>
                    <div class="col-sm-9">
                        <input type="date" name="start" class="form-control" value="{{ $request->start }}" />
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-3">{{ __('Ending date') }}</label>
                    <div class="col-sm-9">
                        <input type="date" name="end" class="form-control" value="{{ $request->end }}" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('seller.order.index') }}" class="btn btn-secondary">{{ __('Clear Filter') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
            </div>
            </form>
        </div>
    </div>
</div>
<input type="hidden" id="payment" value="{{ $request->payment_status ?? '' }}">
<input type="hidden" id="order_status" value="{{ $request->status ?? '' }}">
@endsection

@push('js')
<!--<script src="{{ asset('assets/js/form.js') }}"></script>-->
<!--<script src="{{ asset('assets/js/order_index.js') }}"></script>-->
@endpush
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
            let checked = $('input[name="ids[]"]:checked');

            if (checked.length === 0) {
                alert('Please select at least one order.');
                return;
            }

            if (checked.length > 1) {
                alert('Please select only one order to cancel and refund.');
                return;
            }

            let orderId = checked.first().val();
            window.location.href = @json(url('/seller/order')) + '/' + orderId + '?refund=1';
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


