@foreach($info->optionwithcategories ?? [] as $key => $row)
@php
    $groupName = $row->category->name ?? 'Option';
    $slug = strtolower($row->category->slug ?? '');
    $isSelect = ($slug == 'select');
    $variationItems = $row->priceswithvaritions ?? collect([]);
    $hasVaritions = $variationItems->isNotEmpty();
    $legacyItems = !$hasVaritions ? ($row->priceswithcategories ?? collect([])) : collect([]);
@endphp

<div class="product-weight variation-group mb-3" data-group-id="{{ $row->id }}" data-required="{{ $row->is_required ?? 0 }}">
    <h6 class="mb-2" style="font-size: 14px; font-weight: 600; color: #1e293b;">
        <span class="text-danger {{ $row->is_required == 1 ? 'required_var' : '' }}" data-id="{{ $row->id }}">
            {{ $row->is_required == 1 ? '*' : '' }}
        </span>{{ $groupName }} :
    </h6>

    @if($hasVaritions)
        @if($isSelect)
            <div class="variation-select-wrapper" style="max-width: 320px;">
                <select class="form-control variation-select-input pricesvariations" name="option[{{ $row->id }}][]" data-group-id="{{ $row->id }}" style="height: 42px; border-radius: 6px; border: 1.5px solid #cbd5e1; font-weight: 500; font-size: 14px; color: #334155; padding: 0 14px;">
                    <option value="">-- {{ __('Select') }} {{ $groupName }} --</option>
                    @foreach($variationItems as $price_category)
                        <option value="{{ $price_category->id }}">{{ $price_category->name }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="variation-chips-wrapper d-flex flex-wrap" style="gap: 8px;">
                @foreach($variationItems as $k => $price_category)
                    <div class="variation-chip">
                        <input 
                            type="radio" 
                            class="variation-radio-input pricesvariations" 
                            id="var_opt_{{ $row->id }}_{{ $price_category->id }}" 
                            name="option[{{ $row->id }}][]" 
                            value="{{ $price_category->id }}"
                            data-group-id="{{ $row->id }}"
                            style="display: none;"
                        >
                        <label for="var_opt_{{ $row->id }}_{{ $price_category->id }}" class="variation-chip-label">
                            {{ $price_category->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endif
    @elseif($legacyItems->isNotEmpty())
        {{-- Fallback for legacy standalone options --}}
        <div class="variation-chips-wrapper d-flex flex-wrap" style="gap: 8px;">
            @foreach($legacyItems as $k => $price)
                <div class="variation-chip">
                    <input 
                        type="{{ $row->select_type == 1 ? 'checkbox' : 'radio' }}" 
                        class="variation-radio-input pricesvariations variations{{ $row->id }} {{ $row->is_required == 1 ? 'req' : '' }}" 
                        data-stockstatus="{{ $price->stock_status }}"  
                        data-stockmanage="{{ $price->stock_manage }}" 
                        data-sku="{{ $price->sku }}" 
                        data-qty="{{ $price->qty }}"  
                        data-oldprice="{{ $price->old_price }}" 
                        data-price="{{ $price->price }}" 
                        id="variation{{ $price->id.$k+$key }}" 
                        name="option[{{ $row->id }}][]" 
                        value="{{ $price->id ?? '' }}"
                        style="display: none;"
                    >
                    <label for="variation{{ $price->id.$k+$key }}" class="variation-chip-label">
                        {{ $price->category->name ?? '' }}
                    </label>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endforeach
