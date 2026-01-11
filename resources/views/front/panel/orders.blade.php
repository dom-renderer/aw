@extends('front.layout', [
    'metaInfo' => [
        'title' => 'Order History - My Account',
        'content' => 'View and manage your order history',
    ],
])

@push('css')
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-confirmed {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    .status-processing {
        background-color: #cce5ff;
        color: #004085;
    }
    .status-packed {
        background-color: #d4edda;
        color: #155724;
    }
    .status-shipped {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    .status-delivered {
        background-color: #d4edda;
        color: #155724;
    }
    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    .status-refunded {
        background-color: #f8d7da;
        color: #721c24;
    }
    .stats-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
    }
    .stats-card-left h3 {
        margin-bottom: 5px;
    }
    .stats-card-img img {
        width: 50px;
        height: 50px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .pagination-account {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .custom-pagination .page-link {
        color: #1a73e8;
        border: 1px solid #dee2e6;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #1a73e8;
        border-color: #1a73e8;
        color: white;
    }
    .custom-pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<section>
    <div class="bred-pro">
        <div class="container">
            <div class="breadcrumb-container">
                <ol class="breadcrumb">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('orders') }}">My Account</a></li>
                    <li class="active">Order History</li>
                </ol>
            </div>
            <div class="acount-bred my-3 d-flex align-items-center justify-content-between ps-2">
                <div class="account-tleft">
                    <h1 class="h-36">My Account</h1>
                    <p>Welcome back, {{ auth()->guard('customer')->user()->name }}</p>
                </div>
                <div class="account-tright">
                    <div class="pro-image">
                        <img src="{{ asset('front-theme/images/profile.png') }}" alt="">
                    </div>
                    <div class="profile-account">
                        <p>{{ auth()->guard('customer')->user()->name }}</p>
                        <p>Business Account</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="accoont-menu">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-4 col-xl-3 mb-4 d-none d-lg-block">
                <div class="sidebar acount-leftbx c-lft-box bdr-clr nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <h3 class="mb-4">Menu</h3>
                    <button class="nav-link active" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-1.svg') }}" alt=""> Order History
                    </button>
                    <button class="nav-link" id="dashboard-tab" data-bs-toggle="pill" data-bs-target="#dashboard" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/aacount-1.svg') }}" class="bar-img" alt=""> Dashboard
                    </button>
                    <a href="{{ route('wishlist') }}" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt=""> Wishlist
                    </a>
                    <a href="{{ route('addresses') }}" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt=""> Addresses
                    </a>
                    <button class="nav-link" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt=""> Profile Settings
                    </button>
                    <button class="nav-link" id="invoices-tab" data-bs-toggle="pill" data-bs-target="#invoices" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-6.svg') }}" alt=""> Invoices
                    </button>
                    <button class="nav-link" id="payments-tab" data-bs-toggle="pill" data-bs-target="#payments" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt=""> Payment Methods
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt=""> Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title fw-bold" id="offcanvasSidebarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body nav flex-column nav-pills" id="v-pills-tab-mobile" role="tablist">
                    <button class="nav-link active" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-1.svg') }}" alt=""> Order History
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#dashboard" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/aacount-1.svg') }}" class="bar-img" alt=""> Dashboard
                    </button>
                    <a href="{{ route('wishlist') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt=""> Wishlist
                    </a>
                    <a href="{{ route('addresses') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt=""> Addresses
                    </a>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt=""> Profile Settings
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#invoices" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-6.svg') }}" alt=""> Invoices
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#payments" type="button" role="tab">
                        <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt=""> Payment Methods
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt=""> Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 col-xl-9">
                <button class="btn btn-primary mb-3 mobile-menu-btn menu-none-wb" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                    ☰ Menu
                </button>

                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="orders" role="tabpanel">
                        <div class="main-content mb-4">
                            <div class="c-lft-box bdr-clr history-account">
                                <div class="first-block-odr">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <h2 class="h-30">Order History</h2>
                                        <div class="export-odr">
                                            <a href="{{ route('orders.export') }}" class="btn btn-primary btn-sm me-2">
                                                <img src="{{ asset('front-theme/images/Union.svg') }}" alt=""> Export Orders
                                            </a>
                                        </div>
                                    </div>
                                    <p class="mt-2">Track and manage all your wholesale orders</p>
                                </div>
                                <form method="GET" action="{{ route('orders') }}" id="filterForm">
                                    <div class="row mb-4">
                                        <div class="col-md-6 col-lg-6 col-xl-3">
                                            <label for="status_filter" class="form-label mb-1 d-block">Order Status</label>
                                            <select class="form-select" id="status_filter" name="status">
                                                <option value="">All Statuses</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="packed" {{ request('status') == 'packed' ? 'selected' : '' }}>Packed</option>
                                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-xl-3">
                                            <label for="date_from" class="form-label mb-1 d-block">Date From</label>
                                            <input type="date" class="form-select" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-xl-3">
                                            <label for="date_to" class="form-label mb-1 d-block">Date To</label>
                                            <input type="date" class="form-select" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-xl-3">
                                            <label for="search" class="form-label mb-1 d-block">Search Order</label>
                                            <input type="text" class="form-select" id="search" name="search" placeholder="Order Number" value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-sm me-2">Apply Filters</button>
                                            <a href="{{ route('orders') }}" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="row text-center g-3 spent-mn">
                                <div class="col-md-6 col-lg-6 col-xl-3">
                                    <div class="stats-card c-lft-box bdr-clr">
                                        <div class="stats-card-left">
                                            <h3 class="h-30">{{ $statistics['total_orders'] }}</h3>
                                            <p>Total Orders</p>
                                        </div>
                                        <div class="stats-card-img">
                                            <img src="{{ asset('front-theme/images/spent-1.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-3">
                                    <div class="stats-card c-lft-box bdr-clr">
                                        <div class="stats-card-left">
                                            <h3 class="h-30">${{ number_format($statistics['total_spent'], 2) }}</h3>
                                            <p>Total Spent</p>
                                        </div>
                                        <div class="stats-card-img">
                                            <img src="{{ asset('front-theme/images/spent-2.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-3">
                                    <div class="stats-card c-lft-box bdr-clr">
                                        <div class="stats-card-left">
                                            <h3 class="h-30">${{ number_format($statistics['average_order'], 2) }}</h3>
                                            <p>Average Order</p>
                                        </div>
                                        <div class="stats-card-img">
                                            <img src="{{ asset('front-theme/images/spent-3.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-3">
                                    <div class="stats-card c-lft-box bdr-clr">
                                        <div class="stats-card-left">
                                            <h3 class="h-30">{{ $statistics['in_transit'] }}</h3>
                                            <p>In Transit</p>
                                        </div>
                                        <div class="stats-card-img">
                                            <img src="{{ asset('front-theme/images/spent-4.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="recnt-ac-odr">
                                <h3>Recent Orders</h3>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Order#</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Invoice</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                            <tr>
                                                <td>#{{ $order->order_number }}</td>
                                                <td>{{ $order->order_date->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="status-badge status-{{ $order->status }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->items->sum('quantity') }} Items</td>
                                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('order.invoice', ['order_number' => $order->order_number]) }}" class="text-decoration-none text-primary" target="_blank">
                                                        <img src="{{ asset('front-theme/images/tabl-2.svg') }}" alt=""> Download
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('order.confirmation', ['order_number' => $order->order_number]) }}" class="btn blue-v btn-sm">View</a>
                                                    <button class="btn rcd-bt btn-sm" onclick="reorder('{{ $order->id }}')">Reorder</button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <p class="p-18">No orders found</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @if($orders->hasPages())
                                    <div class="pagination-account">
                                        <div class="pegination-box">
                                            <div class="showing">
                                                Showing {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                                            </div>
                                            <div class="acount-peg">
                                                <nav aria-label="Page navigation example">
                                                    <ul class="pagination justify-content-center custom-pagination">
                                                        @if($orders->onFirstPage())
                                                            <li class="page-item disabled">
                                                                <span class="page-link" aria-label="Previous">
                                                                    <span aria-hidden="true"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="page-item">
                                                                <a class="page-link" href="{{ $orders->previousPageUrl() }}" aria-label="Previous">
                                                                    <span aria-hidden="true"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
                                                                </a>
                                                            </li>
                                                        @endif

                                                        @php
                                                            $currentPage = $orders->currentPage();
                                                            $lastPage = $orders->lastPage();
                                                            $pages = [];
                                                            
                                                            if ($lastPage <= 7) {
                                                                for ($i = 1; $i <= $lastPage; $i++) {
                                                                    $pages[] = $i;
                                                                }
                                                            } else {
                                                                if ($currentPage <= 3) {
                                                                    for ($i = 1; $i <= 4; $i++) {
                                                                        $pages[] = $i;
                                                                    }
                                                                    $pages[] = '...';
                                                                    $pages[] = $lastPage;
                                                                } elseif ($currentPage >= $lastPage - 2) {
                                                                    $pages[] = 1;
                                                                    $pages[] = '...';
                                                                    for ($i = $lastPage - 3; $i <= $lastPage; $i++) {
                                                                        $pages[] = $i;
                                                                    }
                                                                } else {
                                                                    $pages[] = 1;
                                                                    $pages[] = '...';
                                                                    for ($i = $currentPage - 1; $i <= $currentPage + 1; $i++) {
                                                                        $pages[] = $i;
                                                                    }
                                                                    $pages[] = '...';
                                                                    $pages[] = $lastPage;
                                                                }
                                                            }
                                                        @endphp
                                                        @foreach($pages as $page)
                                                            @if($page == '...')
                                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                                            @elseif($page == $currentPage)
                                                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                                            @else
                                                                <li class="page-item"><a class="page-link" href="{{ $orders->url($page) }}">{{ $page }}</a></li>
                                                            @endif
                                                        @endforeach

                                                        @if($orders->hasMorePages())
                                                            <li class="page-item">
                                                                <a class="page-link" href="{{ $orders->nextPageUrl() }}" aria-label="Next">
                                                                    <span aria-hidden="true"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li class="page-item disabled">
                                                                <span class="page-link" aria-label="Next">
                                                                    <span aria-hidden="true"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                                                                </span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="dashboard"><div class="main-content">Dashboard Content</div></div>
                    <div class="tab-pane fade" id="profile"><div class="main-content">Profile Content</div></div>
                    <div class="tab-pane fade" id="invoices"><div class="main-content">Invoices Content</div></div>
                    <div class="tab-pane fade" id="payments"><div class="main-content">Payments Content</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function reorder(orderId) {
        if (!confirm('Are you sure you want to reorder all items from this order?')) {
            return;
        }

        $.ajax({
            url: '{{ route('orders.reorder') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId
            },
            success: function(response) {
                if (response.success) {
                    alert('Items added to cart successfully!');
                    window.location.href = '{{ route('cart') }}';
                } else {
                    alert(response.message || 'Error adding items to cart');
                }
            },
            error: function(xhr) {
                let message = 'Error adding items to cart';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            }
        });
    }

    $(document).ready(function() {
        const offcanvasElement = document.getElementById('offcanvasSidebar');
        if (offcanvasElement) {
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
            $('#offcanvasSidebar .nav-link, #offcanvasSidebar a').on('click', function() {
                offcanvas.hide();
            });
        }
    });
</script>
@endpush

