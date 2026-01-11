@extends('front.layout', [
    'metaInfo' => [
        'title' => $product?->seo_title,
        'content' => $product?->seo_description,
        'url' => route('product.index', ['product_slug' => $product?->slug, 'short_url' => $product?->short_url]),
        'keywords' => implode(', ', $product?->tags ?? []),
    ],
])

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        .mainSlider .swiper-slide img {
            height: 500px !important;
            width: 826px !important;
            object-fit: contain !important;
        }

        .thumbSlider .swiper-slide img {
            height: 149px !important;
            width: 149px !important;
            object-fit: contain !important;
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

        .unit-selector-btn {
            transition: all 0.3s ease;
        }

        .unit-selector-btn:hover {
            background-color: #f8f9fa;
        }

        .pricing-display-section {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            background-color: #fafafa;
        }

        .price-display {
            font-size: 24px;
            font-weight: 600;
            color: #203a72;
        }

        .price-display .mrp-price {
            text-decoration: line-through;
            color: #999;
            font-size: 18px;
            margin-right: 10px;
        }

        .price-display .final-price {
            color: #28a745;
        }

        .tier-pricing-table-container {
            margin-top: 20px;
        }

        .tier-pricing-table-container table {
            margin-bottom: 0;
        }

        .add-to-cart-section {
            transition: all 0.3s ease;
        }


        .cart-quantity-controls {
            display: none;
        }

        .cart-quantity-controls.show {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .cart-quantity-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .cart-quantity-display .quantity-info {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .cart-quantity-display .quantity-value {
            font-size: 18px;
            font-weight: 600;
            color: #203a72;
        }

        #removeFromCartBtn {
            white-space: nowrap;
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
                        <li><a href="{{ route('home') }}">Home</a></li>
                        @forelse($categoryHierarchy as $categoryLevel)
                            @if (!isset($categoryLevel['display']))
                                <li><a
                                        href="{{ route('category.index', ['category_slug' => $categoryLevel['slug'], 'short_url' => $categoryLevel['short_url']]) }}">{{ $categoryLevel['name'] }}</a>
                                </li>
                            @else
                                <li><a>{{ $categoryLevel['name'] }}</a></li>
                            @endif
                        @empty
                        @endforelse
                        <li><a href="#" class="text-truncate">{{ $variantModel->name }}</a></li>
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
                                        @if (isset($variantModel->variantImage->id) && is_file(public_path('storage/' . $variantModel->variantImage->file)))
                                            <div class="swiper-slide">
                                                <img src="{{ asset('storage/' . $variantModel->variantImage->file) }}"
                                                    alt="{{ $product->name }}" />
                                            </div>
                                        @endif
                                        @foreach ($variantModel->variantSecondaryImage as $row)
                                            @if (isset($row->id) && is_file(public_path('storage/' . $row->file)))
                                                <div class="swiper-slide">
                                                    <img src="{{ asset('storage/' . $row->file) }}"
                                                        alt="{{ $product->name }}" />
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

                                        @if (isset($variantModel->variantImage->id) && is_file(public_path('storage/' . $variantModel->variantImage->file)))
                                            <div class="swiper-slide">
                                                <img src="{{ asset('storage/' . $variantModel->variantImage->file) }}"
                                                    alt="{{ $variantModel->name }}" />
                                            </div>
                                        @endif
                                        @foreach ($variantModel->variantSecondaryImage as $row)
                                            @if (isset($row->id) && is_file(public_path('storage/' . $row->file)))
                                                <div class="swiper-slide">
                                                    <img src="{{ asset('storage/' . $row->file) }}"
                                                        alt="{{ $product->name }}" />
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
                            <h2 class="h-40 mb-2">{{ $variantModel->name }}</h2>

                            <!-- Variants -->
                            <div class="bult-div">
                                <div class="">
                                    @forelse ($attributes ?? [] as $attributeKey => $attributeValues)
                                        <p class="mt-2 mb-2">
                                            {{ $attributeKey }}
                                        <div class="d-flex gap-2">
                                            @foreach ($attributeValues as $attributeValue)
                                                <a data-attribute="{{ base64_encode($attributeValue['id']) }}"
                                                    class="pill-btn navigate-variant @if (in_array($attributeValue['id'], $existingAttributes)) active @endif">{{ $attributeValue['name'] }}</a>
                                            @endforeach
                                        </div>
                                        </p>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                            <!-- Variants -->

                            @if($units->isNotEmpty())
                            <div class="unit-selection-section mt-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Unit</label>
                                    <div class="d-flex flex-wrap gap-2" id="unitSelector">
                                        @foreach($units as $unit)
                                            <button type="button" 
                                                class="pill-btn unit-selector-btn @if($unit['is_default']) active @endif"
                                                data-unit-type="{{ $unit['unit_type'] }}"
                                                data-unit-id="{{ $unit['id'] }}"
                                                data-unit-title="{{ $unit['title'] }}"
                                                @if(isset($unit['quantity'])) data-unit-quantity="{{ $unit['quantity'] }}" @endif>
                                                {{ $unit['title'] }}
                                                @if(isset($unit['quantity']) && $unit['quantity'] > 1)
                                                    ({{ $unit['quantity'] }})
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="pricingDisplay" class="pricing-display-section">
                                    @php
                                        $defaultUnit = $units->firstWhere('is_default', true) ?? $units->first();
                                        $defaultUnitType = $defaultUnit['unit_type'] ?? null;
                                        $defaultUnitId = $defaultUnit['id'] ?? null;
                                    @endphp
                                    <div class="price-stock-info mb-3">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <span class="current-unit-title fw-bold">{{ $defaultUnit['title'] ?? 'Unit' }}</span>
                                            @if($totalStock > 0)
                                                <span class="badge bg-success">In Stock: {{ $totalStock }}</span>
                                            @else
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @endif
                                        </div>
                                        <div id="priceDisplay" class="price-display">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tierPricingTable" class="tier-pricing-table-container" style="display: none;">
                                        <h6 class="mb-2">Bulk Pricing</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered price-table">
                                                <thead>
                                                    <tr>
                                                        <th>Quantity</th>
                                                        <th>MRP</th>
                                                        <th>Your Price</th>
                                                        <th>Discount</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tierPricingBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="add-to-cart-section mt-4">
                                        <div class="cart-quantity-display" id="cartQuantityDisplay" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="quantity-info">Quantity in cart:</div>
                                                    <div class="quantity-value" id="cartQuantityValue">0</div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeFromCartBtn">
                                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="me-1">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cart-quantity-controls" id="cartQuantityControls">
                                            <div class="row align-items-center w-100">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold mb-2">Quantity</label>
                                                    <div class="input-group quantity-group">
                                                        <button class="btn btn-outline-secondary btn-minus" type="button" id="qtyMinus">−</button>
                                                        <input type="number" class="form-control text-center" id="productQuantity" value="1" min="1" step="0.01">
                                                        <button class="btn btn-outline-secondary btn-plus" type="button" id="qtyPlus">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="addToCartInitial">
                                            <button type="button" class="btn cart-btn w-100" id="addToCartBtn">
                                                <span class="btn-text">Add to Cart</span>
                                                <span class="btn-loading d-none">
                                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                    Adding...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

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
        $(document).ready(function() {

            $(document).on('click', '.navigate-variant', function() {
                let clickedAttribute = $(this).data('attribute');
                let clickedPill = $(this);

                let clickedGroup = clickedPill.closest('.d-flex');

                let selectedAttributes = [];

                $('.d-flex').each(function() {
                    let group = $(this);
                    if (group.is(clickedGroup)) {
                        selectedAttributes.push(clickedAttribute);
                    } else {
                        let activeAttr = group.find('.pill-btn.active').data('attribute');
                        if (activeAttr) {
                            selectedAttributes.push(activeAttr);
                        }
                    }
                });

                $.ajax({
                    url: '{{ route('product.getVariant') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_short_url: '{{ $product->short_url }}',
                        attributes: selectedAttributes
                    },
                    success: function(response) {
                        if (response.success && response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            console.log('No matching variant found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error finding variant:', xhr);
                    }
                });
            });

            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            function setWishlistIcon(inWishlist) {
                let imageElement = $('#addToWishlist').find('img');
                if (inWishlist) {
                    imageElement.attr('src', '{{ asset('front-theme/images/added-to-wishlist.png') }}');
                } else {
                    imageElement.attr('src', '{{ asset('front-theme/images/not-added-to-wishlist.png') }}');
                }
            }

            function currentItemKey() {
                let info = $('#addToWishlist').data('product');
                return {
                    product_short_url: '{{ $product->short_url }}',
                    product_variant_short_url: info?.variant || null
                };
            }

            function localHasItem() {
                let key = currentItemKey();
                return (JSON.parse(localStorage.getItem('wishlist')) || []).some(function(x) {
                    return x.product_short_url === key.product_short_url && x.product_variant_short_url ===
                        key.product_variant_short_url;
                });
            }

            function localAddOrRemove() {
                let key = currentItemKey();
                let wl = JSON.parse(localStorage.getItem('wishlist')) || [];
                let exists = wl.some(function(x) {
                    return x.product_short_url === key.product_short_url && x.product_variant_short_url ===
                        key.product_variant_short_url;
                });
                if (exists) {
                    wl = wl.filter(function(x) {
                        return !(x.product_short_url === key.product_short_url && x
                            .product_variant_short_url === key.product_variant_short_url);
                    });
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

            function mergeLocalToServer() {
                let wl = JSON.parse(localStorage.getItem('wishlist')) || [];
                if (!wl.length) {
                    return;
                }
                $.ajax({
                    url: '{{ route('wishlist.merge') }}',
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
                    url: '{{ route('wishlist.status') }}',
                    method: 'GET',
                    success: function(resp) {
                        if (!resp?.success) {
                            setWishlistIcon(localHasItem());
                            return;
                        }
                        if (!resp.logged_in) {
                            setWishlistIcon(localHasItem());
                            return;
                        }
                        mergeLocalToServer();
                        let key = currentItemKey();
                        let inList = (resp.wishlists || []).some(function(x) {
                            return x.product_short_url === key.product_short_url && x
                                .product_variant_short_url === key.product_variant_short_url;
                        });
                        setWishlistIcon(inList);
                    },
                    error: function() {
                        setWishlistIcon(localHasItem());
                    }
                });
            }

            initWishlistIconFromStatus();

            $(document).on('click', '#addToWishlist', function() {
                let info = $(this).data('product');
                if (!info || !info.type) {
                    return;
                }

                $.ajax({
                    url: '{{ route('wishlist.status') }}',
                    method: 'GET',
                    success: function(resp) {
                        if (resp?.logged_in) {
                            let key = currentItemKey();
                            $.ajax({
                                url: '{{ route('wishlist.toggle') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    product_short_url: key.product_short_url,
                                    product_variant_short_url: key
                                        .product_variant_short_url
                                },
                                success: function(r) {
                                    setWishlistIcon(!!r?.in_wishlist);
                                },
                                error: function(xhr) {
                                    if (xhr && xhr.status === 401) {
                                        localAddOrRemove();
                                    }
                                }
                            });
                        } else {
                            localAddOrRemove();
                        }
                    },
                    error: function() {
                        localAddOrRemove();
                    }
                });
            });

            var thumbSlider = new Swiper(".thumbSlider", {
                spaceBetween: 10,
                slidesPerView: 5,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {

                    320: {
                        slidesPerView: 3,
                        spaceBetween: 20
                    },
                    576: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    },
                    992: {
                        slidesPerView: 5,
                        spaceBetween: 20
                    },
                },
            });

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

            let currentSelectedUnit = {
                unitType: null,
                unitId: null,
                unitTitle: null
            };

            function initializeDefaultUnit() {
                const defaultBtn = $('.unit-selector-btn.active').first();
                if (defaultBtn.length) {
                    currentSelectedUnit = {
                        unitType: defaultBtn.data('unit-type'),
                        unitId: defaultBtn.data('unit-id'),
                        unitTitle: defaultBtn.data('unit-title')
                    };
                    loadPricingData();
                    setTimeout(function() {
                        checkSpecificCartItem();
                    }, 500);
                } else {
                    const firstBtn = $('.unit-selector-btn').first();
                    if (firstBtn.length) {
                        firstBtn.addClass('active');
                        currentSelectedUnit = {
                            unitType: firstBtn.data('unit-type'),
                            unitId: firstBtn.data('unit-id'),
                            unitTitle: firstBtn.data('unit-title')
                        };
                        $('.current-unit-title').text(currentSelectedUnit.unitTitle);
                        loadPricingData();
                        setTimeout(function() {
                            checkSpecificCartItem();
                        }, 500);
                    }
                }
            }

            function loadPricingData() {
                if (!currentSelectedUnit.unitId) {
                    return;
                }

                const priceDisplay = $('#priceDisplay');
                const tierPricingTable = $('#tierPricingTable');
                const tierPricingBody = $('#tierPricingBody');

                priceDisplay.html('<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>');
                tierPricingTable.hide();
                tierPricingBody.empty();

                $.ajax({
                    url: '{{ route('product.pricing') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_short_url: '{{ $product->short_url }}',
                        product_variant_short_url: '{{ $variant ?? null }}',
                        unit_type: currentSelectedUnit.unitType,
                        unit_id: currentSelectedUnit.unitId
                    },
                    success: function(response) {
                        if (response.success) {
                            updatePriceDisplay(response, priceDisplay);
                            updateStockDisplay(response.total_stock);
                            
                            if (response.pricing_type === 0 && response.tier_pricings && response.tier_pricings.length > 0) {
                                displayTierPricing(response.tier_pricings, tierPricingTable, tierPricingBody);
                            } else {
                                tierPricingTable.hide();
                            }
                        } else {
                            priceDisplay.html('<span class="text-danger">Price information not available</span>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading pricing:', xhr);
                        priceDisplay.html('<span class="text-danger">Error loading pricing information</span>');
                    }
                });
            }

            function updatePriceDisplay(response, priceDisplay) {
                let priceHtml = '';

                if (response.pricing_type === 1 && response.single_pricing) {
                    const single = response.single_pricing;
                    if (single.your_price < single.mrp) {
                        priceHtml = '<span class="mrp-price">$' + parseFloat(single.mrp).toFixed(2) + '</span>';
                        priceHtml += '<span class="final-price">$' + parseFloat(single.your_price).toFixed(2) + '</span>';
                    } else {
                        priceHtml = '<span class="final-price">$' + parseFloat(single.mrp).toFixed(2) + '</span>';
                    }
                } else if (response.pricing_type === 0 && response.tier_pricings && response.tier_pricings.length > 0) {
                    const firstTier = response.tier_pricings[0];
                    if (firstTier.your_price < firstTier.mrp) {
                        priceHtml = '<span class="mrp-price">$' + parseFloat(firstTier.mrp).toFixed(2) + '</span>';
                        priceHtml += '<span class="final-price">$' + parseFloat(firstTier.your_price).toFixed(2) + '</span>';
                        priceHtml += '<small class="d-block text-muted mt-1">Starting from</small>';
                    } else {
                        priceHtml = '<span class="final-price">$' + parseFloat(firstTier.mrp).toFixed(2) + '</span>';
                        priceHtml += '<small class="d-block text-muted mt-1">Starting from</small>';
                    }
                } else {
                    priceHtml = '<span class="text-muted">Price not available</span>';
                }

                priceDisplay.html(priceHtml);
            }

            function updateStockDisplay(stock) {
                const stockBadge = $('.current-unit-title').siblings('.badge');
                if (stock > 0) {
                    stockBadge.removeClass('bg-danger').addClass('bg-success').text('In Stock: ' + stock);
                } else {
                    stockBadge.removeClass('bg-success').addClass('bg-danger').text('Out of Stock');
                }
            }

            function displayTierPricing(tierPricings, tierPricingTable, tierPricingBody) {
                tierPricingBody.empty();
                
                tierPricings.forEach(function(tier) {
                    const qtyRange = tier.max_qty > 0 
                        ? tier.min_qty + ' - ' + tier.max_qty 
                        : tier.min_qty + '+';
                    
                    const discountText = tier.discount_amount > 0
                        ? (tier.discount_type === 1 
                            ? tier.discount_value + '%' 
                            : '$' + parseFloat(tier.discount_value).toFixed(2))
                        : '-';

                    const rowClass = tierPricingBody.children().length === 0 ? 'highlight-row' : '';
                    
                    const row = '<tr class="' + rowClass + '">' +
                        '<td>' + qtyRange + '</td>' +
                        '<td>$' + parseFloat(tier.mrp).toFixed(2) + '</td>' +
                        '<td class="fw-bold text-success">$' + parseFloat(tier.your_price).toFixed(2) + '</td>' +
                        '<td>' + discountText + '</td>' +
                        '</tr>';
                    
                    tierPricingBody.append(row);
                });

                tierPricingTable.show();
            }

            $(document).on('click', '.unit-selector-btn', function() {
                $('.unit-selector-btn').removeClass('active');
                $(this).addClass('active');

                currentSelectedUnit = {
                    unitType: $(this).data('unit-type'),
                    unitId: $(this).data('unit-id'),
                    unitTitle: $(this).data('unit-title')
                };

                $('.current-unit-title').text(currentSelectedUnit.unitTitle);
                loadPricingData();
                
                setTimeout(function() {
                    checkSpecificCartItem();
                }, 500);
            });

            initializeDefaultUnit();

            let currentCartQuantity = 0;

            let currentCartItemId = null;

            function checkSpecificCartItem() {
                if (!currentSelectedUnit.unitId) {
                    hideCartQuantityControls();
                    return;
                }

                $.ajax({
                    url: '{{ route('cart.item-quantity') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_short_url: '{{ $product->short_url }}',
                        product_variant_short_url: '{{ $variant ?? null }}',
                        unit_type: currentSelectedUnit.unitType,
                        unit_id: currentSelectedUnit.unitId
                    },
                    success: function(response) {
                        if (response.success && response.quantity > 0) {
                            currentCartItemId = response.item_id;
                            currentCartQuantity = response.quantity;
                            showCartQuantityControls(response.quantity);
                        } else {
                            hideCartQuantityControls();
                        }
                    },
                    error: function() {
                        hideCartQuantityControls();
                    }
                });
            }

            function showCartQuantityControls(quantity) {
                currentCartQuantity = quantity;
                $('#addToCartInitial').hide();
                $('#cartQuantityDisplay').show();
                $('#cartQuantityValue').text(quantity);
                $('#cartQuantityControls').addClass('show');
                $('#productQuantity').val(quantity);
            }

            function hideCartQuantityControls() {
                currentCartQuantity = 0;
                currentCartItemId = null;
                $('#addToCartInitial').show();
                $('#cartQuantityDisplay').hide();
                $('#cartQuantityControls').removeClass('show');
                $('#productQuantity').val(1);
            }

            $(document).on('click', '.unit-selector-btn', function() {
                setTimeout(function() {
                    checkSpecificCartItem();
                }, 500);
            });

            let updateTimeout = null;

            function updateCartQuantity(quantity) {
                if (!currentCartItemId || quantity < 1) {
                    return;
                }

                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ route('cart.update') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            item_id: currentCartItemId,
                            quantity: quantity
                        },
                        success: function(response) {
                            if (response.success) {
                                updateCartCount();
                                $('#cartQuantityValue').text(quantity);
                                currentCartQuantity = quantity;
                            }
                        },
                        error: function() {
                            checkSpecificCartItem();
                        }
                    });
                }, 500);
            }

            $('#qtyPlus').on('click', function() {
                const input = $('#productQuantity');
                const currentVal = parseFloat(input.val()) || 1;
                const newVal = currentVal + 1;
                input.val(newVal);
                
                if (currentCartItemId) {
                    updateCartQuantity(newVal);
                }
            });

            $('#qtyMinus').on('click', function() {
                const input = $('#productQuantity');
                const currentVal = parseFloat(input.val()) || 1;
                if (currentVal > 1) {
                    const newVal = currentVal - 1;
                    input.val(newVal);
                    
                    if (currentCartItemId) {
                        updateCartQuantity(newVal);
                    }
                }
            });

            $('#productQuantity').on('change', function() {
                const quantity = parseFloat($(this).val()) || 1;
                if (quantity < 1) {
                    $(this).val(1);
                    return;
                }
                
                if (currentCartItemId) {
                    updateCartQuantity(quantity);
                }
            });

            $('#addToCartBtn').on('click', function() {
                if (!currentSelectedUnit.unitId) {
                    return;
                }

                const btn = $(this);
                const btnText = btn.find('.btn-text');
                const btnLoading = btn.find('.btn-loading');
                const quantity = 1;

                btn.prop('disabled', true);
                btnText.addClass('d-none');
                btnLoading.removeClass('d-none');

                $.ajax({
                    url: '{{ route('cart.add') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_short_url: '{{ $product->short_url }}',
                        product_variant_short_url: '{{ $variant ?? null }}',
                        unit_type: currentSelectedUnit.unitType,
                        unit_id: currentSelectedUnit.unitId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCartCount();
                            setTimeout(function() {
                                checkSpecificCartItem();
                            }, 300);
                        }
                    },
                    error: function() {
                        checkSpecificCartItem();
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        btnText.removeClass('d-none');
                        btnLoading.addClass('d-none');
                    }
                });
            });

            $('#removeFromCartBtn').on('click', function() {
                if (!currentCartItemId) {
                    return;
                }

                if (!confirm('Are you sure you want to remove this item from cart?')) {
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('cart.remove') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        item_id: currentCartItemId
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCartCount();
                            hideCartQuantityControls();
                        }
                    },
                    error: function() {
                        checkSpecificCartItem();
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });

            setTimeout(function() {
                checkSpecificCartItem();
            }, 1000);

            function updateCartCount() {
                $.ajax({
                    url: '{{ route('cart.count') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const count = response.count || 0;
                            if ($('#cartItemsCountText').length) {
                                $('#cartItemsCountText').text('(' + count + ' items)');
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
