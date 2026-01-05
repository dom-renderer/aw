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

@section('product-content')
    <div class="row">
        <div class="col-12">
            <div id="pricing-matrix" class="mt-4">
                <h3 class="titleOfCurrentTabUnit">{{ $baseUnit?->unit?->title ?? 'N/A' }} Pricing Tiers</h3>
                <p>Set quantity-based pricing for individual <span
                        class="titleOfCurrentTabUnit">{{ $baseUnit?->unit?->title ?? 'N/A' }}</span></p>

                <ul class="nav nav-tabs" role="tablist">
                    @if($baseUnit)
                        @php
                            $baseTabId = md5('base-' . $baseUnit->id);
                        @endphp
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="{{ $baseTabId }}-tab"
                                data-current-unit="{{ $baseUnit->unit->title ?? 'N/A' }}" data-bs-toggle="tab"
                                href="#{{ $baseTabId }}" role="tab" aria-controls="{{ $baseTabId }}"
                                aria-selected="true">{{ $baseUnit->unit->title ?? 'N/A' }} (Base Unit)</a>
                        </li>
                    @endif
                    @foreach($additionalUnits as $row)
                        @php
                            $tabId = md5('add-' . $row->id);
                        @endphp
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="{{ $tabId }}-tab" data-current-unit="{{ $row->unit->title ?? 'N/A' }}"
                                data-bs-toggle="tab" href="#{{ $tabId }}" role="tab" aria-controls="{{ $tabId }}"
                                aria-selected="false">{{ $row->unit->title ?? 'N/A' }}</a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @if($baseUnit)
                        @php
                            $baseTabId = md5('base-' . $baseUnit->id);
                        @endphp
                        <div class="tab-pane fade show active" id="{{ $baseTabId }}" role="tabpanel"
                            aria-labelledby="{{ $baseTabId }}-tab">
                            
                            @php
                                $baseUnitPricingType = $baseUnit->pricing_type ?? 'tier';
                                $baseUnitPrice = \App\Models\ProductUnitPrice::where('product_id', $product->id)
                                    ->whereNull('product_variant_id')
                                    ->where('unit_type', 0)
                                    ->where('product_additional_unit_id', $baseUnit->id)
                                    ->first();
                            @endphp

                            <div class="pricing-type-selector">
                                <label class="form-label fw-bold">Pricing Type:</label>
                                <select class="form-select pricing-type-select" data-unit-id="{{ $baseUnit->id }}" data-unit-type="0" style="max-width: 300px;">
                                    <option value="tier" @if($baseUnitPricingType == 'tier') selected @endif>Tier Pricing</option>
                                    <option value="non-tier" @if($baseUnitPricingType == 'non-tier') selected @endif>Non-Tier Pricing</option>
                                </select>
                            </div>

                            <div class="tier-pricing-container" style="display: {{ $baseUnitPricingType == 'tier' ? 'block' : 'none' }};">
                                <table class="table table-bordered pricing-table-instance" data-variant-id=""
                                    data-unit-row-id="{{ $baseUnit->id }}" data-unit-type="0">
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
                                        @forelse(\App\Models\ProductTierPricing::where('unit_type', 0)->where('product_id', $product->id)->whereNull('product_variant_id')->where('product_additional_unit_id', $baseUnit->id)->get() as $tier)
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control" name="min_quantity[]"
                                                        value="{{ $tier->min_qty }}" min="1" step="1">
                                                </td>
                                                <td><input type="number" class="form-control" name="max_quantity[]"
                                                        value="{{ $tier->max_qty ?: '' }}"></td>
                                                <td><input type="number" class="form-control" name="price_per_unit[]"
                                                        value="{{ $tier->price_per_unit }}" step="0.01"></td>
                                                <td><input type="number" class="form-control" name="discount[]"
                                                        value="{{ $tier->discount_amount }}" step="0.01"></td>
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

                            <div class="non-tier-pricing-container" style="display: {{ $baseUnitPricingType == 'non-tier' ? 'block' : 'none' }};">
                                <div class="non-tier-pricing-form">
                                    <div class="form-group">
                                        <label class="form-label">Price per Unit <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control non-tier-price" 
                                            name="non_tier_price[{{ $baseUnit->id }}]" 
                                            value="{{ $baseUnitPrice ? $baseUnitPrice->price_per_unit : '' }}" 
                                            step="0.01" min="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Discount Type</label>
                                        <select class="form-select non-tier-discount-type" name="non_tier_discount_type[{{ $baseUnit->id }}]">
                                            <option value="1" @if($baseUnitPrice && $baseUnitPrice->discount_type == 1) selected @endif>Percentage (%)</option>
                                            <option value="0" @if($baseUnitPrice && $baseUnitPrice->discount_type == 0) selected @endif>Fixed Amount</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Discount Amount</label>
                                        <input type="number" class="form-control non-tier-discount" 
                                            name="non_tier_discount[{{ $baseUnit->id }}]" 
                                            value="{{ $baseUnitPrice ? $baseUnitPrice->discount_amount : '0' }}" 
                                            step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @foreach($additionalUnits as $row)
                        @php
                            $tabId = md5('add-' . $row->id);
                        @endphp
                        <div class="tab-pane fade @if(!$baseUnit && $loop->first) show active @endif" id="{{ $tabId }}"
                            role="tabpanel" aria-labelledby="{{ $tabId }}-tab">

                            @php
                                $additionalUnitPricingType = $row->pricing_type ?? 'tier';
                                $additionalUnitPrice = \App\Models\ProductUnitPrice::where('product_id', $product->id)
                                    ->whereNull('product_variant_id')
                                    ->where('unit_type', 1)
                                    ->where('product_additional_unit_id', $row->id)
                                    ->first();
                            @endphp

                            <div class="pricing-type-selector">
                                <label class="form-label fw-bold">Pricing Type:</label>
                                <select class="form-select pricing-type-select" data-unit-id="{{ $row->id }}" data-unit-type="1" style="max-width: 300px;">
                                    <option value="tier" @if($additionalUnitPricingType == 'tier') selected @endif>Tier Pricing</option>
                                    <option value="non-tier" @if($additionalUnitPricingType == 'non-tier') selected @endif>Non-Tier Pricing</option>
                                </select>
                            </div>

                            <div class="tier-pricing-container" style="display: {{ $additionalUnitPricingType == 'tier' ? 'block' : 'none' }};">
                                <table class="table table-bordered pricing-table-instance" data-variant-id=""
                                    data-unit-row-id="{{ $row->id }}" data-unit-type="1">
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
                                        @forelse(\App\Models\ProductTierPricing::where('unit_type', 1)->where('product_id', $product->id)->whereNull('product_variant_id')->where('product_additional_unit_id', $row->id)->get() as $tier)
                                            <tr>
                                                <td><input type="number" class="form-control" name="min_quantity[]"
                                                        value="{{ $tier->min_qty }}" min="1" step="1"></td>
                                                <td><input type="number" class="form-control" name="max_quantity[]"
                                                        value="{{ $tier->max_qty ?: '' }}"></td>
                                                <td><input type="number" class="form-control" name="price_per_unit[]"
                                                        value="{{ $tier->price_per_unit }}" step="0.01"></td>
                                                <td><input type="number" class="form-control" name="discount[]"
                                                        value="{{ $tier->discount_amount }}" step="0.01"></td>
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

                            <div class="non-tier-pricing-container" style="display: {{ $additionalUnitPricingType == 'non-tier' ? 'block' : 'none' }};">
                                <div class="non-tier-pricing-form">
                                    <div class="form-group">
                                        <label class="form-label">Price per Unit <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control non-tier-price" 
                                            name="non_tier_price[{{ $row->id }}]" 
                                            value="{{ $additionalUnitPrice ? $additionalUnitPrice->price_per_unit : '' }}" 
                                            step="0.01" min="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Discount Type</label>
                                        <select class="form-select non-tier-discount-type" name="non_tier_discount_type[{{ $row->id }}]">
                                            <option value="1" @if($additionalUnitPrice && $additionalUnitPrice->discount_type == 1) selected @endif>Percentage (%)</option>
                                            <option value="0" @if($additionalUnitPrice && $additionalUnitPrice->discount_type == 0) selected @endif>Fixed Amount</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Discount Amount</label>
                                        <input type="number" class="form-control non-tier-discount" 
                                            name="non_tier_discount[{{ $row->id }}]" 
                                            value="{{ $additionalUnitPrice ? $additionalUnitPrice->discount_amount : '0' }}" 
                                            step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('product-js')
    <script>
        $(document).ready(function () {
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

            $(document).on('click', '.addANewLevel', function () {
                addRow(this);
            });

            $(document).on('click', '.remove-row', function () {
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
                $(e.target).closest('#pricing-matrix').find('.titleOfCurrentTabUnit').text(`${targetTab} Pricing`);
            });

            $('#productStep1Form').on('submit', function (e) {
                let items = [];
                let nonTierItems = [];
                let pricingTypes = {};

                // Collect pricing types
                $('.pricing-type-select').each(function() {
                    const unitId = $(this).data('unit-id');
                    const unitType = $(this).data('unit-type');
                    pricingTypes[unitId] = {
                        type: $(this).val(),
                        unit_type: unitType
                    };
                });

                // Collect tier pricing items
                $('.pricing-table-instance').each(function () {
                    const unitRowId = parseInt($(this).data('unit-row-id')) || null;
                    const unitType = $(this).data('unit-type');
                    const pricingType = pricingTypes[unitRowId]?.type || 'tier';

                    if (pricingType === 'tier') {
                        $(this).find('tbody tr').each(function () {
                            const minQty = parseFloat($(this).find('input[name="min_quantity[]"]').val());
                            const maxQtyRaw = $(this).find('input[name="max_quantity[]"]').val();
                            const maxQty = maxQtyRaw === '' ? null : parseFloat(maxQtyRaw);
                            const price = parseFloat($(this).find('input[name="price_per_unit[]"]').val());
                            const discount = parseFloat($(this).find('input[name="discount[]"]').val());

                            if (!isNaN(minQty) || !isNaN(maxQty) || !isNaN(price) || !isNaN(discount)) {
                                items.push({
                                    product_variant_id: null,
                                    is_base_unit: unitType,
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
                    const unitId = pricingSelect.data('unit-id');
                    const unitType = pricingSelect.data('unit-type');
                    const pricingType = pricingSelect.val();

                    if (pricingType === 'non-tier') {
                        const price = parseFloat($(this).find('.non-tier-price').val());
                        const discountType = $(this).find('.non-tier-discount-type').val();
                        const discount = parseFloat($(this).find('.non-tier-discount').val()) || 0;

                        if (!isNaN(price) && price > 0) {
                            nonTierItems.push({
                                product_variant_id: null,
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
                $('<input>').attr({ type: 'hidden', name: 'tier_pricings', id: 'tier_pricings_input' })
                    .val(JSON.stringify(items)).appendTo('#productStep1Form');
                $('<input>').attr({ type: 'hidden', name: 'non_tier_pricings', id: 'non_tier_pricings_input' })
                    .val(JSON.stringify(nonTierItems)).appendTo('#productStep1Form');
                $('<input>').attr({ type: 'hidden', name: 'pricing_types', id: 'pricing_types_input' })
                    .val(JSON.stringify(pricingTypes)).appendTo('#productStep1Form');
            });
        });
    </script>
@endpush