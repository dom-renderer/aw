@extends('front.layout')

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    #checkoutErrors {
        margin-bottom: 20px;
    }
    #checkoutErrors ul {
        margin-bottom: 0;
        padding-left: 20px;
    }
</style>
@endpush

@section('content')
    <!-- MAin-section Content Start -->
    <section class="cart-mian">
        <div class="car-block ">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-xl-6 col-xxl-8 col-md-12">
                        <div class="c-left-block">
                            <h2 class="h-30 mb-4">
                                Your Cart
                                <span class="p-18" id="cartItemsCountText">
                                    ({{ isset($cartItems) ? $cartItems->sum('quantity') : 0 }} items)
                                </span>
                            </h2>

                            <div class="c-left-top" id="cartItemsContainer">
                                @if(isset($cartItems) && $cartItems->count())
                                    @foreach($cartItems as $item)
                                        @php
                                            $product = $item->product;
                                            $variant = $item->productVariant;
                                            $quantity = (float) $item->quantity;
                                        @endphp
                                        <div class="c-lft-box bdr-clr cart-item-row"
                                             data-item-id="{{ $item->id }}"
                                             data-product-short-url="{{ $product?->short_url }}"
                                             data-variant-short-url="{{ $variant?->short_url }}"
                                             data-unit-type="{{ $item->unit_type }}"
                                             data-unit-id="{{ $item->unit_id }}">
                                             @if($product->type == 'simple')
                                            <div class="row align-items-center">
                                                <div class="col-lg-12 col-xl-12 col-xxl-8 col-md-6">
                                                    <div class="d-flex gap-4 align-items-center">
                                                        <div class="crt-img">
                                                            @if($product?->primaryImage?->file)
                                                                <img src="{{ asset('storage/' . $product?->primaryImage?->file) }}" class="w-100" alt="">
                                                            @else
                                                                <img src="{{ asset('front-theme/images/cart-1.png') }}" class="w-100" alt="">
                                                            @endif
                                                        </div>
                                                        <div class="cart-details">
                                                            <h3 class="h-24 mb-2">
                                                                {{ $product?->name ?? 'Product' }}
                                                            </h3>
                                                            @if(!empty($product?->sku))
                                                                <p class="p-18 mb-2">SKU: {{ $product->sku }}</p>
                                                            @endif
                                                            <p class="p-18 mb-2">
                                                                @if($variant)
                                                                    Variant: {{ $variant->short_url }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-xl-12 col-xxl-4 col-md-6">
                                                    <div class="cart-all-dtl">
                                                        <div class="col-auto">
                                                            <div class="input-group quantity-group">
                                                                <button class="btn btn-outline-secondary btn-minus cart-qty-minus" type="button">−</button>
                                                                <input type="text"
                                                                       class="form-control text-center cart-qty-input"
                                                                       value="{{ $quantity }}"
                                                                       readonly="">
                                                                <button class="btn btn-outline-secondary btn-plus cart-qty-plus" type="button">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="cart-pra">
                                                            <p class="h-24 cart-item-price">
                                                                <span class="price-loading">Loading...</span>
                                                            </p>
                                                            <p class="p-18">Total</p>
                                                        </div>
                                                        <button type="button" class="cart-delete cart-item-remove btn p-0 border-0 bg-transparent">
                                                            <img src="{{ asset('front-theme/images/cart-delete.png') }}" alt="">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @elseif($product->type == 'variable' && isset($variant->id))
                                            <div class="row align-items-center">
                                                <div class="col-lg-12 col-xl-12 col-xxl-8 col-md-6">
                                                    <div class="d-flex gap-4 align-items-center">
                                                        <div class="crt-img">
                                                            @if($variant?->variantImage?->file)
                                                                <img src="{{ asset('storage/' . $variant?->variantImage?->file) }}" class="w-100" alt="">
                                                            @else
                                                                <img src="{{ asset('front-theme/images/cart-1.png') }}" class="w-100" alt="">
                                                            @endif
                                                        </div>
                                                        <div class="cart-details">
                                                            <h3 class="h-24 mb-2">
                                                                {{ $variant?->name ?? 'Product' }}
                                                            </h3>
                                                            @if(!empty($product?->sku))
                                                                <p class="p-18 mb-2">SKU: {{ $variant->sku }}</p>
                                                            @endif
                                                            <p class="p-18 mb-2">
                                                                @if($variant)
                                                                    Variant: {{ $variant->short_url }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-xl-12 col-xxl-4 col-md-6">
                                                    <div class="cart-all-dtl">
                                                        <div class="col-auto">
                                                            <div class="input-group quantity-group">
                                                                <button class="btn btn-outline-secondary btn-minus cart-qty-minus" type="button">−</button>
                                                                <input type="text"
                                                                       class="form-control text-center cart-qty-input"
                                                                       value="{{ $quantity }}"
                                                                       readonly="">
                                                                <button class="btn btn-outline-secondary btn-plus cart-qty-plus" type="button">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="cart-pra">
                                                            <p class="h-24 cart-item-price">
                                                                <span class="price-loading">Loading...</span>
                                                            </p>
                                                            <p class="p-18">Total</p>
                                                        </div>
                                                        <button type="button" class="cart-delete cart-item-remove btn p-0 border-0 bg-transparent">
                                                            <img src="{{ asset('front-theme/images/cart-delete.png') }}" alt="">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-cart-message text-center py-5">
                                        <p class="p-18">Your cart is empty</p>
                                    </div>
                                @endif
                            </div>

                            <div class="crt-middle bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-4">Promo Code</h3>
                                <div class="promo-code">
                                    <input type="text" placeholder="Enter Promo">
                                    <button class="btn blue-btnm">Apply</button>
                                </div>
                            </div>

                            <div class="cart-summery bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-4">Order Summary</h3>
                                <ul>
                                    <li>
                                        <span>Subtotal</span>
                                        <span id="cartSubtotalText">$0.00</span>
                                    </li>
                                    <li>
                                        <span>Shipping</span>
                                        <span id="cartShippingText">$0.00</span>
                                    </li>
                                    <li>
                                        <span>Tax Estimate</span>
                                        <span id="cartTaxText">$0.00</span>
                                    </li>
                                </ul>
                                <div class="crt-total d-flex justify-content-between gap-2">
                                    <p class="h-24">Total</p>
                                    <p class="h-24" id="cartTotalText">$0.00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="col-lg-6 col-xl-6 col-xxl-4 col-md-12">
                    <div class="c-right-block">
                        <h2 class="h-30 mb-4">Checkout</h2>
                        <form id="checkoutForm" action="{{ route('order.place') }}" method="POST">
                            @csrf
                            <div id="checkoutErrors" class="alert alert-danger d-none mb-3"></div>
                            <div class="shopping-text bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-3">Shipping Information</h3>
                                <div class="row mb-3">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fm-inpt" id="first_name" name="first_name" value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->name : '' }}" required>
                                    </div>
                                     <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fm-inpt" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" class="form-control fm-inpt" id="company_name" name="company_name">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control fm-inpt" id="email" name="email" value="{{ auth()->guard('customer')->check() ? auth()->guard('customer')->user()->email : '' }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fm-inpt" id="phone" name="phone" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="address_line_1" class="form-label">Street Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fm-inpt" id="address_line_1" name="address_line_1" required>
                                    </div>                                    
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="address_line_2" class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control fm-inpt" id="address_line_2" name="address_line_2">
                                    </div>                                    
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fm-inpt" id="city" name="city" required>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="zipcode" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control fm-inpt" id="zipcode" name="zipcode" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                                        <select class="form-control fm-inpt" id="country_id" name="country_id" required>
                                            <option value="">Select Country</option>
                                            @foreach(\App\Models\Country::all() as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>                                    
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="state_id" class="form-label">State <span class="text-danger">*</span></label>
                                        <select class="form-control fm-inpt" id="state_id" name="state_id" required>
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="customer_notes" class="form-label">Order Notes</label>
                                        <textarea class="form-control fm-inpt" id="customer_notes" name="customer_notes" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="shop-info bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-3">Payment Method</h3>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input payment-method" type="radio" name="payment_method" id="payment_card" value="credit_debit_card" checked>
                                            <label class="form-check-label" for="payment_card">
                                                <img src="{{ asset('front-theme/images/credit.png') }}" alt="" style="height: 20px;"> &nbsp; Credit/Debit Card
                                            </label>
                                        </div>
                                        <div id="cardDetails" class="payment-details">
                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <label for="card_number" class="form-label">Card Number <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control fm-inpt" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                                    <label for="expiry_date" class="form-label">Expiration Date <span class="text-danger">*</span></label>
                                                    <input type="month" class="form-control fm-inpt" id="expiry_date" name="expiry_date">
                                    </div>
                                     <div class="col-lg-6">
                                                    <label for="cvv" class="form-label">CVV <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control fm-inpt" id="cvv" name="cvv" placeholder="123" maxlength="4">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <label for="cardholder_name" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control fm-inpt" id="cardholder_name" name="cardholder_name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <input class="form-check-input payment-method" type="radio" name="payment_method" id="payment_cod" value="cash_on_delivery">
                                            <label class="form-check-label" for="payment_cod">
                                                Cash on Delivery
                                            </label>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="order-summry bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-3">Order Summary</h3>
                                <ul>
                                    <li>
                                        <span>Subtotal</span>
                                        <span id="checkoutSubtotal">$0.00</span>
                                    </li>
                                    <li>
                                        <span>Shipping</span>
                                        <span id="checkoutShipping">$0.00</span>
                                    </li>
                                    <li>
                                        <span>Tax Estimate</span>
                                        <span id="checkoutTax">$0.00</span>
                                    </li>
                                </ul>
                                <div class="crt-total d-flex justify-content-between gap-2">
                                    <p class="h-24">Total</p>
                                    <p class="h-24" id="checkoutTotal">$0.00</p>
                                </div>
                                <div class="crt-estimate">
                                    <img src="{{ asset('front-theme/images/location.png') }}" alt="">
                                    Estimated Delivery: <span id="estimatedDelivery">Within 5-7 business days</span>
                                </div>
                                <div class="crt-place">
                                    <button type="submit" class="btn cart-btn d-block w-100" id="placeOrderBtn">
                                        <span class="btn-text">Place Order</span>
                                        <span class="btn-loading d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Processing...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAin-section Content Start -->
   
<!-- Service Content Start -->
<section class="service">
    <div class="service-block bg-sky">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-xl-4  col-md-4 col-sm-4">
                    <div class="round-png">
                        <img src="images/service-img1.png" alt="">
                    </div>
                    <h2 class="h-24 my-3">Secure Payments</h2>
                    <p class="p-18">256-bit SSL encryption protects your <br> payment information</p>
                </div>
                <div class="col-lg-4 col-xl-4  col-md-4 col-sm-4">
                    <div class="round-png">
                        <img src="images/service-img2.png" alt="">
                    </div>
                    <h2 class="h-24 my-3">Secure Payments</h2>
                    <p class="p-18">Same-day dispatch for orders placed before <br> 2PM</p>
                </div>
                <div class="col-lg-4 col-xl-4  col-md-4 col-sm-4">
                    <div class="round-png">
                        <img src="images/service-img3.png" alt="">
                    </div>
                    <h2 class="h-24 my-3">24/7 Support</h2>
                    <p class="p-18">Dedicated support team for all your <br> wholesale needs</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        function updateCartTotals() {
            let subtotal = 0;
            $('.cart-item-row').each(function() {
                const priceText = $(this).find('.cart-item-price').text().replace('$', '').replace(',', '');
                const price = parseFloat(priceText) || 0;
                subtotal += price;
            });

            $('#cartSubtotalText').text('$' + subtotal.toFixed(2));
            $('#cartTotalText').text('$' + subtotal.toFixed(2));
        }

        function loadItemPrice(itemRow) {
            const productShortUrl = itemRow.data('product-short-url');
            const variantShortUrl = itemRow.data('variant-short-url');
            const unitType = itemRow.data('unit-type');
            const unitId = itemRow.data('unit-id');
            const quantity = parseFloat(itemRow.find('.cart-qty-input').val()) || 1;

            if (!productShortUrl || !unitId) {
                return;
            }

            $.ajax({
                url: '{{ route('product.pricing') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_short_url: productShortUrl,
                    product_variant_short_url: variantShortUrl || null,
                    unit_type: unitType,
                    unit_id: unitId
                },
                success: function(response) {
                    if (response.success) {
                        let price = 0;
                        if (response.pricing_type === 1 && response.single_pricing) {
                            price = response.single_pricing.your_price * quantity;
                        } else if (response.pricing_type === 0 && response.tier_pricings && response.tier_pricings.length > 0) {
                            let applicableTier = null;
                            for (let i = response.tier_pricings.length - 1; i >= 0; i--) {
                                const tier = response.tier_pricings[i];
                                if (quantity >= tier.min_qty && (tier.max_qty === 0 || quantity <= tier.max_qty)) {
                                    applicableTier = tier;
                                    break;
                                }
                            }
                            if (applicableTier) {
                                price = applicableTier.your_price * quantity;
                            } else {
                                price = response.tier_pricings[0].your_price * quantity;
                            }
                        }

                        itemRow.find('.cart-item-price').html('$' + price.toFixed(2));
                        updateCartTotals();
                    }
                },
                error: function() {
                    itemRow.find('.cart-item-price').html('<span class="text-danger">Error</span>');
                }
            });
        }

        $('.cart-item-row').each(function() {
            loadItemPrice($(this));
        });

        $(document).on('click', '.cart-qty-plus', function() {
            const input = $(this).siblings('.cart-qty-input');
            const currentVal = parseFloat(input.val()) || 0;
            const newVal = currentVal + 1;
            input.val(newVal);
            
            const itemRow = $(this).closest('.cart-item-row');
            const itemId = itemRow.data('item-id');
            
            updateCartItemQuantity(itemId, newVal, itemRow);
        });

        $(document).on('click', '.cart-qty-minus', function() {
            const input = $(this).siblings('.cart-qty-input');
            const currentVal = parseFloat(input.val()) || 0;
            if (currentVal > 1) {
                const newVal = currentVal - 1;
                input.val(newVal);
                
                const itemRow = $(this).closest('.cart-item-row');
                const itemId = itemRow.data('item-id');
                
                updateCartItemQuantity(itemId, newVal, itemRow);
            }
        });

        function updateCartItemQuantity(itemId, quantity, itemRow) {
            $.ajax({
                url: '{{ route('cart.update') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: itemId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        loadItemPrice(itemRow);
                        updateCartCount();
                    }
                },
                error: function() {
                    alert('Error updating cart item');
                }
            });
        }

        $(document).on('click', '.cart-item-remove', function() {
            const itemRow = $(this).closest('.cart-item-row');
            const itemId = itemRow.data('item-id');

            if (!confirm('Are you sure you want to remove this item from cart?')) {
                return;
            }

            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id: itemId
                },
                success: function(response) {
                    if (response.success) {
                        itemRow.fadeOut(300, function() {
                            $(this).remove();
                            updateCartTotals();
                            updateCartCount();
                            
                            if ($('.cart-item-row').length === 0) {
                                $('#cartItemsContainer').html('<div class="empty-cart-message text-center py-5"><p class="p-18">Your cart is empty</p></div>');
                            }
                        });
                    }
                },
                error: function() {
                    alert('Error removing item from cart');
                }
            });
        });

        function updateCartCount() {
            $.ajax({
                url: '{{ route('cart.count') }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const count = response.count || 0;
                        $('#cartItemsCountText').text('(' + count + ' items)');
                    }
                }
            });
        }

        updateCartTotals();

        $('#country_id').select2({
            allowClear: true,
            theme: 'classic',
            placeholder: 'Select country',
            width: '100%'
        });

        $('#state_id').select2({
        allowClear: true,
        placeholder: 'Select state',
        theme: 'classic',
        width: '100%',
        ajax: {
            url: "{{ route('state-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchQuery: params.term,
                    page: params.page || 1,
                    country_id: $('#country_id').val(),
                    _token: "{{ csrf_token() }}"
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

        $('.payment-method').on('change', function() {
            if ($(this).val() === 'credit_debit_card') {
                $('#cardDetails').show();
                $('#card_number, #expiry_date, #cvv, #cardholder_name').prop('required', true);
            } else {
                $('#cardDetails').hide();
                $('#card_number, #expiry_date, #cvv, #cardholder_name').prop('required', false);
            }
        });

        $('#card_number').on('input', function() {
            let value = $(this).val().replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            $(this).val(formattedValue);
        });

        $('#cvv').on('input', function() {
            $(this).val($(this).val().replace(/\D/g, ''));
        });

        function updateCheckoutTotals() {
            const subtotal = parseFloat($('#cartSubtotalText').text().replace('$', '').replace(',', '')) || 0;
            const shipping = 0;
            const tax = (subtotal * 0.1);
            const total = subtotal + shipping + tax;

            $('#checkoutSubtotal').text('$' + subtotal.toFixed(2));
            $('#checkoutShipping').text('$' + shipping.toFixed(2));
            $('#checkoutTax').text('$' + tax.toFixed(2));
            $('#checkoutTotal').text('$' + total.toFixed(2));
        }

        updateCheckoutTotals();
        setInterval(updateCheckoutTotals, 1000);

        function validateForm() {
            let isValid = true;
            const errors = [];
            $('#checkoutErrors').addClass('d-none').html('');

            const requiredFields = {
                'first_name': 'First Name',
                'last_name': 'Last Name',
                'email': 'Email',
                'phone': 'Phone Number',
                'address_line_1': 'Street Address',
                'city': 'City',
                'zipcode': 'Postal Code',
                'country_id': 'Country',
                'state_id': 'State'
            };

            $.each(requiredFields, function(field, label) {
                const value = $('#' + field).val();
                if (!value || value.trim() === '') {
                    errors.push(label + ' is required');
                    isValid = false;
                    $('#' + field).addClass('is-invalid');
                } else {
                    $('#' + field).removeClass('is-invalid');
                }
            });

            const email = $('#email').val();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.push('Please enter a valid email address');
                isValid = false;
                $('#email').addClass('is-invalid');
            }

            const paymentMethod = $('input[name="payment_method"]:checked').val();
            if (paymentMethod === 'credit_debit_card') {
                const cardFields = {
                    'card_number': 'Card Number',
                    'expiry_date': 'Expiration Date',
                    'cvv': 'CVV',
                    'cardholder_name': 'Cardholder Name'
                };

                $.each(cardFields, function(field, label) {
                    const value = $('#' + field).val();
                    if (!value || value.trim() === '') {
                        errors.push(label + ' is required');
                        isValid = false;
                        $('#' + field).addClass('is-invalid');
                    } else {
                        $('#' + field).removeClass('is-invalid');
                    }
                });

                const cardNumber = $('#card_number').val().replace(/\s/g, '');
                if (cardNumber && (cardNumber.length < 13 || cardNumber.length > 19)) {
                    errors.push('Card number must be between 13 and 19 digits');
                    isValid = false;
                    $('#card_number').addClass('is-invalid');
                }

                const cvv = $('#cvv').val();
                if (cvv && (cvv.length < 3 || cvv.length > 4)) {
                    errors.push('CVV must be 3 or 4 digits');
                    isValid = false;
                    $('#cvv').addClass('is-invalid');
                }
            }

            if (errors.length > 0) {
                $('#checkoutErrors').removeClass('d-none').html('<ul class="mb-0"><li>' + errors.join('</li><li>') + '</li></ul>');
                $('html, body').animate({
                    scrollTop: $('#checkoutErrors').offset().top - 100
                }, 500);
            }

            return isValid;
        }

        $('#checkoutForm input, #checkoutForm select').on('blur', function() {
            $(this).removeClass('is-invalid');
        });

        $('#checkoutForm').on('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return false;
            }

            const btn = $('#placeOrderBtn');
            const btnText = btn.find('.btn-text');
            const btnLoading = btn.find('.btn-loading');

            btn.prop('disabled', true);
            btnText.addClass('d-none');
            btnLoading.removeClass('d-none');
            $('#checkoutErrors').addClass('d-none');

            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success && response.order_id) {
                        window.location.href = response.redirect_url;
                    } else {
                        $('#checkoutErrors').removeClass('d-none').html('<ul class="mb-0"><li>' + (response.message || 'Error placing order') + '</li></ul>');
                        btn.prop('disabled', false);
                        btnText.removeClass('d-none');
                        btnLoading.addClass('d-none');
                    }
                },
                error: function(xhr) {
                    let errors = [];
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(field, messages) {
                            errors = errors.concat(messages);
                            $('#' + field).addClass('is-invalid');
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errors.push(xhr.responseJSON.message);
                    } else {
                        errors.push('Error placing order. Please try again.');
                    }
                    
                    $('#checkoutErrors').removeClass('d-none').html('<ul class="mb-0"><li>' + errors.join('</li><li>') + '</li></ul>');
                    $('html, body').animate({
                        scrollTop: $('#checkoutErrors').offset().top - 100
                    }, 500);
                    
                    btn.prop('disabled', false);
                    btnText.removeClass('d-none');
                    btnLoading.addClass('d-none');
                }
            });
        });

        updateCartCount();
    });
</script>
@endpush