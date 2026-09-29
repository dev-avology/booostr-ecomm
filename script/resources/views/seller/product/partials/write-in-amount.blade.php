@php
    $name = $name ?? 'is_write_in_amount_enabled';
    $uid = preg_replace('/[^A-Za-z0-9_-]/', '-', (string) ($uid ?? 'simple'));
    $enabled = !empty($enabled);
    $isVariant = !empty($isVariant);
    $intro = $isVariant
        ? 'Normally stores set the product pricing for each product variant, and that is how we set your products by default. However, in some cases you may offer a product where you are asking for a donation in exchange for the product or product variant. In these instances you would want to Allow the Customer to enter a price.'
        : 'Normally stores set the product pricing for each product, and that is how we set your products by default. However, in some cases you may offer a product where you are asking for a donation in exchange for the product. In these instances you would want to Allow the Customer to enter a price.';
    $yesHelp = $isVariant
        ? 'You are choosing to allow customers to enter in the price they would like to pay for this product variant. Due to payment processing minimums, by default the lowest input price a customer can enter in is $0.75. We do allow you to set an alternative minimum amount as well to over-ride Booostr minimum, but your custom minimum must be $0.75 or greater. The default or custom minimum price prevents your customers from entering an amount below the minimum default or set threshold.'
        : 'You are choosing to allow customers to enter in the price they would like to pay for this product. Due to payment processing minimums, by default the lowest input price a customer can enter in is $0.75. We do allow you to set an alternative minimum amount as well to over-ride Booostr minimum, but your custom minimum must be $0.75 or greater. The default or custom minimum price prevents your customers from entering an amount below the minimum default or set threshold.';
@endphp
<div class="write-in-amount-box" data-write-in-block>
    <div class="write-in-amount-title">Allow Customer To Enter Price?</div>
    <p class="write-in-amount-intro mb-2">{{ $intro }}</p>
    <div class="write-in-amount-radios mb-2">
        <label class="mr-3 mb-0">
            <input type="radio"
                   class="write-in-amount-radio"
                   name="{{ $name }}"
                   id="write-in-no-{{ $uid }}"
                   value="0"
                   {{ $enabled ? '' : 'checked' }}>
            No
        </label>
        <label class="mb-0">
            <input type="radio"
                   class="write-in-amount-radio"
                   name="{{ $name }}"
                   id="write-in-yes-{{ $uid }}"
                   value="1"
                   {{ $enabled ? 'checked' : '' }}>
            Yes
        </label>
    </div>
    <p class="write-in-amount-yes-help mb-0" @if(!$enabled) style="display:none;" @endif>{{ $yesHelp }}</p>
</div>
