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
                                @if(isset($loggedIn) && $loggedIn && isset($cartItems) && $cartItems->count())
                                    @foreach($cartItems as $item)
                                        @php
                                            $product = $item->product;
                                            $variant = $item->productVariant;
                                            $quantity = (int) $item->quantity;
                                        @endphp
                                        <div class="c-lft-box bdr-clr cart-item-row"
                                             data-item-id="{{ $item->id }}"
                                             data-product-short-url="{{ $product?->short_url }}"
                                             data-variant-short-url="{{ $variant?->short_url }}"
                                             data-unit-id="{{ $item->unit_id }}"
                                             data-unit-type="{{ $item->unit_type }}">
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
                                                            <p class="h-24 cart-item-price" data-unit-price="{{ $item->calculated_price ?? 0 }}">
                                                                ${{ number_format(($item->calculated_total ?? 0), 2) }}
                                                            </p>
                                                            <p class="p-18">Total</p>
                                                        </div>
                                                        <button type="button" class="cart-delete cart-item-remove btn p-0 border-0 bg-transparent">
                                                            <img src="{{ asset('front-theme/images/cart-delete.png') }}" alt="">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- For guests and empty carts, items are injected via JS / localStorage -->
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
    (function () {
        const loggedIn = @json($loggedIn ?? false);
        const CART_STORAGE_KEY = 'anjo_cart_items';

        function readGuestCart() {
            try {
                const raw = localStorage.getItem(CART_STORAGE_KEY);
                if (!raw) return [];
                const parsed = JSON.parse(raw);
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function writeGuestCart(items) {
            try {
                localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items || []));
            } catch (e) {
                // ignore storage errors
            }
        }

        function formatMoney(value) {
            const num = Number(value) || 0;
            return '$' + num.toFixed(2);
        }

        function recalcSummaryFromDom() {
            let totalQty = 0;
            let subtotal = 0;

            document.querySelectorAll('.cart-item-row').forEach(row => {
                const qtyInput = row.querySelector('.cart-qty-input');
                const qty = parseInt(qtyInput?.value || '0', 10) || 0;
                totalQty += qty;

                const priceEl = row.querySelector('.cart-item-price');
                const price = Number(priceEl?.dataset?.unitPrice || 0);
                subtotal += price * qty;
            });

            const itemsCountEl = document.getElementById('cartItemsCountText');
            if (itemsCountEl) {
                itemsCountEl.textContent = `(${totalQty} item${totalQty === 1 ? '' : 's'})`;
            }

            const shipping = 0;
            const tax = 0;
            const total = subtotal + shipping + tax;

            const subtotalEl = document.getElementById('cartSubtotalText');
            const shippingEl = document.getElementById('cartShippingText');
            const taxEl = document.getElementById('cartTaxText');
            const totalEl = document.getElementById('cartTotalText');

            if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
            if (shippingEl) shippingEl.textContent = formatMoney(shipping);
            if (taxEl) taxEl.textContent = formatMoney(tax);
            if (totalEl) totalEl.textContent = formatMoney(total);
        }

        function buildGuestCartRow(item) {
            const container = document.createElement('div');
            container.className = 'c-lft-box bdr-clr cart-item-row';
            container.dataset.productShortUrl = item.product_short_url || '';
            container.dataset.variantShortUrl = item.product_variant_short_url || '';
            container.dataset.unitId = item.unit_id || '';
            container.dataset.unitType = item.unit_type || '';

            const quantity = Number(item.quantity) || 1;
            const unitPrice = Number(item.price) || 0;
            const totalPrice = unitPrice * quantity;

            container.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-lg-12 col-xl-12 col-xxl-8 col-md-6">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="crt-img">
                                <img src="${item.image || '{{ asset('front-theme/images/cart-1.png') }}'}" class="w-100" alt="">
                            </div>
                            <div class="cart-details">
                                <h3 class="h-24 mb-2">${item.name || 'Product'}</h3>
                                ${item.sku ? `<p class="p-18 mb-2">SKU: ${item.sku}</p>` : ''}
                                ${item.variant_name ? `<p class="p-18 mb-2">Variant: ${item.variant_name}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-12 col-xxl-4 col-md-6">
                        <div class="cart-all-dtl">
                            <div class="col-auto">
                                <div class="input-group quantity-group">
                                    <button class="btn btn-outline-secondary btn-minus cart-qty-minus" type="button">−</button>
                                    <input type="text" class="form-control text-center cart-qty-input" value="${quantity}" readonly="">
                                    <button class="btn btn-outline-secondary btn-plus cart-qty-plus" type="button">+</button>
                                </div>
                            </div>
                            <div class="cart-pra">
                                <p class="h-24 cart-item-price" data-unit-price="${unitPrice}">
                                    ${formatMoney(totalPrice)}
                                </p>
                                <p class="p-18">Total</p>
                            </div>
                            <button type="button" class="cart-delete cart-item-remove btn p-0 border-0 bg-transparent">
                                <img src="{{ asset('front-theme/images/cart-delete.png') }}" alt="">
                            </button>
                        </div>
                    </div>
                </div>
            `;

            return container;
        }

        async function fetchCartProductDetails(items) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('{{ route("cart.product.details") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({items: items})
                });
                const data = await response.json();
                if (data.success && data.items) {
                    return data.items;
                }
            } catch (e) {
                console.error('Failed to fetch product details:', e);
            }
            return [];
        }

        async function renderGuestCart() {
            const container = document.getElementById('cartItemsContainer');
            if (!container) return;

            const items = readGuestCart();
            container.innerHTML = '';

            if (items.length === 0) {
                container.innerHTML = '<div class="text-center py-5"><p class="p-18">Your cart is empty</p></div>';
                recalcSummaryFromDom();
                return;
            }

            // Fetch product details
            const productDetails = await fetchCartProductDetails(items);
            const detailsMap = {};
            productDetails.forEach(detail => {
                const key = `${detail.product_short_url}_${detail.product_variant_short_url || ''}_${detail.unit_id || ''}`;
                detailsMap[key] = detail;
            });

            // Render items with product details
            items.forEach(item => {
                const key = `${item.product_short_url}_${item.product_variant_short_url || ''}_${item.unit_id || ''}`;
                const detail = detailsMap[key] || {};
                
                const itemWithDetails = {
                    ...item,
                    name: detail.name || item.name || 'Product',
                    sku: detail.sku || item.sku || '',
                    variant_name: detail.variant_name || '',
                    image: detail.image || item.image || '{{ asset('front-theme/images/cart-1.png') }}',
                    price: detail.price || item.price || 0,
                };
                
                const row = buildGuestCartRow(itemWithDetails);
                container.appendChild(row);
            });

            attachRowHandlers();
            recalcSummaryFromDom();
        }

        function attachRowHandlers() {
            document.querySelectorAll('.cart-item-row').forEach(row => {
                const minusBtn = row.querySelector('.cart-qty-minus');
                const plusBtn = row.querySelector('.cart-qty-plus');
                const qtyInput = row.querySelector('.cart-qty-input');
                const removeBtn = row.querySelector('.cart-item-remove');

                if (minusBtn && qtyInput) {
                    minusBtn.addEventListener('click', () => handleQuantityChange(row, qtyInput, -1));
                }

                if (plusBtn && qtyInput) {
                    plusBtn.addEventListener('click', () => handleQuantityChange(row, qtyInput, 1));
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', () => handleRemoveRow(row));
                }
            });
        }

        function handleQuantityChange(row, qtyInput, delta) {
            const current = parseInt(qtyInput.value || '1', 10) || 1;
            let next = current + delta;
            if (next < 1) {
                next = 1;
            }

            qtyInput.value = String(next);

            const priceEl = row.querySelector('.cart-item-price');
            if (priceEl) {
                const unitPrice = Number(priceEl.dataset.unitPrice || 0);
                priceEl.textContent = formatMoney(unitPrice * next);
            }

            if (loggedIn) {
                const itemId = row.dataset.itemId;
                if (itemId) {
                    updateServerCartItem(itemId, next, function() {
                        // Reload page to get updated pricing
                        window.location.reload();
                    });
                }
            } else {
                updateGuestCartItemFromRow(row, next);
                // Recalculate price for guest cart
                const productShortUrl = row.dataset.productShortUrl;
                const variantShortUrl = row.dataset.variantShortUrl || '';
                const unitId = row.dataset.unitId || '';
                const unitType = row.dataset.unitType || '';
                
                fetchProductDetails(productShortUrl, variantShortUrl, unitId, unitType).then(price => {
                    if (price > 0) {
                        const priceEl = row.querySelector('.cart-item-price');
                        if (priceEl) {
                            priceEl.dataset.unitPrice = price;
                            priceEl.textContent = formatMoney(price * next);
                        }
                        recalcSummaryFromDom();
                    }
                });
            }

            recalcSummaryFromDom();
        }

        function handleRemoveRow(row) {
            if (loggedIn) {
                const itemId = row.dataset.itemId;
                if (itemId) {
                    removeServerCartItem(itemId, () => {
                        row.remove();
                        recalcSummaryFromDom();
                    });
                    return;
                }
            } else {
                removeGuestCartItemFromRow(row);
                row.remove();
                recalcSummaryFromDom();
            }
        }

        function updateGuestCartItemFromRow(row, quantity) {
            const pShort = row.dataset.productShortUrl || '';
            const vShort = row.dataset.variantShortUrl || '';
            const unitId = row.dataset.unitId || '';
            const items = readGuestCart();

            const updated = items.map(item => {
                if (
                    (item.product_short_url || '') === pShort &&
                    (item.product_variant_short_url || '') === vShort &&
                    String(item.unit_id || '') === String(unitId)
                ) {
                    return Object.assign({}, item, {quantity});
                }
                return item;
            });

            writeGuestCart(updated);
            recalcSummaryFromDom();
        }

        function removeGuestCartItemFromRow(row) {
            const pShort = row.dataset.productShortUrl || '';
            const vShort = row.dataset.variantShortUrl || '';
            const unitId = row.dataset.unitId || '';
            const items = readGuestCart().filter(item => {
                return !(
                    (item.product_short_url || '') === pShort &&
                    (item.product_variant_short_url || '') === vShort &&
                    String(item.unit_id || '') === String(unitId)
                );
            });

            writeGuestCart(items);
            recalcSummaryFromDom();
        }

        function updateServerCartItem(itemId, quantity, onSuccess) {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            fetch(`{{ url('/cart/item') }}/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({quantity}),
            })
            .then(resp => {
                if (resp.ok && typeof onSuccess === 'function') {
                    onSuccess();
                }
            })
            .catch(() => {
                // ignore errors; UI is already updated
            });
        }

        function removeServerCartItem(itemId, onSuccess) {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            fetch(`{{ url('/cart/item') }}/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token || '',
                    'Accept': 'application/json',
                },
            })
                .then(resp => resp.ok ? resp.json() : null)
                .then(() => {
                    if (typeof onSuccess === 'function') {
                        onSuccess();
                    }
                })
                .catch(() => {
                    if (typeof onSuccess === 'function') {
                        onSuccess();
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            if (!loggedIn) {
                await renderGuestCart();
            } else {
                // Sync localStorage cart on login
                const localCart = readGuestCart();
                if (localCart.length > 0) {
                    try {
                        const response = await fetch('{{ route("cart.merge") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({items: localCart})
                        });
                        if (response.ok) {
                            writeGuestCart([]);
                            window.location.reload();
                            return;
                        }
                    } catch (e) {
                        console.error('Failed to sync cart:', e);
                    }
                }
                // Logged-in cart is already rendered from server; just bind handlers
                attachRowHandlers();
                recalcSummaryFromDom();
            }
        });
    })();
</script>
@endpush