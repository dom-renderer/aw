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
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }

        .pricing-type-selector h5 {
            margin-bottom: 15px;
            color: #495057;
            font-weight: 600;
        }

        .pricing-type-options {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .pricing-type-option {
            flex: 1;
            min-width: 200px;
        }

        .pricing-type-option input[type="radio"] {
            display: none;
        }

        .pricing-type-option label {
            display: block;
            padding: 15px 20px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: white;
            font-weight: 500;
            color: #495057;
        }

        .pricing-type-option input[type="radio"]:checked + label {
            border-color: #0d6efd;
            background: #e7f1ff;
            color: #0d6efd;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
        }

        .pricing-type-option label:hover {
            border-color: #0d6efd;
            background: #f0f7ff;
        }

        .single-price-container {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin-top: 20px;
        }

        .single-price-container .form-group {
            margin-bottom: 20px;
        }

        .single-price-container label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .single-price-container .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            font-size: 16px;
        }

        .single-price-container .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .pricing-content-wrapper {
            min-height: 200px;
        }

        .tier-pricing-container {
            display: none;
        }

        .single-pricing-container {
            display: none;
        }

        .tier-pricing-container.active,
        .single-pricing-container.active {
            display: block;
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
                            $baseTierPricing = \App\Models\ProductTierPricing::where('unit_type', 0)
                                ->where('product_id', $product->id)
                                ->whereNull('product_variant_id')
                                ->where('product_additional_unit_id', $baseUnit->id)
                                ->where('pricing_type', 0)
                                ->get();
                            $baseSinglePricing = \App\Models\ProductTierPricing::where('unit_type', 0)
                                ->where('product_id', $product->id)
                                ->whereNull('product_variant_id')
                                ->where('product_additional_unit_id', $baseUnit->id)
                                ->where('pricing_type', 1)
                                ->first();
                            $basePricingType = $baseSinglePricing ? 1 : 0;
                        @endphp
                        <div class="tab-pane fade show active" id="{{ $baseTabId }}" role="tabpanel"
                            aria-labelledby="{{ $baseTabId }}-tab">
                            
                            <div class="pricing-type-selector" data-unit-id="{{ $baseUnit->id }}" data-unit-type="0">
                                <h5>Select Pricing Type</h5>
                                <div class="pricing-type-options">
                                    <div class="pricing-type-option">
                                        <input type="radio" name="pricing_type_{{ $baseUnit->id }}" id="tier_{{ $baseUnit->id }}" value="0" {{ $basePricingType == 0 ? 'checked' : '' }}>
                                        <label for="tier_{{ $baseUnit->id }}">Tier Pricing</label>
                                    </div>
                                    <div class="pricing-type-option">
                                        <input type="radio" name="pricing_type_{{ $baseUnit->id }}" id="single_{{ $baseUnit->id }}" value="1" {{ $basePricingType == 1 ? 'checked' : '' }}>
                                        <label for="single_{{ $baseUnit->id }}">Single Price</label>
                                    </div>
                                </div>
                            </div>

                            <div class="pricing-content-wrapper">
                                <div class="tier-pricing-container {{ $basePricingType == 0 ? 'active' : '' }}" data-unit-id="{{ $baseUnit->id }}">
                                    <table class="table table-bordered pricing-table-instance" data-variant-id=""
                                        data-unit-row-id="{{ $baseUnit->id }}" data-unit-type="0" data-pricing-type="0">
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
                                            @forelse($baseTierPricing as $tier)
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

                                <div class="single-pricing-container {{ $basePricingType == 1 ? 'active' : '' }}" data-unit-id="{{ $baseUnit->id }}">
                                    <div class="single-price-container">
                                        <div class="form-group">
                                            <label for="single_price_{{ $baseUnit->id }}">Price per Unit</label>
                                            <input type="number" class="form-control single-price-input" 
                                                id="single_price_{{ $baseUnit->id }}"
                                                data-unit-id="{{ $baseUnit->id }}"
                                                name="single_price[{{ $baseUnit->id }}]"
                                                value="{{ $baseSinglePricing ? $baseSinglePricing->price_per_unit : '' }}"
                                                step="0.01" min="0.01" placeholder="0.00">
                                        </div>
                                        <div class="form-group">
                                            <label for="single_discount_{{ $baseUnit->id }}">Discount % (Optional)</label>
                                            <input type="number" class="form-control single-discount-input" 
                                                id="single_discount_{{ $baseUnit->id }}"
                                                data-unit-id="{{ $baseUnit->id }}"
                                                name="single_discount[{{ $baseUnit->id }}]"
                                                value="{{ $baseSinglePricing ? $baseSinglePricing->discount_amount : '' }}"
                                                step="0.01" min="0" max="100" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @foreach($additionalUnits as $row)
                        @php
                            $tabId = md5('add-' . $row->id);
                            $addTierPricing = \App\Models\ProductTierPricing::where('unit_type', 1)
                                ->where('product_id', $product->id)
                                ->whereNull('product_variant_id')
                                ->where('product_additional_unit_id', $row->id)
                                ->where('pricing_type', 0)
                                ->get();
                            $addSinglePricing = \App\Models\ProductTierPricing::where('unit_type', 1)
                                ->where('product_id', $product->id)
                                ->whereNull('product_variant_id')
                                ->where('product_additional_unit_id', $row->id)
                                ->where('pricing_type', 1)
                                ->first();
                            $addPricingType = $addSinglePricing ? 1 : 0;
                        @endphp
                        <div class="tab-pane fade @if(!$baseUnit && $loop->first) show active @endif" id="{{ $tabId }}"
                            role="tabpanel" aria-labelledby="{{ $tabId }}-tab">

                            <div class="pricing-type-selector" data-unit-id="{{ $row->id }}" data-unit-type="1">
                                <h5>Select Pricing Type</h5>
                                <div class="pricing-type-options">
                                    <div class="pricing-type-option">
                                        <input type="radio" name="pricing_type_{{ $row->id }}" id="tier_{{ $row->id }}" value="0" {{ $addPricingType == 0 ? 'checked' : '' }}>
                                        <label for="tier_{{ $row->id }}">Tier Pricing</label>
                                    </div>
                                    <div class="pricing-type-option">
                                        <input type="radio" name="pricing_type_{{ $row->id }}" id="single_{{ $row->id }}" value="1" {{ $addPricingType == 1 ? 'checked' : '' }}>
                                        <label for="single_{{ $row->id }}">Single Price</label>
                                    </div>
                                </div>
                            </div>

                            <div class="pricing-content-wrapper">
                                <div class="tier-pricing-container {{ $addPricingType == 0 ? 'active' : '' }}" data-unit-id="{{ $row->id }}">
                                    <table class="table table-bordered pricing-table-instance" data-variant-id=""
                                        data-unit-row-id="{{ $row->id }}" data-unit-type="1" data-pricing-type="0">
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
                                            @forelse($addTierPricing as $tier)
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

                                <div class="single-pricing-container {{ $addPricingType == 1 ? 'active' : '' }}" data-unit-id="{{ $row->id }}">
                                    <div class="single-price-container">
                                        <div class="form-group">
                                            <label for="single_price_{{ $row->id }}">Price per Unit</label>
                                            <input type="number" class="form-control single-price-input" 
                                                id="single_price_{{ $row->id }}"
                                                data-unit-id="{{ $row->id }}"
                                                name="single_price[{{ $row->id }}]"
                                                value="{{ $addSinglePricing ? $addSinglePricing->price_per_unit : '' }}"
                                                step="0.01" min="0.01" placeholder="0.00">
                                        </div>
                                        <div class="form-group">
                                            <label for="single_discount_{{ $row->id }}">Discount % (Optional)</label>
                                            <input type="number" class="form-control single-discount-input" 
                                                id="single_discount_{{ $row->id }}"
                                                data-unit-id="{{ $row->id }}"
                                                name="single_discount[{{ $row->id }}]"
                                                value="{{ $addSinglePricing ? $addSinglePricing->discount_amount : '' }}"
                                                step="0.01" min="0" max="100" placeholder="0.00">
                                        </div>
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
            // Handle pricing type switching
            $(document).on('change', 'input[type="radio"][name^="pricing_type_"]', function() {
                const unitId = $(this).closest('.pricing-type-selector').data('unit-id');
                const pricingType = $(this).val();
                const tabPane = $(this).closest('.tab-pane');
                
                if (pricingType == '0') {
                    // Show tier pricing, hide single pricing
                    tabPane.find('.tier-pricing-container[data-unit-id="' + unitId + '"]').addClass('active');
                    tabPane.find('.single-pricing-container[data-unit-id="' + unitId + '"]').removeClass('active');
                } else {
                    // Show single pricing, hide tier pricing
                    tabPane.find('.single-pricing-container[data-unit-id="' + unitId + '"]').addClass('active');
                    tabPane.find('.tier-pricing-container[data-unit-id="' + unitId + '"]').removeClass('active');
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
                
                // Process tier pricing
                $('.pricing-table-instance').each(function () {
                    const unitRowId = parseInt($(this).data('unit-row-id')) || null;
                    const unitType = $(this).data('unit-type');
                    const pricingType = $(this).data('pricing-type') || 0;
                    
                    // Only process if this is tier pricing and is active
                    if (pricingType == 0) {
                        const container = $(this).closest('.tier-pricing-container');
                        if (container.hasClass('active')) {
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
                                        pricing_type: 0,
                                        min_qty: isNaN(minQty) ? null : minQty,
                                        max_qty: isNaN(maxQty) ? null : maxQty,
                                        price_per_unit: isNaN(price) ? null : price,
                                        discount_type: 1,
                                        discount_amount: isNaN(discount) ? 0 : discount
                                    });
                                }
                            });
                        }
                    }
                });
                
                // Process single pricing
                $('.single-price-input').each(function() {
                    const unitId = parseInt($(this).data('unit-id')) || null;
                    const price = parseFloat($(this).val());
                    const container = $(this).closest('.single-pricing-container');
                    
                    if (container.hasClass('active') && !isNaN(price) && price > 0) {
                        const discountInput = container.find('.single-discount-input');
                        const discount = parseFloat(discountInput.val()) || 0;
                        
                        // Find unit type from the tab
                        const tabPane = $(this).closest('.tab-pane');
                        const unitTypeSelector = tabPane.find('.pricing-type-selector');
                        const unitType = unitTypeSelector.data('unit-type') || 0;
                        
                        items.push({
                            product_variant_id: null,
                            is_base_unit: unitType,
                            product_additional_unit_id: unitId,
                            pricing_type: 1,
                            min_qty: 1,
                            max_qty: null,
                            price_per_unit: price,
                            discount_type: 1,
                            discount_amount: discount
                        });
                    }
                });
                
                $('#tier_pricings_input').remove();
                $('<input>').attr({ type: 'hidden', name: 'tier_pricings', id: 'tier_pricings_input' })
                    .val(JSON.stringify(items)).appendTo('#productStep1Form');
            });
        });
    </script>
@endpush