@extends('front.layout')

@push('css')

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
                        <form action="">
                            <div class="shopping-text bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-3">Shipping Information</h3>
                                <div class="row mb-3">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="exampleInputEmail1" class="form-label">First Name</label>
                                        <input type="text" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>
                                     <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="exampleInputEmail1" class="form-label">Last Name</label>
                                        <input type="text" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="lname">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="exampleInputEmail1" class="form-label">Company Name</label>
                                        <input type="text" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>                                    
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="exampleInputEmail1" class="form-label">Street Address</label>
                                        <input type="text" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>                                    
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="exampleInputEmail1" class="form-label">City</label>
                                        <input type="text" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label for="exampleInputEmail1" class="form-label">Postal Code</label>
                                        <input type="number" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="lname">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="exampleInputEmail1" class="form-label">Phone Number</label>
                                        <input type="number" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="shop-info bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-3">Shipping Information</h3>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                               <img src="images/credit.png" alt=""> &nbsp; Credit/Debit Card
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="exampleInputEmail1" class="form-label">Postal Code</label>
                                        <input type="number" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="lname">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-6">
                                        <label for="exampleInputEmail1" class="form-label">Expiration Date</label>
                                        <input type="date" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>
                                     <div class="col-lg-6">
                                        <label for="exampleInputEmail1" class="form-label">CVV</label>
                                        <input type="number" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="lname">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="exampleInputEmail1" class="form-label">Cardholder Name</label>
                                        <input type="text" class="form-control fm-inpt" id="exampleInputEmail1" aria-describedby="fname">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="order-summry bdr-clr crt-pading mt-30">
                                <h3 class="h-24 mb-3">Order Summary</h3>
                                <ul>
                                    <li>
                                        <span>Subtotal</span>
                                        <span>$434.94</span>
                                    </li>
                                    <li>
                                        <span>Bulk Discount</span>
                                        <span>-$43.49</span>
                                    </li>
                                    <li>
                                        <span>Shipping</span>
                                        <span>$25.00</span>
                                    </li>
                                    <li>
                                        <span>Tax</span>
                                        <span>$33.32</span>
                                    </li>
                                </ul>
                                <div class="crt-total d-flex justify-content-between gap-2">
                                    <p class="h-24">Total</p>
                                    <p class="h-24">$449.77</p>
                                </div>
                                <div class="crt-estimate">
                                    <img src="images/location.png" alt="">
                                    Estimated Delivery: <span> March 15-17 2025</span>
                                </div>
                                <div class="crt-place">
                                    <a href="" class="btn cart-btn d-block">Place Order</a>
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

        updateCartCount();
    });
</script>
@endpush