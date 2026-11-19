@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Customer Purchase History / Orders View
    Features:
      - Clean responsive card layout matching Edit Profile design
      - Accurate total items calculation (sum of quantities across line items)
      - Instant AJAX search by Order No with 500ms debounce
      - Dynamic AJAX filtering by Order Status & Payment Status
      - Full AJAX pagination preserving current filter queries
      - Modern soft-pill badges, clear invoice links & responsive table/empty state
    =================================================================================
--}}
<div class="dashboard-block mt-0 orders-history-block bg-white p-4 rounded shadow-sm">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3 flex-wrap gap-2">
        <div>
            <h3 class="block-title fs-5 mb-1">
                <i class="icofont-shopping-cart me-2 text-primary"></i>{{ __('Purchase History') }}
            </h3>
            <p class="text-muted small mb-0">{{ __('Track, review, and view invoices for all your past purchases.') }}</p>
        </div>
        <div>
            <span class="badge bg-light text-dark border px-3 py-2 fs-6" id="totalOrdersCountBadge">
                <i class="icofont-tag me-1 text-primary"></i><span id="totalOrdersCountText">{{ $orders->total() }}</span> {{ __('Total Orders') }}
            </span>
        </div>
    </div>

    {{-- Filters & Search Toolbar --}}
    <div class="orders-filter-bar mb-4 p-3 bg-light rounded border">
        <div class="row g-2 align-items-center">
            {{-- 1. Search with 500ms debounce --}}
            <div class="col-lg-5 col-md-6 col-12">
                <div class="search-input-wrapper position-relative">
                    <i class="icofont-search search-icon position-absolute"></i>
                    <input 
                        type="text" 
                        id="orderSearchInput" 
                        class="form-control order-search-field" 
                        placeholder="{{ __('Search by Order No (e.g. 0001209)...') }}"
                        autocomplete="off"
                        value="{{ request('search') }}"
                    >
                    <button type="button" id="btnClearSearch" class="btn-clear-search position-absolute {{ request('search') ? '' : 'd-none' }}" title="{{ __('Clear search') }}">
                        &times;
                    </button>
                </div>
            </div>

            {{-- 2. Order Status Filter --}}
            <div class="col-lg-3 col-md-3 col-6">
                <select id="filterOrderStatus" class="form-select order-filter-select">
                    <option value="all">{{ __('All Order Statuses') }}</option>
                    @foreach(($orderStatuses ?? []) as $status)
                        <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Payment Status Filter --}}
            <div class="col-lg-3 col-md-3 col-6">
                <select id="filterPaymentStatus" class="form-select order-filter-select">
                    <option value="all">{{ __('All Payment Statuses') }}</option>
                    <option value="1" {{ request('payment_status') === '1' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    <option value="2" {{ request('payment_status') === '2' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="5" {{ request('payment_status') === '5' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                    <option value="4" {{ request('payment_status') === '4' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                    <option value="0" {{ request('payment_status') === '0' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    <option value="3" {{ request('payment_status') === '3' ? 'selected' : '' }}>{{ __('Incomplete') }}</option>
                </select>
            </div>

            {{-- 4. Reset Filters Button --}}
            <div class="col-lg-1 col-md-12 col-12 text-lg-end text-start">
                <button type="button" id="btnResetFilters" class="btn btn-outline-secondary btn-sm w-100 h-100 py-2" title="{{ __('Reset Filters') }}" onclick="resetOrderFilters()">
                    <i class="icofont-refresh"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Orders Content Area (Replaced dynamically via AJAX) --}}
    <div id="ordersContainer" class="position-relative">
        {{-- Loading Overlay --}}
        <div id="ordersLoadingSpinner" class="orders-loading-overlay d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
        </div>

        {{-- Orders Table Partial --}}
        <div id="ordersTableContent">
            @include('theme.resto.customer.orders_table', ['orders' => $orders])
        </div>
    </div>
</div>

<style>
    /* 1. Eliminate nice-select wrapper on filter dropdowns */
    .orders-filter-bar .nice-select {
        display: none !important;
    }

    /* 2. Search Field & Inputs */
    .search-input-wrapper {
        position: relative !important;
        width: 100% !important;
    }

    .search-input-wrapper .search-icon {
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        pointer-events: none;
    }

    .order-search-field {
        height: 44px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding-left: 40px !important;
        padding-right: 36px !important;
        font-size: 14px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .order-search-field:focus {
        border-color: var(--primary, #0A8CDE) !important;
        box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.2) !important;
        outline: none !important;
    }

    .btn-clear-search {
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .btn-clear-search:hover {
        color: #1e293b;
    }

    .order-filter-select {
        display: block !important;
        width: 100% !important;
        height: 44px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 0 32px 0 12px !important;
        font-size: 13.5px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 12px 8px !important;
        cursor: pointer !important;
    }

    .order-filter-select:focus {
        border-color: var(--primary, #0A8CDE) !important;
        box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.2) !important;
        outline: none !important;
    }

    /* 3. Table Styling */
    .orders-table {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }

    .orders-table-head th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        padding: 14px 16px !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    .orders-table tbody td {
        padding: 14px 16px !important;
        font-size: 14px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
    }

    .order-table-row:hover {
        background-color: #f8fafc !important;
    }

    .order-invoice-link {
        color: var(--primary, #0A8CDE) !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        transition: color 0.2s ease !important;
    }

    .order-invoice-link:hover {
        color: #467fcb !important;
        text-decoration: underline !important;
    }

    .order-price-sub {
        font-size: 13.5px !important;
    }

    /* 4. Badges */
    .items-count-badge {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 12.5px !important;
        padding: 6px 10px !important;
        border-radius: 20px !important;
    }

    /* Order Status Badges */
    .order-status-badge {
        font-size: 12.5px !important;
        font-weight: 600 !important;
        padding: 6px 12px !important;
        border-radius: 20px !important;
        display: inline-block !important;
    }

    .order-badge-pending {
        background-color: #fffbeb !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
    }

    .order-badge-processing {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }

    .order-badge-completed {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .order-badge-cancelled {
        background-color: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }

    /* Payment Status Badges */
    .payment-status-badge {
        font-size: 12.5px !important;
        font-weight: 600 !important;
        padding: 6px 12px !important;
        border-radius: 20px !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .payment-badge-paid {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .payment-badge-refunded {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
    }

    .payment-badge-processing {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }

    .payment-badge-pending {
        background-color: #fffbeb !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
    }

    .payment-badge-cancelled {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        border: 1px solid #cbd5e1 !important;
    }

    .payment-badge-incomplete {
        background-color: #fff7ed !important;
        color: #c2410c !important;
        border: 1px solid #ffedd5 !important;
    }

    .payment-badge-failed {
        background-color: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }

    /* View Button */
    .view-order-btn {
        background: transparent !important;
        border: 1.5px solid var(--primary, #0A8CDE) !important;
        color: var(--primary, #0A8CDE) !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        padding: 6px 14px !important;
        transition: all 0.2s ease !important;
    }

    .view-order-btn:hover {
        background: var(--primary, #0A8CDE) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(10, 140, 222, 0.25) !important;
    }

    /* 5. Loading Overlay */
    .orders-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 8px;
        backdrop-filter: blur(1px);
    }

    /* 6. Empty State */
    .orders-empty-state .empty-icon-wrapper i {
        font-size: 58px;
        color: #cbd5e1;
    }

    .btn-reset-filters {
        border-radius: 6px !important;
        font-weight: 600 !important;
    }

    /* 7. Pagination Styling */
    .ajax-pagination .pagination {
        margin-bottom: 0 !important;
    }

    .ajax-pagination .page-item .page-link {
        color: var(--primary, #0A8CDE) !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        margin: 0 3px !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
    }

    .ajax-pagination .page-item.active .page-link {
        background-color: var(--primary, #0A8CDE) !important;
        border-color: var(--primary, #0A8CDE) !important;
        color: #ffffff !important;
    }
</style>
@endsection

@push('js')
<script>
    // Debounce Utility (500 ms wait)
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Main AJAX Orders Fetcher
    let currentOrdersPage = 1;

    function fetchOrders(page = 1) {
        currentOrdersPage = page;
        const searchVal = document.getElementById('orderSearchInput') ? document.getElementById('orderSearchInput').value.trim() : '';
        const statusVal = document.getElementById('filterOrderStatus') ? document.getElementById('filterOrderStatus').value : 'all';
        const paymentVal = document.getElementById('filterPaymentStatus') ? document.getElementById('filterPaymentStatus').value : 'all';

        const spinner = document.getElementById('ordersLoadingSpinner');
        const container = document.getElementById('ordersTableContent');
        const clearBtn = document.getElementById('btnClearSearch');

        // Toggle clear search button
        if (clearBtn) {
            if (searchVal) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
        }

        if (spinner) spinner.classList.remove('d-none');

        const params = new URLSearchParams();
        if (page && page > 1) params.append('page', page);
        if (searchVal) params.append('search', searchVal);
        if (statusVal && statusVal !== 'all') params.append('status', statusVal);
        if (paymentVal && paymentVal !== 'all') params.append('payment_status', paymentVal);

        const fetchUrl = `{{ route('customer.purchase.history') }}?${params.toString()}`;

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (container && data.html) {
                container.innerHTML = data.html;
            }

            // Update Total Orders Badge
            const totalText = document.getElementById('totalOrdersCountText');
            if (totalText && typeof data.total !== 'undefined') {
                totalText.textContent = data.total;
            }

            // Update browser URL query string without reloading page
            const newUrl = window.location.pathname + (params.toString() ? `?${params.toString()}` : '');
            window.history.replaceState({}, '', newUrl);
        })
        .catch(err => {
            console.error('Error fetching orders:', err);
            if (container) {
                container.innerHTML = `
                    <div class="alert alert-danger my-4 p-3 text-center" role="alert" style="border-radius: 8px;">
                        <i class="icofont-warning-alt me-2 fs-5"></i>
                        <span>{{ __('Unable to load orders at this time. Please check your connection and try again.') }}</span>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="fetchOrders(${page})">
                                <i class="icofont-refresh me-1"></i>{{ __('Retry') }}
                            </button>
                        </div>
                    </div>
                `;
            }
        })
        .finally(() => {
            if (spinner) spinner.classList.add('d-none');
        });
    }

    // Reset All Filters
    function resetOrderFilters() {
        const searchInput = document.getElementById('orderSearchInput');
        const statusSelect = document.getElementById('filterOrderStatus');
        const paymentSelect = document.getElementById('filterPaymentStatus');
        const clearBtn = document.getElementById('btnClearSearch');

        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = 'all';
        if (paymentSelect) paymentSelect.value = 'all';
        if (clearBtn) clearBtn.classList.add('d-none');

        fetchOrders(1);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Remove nice-select on order filter dropdowns if loaded
        function removeNiceSelect() {
            if (window.jQuery && typeof jQuery.fn.niceSelect === 'function') {
                try {
                    jQuery('.orders-filter-bar select').niceSelect('destroy');
                } catch (e) {}
            }
        }
        removeNiceSelect();
        setTimeout(removeNiceSelect, 150);

        // 1. Debounced Search (500ms)
        const searchInput = document.getElementById('orderSearchInput');
        if (searchInput) {
            const debouncedSearch = debounce(function () {
                fetchOrders(1);
            }, 500);

            searchInput.addEventListener('input', debouncedSearch);
            searchInput.addEventListener('keyup', function (e) {
                if (e.key === 'Enter') {
                    fetchOrders(1);
                }
            });
        }

        // 2. Clear Search Button
        const clearBtn = document.getElementById('btnClearSearch');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                this.classList.add('d-none');
                fetchOrders(1);
            });
        }

        // 3. Status Filter Dropdowns
        const statusSelect = document.getElementById('filterOrderStatus');
        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                fetchOrders(1);
            });
        }

        const paymentSelect = document.getElementById('filterPaymentStatus');
        if (paymentSelect) {
            paymentSelect.addEventListener('change', function () {
                fetchOrders(1);
            });
        }

        // 4. AJAX Pagination Click Handler (Delegated)
        document.addEventListener('click', function (e) {
            const paginationLink = e.target.closest('.ajax-pagination a.page-link');
            if (paginationLink) {
                e.preventDefault();
                const url = new URL(paginationLink.href);
                const page = url.searchParams.get('page') || 1;
                fetchOrders(page);

                // Scroll smoothly to top of table
                const tableBlock = document.querySelector('.orders-history-block');
                if (tableBlock) {
                    tableBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
</script>
@endpush
