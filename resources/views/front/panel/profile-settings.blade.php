@extends('front.layout')

@push('css')
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
                    <button class="nav-link" id="dashboard-tab" data-bs-toggle="pill" data-bs-target="#dashboard"
                        type="button" role="tab">
                        <img src="images/aacount-1.svg" class="bar-img" alt=""> Dashboard
                    </button>
                    <button class="nav-link" id="wishlist-tab" data-bs-toggle="pill" data-bs-target="#wishlist"
                        type="button" role="tab">
                        <img src="images/menuicon-3.svg" alt=""> Wishlist
                    </button>
                    <button class="nav-link" id="addresses-tab" data-bs-toggle="pill" data-bs-target="#addresses"
                        type="button" role="tab">
                        <img src="images/menuicon-4.svg" alt=""> Addresses
                    </button>
                    <button class="nav-link" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile"
                        type="button" role="tab">
                        <img src="images/menuicon-5.svg" alt=""> Profile Settings
                    </button>
                    <button class="nav-link" id="payments-tab" data-bs-toggle="pill" data-bs-target="#payments"
                        type="button" role="tab">
                        <img src="images/menuicon-7.svg" alt=""> Payment Methods
                    </button>
                    <button class="nav-link" id="logout-tab" data-bs-toggle="pill" data-bs-target="#logout"
                        type="button" role="tab">
                        <img src="images/menuicon-9.svg" alt=""> Logout
                    </button>
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
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill"
                        data-bs-target="#dashboard" type="button" role="tab">
                        <img src="images/aacount-1.svg" class="bar-img" alt=""> Dashboard
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill"
                        data-bs-target="#wishlist" type="button" role="tab">
                        <img src="images/menuicon-3.svg" alt=""> Wishlist
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill"
                        data-bs-target="#addresses" type="button" role="tab">
                        <img src="images/menuicon-4.svg" alt=""> Addresses
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#profile"
                        type="button" role="tab">
                        <img src="images/menuicon-5.svg" alt=""> Profile Settings
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill"
                        data-bs-target="#payments" type="button" role="tab">
                        <img src="images/menuicon-7.svg" alt=""> Payment Methods
                    </button>
                    <button class="nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="pill" data-bs-target="#logout"
                        type="button" role="tab">
                        <img src="images/menuicon-9.svg" alt=""> Logout
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8 col-xl-9">
                <!-- Mobile Menu Button -->
                <button class="btn btn-primary mb-3 mobile-menu-btn menu-none-wb" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                    ☰ Menu
                </button>

                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Orders Tab -->
                    <div class="tab-pane fade show active" id="orders" role="tabpanel">
                        <div class="main-content  mb-4">

                        </div>
                    </div>

                    <!-- Other tabs (placeholders) -->
                    <div class="tab-pane fade" id="dashboard">
                        <div class="main-content">Dashboard Content</div>
                    </div>
                    <div class="tab-pane fade" id="wishlist">
                        <div class="main-content">Wishlist Content</div>
                    </div>
                    <div class="tab-pane fade" id="addresses">
                        <div class="main-content">Addresses Content</div>
                    </div>
                    <div class="tab-pane fade" id="profile">
                        <div class="main-content">Profile Content</div>
                    </div>
                    <div class="tab-pane fade" id="payments">
                        <div class="main-content">Payments Content</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>

</script>
@endpush