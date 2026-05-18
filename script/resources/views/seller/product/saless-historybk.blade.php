@extends('layouts.backend.app')

@section('title','Purchase History')

@section('style')
<style>
    .purchase-history-card { padding: 16px 18px; }

    .purchase-history-card table thead th {
        background: #f7f7f7;
        font-size: 13px;
        font-weight: 700;
        color: #555;
        padding: 15px 18px;
        border: none;
        white-space: nowrap;
    }

    .purchase-history-card table tbody td {
        padding: 14px 18px;
        border: none;
        color: #777;
        font-size: 13px;
        vertical-align: middle;
    }

.btn-action {
    background: #08bff3 !important;
    color: #fff !important;
    border: 1px solid #08bff3 !important;
    padding: 7px 13px;
    font-size: 12px;
    border-radius: 3px;
}

.ticket-id {
    max-width: 170px;
    min-width: 170px;
    word-break: break-word;
    line-height: 22px;
    padding-right: 20px !important;
    padding-bottom: 12px !important;
}

    .dropdown-menu { min-width: 180px; }
    .dropdown-item { font-size: 13px; padding: 8px 14px; }
    
    .btn-action:hover,
.btn-action:focus,
.btn-action:active,
.btn-action.dropdown-toggle.show {
    background: #08bff3 !important;
    color: #fff !important;
    border-color: #08bff3 !important;
    box-shadow: none !important;
}

.btn-light:hover,
.btn-light:focus,
.btn-light:active {
    background: #d9d9d9 !important;
    border-color: #d9d9d9 !important;
    color: #fff !important;
    box-shadow: none !important;
}
</style>
@endsection

@section('head')
<div class="section-header">
    <h1>
        <a href="{{ route('seller.product.index') }}" style="color:#1f2d5c;text-decoration:none;margin-right:8px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        Purchase History - {{ $product->title }}
    </h1>

    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item">seller</div>
        <div class="breadcrumb-item">product</div>
        <div class="breadcrumb-item">{{ $product->id }}</div>
        <div class="breadcrumb-item">sales-history</div>
    </div>
</div>
@endsection

@section('content')

@php
    $isTicket = $product->is_variation == 2;
@endphp

@if($sales->total() == 0)

<div class="card purchase-history-card">
    <div class="card-body d-flex align-items-center justify-content-center" style="min-height:520px;">
        <div class="text-center">

            <h4 style="font-weight:700;color:#555;">
                There have been 0 sales of {{ $product->title }} to date.
            </h4>

            <br>

            <a href="{{ route('seller.product.index') }}"
               style="font-size:20px;font-weight:700;color:#08bff3;text-decoration:underline;">
                Go back to Product List
            </a>

        </div>
    </div>
</div>

@else



<div class="card purchase-history-card">
    <div class="card-body">

        <div class="row mb-3 align-items-end">
            <div class="col-md-4">
                <form method="GET">
                    <label>Search</label>
                
                    <input type="text"
                           name="src"
                           class="form-control"
                           placeholder="search..."
                           value="{{ request('src') }}">
                </form>
                
            </div>

            <div class="col-md-5">
                <p class="mb-0">
                    @if($isTicket)
                        Below is a list of total sales of {{ $product->title }} tickets online.
                        You can update Ticket Status using the Action button.
                    @else
                        Below is a list of total sales of {{ $product->title }}
                        {{ $product->is_variation == 1 ? 'variant' : 'simple' }} product.
                    @endif
                </p>
            </div>

            <div class="col-md-3">
                <form class="float-right">
                    <div class="input-group">
                        <select class="form-control">
                            <option>Select Action</option>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive custom-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>FIRST NAME</th>
                        <th>LAST NAME</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>ORDER DATE</th>
                        <th>ORDER #</th>
                        <th>CHANNEL</th>

                        @if($isTicket)
                            <th>TICKET ID</th>
                            <th>TICKET STATUS</th>
                        @else
                            <th>VARIANT ORDERED</th>
                        @endif

                        <th class="text-right">ACTION</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sales as $sale)
                        @php
                            $order = $sale->order ?? null;
                            $ordermeta = json_decode($order->ordermeta->value ?? '{}');

                            $fullName = $ordermeta->name ?? 'Guest User';
                            $email = $ordermeta->email ?? '-';
                            $phone = $ordermeta->phone ?? '-';

                            $variantText = '-';
                            $ticketId = '-';
                            $ticketStatus = 'Not Checked In';

                            $info = json_decode($sale->info ?? '{}', true);

                            if (!empty($info['options']) && is_array($info['options'])) {
                                $variantNames = [];

                                foreach ($info['options'] as $option) {
                                    if (!empty($option['varitions']) && is_array($option['varitions'])) {
                                        foreach ($option['varitions'] as $variation) {
                                            if (!empty($variation['name'])) {
                                                $variantNames[] = $variation['name'];
                                            }
                                        }
                                    }
                                }

                                if (!empty($variantNames)) {
                                    $variantText = implode(', ', $variantNames);
                                }
                            }

                            $ticket = $sale->eventTicket ?? null;

                            if ($isTicket && $ticket) {
                                $fullName = $ticket->attendee_name ?? $fullName;
                                $email = $ticket->attendee_email ?? $email;
                                $phone = $ticket->attendee_phone ?? $phone;
                                $ticketId = $ticket->ticket_uuid ?? '-';

                                if ($ticket->status == 'used') {
                                    $ticketStatus = 'Checked In';
                                } elseif ($ticket->status == 'cancelled') {
                                    $ticketStatus = 'Cancelled & Refunded';
                                } else {
                                    $ticketStatus = 'Not Checked In';
                                }
                            }

                            $nameParts = explode(' ', trim($fullName), 2);
                            $firstName = $nameParts[0] ?? '-';
                            $lastName = $nameParts[1] ?? '-';

                            if ($order && ($order->order_from == 4 || $order->order_from == 5)) {
                                $channel = 'Point of Sale';
                            } elseif ($order && $order->order_from == 0) {
                                $channel = 'POS(Web)';
                            } else {
                                $channel = 'Ecommerce';
                            }
                        @endphp

                        <tr>
                            <td>{{ $firstName ?: '-' }}</td>
                            <td>{{ $lastName ?: '-' }}</td>
                            <td>{{ $email }}</td>
                            <td>{{ $phone }}</td>
                            <td>{{ !empty($order->created_at) ? date('m/d/Y', strtotime($order->created_at)) : '-' }}</td>
                            <td>
                                @if(!empty($order->id))
                                    <a href="{{ route('seller.order.show', $order->id) }}">
                                        {{ $order->invoice_no ?? str_pad($order->id, 7, '0', STR_PAD_LEFT) }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $channel }}</td>

                            @if($isTicket)
                                <td class="ticket-id">{{ $ticketId }}</td>
                                <td>{{ $ticketStatus }}</td>
                            @else
                                <td>{{ $variantText }}</td>
                            @endif

                            <td class="text-right">
                                @if($isTicket && !empty($ticket))
                                    <div class="dropdown">
                                        <button class="btn btn-action dropdown-toggle" type="button" data-toggle="dropdown">
                                            Action
                                        </button>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="used">Checked In</a>
                                            <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="active">Not Checked In</a>
                                            <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="cancelled">Cancelled & Refunded</a>
                                        </div>
                                    </div>
                                @else
                                   <button class="btn btn-light btn-sm"
                                        style="background:#d9d9d9;color:#fff;border:none;"
                                        type="button">
                                        Action <i class="fas fa-caret-down"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isTicket ? 10 : 9 }}" class="text-center py-5">
                                No sales found for this product.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $sales->appends(request()->all())->links('vendor.pagination.bootstrap-4') }}
        </div>

    </div>
</div>
@endif

@push('script')
<script>
$(document).on('click', '.ticket-status-update', function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('seller.product.ticket.status.update') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            ticket_id: $(this).data('id'),
            status: $(this).data('status')
        },
        success: function() {
            location.reload();
        },
        error: function() {
            alert('Something went wrong');
        }
    });
});
</script>
@endpush
@endsection