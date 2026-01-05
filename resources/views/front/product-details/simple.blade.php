@extends('front.layout', [
    'metaInfo' => [
        'title' => $product?->seo_title,
        'content' => $product?->seo_description,
        'url' => route('product.index', ['product_slug' => $product?->slug, 'short_url' => $product?->short_url]),
        'keywords' => implode(', ', ($product?->tags) ?? [])
    ]
])

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<style>
    .mainSlider .swiper-slide img {
        height: 500px!important;
        width: 826px!important;
        object-fit: contain!important;
    }

    .thumbSlider .swiper-slide img {
        height: 149px!important;
        width: 149px!important;
        object-fit: contain!important;
    }

    .pill-btn {
        padding: 6px 14px;
        border: 2px dashed #bbb;
        border-radius: 13px;
        background-color: #fff;
        color: #333;
        font-size: 14px;
        cursor: pointer;
    }

    .pill-btn.active {
        border-color: #000;
        font-weight: 600;
        border: 2px solid;
        background-color: #203a7217;
    }

    /* Pricing Table Styles */
    .price-table {
        border-radius: 8px;
        overflow: hidden;
    }
    .price-table thead tr {
        background-color: #203a72;
        color: #fff;
    }
    .price-table tbody tr.highlight-row {
        background-color: #d4edda !important;
        border-left: 3px solid #28a745;
    }
    .price-table tbody tr {
        transition: background-color 0.2s;
    }

    /* Quantity Selector */
    .quantity-group {
        width: 130px;
    }
    .quantity-group .form-control {
        background: #fff;
    }
    .quantity-group .btn {
        padding: 0.5rem 0.75rem;
    }

    /* Stock Badge */
    .bulk-pr.btn {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        font-weight: 500;
    }

    /* Unit Selector */
    #unitSelector {
        min-width: 120px;
    }
</style>
@endpush

@section('content')

<!-- beadcrum Section Start -->
<section>
    <div class="bred-pro">
        <div class="container">
            <div class="breadcrumb-container">
                <ol class="breadcrumb">
                    <li><a href="{{ route('home')  }}">Home</a></li>
                    @forelse($categoryHierarchy as $categoryLevel)
                        @if(!isset($categoryLevel['display']))
                            <li><a href="{{ route('category.index', ['category_slug' => $categoryLevel['slug'], 'short_url' => $categoryLevel['short_url']]) }}">{{ $categoryLevel['name'] }}</a></li>
                        @else
                            <li><a>{{ $categoryLevel['name'] }}</a></li>
                        @endif
                    @empty
                    @endforelse
                    <li><a href="#" class="text-truncate">{{ $product->name }}</a></li>
                </ol>
            </div>
        </div>
    </div>
</section>
<!-- beadcrum Section End -->
<!-- MAin-section Content Start -->

<section class="pro-dt-hero"> 
    <div class="pro-detail-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="pro-dtl-slider">
                        <div class="product-gallery">
                            <!-- Main Image Slider -->
                            <div class="swiper mainSlider">
                                <div class="swiper-wrapper">
                                    @if(isset($product->primaryImage->id) && is_file(public_path('storage/' . $product->primaryImage->file)))
                                        <div class="swiper-slide">
                                            <img src="{{ asset('storage/' . $product->primaryImage->file) }}" alt="{{ $product->name }}" />
                                        </div>
                                    @endif
                                    @foreach ($product->secondaryImages as $row)
                                        @if(isset($row->id) && is_file(public_path('storage/' . $row->file)))
                                            <div class="swiper-slide">
                                                <img src="{{ asset('storage/' . $row->file) }}" alt="{{ $product->name }}" />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Navigation -->
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                            </div>

                            <!-- Thumbnail Slider -->
                            <div class="swiper thumbSlider mt-3">
                                    <div class="swiper-wrapper">

                                    @if(isset($product->primaryImage->id) && is_file(public_path('storage/' . $product->primaryImage->file)))
                                        <div class="swiper-slide">
                                            <img src="{{ asset('storage/' . $product->primaryImage->file) }}" alt="{{ $product->name }}" />
                                        </div>
                                    @endif
                                    @foreach ($product->secondaryImages as $row)
                                        @if(isset($row->id) && is_file(public_path('storage/' . $row->file)))
                                            <div class="swiper-slide">
                                                <img src="{{ asset('storage/' . $row->file) }}" alt="{{ $product->name }}" />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="detail-right">
                        <h2 class="h-40 mb-2">{{ $product->name }}</h2>
                        <div class="bult-div">
                            <!-- Stock Badge -->
                            @if($totalStock > 0)
                            <div class="pt-4 pb-4">
                                <a href="javascript:void(0)" class="bulk-pr btn">In Stock: {{ number_format($totalStock) }} units</a>
                            </div>
                            @else
                            <div class="pt-4 pb-4">
                                <a href="javascript:void(0)" class="bulk-pr btn" style="background-color: #dc3545;">Out of Stock</a>
                            </div>
                            @endif

                            <!-- Bulk Pricing Table -->
                            @if($units->count() > 0)
                            <h3 class="h-24 mb-4">Bulk Pricing</h3>
                            <div class="table-responsive">
                                <table class="table price-table text-center table-striped align-middle" id="pricingTable">
                                    <thead>
                                    <tr>
                                        <th>Quantity</th>
                                        <th>MRP</th>
                                        <th>Your Price</th>
                                        <th>You Save</th>
                                    </tr>
                                    </thead>
                                    <tbody id="pricingTableBody">
                                    @forelse($tierPricings as $tier)
                                    <tr data-unit-id="{{ $tier['product_additional_unit_id'] }}" 
                                        data-unit-type="{{ $tier['unit_type'] }}"
                                        data-min="{{ $tier['min_qty'] }}" 
                                        data-max="{{ $tier['max_qty'] }}"
                                        data-price="{{ $tier['your_price'] }}">
                                        <td>
                                            @if($tier['max_qty'] > 0)
                                                {{ number_format($tier['min_qty']) }}–{{ number_format($tier['max_qty']) }}
                                            @else
                                                {{ number_format($tier['min_qty']) }}+
                                            @endif
                                        </td>
                                        <td>${{ number_format($tier['mrp'], 2) }}</td>
                                        <td>${{ number_format($tier['your_price'], 2) }}</td>
                                        <td>
                                            @if($tier['discount_amount'] > 0)
                                                ${{ number_format($tier['discount_amount'], 2) }}
                                                @if($tier['discount_type'] == 1)
                                                    ({{ number_format($tier['discount_value']) }}%)
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4">No pricing tiers available</td>
                                    </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <!-- Quantity and Unit Selector -->
                            <div class="row g-3 align-items-center mt-4">
                                <div class="">
                                    <label for="quantity" class="form-label mb-1 d-block">Quantity</label>
                                    <div class="input-group quantity-group">
                                        <button class="btn btn-outline-secondary btn-minus" type="button">−</button>
                                        <input type="text" class="form-control text-center" value="1" id="quantity" readonly>
                                        <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                    </div>
                                </div>
                                @if($units->count() > 0)
                                <div class="col-auto">
                                    <label for="unit" class="form-label mb-1 d-block">Unit</label>
                                    <select class="form-select" id="unitSelector">
                                        @foreach($units as $unit)
                                        <option value="{{ $unit['id'] }}" 
                                                data-unit-type="{{ $unit['unit_type'] }}"
                                                data-unit-id="{{ $unit['unit_id'] }}"
                                                @if($unit['is_default']) selected @endif>
                                            {{ $unit['title'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>

                            <!-- Add to Cart Section -->
                            <div class="produc-crt-section">
                                <!-- Default Add to Cart Button -->
                                <button type="button" class="btn cart-btn d-block" id="addToCartBtnNew" @if($totalStock <= 0) disabled @endif>
                                    @if($totalStock > 0) Add to Cart @else Out of Stock @endif
                                </button>

                                <!-- Quantity Controls (Hidden by default) -->
                                <div id="cartControls" class="d-none w-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 5px;">
                                        <button class="btn btn-sm btn-outline-secondary cart-qty-minus" type="button" style="width: 30px;">−</button>
                                        <input type="text" class="form-control text-center border-0 bg-transparent p-0 cart-qty-input" value="1" readonly style="width: 50px; height: auto;">
                                        <button class="btn btn-sm btn-outline-secondary cart-qty-plus" type="button" style="width: 30px;">+</button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger w-100" id="removeFromCartBtn">
                                        Remove from Cart
                                    </button>
                                </div>

                                <a class="cart-like cursor-pointer" id="addToWishlist" data-product="{{ json_encode([
                                        'type' => 'simple',
                                        'variant' => null
                                    ]) }}">
                                    <img src="{{ asset('front-theme/images/not-added-to-wishlist.png') }}" style="height: 25px;position:relative;bottom:3px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- MAin-section Content Start -->
   
<!-- Description Content Start -->
<section class="Description">
    <div class="Description__block">
        <div class="Description__box">
            <h2 class="h-30">Product Description</h2>
            <div class="Description__text">
                <div class="top-des ">
                    {!! $product->long_description !!}
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Description Content End -->


<!-- Custonmer Content Start -->
<section class="recent-ml customer-product bg-12">
    <div class="recent-block pd-y50">
        <div class="container">
            <h2 class="h-30">
                Customers Also Bought
            </h2>
            <div class="row">
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p1.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">True Adult Dog Food Dried Pebbles</h3>
                            <p class="pr-bold mb-3">$25.99</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p2.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">True Salmon Oil</h3>
                            <p class="pr-bold mb-3">$89.99</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p3.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">Barcelo Imperial Mizunara Cask</h3>
                            <p class="pr-bold mb-3">$322.80</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p4.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">Real Ginger Infused Syrup</h3>
                            <p class="pr-bold mb-3">$15.20</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Custonmer Content End -->

<!-- Recent Content Start -->
<section class="recent-ml">
    <div class="recent-block pd-y50">
        <div class="container">
            <h2 class="h-30">
                Recently Viewed
                <a href="" class="view-black">
                    View All
                    <img src="{{ asset('front-theme/images/view-blue-arrow.svg') }}" alt="">
                </a>
            </h2>
            <div class="row">
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p1.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">True Adult Dog Food Dried Pebbles</h3>
                            <p class="pr-bold mb-3">$25.99</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p2.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">True Salmon Oil</h3>
                            <p class="pr-bold mb-3">$89.99</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p3.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">Barcelo Imperial Mizunara Cask</h3>
                            <p class="pr-bold mb-3">$322.80</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                    <div class="recent-box">
                        <img class="w-100" src="{{ asset('front-theme/images/recent-p4.png') }}" alt="">
                        <div class="rc-bx-in">
                            <h3 class="h-20 mb-3">Real Ginger Infused Syrup</h3>
                            <p class="pr-bold mb-3">$15.20</p>
                            <a href="" class="btn cart-btn d-block">Add to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    $(document).ready(function () {
            var thumbSlider = new Swiper(".thumbSlider", {
            spaceBetween: 10,
            slidesPerView: 5,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
            
            320: { slidesPerView: 3, spaceBetween: 20 },
            576: { slidesPerView: 4, spaceBetween: 20 },
            992: { slidesPerView: 5, spaceBetween: 20 },
            },
        });

        // Main Image Slider
        var mainSlider = new Swiper(".mainSlider", {
            spaceBetween: 10,
            navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
            },
            thumbs: {
            swiper: thumbSlider,
            },
        });

        // Quantity +/- buttons (Product Page)
        document.querySelector('.btn-plus').addEventListener('click', function() {
            let input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
            highlightApplicableTier();
        });

        document.querySelector('.btn-minus').addEventListener('click', function() {
            let input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                highlightApplicableTier();
            }
        });

        // Unit selector change
        $('#unitSelector').on('change', function() {
            highlightApplicableTier();
            checkCartStatus(); // Check if this new unit is in cart
        });

        // Highlight applicable tier in pricing table
        function highlightApplicableTier() {
            let qty = parseInt($('#quantity').val()) || 1;
            let selectedOption = $('#unitSelector option:selected');
            
            if (selectedOption.length === 0) {
                // No units, maybe no pricing table or default behavior
                return;
            }

            let unitId = selectedOption.val();
            let unitType = selectedOption.data('unit-type');

            // Remove existing highlights
            $('#pricingTableBody tr').removeClass('highlight-row table-primary');

            // Find matching tier
            $('#pricingTableBody tr').each(function() {
                let row = $(this);
                let rowUnitId = row.data('unit-id');
                let rowUnitType = row.data('unit-type');
                let minQty = parseFloat(row.data('min'));
                let maxQty = parseFloat(row.data('max'));

                // Check if unit matches and quantity is within range
                if (rowUnitId == unitId && rowUnitType == unitType) {
                    if (qty >= minQty && (maxQty === 0 || qty <= maxQty)) {
                        row.addClass('highlight-row table-primary');
                    }
                }
            });
        }

        // Initial highlight
        highlightApplicableTier();

        // Cart key for current item
        function currentCartItemKey() {
            let selectedOption = $('#unitSelector option:selected');
            let unitType = null;
            let unitId = null;

            if (selectedOption.length > 0) {
                unitType = selectedOption.data('unit-type');
                unitId = selectedOption.val();
            }

            return {
                product_short_url: '{{ $product->short_url }}',
                product_variant_short_url: null,
                unit_type: unitType,
                unit_id: unitId
            };
        }

        // Local cart functions for guests
        function getLocalCart() {
            return JSON.parse(localStorage.getItem('cart')) || [];
        }

        function saveLocalCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        function addToLocalCart(qty) {
            let key = currentCartItemKey();
            let cart = getLocalCart();
            
            let existingIndex = cart.findIndex(function(x) {
                return x.product_short_url === key.product_short_url && 
                       x.product_variant_short_url === key.product_variant_short_url &&
                       x.unit_id == key.unit_id;
            });

            if (existingIndex >= 0) {
                cart[existingIndex].quantity += qty;
            } else {
                cart.push({
                    product_short_url: key.product_short_url,
                    product_variant_short_url: key.product_variant_short_url,
                    unit_type: key.unit_type,
                    unit_id: key.unit_id,
                    quantity: qty
                });
            }
            
            saveLocalCart(cart);
            return true;
        }

        function updateLocalCartQuantity(newQty) {
            let key = currentCartItemKey();
            let cart = getLocalCart();
            
            let existingIndex = cart.findIndex(function(x) {
                return x.product_short_url === key.product_short_url && 
                       x.product_variant_short_url === key.product_variant_short_url &&
                       x.unit_id == key.unit_id;
            });

            if (existingIndex >= 0) {
                cart[existingIndex].quantity = newQty;
                saveLocalCart(cart);
            }
        }

        function removeFromLocalCart() {
            let key = currentCartItemKey();
            let cart = getLocalCart();
            
            cart = cart.filter(function(x) {
                return !(x.product_short_url === key.product_short_url && 
                         x.product_variant_short_url === key.product_variant_short_url &&
                         x.unit_id == key.unit_id);
            });
            
            saveLocalCart(cart);
        }

        // Check if item is in cart and update UI
        function checkCartStatus() {
            let key = currentCartItemKey();
            
            // Check login status first
            $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                        // Check server cart
                        let item = (resp.items || []).find(function(x) {
                            return x.product_short_url === key.product_short_url && 
                                   x.product_variant_short_url === key.product_variant_short_url &&
                                   x.unit_id == key.unit_id;
                        });

                        if (item) {
                            showCartControls(item.quantity);
                        } else {
                            showAddButton();
                        }
                        updateCartCount(resp.items ? resp.items.length : 0); 
                    } else {
                        // Check local cart
                        let cart = getLocalCart();
                        let item = cart.find(function(x) {
                            return x.product_short_url === key.product_short_url && 
                                   x.product_variant_short_url === key.product_variant_short_url &&
                                   x.unit_id == key.unit_id;
                        });

                        if (item) {
                            showCartControls(item.quantity);
                        } else {
                            showAddButton();
                        }
                        updateCartCount();
                    }
                }
            });
        }

        function showCartControls(qty) {
            $('#addToCartBtnNew').addClass('d-none');
            $('#cartControls').removeClass('d-none');
            $('.cart-qty-input').val(qty);
        }

        function showAddButton() {
            $('#addToCartBtnNew').removeClass('d-none').text('Add to Cart').prop('disabled', false);
            $('#cartControls').addClass('d-none');
        }

        function updateCartCount(count) {
            if (count === undefined) {
                let cart = getLocalCart();
                count = cart.length; 
            }
            // Update header badge
            $('.nav-item.nav-link.cart span').first().text(count);
        }

        // Add to Cart button click
        $(document).on('click', '#addToCartBtnNew', function() {
            let btn = $(this);
            let originalText = btn.text();
            
            btn.prop('disabled', true).text('Added!');

            let selectedOption = $('#unitSelector option:selected');
            let qty = parseInt($('#quantity').val()) || 1;
            let unitType = null;
            let unitId = null;

            if (selectedOption.length > 0) {
                unitType = selectedOption.data('unit-type');
                unitId = parseInt(selectedOption.val());
            }

            // Check login status first
            $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                        // Logged in - use server-side cart
                        $.ajax({
                            url: '{{ route("cart.add") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                product_short_url: '{{ $product->short_url }}',
                                product_variant_short_url: null,
                                unit_type: unitType,
                                unit_id: unitId,
                                quantity: qty
                            },
                            success: function(r) {
                                if (r?.success) {
                                    setTimeout(function() {
                                        showCartControls(qty); // Switch to controls
                                        checkCartStatus(); // Refresh to ensure sync
                                    }, 1000);
                                } else {
                                    alert(r?.message || 'Failed to add to cart');
                                    btn.prop('disabled', false).text(originalText);
                                }
                            },
                            error: function(xhr) {
                                // Fallback or error
                                alert('Error adding to cart');
                                btn.prop('disabled', false).text(originalText);
                            }
                        });
                    } else {
                        // Guest - use local storage
                        addToLocalCart(qty);
                        setTimeout(function() {
                            showCartControls(qty);
                        }, 1000);
                    }
                }
            });
        });

        // Cart Controls: Plus
        $(document).on('click', '.cart-qty-plus', function() {
            let input = $(this).siblings('.cart-qty-input');
            let newQty = parseInt(input.val()) + 1;
            input.val(newQty);
            
            // Debounce update
            updateCartQuantity(newQty);
        });

        // Cart Controls: Minus
        $(document).on('click', '.cart-qty-minus', function() {
            let input = $(this).siblings('.cart-qty-input');
            let newQty = parseInt(input.val()) - 1;
            
            if (newQty < 1) {
                newQty = 1;
                return; 
            }
            
            input.val(newQty);
            updateCartQuantity(newQty);
        });

        // Cart Controls: Remove
        $(document).on('click', '#removeFromCartBtn', function() {
            let selectedOption = $('#unitSelector option:selected');
            let unitType = null;
            let unitId = null;

            if (selectedOption.length > 0) {
                unitType = selectedOption.data('unit-type');
                unitId = parseInt(selectedOption.val());
            }

            // Check login status
             $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                         let item = (resp.items || []).find(function(x) {
                            return x.product_short_url === '{{ $product->short_url }}' && 
                                   x.product_variant_short_url === null &&
                                   x.unit_id == unitId;
                        });

                        if (item) {
                             $.ajax({
                                url: '{{ route("cart.item.remove", ":id") }}'.replace(':id', item.id),
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                success: function() {
                                    showAddButton();
                                    checkCartStatus();
                                }
                            });
                        }
                    } else {
                        removeFromLocalCart();
                        showAddButton();
                    }
                }
            });
        });

        function updateCartQuantity(qty) {
             let selectedOption = $('#unitSelector option:selected');
             let unitId = null;
             if (selectedOption.length > 0) {
                 unitId = parseInt(selectedOption.val());
             }

             $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                        let item = (resp.items || []).find(function(x) {
                            return x.product_short_url === '{{ $product->short_url }}' && 
                                   x.product_variant_short_url === null &&
                                   x.unit_id == unitId;
                        });

                        if (item) {
                            $.ajax({
                                url: '{{ route("cart.item.update", ":id") }}'.replace(':id', item.id),
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'PUT',
                                    quantity: qty
                                },
                                success: function() {
                                    checkCartStatus();
                                }
                            });
                        }
                    } else {
                        updateLocalCartQuantity(qty);
                    }
                }
            });
        }

        // Initial check
        checkCartStatus();

        // Wishlist Logic
        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
        
        function setWishlistIcon(inWishlist) {
            let imageElement = $('#addToWishlist').find('img');
            if (inWishlist) {
                imageElement.attr('src', '{{ asset("front-theme/images/added-to-wishlist.png") }}');
            } else {
                imageElement.attr('src', '{{ asset("front-theme/images/not-added-to-wishlist.png") }}');
            }
        }

        function currentItemKeyWishlist() {
            let info = $('#addToWishlist').data('product');
            return {
                product_short_url: '{{ $product->short_url }}',
                product_variant_short_url: info?.variant || null
            };
        }

        function localHasItemWishlist() {
            let key = currentItemKeyWishlist();
            return (JSON.parse(localStorage.getItem('wishlist')) || []).some(function (x) {
                return x.product_short_url === key.product_short_url && x.product_variant_short_url === key.product_variant_short_url;
            });
        }

        function localAddOrRemoveWishlist() {
            let key = currentItemKeyWishlist();
            let wl = JSON.parse(localStorage.getItem('wishlist')) || [];
            let exists = wl.some(function (x) { return x.product_short_url === key.product_short_url && x.product_variant_short_url === key.product_variant_short_url; });
            if (exists) {
                wl = wl.filter(function (x) { return !(x.product_short_url === key.product_short_url && x.product_variant_short_url === key.product_variant_short_url); });
                localStorage.setItem('wishlist', JSON.stringify(wl));
                setWishlistIcon(false);
                return false;
            } else {
                wl.push(key);
                localStorage.setItem('wishlist', JSON.stringify(wl));
                setWishlistIcon(true);
                return true;
            }
        }

        function mergeLocalToServerWishlist() {
            let wl = JSON.parse(localStorage.getItem('wishlist')) || [];
            if (!wl.length) { return; }
            $.ajax({
                url: '{{ route("wishlist.merge") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    items: wl
                },
                success: function() {
                    localStorage.removeItem('wishlist');
                }
            });
        }

        function initWishlistIconFromStatus() {
            $.ajax({
                url: '{{ route("wishlist.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (!resp?.success) { setWishlistIcon(localHasItemWishlist()); return; }
                    if (!resp.logged_in) {
                        setWishlistIcon(localHasItemWishlist());
                        return;
                    }
                    mergeLocalToServerWishlist();
                    let key = currentItemKeyWishlist();
                    let inList = (resp.wishlists || []).some(function (x) {
                        return x.product_short_url === key.product_short_url && x.product_variant_short_url === key.product_variant_short_url;
                    });
                    setWishlistIcon(inList);
                },
                error: function() {
                    setWishlistIcon(localHasItemWishlist());
                }
            });
        }

        initWishlistIconFromStatus();

        $(document).on('click', '#addToWishlist', function () {
            let info = $(this).data('product');
            if (!info || !info.type) { return; }

            $.ajax({
                url: '{{ route("wishlist.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                        let key = currentItemKeyWishlist();
                        $.ajax({
                            url: '{{ route("wishlist.toggle") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                product_short_url: key.product_short_url,
                                product_variant_short_url: key.product_variant_short_url
                            },
                            success: function(r) {
                                setWishlistIcon(!!r?.in_wishlist);
                            },
                            error: function(xhr) {
                                if (xhr && xhr.status === 401) {
                                    localAddOrRemoveWishlist();
                                }
                            }
                        });
                    } else {
                        localAddOrRemoveWishlist();
                    }
                },
                error: function() {
                    localAddOrRemoveWishlist();
                }
            });
        });
    }); 
</script>
@endpush