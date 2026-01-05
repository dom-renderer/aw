@extends('products.layout', ['step' => $step, 'type' => $type, 'product' => $product])

@push('product-css')
<style>
    .pricing-table th,
    .pricing-table td {
        text-align: center;
        vertical-align: middle;
    }

    .pricing-table .form-control {
        width: 80px;
    }

    .pricing-table td input[type="number"] {
        max-width: 100px;
    }

    .pricing-table td button {
        font-size: 12px;
        padding: 5px 10px;
    }

    .pricing-table .tab-content {
        padding-top: 20px;
    }

    .add-row-btn {
        text-align: center;
        margin-top: 20px;
    }

    .pricing-table {
        margin-top: 20px;
    }

    .actions-btn {
        display: flex;
        justify-content: space-between;
    }

    .actions-btn button {
        font-size: 14px;
    }

    .pricing-type-selector {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .non-tier-pricing-form {
        padding: 20px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }

    .non-tier-pricing-form .form-group {
        margin-bottom: 15px;
    }
</style>
@endpush

@php
$finalVariants = $finalVariantsInfo = [];
foreach ($product->variants as $variant) {
    $temp = $variant?->additionalUnits()?->with('unit')?->get()?->toArray() ?? [];
    $baseUn = $variant?->baseUnit()?->with('unit')?->first()?->toArray() ?? [];

    array_unshift($temp, $baseUn);
    $finalVariants[] = $temp;
    $finalVariantsInfo[] = $variant;
}
@endphp

@section('product-content')
<div class="row">
    <div class="col-12">
        <label for="variant"> Select Variant </label>
        <select name="variant" id="variant" class="form-control">
            @foreach($product->variants as $variant)
            <option value="{{ $variant->id }}" @if($loop->first) selected @endif> {{ $variant->name }} - {{ $variant->sku }} </option>
            @endforeach
        </select>

        <div id="pricing-matrix" class="mt-4">
            @foreach ($finalVariants as $variant)
                <div class="mt-4 main-visibility-container @if(!$loop->first) d-none @endif" data-current-unit-id="{{ $finalVariantsInfo[$loop->iteration - 1]['id'] }}">
                    <h3 class="titleOfCurrentTabUnit">{{ $variant[0]['unit']['title'] ?? 'N/A' }} Pricing Tiers</h3>
                    <p>Set quantity-based pricing for individual <span class="titleOfCurrentTabUnit"> {{ $variant[0]['unit']['title'] ?? 'N/A' }} </span> </p>

                    <ul class="nav nav-tabs" role="tablist">
                        @if(isset($variant[0]['id']))
                            @foreach ($variant as $row)
                                @php
                                $tabId = md5($row['id'] . '-' . $row['variant_id']);
                                @endphp
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if($loop->first) active @endif" id="{{ $tabId }}-tab" data-current-unit="{{ $row['unit']['title'] ?? 'N/A' }}" data-bs-toggle="tab" href="#{{ $tabId }}" role="tab" aria-controls="{{ $tabId }}" aria-selected="true">{{ $row['unit']['title'] ?? 'N/A' }} @if($loop->first) (Base Unit) @endif </a>
                            </li>
                            @endforeach
                        @endif
                    </ul>

                    <div class="tab-content">
                        @if(isset($variant[0]['id']))
                            @foreach ($variant as $row)
                                @php
                                $tabId = md5($row['id'] . '-' . $row['variant_id']);
                                @endphp
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                                @php
                                    $unitPricingType = 'tier';
                                    $unitType = $loop->first ? 0 : 1;
                                    $unitModel = $loop->first 
                                        ? \App\Models\ProductBaseUnit::find($row['id'])
                                        : \App\Models\ProductAdditionalUnit::find($row['id']);
                                    if ($unitModel) {
                                        $unitPricingType = $unitModel->pricing_type ?? 'tier';
                                    }
                                    $unitPrice = \App\Models\ProductUnitPrice::where('product_id', $product->id)
                                        ->where('product_variant_id', $row['variant_id'])
                                        ->where('unit_type', $unitType)
                                        ->where('product_additional_unit_id', $row['id'])
                                        ->first();
                                @endphp

                                <div class="pricing-type-selector">
                                    <label class="form-label fw-bold">Pricing Type:</label>
                                    <select class="form-select pricing-type-select" 
                                        data-variant-id="{{ $row['variant_id'] }}" 
                                        data-unit-id="{{ $row['id'] }}" 
                                        data-unit-type="{{ $unitType }}" 
                                        style="max-width: 300px;">
                                        <option value="tier" @if($unitPricingType == 'tier') selected @endif>Tier Pricing</option>
                                        <option value="non-tier" @if($unitPricingType == 'non-tier') selected @endif>Non-Tier Pricing</option>
                                    </select>
                                </div>

                                <div class="tier-pricing-container" style="display: {{ $unitPricingType == 'tier' ? 'block' : 'none' }};">
                                    <table class="table table-bordered pricing-table-instance" data-variant-id="{{ $row['variant_id'] }}" data-unit-row-id="{{ $row['id'] }}">
                                        <thead>
                                            <tr>
                                                <th>Min Quantity</th>
                                                <th>Max Quantity</th>
                                                <th>Price per Unit</th>
                                                <th>Discount %</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(\App\Models\ProductTierPricing::where('product_variant_id', $row['variant_id'])->where('product_additional_unit_id', $row['id'])->get() as $tier)
                                            <tr>
                                                <td><input type="number" class="form-control" name="min_quantity[]" value="{{ $tier->min_qty }}" min="1" step="1"></td>
                                                <td><input type="number" class="form-control" name="max_quantity[]" value="{{ $tier->max_qty }}"></td>
                                                <td><input type="number" class="form-control" name="price_per_unit[]" value="{{ $tier->price_per_unit }}" step="0.01"></td>
                                                <td><input type="number" class="form-control" name="discount[]" value="{{ $tier->discount_amount }}" step="0.01"></td>
                                                <td class="actions-btn">
                                                    <button type="button" class="btn btn-danger remove-row">Delete</button>
                                                </td>
                                            </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="add-row-btn">
                                        <button type="button" class="btn btn-primary addANewLevel">+ Add New Pricing Tier</button>
                                    </div>
                                </div>

                                <div class="non-tier-pricing-container" style="display: {{ $unitPricingType == 'non-tier' ? 'block' : 'none' }};">
                                    <div class="non-tier-pricing-form">
                                        <div class="form-group">
                                            <label class="form-label">Price per Unit <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control non-tier-price" 
                                                name="non_tier_price[{{ $row['variant_id'] }}][{{ $row['id'] }}]" 
                                                value="{{ $unitPrice ? $unitPrice->price_per_unit : '' }}" 
                                                step="0.01" min="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Discount Type</label>
                                            <select class="form-select non-tier-discount-type" name="non_tier_discount_type[{{ $row['variant_id'] }}][{{ $row['id'] }}]">
                                                <option value="1" @if($unitPrice && $unitPrice->discount_type == 1) selected @endif>Percentage (%)</option>
                                                <option value="0" @if($unitPrice && $unitPrice->discount_type == 0) selected @endif>Fixed Amount</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Discount Amount</label>
                                            <input type="number" class="form-control non-tier-discount" 
                                                name="non_tier_discount[{{ $row['variant_id'] }}][{{ $row['id'] }}]" 
                                                value="{{ $unitPrice ? $unitPrice->discount_amount : '0' }}" 
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('product-js')
<script>
    let priceMatrix = {};

    $(document).ready(function() {

        $('#variant').select2({
            placeholder: 'Select Variant',
            allowClear: true,
            width: '100%'
        }).on('change', function () {
            let variantId = $('option:selected', this).val();
            
            $('.main-visibility-container').addClass('d-none');
            
            $('.main-visibility-container').each(function() {
                if ($(this).data('current-unit-id') == variantId) {
                    $(this).removeClass('d-none');
                }
            });
        });

        // Handle pricing type selector change
        $(document).on('change', '.pricing-type-select', function () {
            const pricingType = $(this).val();
            const tabPane = $(this).closest('.tab-pane');
            const tierContainer = tabPane.find('.tier-pricing-container');
            const nonTierContainer = tabPane.find('.non-tier-pricing-container');

            if (pricingType === 'tier') {
                tierContainer.slideDown();
                nonTierContainer.slideUp();
            } else {
                tierContainer.slideUp();
                nonTierContainer.slideDown();
            }
        });

    function addRow(element) {
      var newRow = `
        <tr>
          <td><input type="number" class="form-control" name="min_quantity[]" value="1" min="1" step="1"></td>
          <td><input type="number" class="form-control" name="max_quantity[]" value="5"></td>
          <td><input type="number" class="form-control" name="price_per_unit[]" value="0" step="0.01"></td>
          <td><input type="number" class="form-control" name="discount[]" value="0" step="0.01"></td>
          <td class="actions-btn">
            <button type="button" class="btn btn-danger remove-row">Delete</button>
          </td>
        </tr>
      `;
      $(element).parent().prev().find('tbody').append(newRow);
    }

    $(document).on('click', '.addANewLevel', function() {
      addRow(this);
    });
    
    $(document).on('click', '.remove-row', function() {
        let that = this;
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(that).closest('tr').remove();
            }
        });
    });

    $(document).on('shown.bs.tab', '.nav-tabs a', function (e) {
        var targetTab = $(e.target).data('current-unit');
        $(e.target).parent().parent().parent().find('.titleOfCurrentTabUnit').text(`${targetTab} Pricing`);
    });

    $('#productStep1Form').on('submit', function(e) {
        let items = [];
        let nonTierItems = [];
        let pricingTypes = {};

        // Collect pricing types
        $('.pricing-type-select').each(function() {
            const variantId = $(this).data('variant-id');
            const unitId = $(this).data('unit-id');
            const unitType = $(this).data('unit-type');
            const key = `${variantId}_${unitId}`;
            pricingTypes[key] = {
                variant_id: variantId,
                unit_id: unitId,
                unit_type: unitType,
                type: $(this).val()
            };
        });

        // Collect tier pricing items
        $('.pricing-table-instance').each(function() {
            const variantId = parseInt($(this).data('variant-id')) || null;
            const unitRowId = parseInt($(this).data('unit-row-id')) || null;
            const key = `${variantId}_${unitRowId}`;
            const pricingType = pricingTypes[key]?.type || 'tier';

            if (pricingType === 'tier') {
                $(this).find('tbody tr').each(function(index) {
                    const minQty = parseFloat($(this).find('input[name="min_quantity[]"]').val());
                    const maxQtyRaw = $(this).find('input[name="max_quantity[]"]').val();
                    const maxQty = maxQtyRaw === '' ? null : parseFloat(maxQtyRaw);
                    const price = parseFloat($(this).find('input[name="price_per_unit[]"]').val());
                    const discount = parseFloat($(this).find('input[name="discount[]"]').val());
                    if (!isNaN(minQty) || !isNaN(maxQty) || !isNaN(price) || !isNaN(discount)) {
                        items.push({
                            product_variant_id: variantId,
                            product_additional_unit_id: unitRowId,
                            min_qty: isNaN(minQty) ? null : minQty,
                            max_qty: isNaN(maxQty) ? null : maxQty,
                            price_per_unit: isNaN(price) ? null : price,
                            discount_type: 1,
                            discount_amount: isNaN(discount) ? 0 : discount
                        });
                    }
                });
            }
        });

        // Collect non-tier pricing items
        $('.non-tier-pricing-container').each(function() {
            const tabPane = $(this).closest('.tab-pane');
            const pricingSelect = tabPane.find('.pricing-type-select');
            const variantId = pricingSelect.data('variant-id');
            const unitId = pricingSelect.data('unit-id');
            const unitType = pricingSelect.data('unit-type');
            const pricingType = pricingSelect.val();

            if (pricingType === 'non-tier') {
                const price = parseFloat($(this).find('.non-tier-price').val());
                const discountType = $(this).find('.non-tier-discount-type').val();
                const discount = parseFloat($(this).find('.non-tier-discount').val()) || 0;

                if (!isNaN(price) && price > 0) {
                    nonTierItems.push({
                        product_variant_id: variantId,
                        unit_type: unitType,
                        product_additional_unit_id: unitId,
                        price_per_unit: price,
                        discount_type: parseInt(discountType),
                        discount_amount: discount
                    });
                }
            }
        });

        // Remove old hidden inputs
        $('#tier_pricings_input, #non_tier_pricings_input, #pricing_types_input').remove();
        
        // Add new hidden inputs
        $('<input>').attr({type:'hidden', name:'tier_pricings', id:'tier_pricings_input'})
            .val(JSON.stringify(items)).appendTo('#productStep1Form');
        $('<input>').attr({type:'hidden', name:'non_tier_pricings', id:'non_tier_pricings_input'})
            .val(JSON.stringify(nonTierItems)).appendTo('#productStep1Form');
        $('<input>').attr({type:'hidden', name:'pricing_types', id:'pricing_types_input'})
            .val(JSON.stringify(pricingTypes)).appendTo('#productStep1Form');
    });

    });
</script>
@endpush