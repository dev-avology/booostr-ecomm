@extends('theme.resto.layouts.app')
@section('content')
@php
$autoloaddata=getautoloadquery();
$currency_data=$autoloaddata['currency_data'];
$wishlistCount = $contents->count();
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

<input type="hidden" id="cart_link" value="{{ route('add.tocart') }}" />

<!-- Wishlist Section -->
<div class="shopping-cart section">
    <div class="container">
        <!-- Empty State View -->
        <div class="wishlist-empty-view text-center py-5" style="{{ $wishlistCount == 0 ? '' : 'display: none;' }}">
            <div class="empty-cart-card">
                <div class="empty-cart-icon-wrap">
                    <i class="icofont-heart"></i>
                </div>
                <h3 class="empty-cart-title">{{ __('Your Wishlist is Empty') }}</h3>
                <p class="empty-cart-desc">{{ __('Explore our collection and save your favorite items here for easy access.') }}</p>
                <div class="empty-cart-btn-wrap">
                    <a href="{{ url('/products') }}" class="btn btn-empty-cart">
                        <i class="icofont-shopping-bag mr-1"></i> {{ __('Explore Products') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Filled State View -->
        <div class="wishlist-filled-view" style="{{ $wishlistCount > 0 ? '' : 'display: none;' }}">
            <div class="row">
                <div class="col-12">
                    <div class="cart-table-wrapper">
                        <table class="table shopping-summery">
                            <thead>
                                <tr class="main-hading">
                                    <th>{{ __('PRODUCT') }}</th>
                                    <th>{{ __('PRODUCT DETAILS') }}</th>
                                    <th class="text-center">{{ __('UNIT PRICE') }}</th>
                                    <th class="text-center">{{ __('STOCK STATUS') }}</th>
                                    <th class="text-center">{{ __('ACTION') }}</th>
                                    <th class="text-center">{{ __('REMOVE') }}</th>
                                </tr>
                            </thead>
                            <tbody class="wishlist_page">
                                @foreach($contents as $row)
                                @php
                                    $product = isset($products) ? ($products[$row->id] ?? null) : null;
                                    $firstPrice = $product ? $product->firstprice : null;
                                    $isOutOfStock = false;
                                    if ($firstPrice) {
                                        if ($firstPrice->stock_status == 0 || ($firstPrice->stock_manage == 1 && $firstPrice->qty <= 0)) {
                                            $isOutOfStock = true;
                                        }
                                    } elseif (isset($row->options->stock) && $row->options->stock !== null && $row->options->stock <= 0) {
                                        $isOutOfStock = true;
                                    }
                                    $isVariation = $product ? ($product->is_variation ?? 0) : 0;
                                    $previewImg = $row->options->preview ?? asset('uploads/default.png');
                                    $slug = $row->options->slug ?? ($product->slug ?? '#');
                                    $sku = $row->options->sku ?? ($firstPrice->sku ?? null);
                                @endphp
                                <tr class="wishlist_item" data-row-id="{{ $row->rowId }}" data-product-id="{{ $row->id }}">
                                    <td class="image" data-title="{{ __('Product') }}">
                                        <a href="{{ url('/product', $slug) }}">
                                            <img src="{{ asset($previewImg) }}" alt="{{ $row->name }}">
                                        </a>
                                    </td>
                                    <td class="product-desc" data-title="{{ __('Description') }}">
                                        <p class="product-name">
                                            <a href="{{ url('/product', $slug) }}">{{ $row->name }}</a>
                                        </p>
                                        @if(!empty($sku))
                                            <span class="product-sku text-muted small"><i class="icofont-barcode"></i> SKU: {{ $sku }}</span>
                                        @endif
                                    </td>
                                    <td class="price text-center" data-title="{{ __('Price') }}">
                                        <span>{{ booostr_price($row->price) }}</span>
                                    </td>
                                    <td class="stock text-center" data-title="{{ __('Stock Status') }}">
                                        @if($isOutOfStock)
                                            <span class="badge badge-stock out-of-stock"><i class="icofont-close-circled"></i> {{ __('Out of Stock') }}</span>
                                        @else
                                            <span class="badge badge-stock in-stock"><i class="icofont-check-circled"></i> {{ __('In Stock') }}</span>
                                        @endif
                                    </td>
                                    <td class="cart-action text-center" data-title="{{ __('Action') }}">
                                        @if($isOutOfStock)
                                            <button class="btn btn-wishlist-cart disabled" disabled>
                                                <i class="icofont-ban"></i> {{ __('Out of Stock') }}
                                            </button>
                                        @elseif($isVariation == 1)
                                            <a href="{{ url('/product', $slug) }}" class="btn btn-wishlist-cart">
                                                <i class="icofont-options"></i> {{ __('Select Options') }}
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-wishlist-cart btn-add-wishlist-to-cart" data-id="{{ $row->id }}">
                                                <i class="icofont-shopping-cart"></i> {{ __('Add to Cart') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td class="action text-center" data-title="{{ __('Remove') }}">
                                        <a href="javascript:void(0);" class="btn-remove-wishlist" data-row-id="{{ $row->rowId }}" title="{{ __('Remove Item') }}">
                                            <i class="icofont-trash remove-icon"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="wishlist-bottom-actions d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ url('/products') }}" class="btn-continue-shopping">
                            <i class="icofont-arrow-left"></i> {{ __('Continue Shopping') }}
                        </a>
                        <a href="{{ url('/cart') }}" class="btn btn-view-cart">
                            <i class="icofont-cart mr-1"></i> {{ __('View Cart') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ End Wishlist Section -->
@endsection

@push('css')
<style>
/* Wishlist Section & Layout */
.shopping-cart.section {
    background: #f8fafc;
    padding: 60px 0 80px 0;
}

/* Empty State Card */
.wishlist-empty-view {
    padding: 40px 0;
}
.empty-cart-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 60px 30px;
    max-width: 580px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}
.empty-cart-icon-wrap {
    width: 88px;
    height: 88px;
    background: #e8f4fc;
    color: #0A8CDE;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    margin-bottom: 24px;
}
.empty-cart-title {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
}
.empty-cart-desc {
    color: #64748b;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 28px;
    max-width: 440px;
    margin-left: auto;
    margin-right: auto;
}
.btn-empty-cart {
    background: #0A8CDE !important;
    background-color: #0A8CDE !important;
    color: #ffffff !important;
    padding: 13px 32px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 14px rgba(10, 140, 222, 0.3);
    text-decoration: none !important;
}
.btn-empty-cart:hover {
    background: #0870b2 !important;
    background-color: #0870b2 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(8, 112, 178, 0.4);
}

/* Wishlist Table Wrapper */
.cart-table-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
}
.table.shopping-summery {
    margin-bottom: 0;
    border-collapse: collapse;
    width: 100%;
}
.table.shopping-summery thead {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0;
}
.table.shopping-summery thead tr th {
    background: transparent !important;
    color: #475569 !important;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 20px;
    border: none;
    vertical-align: middle;
}
.table.shopping-summery tbody td {
    padding: 20px;
    vertical-align: middle;
    border-top: 1px solid #f1f5f9;
}
.table.shopping-summery tbody .image img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 4px;
    background: #ffffff;
    box-shadow: none;
}
.table.shopping-summery tbody .product-name a {
    color: #1e293b;
    font-weight: 600;
    font-size: 15px;
    transition: color 0.2s ease;
}
.table.shopping-summery tbody .product-name a:hover {
    color: #0A8CDE;
}
.product-sku {
    display: block;
    margin-top: 4px;
    font-size: 12.5px;
}
.table.shopping-summery tbody .price span {
    color: #1e293b;
    font-weight: 600;
    font-size: 15px;
}
.table.shopping-summery tbody .action .remove-icon {
    color: #94a3b8;
    font-size: 18px;
    transition: color 0.2s ease;
}
.table.shopping-summery tbody .action a:hover .remove-icon {
    color: #ef4444;
}

/* Stock Badges */
.badge-stock {
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-stock.in-stock {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.badge-stock.out-of-stock {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Action Buttons */
.btn-wishlist-cart {
    background: #0A8CDE !important;
    background-color: #0A8CDE !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px;
    padding: 9px 18px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 8px rgba(10, 140, 222, 0.25);
    text-decoration: none !important;
    cursor: pointer;
}
.btn-wishlist-cart:hover {
    background: #0870b2 !important;
    background-color: #0870b2 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(8, 112, 178, 0.35);
}
.btn-wishlist-cart.disabled,
.btn-wishlist-cart[disabled] {
    background: #94a3b8 !important;
    background-color: #94a3b8 !important;
    color: #ffffff !important;
    cursor: not-allowed;
    box-shadow: none !important;
    transform: none !important;
}
.btn-wishlist-cart-success {
    background: #10b981 !important;
    background-color: #10b981 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px;
    padding: 9px 18px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

/* Bottom Actions */
.btn-continue-shopping {
    color: #475569;
    font-weight: 600;
    font-size: 14.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    text-decoration: none !important;
}
.btn-continue-shopping:hover {
    color: #0A8CDE;
    border-color: #0A8CDE;
    background: #f8fafc;
    transform: translateX(-3px);
    box-shadow: 0 3px 8px rgba(10, 140, 222, 0.12);
}
.btn-view-cart {
    background: #1e293b !important;
    background-color: #1e293b !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 14.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    border: none;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 8px rgba(30, 41, 59, 0.2);
    text-decoration: none !important;
}
.btn-view-cart:hover {
    background: #0f172a !important;
    background-color: #0f172a !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
}

/* Responsive Mobile Layout */
@media (max-width: 767px) {
    .shopping-cart.section {
        padding: 30px 0 60px 0;
    }
    .shopping-cart .table td::before,
    .single-item-inner::before {
        display: none !important;
        content: none !important;
    }
    .shopping-cart .table thead {
        display: none !important;
    }
    .cart-table-wrapper {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    .table.shopping-summery {
        display: block !important;
        width: 100% !important;
    }
    .table.shopping-summery tbody {
        display: block !important;
        width: 100% !important;
    }
    .table.shopping-summery tbody tr.wishlist_item {
        display: flex !important;
        flex-wrap: wrap !important;
        position: relative !important;
        padding: 16px !important;
        margin-bottom: 14px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        width: 100% !important;
    }
    .table.shopping-summery tbody td {
        border: none !important;
        padding: 0 !important;
        background: transparent !important;
        margin: 0 !important;
        width: auto !important;
        height: auto !important;
    }
    .table.shopping-summery tbody td.image {
        flex: 0 0 68px !important;
        width: 68px !important;
        height: 68px !important;
        margin-right: 12px !important;
    }
    .table.shopping-summery tbody td.image img {
        width: 68px !important;
        height: 68px !important;
        object-fit: contain !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 4px !important;
    }
    .table.shopping-summery tbody td.product-desc {
        flex: 1 1 calc(100% - 110px) !important;
        padding-right: 28px !important;
    }
    .table.shopping-summery tbody td.product-desc .product-name a {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        line-height: 1.35 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
    }
    .table.shopping-summery tbody td.price {
        width: 100% !important;
        text-align: left !important;
        margin-top: 10px !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0A8CDE !important;
    }
    .table.shopping-summery tbody td.stock {
        width: 100% !important;
        text-align: left !important;
        margin-top: 6px !important;
    }
    .table.shopping-summery tbody td.cart-action {
        width: 100% !important;
        margin-top: 12px !important;
    }
    .table.shopping-summery tbody td.cart-action .btn-wishlist-cart,
    .table.shopping-summery tbody td.cart-action .btn-wishlist-cart-success {
        width: 100% !important;
        height: 42px !important;
        display: flex !important;
    }
    .table.shopping-summery tbody td.action {
        position: absolute !important;
        right: 12px !important;
        top: 14px !important;
        text-align: right !important;
    }
    .table.shopping-summery tbody td.action .remove-icon {
        font-size: 20px !important;
    }
    .wishlist-bottom-actions {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .wishlist-bottom-actions .btn-continue-shopping,
    .wishlist-bottom-actions .btn-view-cart {
        width: 100% !important;
        justify-content: center !important;
    }
}
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // AJAX Wishlist Item Removal
    $(document).on('click', '.btn-remove-wishlist', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var rowId = $btn.data('row-id');
        var $row = $btn.closest('.wishlist_item');

        $row.css('opacity', '0.4').css('pointer-events', 'none');

        $.ajax({
            type: 'GET',
            url: '{{ url("/remove-wishlist") }}/' + rowId,
            dataType: 'json',
            success: function(res) {
                var count = (res && typeof res.count !== 'undefined') ? res.count : res;
                $('.wishlist_count').html(count);

                if (res && res.wishlist_ids && Array.isArray(res.wishlist_ids)) {
                    window.booostr_wishlist_ids = res.wishlist_ids;
                }

                $row.fadeOut(300, function() {
                    $(this).remove();
                    if ($('.wishlist_page .wishlist_item').length === 0) {
                        $('.wishlist-filled-view').hide();
                        $('.wishlist-empty-view').fadeIn(300);
                    }
                });

                if (typeof Sweet === 'function') {
                    Sweet('success', '{{ __("Item removed from wishlist") }}');
                }
            },
            error: function() {
                $row.css('opacity', '1').css('pointer-events', 'auto');
                if (typeof Sweet === 'function') {
                    Sweet('error', '{{ __("Failed to remove item. Please try again.") }}');
                }
            }
        });
    });

    // AJAX Wishlist Add To Cart
    $(document).on('click', '.btn-add-wishlist-to-cart', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var productId = $btn.data('id');
        var originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="icofont-spinner icofont-spin"></i> {{ __("Adding...") }}');

        $.ajax({
            type: 'POST',
            url: $('#cart_link').val() || '{{ route("add.tocart") }}',
            data: {
                id: productId,
                qty: 1,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (typeof render_cart === 'function') {
                    render_cart(response.cart_content);
                }
                $('.cart_count').html(response.cart_count);
                $('.cart_subtotal').html(response.cart_subtotal);
                $('.subtotal').html(response.cart_subtotal);

                $btn.removeClass('btn-wishlist-cart')
                    .addClass('btn-wishlist-cart-success')
                    .html('<i class="icofont-check"></i> {{ __("Added to Cart") }}');

                setTimeout(function() {
                    $btn.removeClass('btn-wishlist-cart-success')
                        .addClass('btn-wishlist-cart')
                        .prop('disabled', false)
                        .html(originalHtml);
                }, 2200);

                if (typeof Sweet === 'function') {
                    Sweet('success', '{{ __("Product added to cart!") }}');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                var err = xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.error ? xhr.responseJSON.errors.error : '{{ __("Failed to add to cart.") }}';
                if (typeof Sweet === 'function') {
                    Sweet('error', err);
                } else {
                    alert(err);
                }
            }
        });
    });
});
</script>
@endpush
