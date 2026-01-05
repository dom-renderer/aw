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
                            <div class="produc-crt-section">
                                <a href="" class="btn cart-btn d-block">Add to Cart</a>
                                <a href="" class="cart-like"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
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

            document.querySelector('.btn-plus').addEventListener('click', function() {
            let input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
        });

        document.querySelector('.btn-minus').addEventListener('click', function() {
            let input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            }
        });
    }); 
</script>
@endpush