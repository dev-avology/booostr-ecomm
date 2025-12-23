@extends('layouts.backend.app')

@section('title','Dashboard')

@section('style')
<style>
    span.risk-badge-text img {
        margin-left: 12px;
    }
    .input-group-btn button.btn.btn-primary.btn-icon {
    padding-top: 8px;
    padding-bottom: 8px;

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
    <div class="card-header">
        <h4>{{ __('Orders') }}</h4>
        <form class="card-header-form">
            <div class="input-group">
                <input type="text" name="src" value="{{ $request->src ?? '' }}" class="form-control" required=""  placeholder="Order Id" />
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <button class="btn btn-sm btn-primary  ml-1" type="button" data-toggle="modal" data-target="#searchmodal">
            <i class="fe fe-sliders mr-1"></i> {{ __('Filter') }} <span class="badge badge-primary ml-1 d-none">0</span>
        </button>
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
            <div class="float-right">
                @if(count($request->all()) > 0)
                {{ $orders->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
                @else
                {{ $orders->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
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
                               @if($row->user_id == null && $row->order_from == 1  )
                                 {{($ordermeta != '' ) ? $ordermeta->name : $row->user->name}} 
                                @elseif($row->user_id !== null)
                                 <a href="{{ route('seller.user.show',$row->user_id) }}">{{($ordermeta != '' ) ? $ordermeta->name : $row->user->name}}</a>
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

                                     @if($row->order_from == 4 || ($row->order_from == 0 && $row->getway->name !== 'cash'))
                                        <span class="badge badge-success">{{ __('CC Complete') }}</span>
                                     @elseif($row->order_from == 5 || ($row->order_from == 0 && $row->getway->name == 'cash'))
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

                                <span class="badge {{ $row->orderstatus == null ? 'badge-warning' :'' }} text-white" style="background-color: {{ $row->orderstatus->slug  }}">{{ $row->orderstatus->name ?? '' }}</span>

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
        <h5 class="modal-title w-100 font-weight-bold">Limitationss</h5>
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


