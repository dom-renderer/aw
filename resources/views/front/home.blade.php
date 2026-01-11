@extends('front.layout')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.6/assets/owl.carousel.min.css">
@endpush

@section('content')
    @foreach ($sections as $section)

    @if($section?->key == 'banner_carousel' && $section?->value?->visible && !empty($section?->value?->slides ?? []))
    <!-- Carousel Section -->
    <section class="hero">
        <div class="hero-block">
            <div class="hero-box">
                <!-- change "carousel slide" to "carousel slide carousel-fade" -->
                <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">

                        @foreach ($section?->value?->slides as $slide)
                        <div class="carousel-item @if($loop->first) active @endif">
                            <div class="hero-caro firt-hr">
                                <img src="{{ asset('storage/' . $slide?->image) }}" class="d-block w-100" alt="Banner">
                                <div class="hero-content">
                                    <h2>{{ $slide?->heading }}</h2>
                                    <p>{{ $slide?->description }}</p>
                                    @if($slide?->has_button)
                                    <a href="{{ $slide?->redirect }}" class="btn hero-btn">{{ $slide?->button_title }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>

                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                        data-bs-slide="prev">
                        <span class="arrow-bg">
                            <img src="{{ asset('front-theme/images/arro-right.svg') }}" alt="">
                        </span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                        data-bs-slide="next">
                        <span class="arrow-bg">
                            <img src="{{ asset('front-theme/images/arrow-left.svg') }}" alt="">
                        </span>
                    </button>
                </div>
            </div>
            <div class="hero-bottom">
                <div class="container">
                    <div class="hero-bttom-bx">
                        <div class="her-bx-left">
                            <h3>Top Categories</h3>
                        </div>
                        <div class="her-bx-right">
                            <a href="">
                                View All
                                <img src="{{ asset('front-theme/images/right-arrow-view.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Carousel Section -->
    @endif

    @if(($section?->key == 'top_categories_grid' && $section?->value?->visible && !empty($section?->value?->categories ?? [])) || ($section?->key == 'top_categories_linear' && $section?->value?->visible && !empty($section?->value?->categories ?? [])))
    <!-- Food Content Start -->
    <section class="food">
        <div class="food-block">
            @if(($section?->key == 'top_categories_grid' && $section?->value?->visible && !empty($section?->value?->categories ?? [])))
            <div class="food-bevrage">
                <div class="container">
                    <div class="row">
                        @foreach ($section?->value?->categories as $category)
                        <div class="col-lg-12 col-xl-6 col-xxl-3 col-md-12 col-sm-12">
                            <div class="food-box">
                                <h3 class="h-30">
                                    <span>{{ $category?->title }}</span>
                                    @if(count($category?->items ?? []) > 4)
                                    <a href="{{ $category?->redirect }}">View All</a>
                                    @endif
                                </h3>
                                <div class="row">
                                    @foreach (Product::with(['primaryImage', 'images'])->select('id', 'name', 'slug', 'short_url')->whereIn('id', array_slice($category->items, 0, 4))->get() as $item)
                                    <a class="col-lg-6 col-xl-6 col-md-6 col-sm-6" href="{{ route('product.index', ['product_slug' => $item->slug, 'short_url' => $item->short_url]) }}">
                                        <div class="f-inbx">
                                            <!-- <img src="{{ asset('storage/' . $item?->primaryImage?->file) }}" alt="{{ $item->name }}"> -->
                                            <div class="hover-slider hover-slider-2">
                                                <div class="products-grid" id="productsGrid-Product-{{ $item->short_url }}" data-info="{{ json_encode(['slug' => $item->slug, 'short_url' => $item->short_url]) }}" data-images="{{ $item->images }}"></div>
                                                <p class="slow-mn text-truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if(($section?->key == 'top_categories_linear' && $section?->value?->visible && !empty($section?->value?->categories ?? [])))
            @foreach ($section->value->categories as $category)
            <div class="food-rum">
                <div class="container">
                    <h3 class="h-30">{{ $category->title }}</h3>
                    <div class="rum-block">
                        @foreach (Product::with('primaryImage')->select('id', 'name', 'slug', 'short_url')->whereIn('id', array_slice($category->items, 0, 5))->get() as $item)
                        <div class="rum-box">
                        <a href="{{ route('product.index', ['product_slug' => $item->slug, 'short_url' => $item->short_url]) }}">
                                <img src="{{ asset('storage/' . $item?->primaryImage?->file) }}" alt="{{ $item->name }}">
                                <p class="p-12">{{ $item->name }}</p>
                            </a>
                        </div>
                        @endforeach
                        @if(count($category->items) > 5)
                        <div class="rum-box view-rm">
                            <a href="">
                                View All
                                <img src="{{ asset('front-theme/images/arrow-blue-vew.png') }}" alt="">
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </section>
    <!-- Food Content End -->
    @endif

    @if($section->key == 'top_selling_products')
    <!-- Seling-product Content Start -->
    <section class="product-section">
        <div class="product-block pd-y50 margin-heg">
            <div class="container">
                <h2 class="h-30">
                    Top Selling Products
                    @if($topSellingProductCount > 4)
                    <a href="" class="view-white">
                        View All
                        <img src="{{ asset('front-theme/images/view-all-white.png') }}" alt="">
                    </a>
                    @endif
                </h2>
                <div class="row">

                @forelse($topSellingProduct as $tsp)
                    <div class="col-lg-6 col-xl-6 col-xxl-3 col-md-6 col-sm-6">
                        <div class="product-box">
                            <img src="{{ asset('storage/' . $tsp?->primaryImage?->file) }}" class="w-100 ofc-h250" alt="{{ $tsp->name }}">
                            <h3 class="h-20 mt-3 mb-3">{{ $tsp->name }}</h3>
                            <div class="price-bxm">
                                <span class="text-offer">$24.99</span>
                                <div class="bulk-div">
                                    <span>$89.99</span>
                                    <a href="" class="bulk-pr btn">Bulk Pricing</a>
                                </div>
                                <p class="p-18 mt-2 mb-3">Min Order: 5 boxes</p>
                            </div>



                            <!-- Wrap the button + cart replacement inside this wrapper -->
                            <div class="cart-toggle-wrapper">
                                <!-- Add to Cart button (can be <a> or <button>) -->
                                <a href="" class="btn cart-btn d-block" id="addToCartBtn">Add to Cart</a>

                                <!-- Replacement cart UI that appears after clicking Add to Cart -->
                                <div class="cart-home" aria-hidden="true">
                                    <div class="cart-all-dtl">
                                        <div class="col-auto">
                                            <div class="input-group quantity-group">
                                                <button class="btn btn-outline-secondary btn-minus"
                                                    type="button">−</button>
                                                <input type="text" class="form-control text-center quantity-value"
                                                    value="1" id="quantity" readonly>
                                                <button class="btn btn-outline-secondary btn-plus"
                                                    type="button">+</button>
                                            </div>
                                        </div>
                                        <div class="cart-pra">
                                            <p class="h-24 price-value">$89.99</p>
                                            <p class="p-18">Total</p>
                                        </div>
                                        <div class="cart-delete" role="button" title="Remove from cart">
                                            <img src="{{ asset('front-theme/images/cart-delete.png') }}" alt="Delete"
                                                class="cart-delete-img">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse

                </div>
            </div>
        </div>
    </section>
    <!-- Seling-product Content End -->
    @endif


    @if($section->key == 'recently_viewed')
    <!-- Recent Section Start -->
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
    <!-- Recent Section End -->
    @endif

    @if($section->key == 'newsletter_subscription')
    <!-- Subscribe Section Start -->
    <section class="sub-section">
        <div class="sub-block">
            <div class="container">
                <div class="sub-box text-center">
                    <h2 class="h-30">Stay Updated</h2>
                    <p class="p-20 py-3">Sign up for updates and exclusive wholesale offers</p>
                    <div class="sub-serch-box">
                        <input type="text" placeholder="Your email address">
                        <button class="btn-sub btn">Subscribe</button>
                    </div>
                    <p class="p-18">By subscribing , you agree to receive marketing communications from Anjo Wholesale
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Subscribe Section End -->
    @endif

    @endforeach
@endsection

@push('js')
    <script src="{{ asset('front-theme/js/Jquery-min.js') }}"></script>
    <script src="{{ asset('front-theme/js/bootstrap-min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    </script>
    <script>
        const productImageUrl = "{{ asset('storage') }}";
        const productsGrids = document.querySelectorAll('.products-grid');
        productsGrids.forEach((element, index) => {
            let allImages = JSON.parse(element?.dataset?.images);
            let allInfo = JSON.parse(element?.dataset?.info);
            let productImage = [];
            const slideshowIntervals = {};

            if (allImages != null && typeof allImages[Symbol.iterator] === "function") {
                Object.values(allImages).forEach(image => {
                    productImage.push(`${productImageUrl}/${image.file}`);
                });
            }

            let productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.dataset.productIndex = index;

            let imageContainer = document.createElement('div');
            imageContainer.className = 'product-image-container';

            productImage.forEach((imgSrc, imgIndex) => {
                const img = document.createElement('img');
                img.src = imgSrc;
                img.className = `product-image ${imgIndex === 0 ? 'active' : ''}`;
                imageContainer.appendChild(img);
            });

            let indicatorContainer = document.createElement('div');
            indicatorContainer.className = 'image-indicator';
            productImage.forEach((_, imgIndex) => {
                const dot = document.createElement('div');
                dot.className = `indicator-dot ${imgIndex === 0 ? 'active' : ''}`;
                indicatorContainer.appendChild(dot);
            });

            imageContainer.appendChild(indicatorContainer);

            const productInfo = document.createElement('a');
            productInfo.className = 'product-info';
            productInfo.dataset.info = JSON.stringify(allInfo);
            productInfo.innerHTML = `
                
                <div class="wish-list">
                    <button class="btn">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt="">
                        Wishlist
                    </button>
                </div>
            `;

            productCard.appendChild(imageContainer);
            productCard.appendChild(productInfo);

            element.appendChild(productCard);

            let currentImageIndex = 0;
            let hoverTimeout = null;

            productCard.addEventListener('mouseenter', () => {
                const images = imageContainer.querySelectorAll('.product-image');
                const dots = indicatorContainer.querySelectorAll('.indicator-dot');

                const changeImage = () => {
                    images[currentImageIndex].classList.remove('active');
                    dots[currentImageIndex].classList.remove('active');

                    currentImageIndex = (currentImageIndex + 1) % images.length;

                    images[currentImageIndex].classList.add('active');
                    dots[currentImageIndex].classList.add('active');
                };

                hoverTimeout = setTimeout(() => {
                    changeImage();
                    slideshowIntervals[index] = setInterval(changeImage, 1000);
                }, 400);
            });

            productCard.addEventListener('mouseleave', () => {
                clearTimeout(hoverTimeout);
                clearInterval(slideshowIntervals[index]);

                const images = imageContainer.querySelectorAll('.product-image');
                const dots = indicatorContainer.querySelectorAll('.indicator-dot');

                images[currentImageIndex].classList.remove('active');
                dots[currentImageIndex].classList.remove('active');

                currentImageIndex = 0;

                images[0].classList.add('active');
                dots[0].classList.add('active');
            });
        });
    </script>

@endpush


