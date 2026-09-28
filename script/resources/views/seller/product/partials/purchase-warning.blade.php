@php
    $purchaseWarnings = get_product_purchase_warnings($info ?? null);
    $warningCountWords = [
        1 => 'one',
        2 => 'two',
        3 => 'three',
    ];
    $warningActions = [];
    if (!empty($purchaseWarnings['is_draft'])) {
        $warningActions[] = 'Change the Product Status from <strong>DRAFT</strong> to <strong>PUBLISH</strong>.';
    }
    if (!empty($purchaseWarnings['is_out_of_stock'])) {
        $warningActions[] = 'Under Price change the Stock Status from <strong>OUT OF STOCK</strong> to <strong>IN STOCK</strong>.';
    }
    $inventoryOnlyWarning = !empty($purchaseWarnings['is_zero_inventory'])
        && empty($purchaseWarnings['is_draft'])
        && empty($purchaseWarnings['is_out_of_stock']);
    if (!empty($purchaseWarnings['is_zero_inventory']) && !$inventoryOnlyWarning) {
        $warningActions[] = 'Under Price update the Stock Quantity set to greater than "0".';
    }
@endphp

<div id="product-purchase-warning-wrap" class="product-purchase-warning" role="alert" @if(empty($purchaseWarnings['show'])) style="display:none;" @endif>
    <div class="product-purchase-warning-icon" aria-hidden="true">!</div>
    <div class="product-purchase-warning-body" id="product-purchase-warning-body">
        @if(!empty($purchaseWarnings['show']))
            @if($inventoryOnlyWarning)
                <div class="product-purchase-warning-title">This product is unable to be purchased as its inventory is showing 0.</div>
                <div><p>This product has Manage Stock set to YES and your current Stock Quantity is at 0. To start selling this product you need to go to Price section and update the Stock Quantity set to greater than "0".</p></div>
            @else
                <div class="product-purchase-warning-title">This product cannot be purchased by your supporters yet</div>
                <div class="product-purchase-warning-body">
                    <p>To sell this product, update { !! $warningCountWords[$purchaseWarnings['settings_count']] ?? 'these' !!} of this product's settings:
                    {!! implode(' ', $warningActions) !!}</p>
                </div>
                <div class="product-purchase-warning-note">
                    <p>Note: Under Price, <strong>If Manage Stock is set to YES</strong>, you ALSO need to have <strong>Stock Quantity set to greater than "0" </strong>for the item to show in stock.</p>
                </div>
            @endif
        @endif
    </div>
</div>
