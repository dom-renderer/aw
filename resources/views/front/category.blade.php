@extends('front.layout')

@section('content')
    <!-- beadcrum Section Start -->
    <section>
        <div class="bred-pro">
            <div class="container">
                <div class="breadcrumb-container">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="javascript:void(0);">Products</a></li>
                        <li class="active">{{ $category->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- beadcrum Section End -->

    <!-- Main-section Content Start -->
    <section class="mn-filter">
        <div class="mn-filter-block">
            <div class="container">
                <div class="row">
                    <!-- Filters Left -->
                    <div class="col-md-6 col-lg-5 co-sm-6 col-xl-3">
                        <div class="flter-left">
                            <form method="GET" action="{{ route('category.index', ['category_slug' => $category->slug, 'short_url' => $category->short_url]) }}">
                                <div class="head-filetr">
                                    <h3 class="h-20 d-flex justify-content-between align-items-center">
                                        <span>Filters</span>
                                        @if(request()->hasAny(['attributes', 'price_range', 'sort']))
                                            <a href="{{ route('category.index', ['category_slug' => $category->slug, 'short_url' => $category->short_url]) }}">Clear All</a>
                                        @endif
                                    </h3>
                                </div>

                                <div class="form-section">
                                    {{-- Attribute filters --}}
                                    @foreach($attributeFilters as $group)
                                        <div class="inbx-fill">
                                            <h3 class="h-20">{{ $group['title'] }}</h3>
                                            <div class="chek-box">
                                                @foreach($group['values'] as $value)
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            name="attributes[]"
                                                            value="{{ $value['encoded'] }}"
                                                            id="attr_{{ $value['id'] }}"
                                                            {{ in_array($value['encoded'], (array) $selectedAttributes, true) ? 'checked' : '' }}
                                                        >
                                                        <label class="form-check-label" for="attr_{{ $value['id'] }}">
                                                            {{ $value['label'] }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Price filters --}}
                                    <div class="inbx-fill">
                                        <h3 class="h-20">Price Range</h3>
                                        <div class="chek-box radio-grip">
                                            @php
                                                $ranges = [
                                                    'under_50'  => 'Under $50',
                                                    '50_100'    => '$50 - $100',
                                                    '100_200'   => '$100 - $200',
                                                    '200_500'   => '$200 - $500',
                                                    'above_500' => 'Above $500',
                                                ];
                                            @endphp
                                            @foreach($ranges as $key => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           name="price_range"
                                                           id="price_{{ $key }}"
                                                           value="{{ $key }}"
                                                           {{ $priceRange === $key ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="price_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="cart-left mt-3">
                                        <button type="submit" class="btn cart-btn d-block w-100">
                                            Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Products Right -->
                    <div class="col-lg-7 col-sm-12 col-md-6 col-xl-9">
                        <div class="right__box">
                            <div class="filt_Rhead">
                                <div class="f-hd">
                                    <h2 class="h-30">{{ $category->name }}</h2>
                                    <p class="p-20">
                                        @php
                                            $from = ($products->currentPage() - 1) * $products->perPage() + 1;
                                            $to   = $from + $products->count() - 1;
                                        @endphp
                                        Showing <span>{{ $from }}-{{ $to }}</span> of {{ $products->total() }} results
                                    </p>
                                </div>
                                <div class="sort-R">
                                    <div class="d-flex align-items-center sort-box">
                                        <label for="sortSelect" class="me-2 mb-0">Sort by:</label>
                                        <form method="GET" id="sortForm"
                                              action="{{ route('category.index', ['category_slug' => $category->slug, 'short_url' => $category->short_url]) }}">
                                            @foreach(request()->except('sort', 'page') as $key => $value)
                                                @if(is_array($value))
                                                    @foreach($value as $v)
                                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                                    @endforeach
                                                @else
                                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                @endif
                                            @endforeach
                                            <select class="form-select sort-select" id="sortSelect" name="sort"
                                                    onchange="document.getElementById('sortForm').submit();">
                                                <option value="az" {{ $sort === 'az' ? 'selected' : '' }}>A-Z</option>
                                                <option value="za" {{ $sort === 'za' ? 'selected' : '' }}>Z-A</option>
                                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                                                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="product-boxes">
                                <div class="row">
                                    @forelse($products as $product)
                                        <div class="col-lg-6 col-md-12 col-xl-6 col-xxl-4 col-sm-6">
                                            <div class="pro-inbox">
                                                <div class="produc-imgbx">
                                                    <a href="{{ route('product.index', ['product_slug' => $product->slug, 'short_url' => $product->short_url]) }}">
                                                        @if($product->primaryImage)
                                                            <img src="{{ asset('storage/' . $product->primaryImage->file) }}" class="w-100" alt="{{ $product->name }}">
                                                        @else
                                                            <img src="{{ asset('assets/images/default-product.png') }}" class="w-100" alt="{{ $product->name }}">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="proctinbx">
                                                    <h3 class="h-20 mb-3 text-truncate" title="{{ $product->name }}">
                                                        <a href="{{ route('product.index', ['product_slug' => $product->slug, 'short_url' => $product->short_url]) }}">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h3>
                                                    @if(!is_null($product->single_product_price))
                                                        <h4 class="h-20">${{ number_format($product->single_product_price, 2) }}</h4>
                                                    @endif
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <p class="p-18">
                                                            {{ optional($product->primaryCategory->category)->name }}
                                                        </p>
                                                    </div>
                                                    <a href="{{ route('product.index', ['product_slug' => $product->slug, 'short_url' => $product->short_url]) }}"
                                                       class="btn cart-btn d-block mt-3">
                                                        View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="p-20">No products found in this category.</p>
                                        </div>
                                    @endforelse
                                </div>

                                @if($products->hasPages())
                                    <div class="product-pegination">
                                        {{ $products->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Main-section Content End -->
@endsection


