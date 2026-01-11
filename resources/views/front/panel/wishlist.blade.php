@extends('front.layout')

@push('css')
<style>
    .wishlist-item {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: box-shadow 0.3s;
    }
    .wishlist-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .product-i {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }
    .empty-wishlist {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-wishlist img {
        max-width: 200px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="accoont-menu">
    <div class="container py-4">
        <div class="row">
            <!-- Sidebar (Desktop) -->
            <div class="col-lg-4 col-xl-3 mb-4 d-none d-lg-block">
                <div class="sidebar acount-leftbx c-lft-box bdr-clr nav flex-column nav-pills" id="v-pills-tab"
                    role="tablist" aria-orientation="vertical">
                    <h3 class="mb-4">Menu</h3>
                    <a href="{{ route('home') }}" class="nav-link">
                        <img src="{{ asset('front-theme/images/aacount-1.svg') }}" class="bar-img" alt=""> Dashboard
                    </a>
                    <a href="{{ route('wishlist') }}" class="nav-link active">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt=""> Wishlist
                    </a>
                    <a href="{{ route('addresses') }}" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt=""> Addresses
                    </a>
                    <a href="#" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt=""> Profile Settings
                    </a>
                    <a href="#" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt=""> Payment Methods
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt=""> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Offcanvas Sidebar (Mobile) -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar"
                aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title fw-bold" id="offcanvasSidebarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body nav flex-column nav-pills" id="v-pills-tab-mobile" role="tablist">
                    <a href="{{ route('home') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/aacount-1.svg') }}" class="bar-img" alt=""> Dashboard
                    </a>
                    <a href="{{ route('wishlist') }}" class="nav-link active" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt=""> Wishlist
                    </a>
                    <a href="{{ route('addresses') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt=""> Addresses
                    </a>
                    <a href="#" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt=""> Profile Settings
                    </a>
                    <a href="#" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt=""> Payment Methods
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" data-bs-dismiss="offcanvas">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt=""> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8 col-xl-9">
                <!-- Mobile Menu Button -->
                <button class="btn btn-primary mb-3 mobile-menu-btn menu-none-wb" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                    ☰ Menu
                </button>

                <div class="main-content">
                    <h2 class="mb-4">My Wishlist</h2>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($wishlists->count() > 0)
                        <div class="wishlist-items">
                            @foreach($wishlists as $wishlist)
                                <div class="wishlist-item" id="wishlist-item-{{ $wishlist->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 col-sm-4 mb-3 mb-md-0">
                                            @php
                                                $product = $wishlist->product;
                                                $variant = $wishlist->productVariant;
                                                $image = $variant && $variant->variantImage ? $variant->variantImage->file : ($product->primaryImage ? $product->primaryImage->file : null);
                                            @endphp
                                            @if($image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="product-i">
                                            @else
                                                <img src="{{ asset('front-theme/images/placeholder.png') }}" alt="{{ $product->name }}" class="product-i">
                                            @endif
                                        </div>
                                        <div class="col-md-6 col-sm-8 mb-3 mb-md-0">
                                            <h5 class="mb-2">
                                                <a href="{{ route('product.index', ['product_slug' => $product->slug, 'short_url' => $product->short_url, 'variant' => $variant->short_url]) }}" class="text-decoration-none">
                                                    {{ $product->name }}
                                                </a>
                                            </h5>
                                            @if($variant)
                                                <p class="text-muted mb-1">
                                                    <small>Variant: {{ $variant->name ?? 'N/A' }}</small>
                                                </p>
                                            @endif
                                            @if($product->primaryCategory && $product->primaryCategory->category)
                                                <p class="text-muted mb-0">
                                                    <small>Category: {{ $product->primaryCategory->category->name }}</small>
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-4 col-sm-12 text-md-end">
                                            <a href="{{ route('product.index', ['product_slug' => $product->slug, 'short_url' => $product->short_url, 'variant' => $variant->short_url]) }}" class="btn btn-primary btn-sm mb-2">
                                                View Product
                                            </a>
                                            <br>
                                            <button type="button" class="btn btn-danger btn-sm remove-wishlist" data-id="{{ $wishlist->id }}">
                                                <i class="fa fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-wishlist">
                            <img src="{{ asset('front-theme/images/empty-wishlist.png') }}" alt="Empty Wishlist" onerror="this.style.display='none'">
                            <h4>Your wishlist is empty</h4>
                            <p class="text-muted">Start adding products to your wishlist!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.remove-wishlist').on('click', function() {
            const wishlistId = $(this).data('id');
            const item = $(this).closest('.wishlist-item');
            
            if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                return;
            }

            $.ajax({
                url: '{{ route("wishlist.remove", ":id") }}'.replace(':id', wishlistId),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        item.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if wishlist is now empty
                            if ($('.wishlist-item').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        alert(response.message || 'Failed to remove item');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        alert('Please login to continue');
                        window.location.href = '{{ route("login") }}';
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endpush
