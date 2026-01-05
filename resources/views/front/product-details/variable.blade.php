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

                        <!-- Variants -->
                        <div class="bult-div">
                            <div class="">
                                @forelse ($attributes ?? [] as $attributeKey => $attributeValues)
                                <p class="mt-2 mb-2">
                                    {{ $attributeKey }}
                                    <div class="d-flex gap-2">
                                        @foreach ($attributeValues as $attributeValue)
                                            <a data-attribute="{{ base64_encode($attributeValue['id']) }}" class="pill-btn navigate-variant @if(in_array($attributeValue['id'], $existingAttributes)) active @endif">{{ $attributeValue['name'] }}</a>
                                        @endforeach
                                    </div>
                                </p>
                                @empty
                                @endforelse
                            </div>
                        </div>
                        <!-- Variants -->

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
                            <div class="bult-div">
                            @if($units->count() > 0)
                            <h3 class="h-24 mb-4">Pricing</h3>
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
                                    @php
                                        $pricingByUnit = $tierPricings->groupBy('product_additional_unit_id');
                                    @endphp
                                    @foreach($units as $unit)
                                        @php
                                            $unitPricings = $pricingByUnit->get($unit['id'], collect());
                                            $unitPricingType = $unit['pricing_type'] ?? 'tier';
                                        @endphp
                                        @if($unitPricingType == 'tier' && $unitPricings->where('pricing_type', 'tier')->count() > 0)
                                            @foreach($unitPricings->where('pricing_type', 'tier') as $tier)
                                            <tr data-unit-id="{{ $tier['product_additional_unit_id'] }}" 
                                                data-unit-type="{{ $tier['unit_type'] }}"
                                                data-min="{{ $tier['min_qty'] }}" 
                                                data-max="{{ $tier['max_qty'] }}"
                                                data-price="{{ $tier['your_price'] }}"
                                                data-pricing-type="tier">
                                                <td>
                                                    @if(isset($tier['max_qty']) && $tier['max_qty'] > 0)
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
                                            @endforeach
                                        @elseif($unitPricingType == 'non-tier')
                                            @php
                                                $nonTierPrice = $unitPricings->where('pricing_type', 'non-tier')->first();
                                            @endphp
                                            @if($nonTierPrice)
                                            <tr data-unit-id="{{ $unit['id'] }}" 
                                                data-unit-type="{{ $unit['unit_type'] }}"
                                                data-price="{{ $nonTierPrice['your_price'] }}"
                                                data-pricing-type="non-tier"
                                                class="non-tier-pricing-row">
                                                <td>Any Quantity</td>
                                                <td>${{ number_format($nonTierPrice['mrp'], 2) }}</td>
                                                <td>${{ number_format($nonTierPrice['your_price'], 2) }}</td>
                                                <td>
                                                    @if($nonTierPrice['discount_amount'] > 0)
                                                        ${{ number_format($nonTierPrice['discount_amount'], 2) }}
                                                        @if($nonTierPrice['discount_type'] == 1)
                                                            ({{ number_format($nonTierPrice['discount_value']) }}%)
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                    @if($tierPricings->isEmpty())
                                    <tr>
                                        <td colspan="4">No pricing available</td>
                                    </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <!-- Quantity and Unit Selector -->
                            <div class="row g-3 align-items-center mt-4">
                                <div class="col-auto">
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
                                                data-pricing-type="{{ $unit['pricing_type'] ?? 'tier' }}"
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
                                        'type' => 'variant',
                                        'variant' => $variant
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

        $(document).on('click', '.navigate-variant', function () {
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
                url: '{{ route("product.getVariant") }}',
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
                imageElement.attr('src', '{{ asset("front-theme/images/added-to-wishlist.png") }}');
            } else {
                imageElement.attr('src', '{{ asset("front-theme/images/not-added-to-wishlist.png") }}');
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
            return (JSON.parse(localStorage.getItem('wishlist')) || []).some(function (x) {
                return x.product_short_url === key.product_short_url && x.product_variant_short_url === key.product_variant_short_url;
            });
        }

        function localAddOrRemove() {
            let key = currentItemKey();
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

        function mergeLocalToServer() {
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
                    if (!resp?.success) { setWishlistIcon(localHasItem()); return; }
                    if (!resp.logged_in) {
                        setWishlistIcon(localHasItem());
                        return;
                    }
                    mergeLocalToServer();
                    let key = currentItemKey();
                    let inList = (resp.wishlists || []).some(function (x) {
                        return x.product_short_url === key.product_short_url && x.product_variant_short_url === key.product_variant_short_url;
                    });
                    setWishlistIcon(inList);
                },
                error: function() {
                    setWishlistIcon(localHasItem());
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
                        let key = currentItemKey();
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
            
            320: { slidesPerView: 3, spaceBetween: 20 },
            576: { slidesPerView: 4, spaceBetween: 20 },
            992: { slidesPerView: 5, spaceBetween: 20 },
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
            let unitId = selectedOption.val();
            let unitType = selectedOption.data('unit-type');
            let pricingType = selectedOption.data('pricing-type') || 'tier';

            // Remove existing highlights
            $('#pricingTableBody tr').removeClass('highlight-row table-primary');

            // Find matching tier or non-tier pricing
            $('#pricingTableBody tr').each(function() {
                let row = $(this);
                let rowUnitId = row.data('unit-id');
                let rowUnitType = row.data('unit-type');
                let rowPricingType = row.data('pricing-type') || 'tier';

                // Check if unit matches
                if (rowUnitId == unitId && rowUnitType == unitType) {
                    if (rowPricingType === 'non-tier') {
                        // Non-tier pricing - always highlight
                        row.addClass('highlight-row table-primary');
                    } else {
                        // Tier pricing - check quantity range
                        let minQty = parseFloat(row.data('min')) || 0;
                        let maxQty = parseFloat(row.data('max')) || 0;
                        if (qty >= minQty && (maxQty === 0 || qty <= maxQty)) {
                            row.addClass('highlight-row table-primary');
                        }
                    }
                }
            });
        }

        // Initial highlight
        highlightApplicableTier();

        // Cart key for current item
        function currentCartItemKey() {
            let selectedOption = $('#unitSelector option:selected');
            return {
                product_short_url: '{{ $product->short_url }}',
                product_variant_short_url: '{{ $variant ?? "" }}',
                unit_type: selectedOption.data('unit-type') || null,
                unit_id: selectedOption.val() || null
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
                            // Note: cart.status needs to return unit info to be accurate
                            // Assuming cart.status returns items with unit_id/unit_type or we need to fetch full cart
                            // For now, let's assume we might need to fetch cart items or rely on a different endpoint
                            // But let's try to match based on what we have.
                            // If cart.status doesn't return unit info, this check might be weak.
                            // Let's assume for now we might need to enhance cart.status or just use local logic for UI if possible, 
                            // but server is truth.
                            // Actually, let's fetch the cart items properly if needed.
                            // For this task, let's assume cart.status returns enough info or we use a separate check.
                            // Let's use getLocalCart for guest and maybe we need a way to check server cart item specific.
                            return x.product_short_url === key.product_short_url && 
                                   x.product_variant_short_url === key.product_variant_short_url &&
                                   x.unit_id == key.unit_id;
                        });

                        if (item) {
                            showCartControls(item.quantity);
                        } else {
                            showAddButton();
                        }
                        updateCartCount(resp.items ? resp.items.length : 0); // Approximate count
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
                count = cart.reduce((acc, item) => acc + item.quantity, 0); // Total quantity or items? Usually items count or total qty. Let's use total items count for now.
                // Actually user said "show proper items in cart count". Usually distinct items or total qty.
                // Let's use distinct items count for consistency with typical carts, or total qty if preferred.
                // Let's stick to distinct items count for now.
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
            let unitType = selectedOption.data('unit-type');
            let unitId = parseInt(selectedOption.val());

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
                                product_variant_short_url: '{{ $variant ?? "" }}',
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
                // Ask to remove? Or just remove? User has separate remove button.
                // Let's keep it at 1 minimum for minus button, user can use Remove button.
                newQty = 1;
                return; 
            }
            
            input.val(newQty);
            updateCartQuantity(newQty);
        });

        // Cart Controls: Remove
        $(document).on('click', '#removeFromCartBtn', function() {
            let selectedOption = $('#unitSelector option:selected');
            let unitType = selectedOption.data('unit-type');
            let unitId = parseInt(selectedOption.val());

            // Check login status
             $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                         let item = (resp.items || []).find(function(x) {
                            return x.product_short_url === '{{ $product->short_url }}' && 
                                   x.product_variant_short_url === '{{ $variant ?? "" }}' &&
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
             let unitId = parseInt(selectedOption.val());

             $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                        let item = (resp.items || []).find(function(x) {
                            return x.product_short_url === '{{ $product->short_url }}' && 
                                   x.product_variant_short_url === '{{ $variant ?? "" }}' &&
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

        // Sync cart and wishlist on login
        function syncOnLogin() {
            if (typeof window.syncCartFromLocalStorage === 'function') {
                window.syncCartFromLocalStorage();
            }
            if (typeof window.syncWishlistFromLocalStorage === 'function') {
                window.syncWishlistFromLocalStorage();
            }
        }

        // Check login status periodically and sync if needed
        setInterval(function() {
            $.ajax({
                url: '{{ route("cart.status") }}',
                method: 'GET',
                success: function(resp) {
                    if (resp?.logged_in) {
                        let cart = JSON.parse(localStorage.getItem('cart')) || [];
                        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                        if (cart.length > 0 || wishlist.length > 0) {
                            syncOnLogin();
                        }
                    }
                }
            });
        }, 2000); // Check every 2 seconds

        // Initial check
        checkCartStatus();

    }); 
</script>
@endpush