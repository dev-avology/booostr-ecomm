@extends('layouts.backend.app')

@section('title','Dashboard')

@section('content')
@php
  $purchaseWarnings = get_product_purchase_warnings($info ?? null);
@endphp
<style>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
.product-purchase-warning {
    border-radius: 4px;
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    background-color: #ffe2aa;
    border: 1px solid #c99954;
    padding: 17px 24px;
}
  .product-purchase-warning-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 50%;
    background: #f0c14b;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
    line-height: 1;
    border: 2px solid #e0b03a;
  }
  .product-purchase-warning-title {
    margin-bottom: 8px;
    font-size: 18px;
    letter-spacing: 0px;
    color: #231616;
    font-weight: 600;
    font-family: "Roboto";
}
#product-purchase-warning-body p {
    font-size: 14px;
    letter-spacing: 0px;
    color: #505050;
    font-weight: 400;
    font-family: "Roboto";
    margin: 0;
    line-height: normal;
}
#product-purchase-warning-body p strong {
    font-weight: 800;
}
  .product-purchase-warning-body {
    color: #444;
    font-size: 13px;
    line-height: 1.45;
  }
  .product-purchase-warning-note {
    margin-top: 6px;
  }
  .product-edit-tab-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .product-tab-warning {
    display: inline-flex;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #f0c14b;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    align-items: center;
    justify-content: center;
    margin-left: 8px;
    line-height: 1;
    border: 1px solid #e0b03a;
  }
</style>
<div class="row">
  <div class="col-sm-12">
    @include('seller.product.partials.purchase-warning')
    <div class="card card-primary">
      <div class="card-body">
      <div class="row">
        <div class="col-sm-3">
          <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
            <li class="nav-item">
              <a class="nav-link product-edit-tab-link {{ route('seller.product.edit',$product_id) == url()->current() ? 'active' : '' }}" href="{{ route('seller.product.edit',$product_id) }}" >
                <span>{{ __('General Information') }}</span>
                <span class="product-tab-warning" id="product-tab-warning-general" title="{{ __('Product Status is Draft') }}" @if(empty($purchaseWarnings['is_draft'])) style="display:none;" @endif>!</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link product-edit-tab-link {{ url('/seller/product/edit/'.$product_id.'/price') == url()->current() ? 'active' : '' }}"  href="{{ url('/seller/product/edit/'.$product_id.'/price') }}">
                <span>{{ __('Price') }}</span>
                <span class="product-tab-warning" id="product-tab-warning-price" title="{{ __('Price or stock settings need attention') }}" @if(empty($purchaseWarnings['is_out_of_stock']) && empty($purchaseWarnings['is_zero_inventory'])) style="display:none;" @endif>!</span>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ url('/seller/product/edit/'.$product_id.'/image') == url()->current() ? 'active' : '' }}"  href="{{ url('/seller/product/edit/'.$product_id.'/image') }}">{{ __('Images') }}</a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link {{ url('/seller/product/edit/'.$product_id.'/discount') == url()->current() ? 'active' : '' }}"  href="{{ url('/seller/product/edit/'.$product_id.'/discount') }}">{{ __('Discount') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ url('/seller/product/edit/'.$product_id.'/seo') == url()->current() ? 'active' : '' }}"  href="{{ url('/seller/product/edit/'.$product_id.'/seo') }}">{{ __('SEO') }}</a>
            </li>
            <li class="nav-item">
               <a class="nav-link {{ url('/seller/product/edit/'.$product_id.'/express-checkout') == url()->current() ? 'active' : '' }}"  href="{{ url('/seller/product/edit/'.$product_id.'/express-checkout') }}">{{ __('Express Checkout') }}</a>
            <li class="nav-item">
               <a class="nav-link {{ url('/seller/product/edit/'.$product_id.'/barcode') == url()->current() ? 'active' : '' }}"  href="{{ url('/seller/product/edit/'.$product_id.'/barcode') }}">{{ __('Barcode Print') }} @if(tenant('barcode') != 'on')  <i class="fa fa-lock text-danger"></i> @endif</a>   
            </li> -->
          </ul>
        </div>
        <div class="col-sm-9">
          <div class="tab-content no-padding">
            @yield('product_content')
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
@yield('product_extra_content')
@endsection

@push('script')
<script>
(function () {
  var serverDraft = @json(!empty($purchaseWarnings['is_draft']));
  var serverOos = @json(!empty($purchaseWarnings['is_out_of_stock']));
  var serverZero = @json(!empty($purchaseWarnings['is_zero_inventory']));

  function isDraft() {
    var $status = jQuery('select[name="status"]');
    if ($status.length) {
      return String($status.val()) === '0';
    }
    return serverDraft;
  }

  function isOutOfStock() {
    var $simple = jQuery('select[name="stock_status"]');
    if ($simple.length) {
      return String($simple.val()) === '0';
    }

    var $variant = jQuery('select[name*="[stock_status]"]');
    if ($variant.length) {
      var allOut = true;
      $variant.each(function () {
        if (String(jQuery(this).val()) !== '0') {
          allOut = false;
        }
      });
      return allOut;
    }

    return serverOos;
  }

  function isZeroInventory() {
    var $simpleManage = jQuery('select[name="stock_manage"]');
    var $simpleQty = jQuery('input[name="qty"]');
    if ($simpleManage.length) {
      return String($simpleManage.val()) === '1' && (parseInt($simpleQty.val(), 10) || 0) <= 0;
    }

    var $variantManage = jQuery('select[name*="[stock_manage]"]');
    if ($variantManage.length) {
      var managedCount = 0;
      var managedZeroCount = 0;

      $variantManage.each(function () {
        if (String(jQuery(this).val()) !== '1') {
          return;
        }
        managedCount += 1;
        var name = jQuery(this).attr('name') || '';
        var match = name.match(/priceoption\]\[([^\]]+)\]/);
        var qty = 0;
        if (match && match[1]) {
          qty = parseInt(jQuery('input[name="childattribute[priceoption][' + match[1] + '][qty]"]').val(), 10) || 0;
        }
        if (qty <= 0) {
          managedZeroCount += 1;
        }
      });

      return managedCount > 0 && managedCount === managedZeroCount;
    }

    return serverZero;
  }

  function renderWarning(draft, oos, zero) {
    var $wrap = jQuery('#product-purchase-warning-wrap');
    var $body = jQuery('#product-purchase-warning-body');
    var $generalIcon = jQuery('#product-tab-warning-general');
    var $priceIcon = jQuery('#product-tab-warning-price');

    if (!$wrap.length) {
      return;
    }

    $generalIcon.toggle(draft);
    $priceIcon.toggle(oos || zero);

    if (!draft && !oos && !zero) {
      $wrap.hide();
      return;
    }

    var html = '';
    if (zero && !draft && !oos) {
      html += '<div class="product-purchase-warning-title">This product is unable to be purchased as its inventory is showing 0.</div>';
      html += '<div>This product has Manage Stock set to YES and your current Stock Quantity is at 0. To start selling this product you need to go to Price section and update the Stock Quantity set to greater than "0".</div>';
    } else {
      var actions = [];
      if (draft) {
        actions.push('Change the Product Status from DRAFT to PUBLISH.');
      }
      if (oos) {
        actions.push('Under Price change the Stock Status from OUT OF STOCK to IN STOCK.');
      }
      if (zero) {
        actions.push('Under Price update the Stock Quantity set to greater than "0".');
      }
      var countWord = actions.length === 1 ? 'one' : (actions.length === 2 ? 'two' : 'three');
      html += '<div class="product-purchase-warning-title">This product cannot be purchased by your supporters yet</div>';
      html += '<div>To sell this product, update ' + countWord + ' of this product\'s settings: ' + actions.join(' ') + '</div>';
      html += '<div class="product-purchase-warning-note">Note: Under Price, If Manage Stock is set to YES, you ALSO need to have Stock Quantity set to greater than "0" for the item to show in stock.</div>';
    }

    $body.html(html);
    $wrap.show();
  }

  function refreshProductPurchaseWarning() {
    renderWarning(isDraft(), isOutOfStock(), isZeroInventory());
  }

  jQuery(document).on('change input selectric-change', 'select[name="status"], select[name="stock_status"], select[name="stock_manage"], input[name="qty"], select[name*="[stock_status]"], select[name*="[stock_manage]"], input[name*="[qty]"]', refreshProductPurchaseWarning);
})();
</script>
@endpush

