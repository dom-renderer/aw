<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @if(isset($metaInfo))
    <meta name="title" content="{{ $metaInfo['title'] }}">
    <meta name="description" content="{{ $metaInfo['content'] }}">
    <meta name="keywords" content="{{ $metaInfo['keywords'] }}">
    <link rel="canonical" href="{{ $metaInfo['url'] }}">
    <meta name="robots" content="index, follow">
    @endif

    <title> {{ Helper::title() }} - {{ isset($title) ? $title : 'Home' }} </title>
    <link rel="icon" type="image/x-icon" href="{{ Helper::favicon() }}">
    <link rel="stylesheet" href="{{ asset('front-theme/css/boostrap-min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('front-theme/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('front-theme/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front-theme/css/responsive.css') }}">
    @stack('css')
    <style>
        .wish-life {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            margin-top: 4px;
            width: 100%;
            max-height: 320px;
            overflow-y: auto;
            opacity: 1;
            z-index: 99999 !important;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
        }

        .wish-life .search-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .wish-life .search-item:hover,
        .wish-life .search-item.active {
            background-color: #f3f4f6;
        }

        .wish-life .search-icon {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9fafb;
            font-size: 14px;
            color: #6b7280;
        }

        .wish-life .search-text {
            flex: 1;
            min-width: 0;
        }

        .wish-life .search-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wish-life .search-meta {
            font-size: 12px;
            color: #6b7280;
        }

        .wish-life .search-empty {
            padding: 10px 14px;
            font-size: 13px;
            color: #6b7280;
        }

        .wish-life .search-section-title {
            padding: 8px 14px 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <!-- header content Start-------------------------------------------->
    <header>
        <nav class="navbar navbar-expand-lg px-0 py-2">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('front-theme/images/aw-log.svg') }}" class="h-8" alt="...">
                </a>
                <!-- Navbar toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Collapse -->
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <!-- Nav -->
                    <div class="navbar-nav mx-lg-auto head-middle">
                        <form action="javascript:void(0);" class="w-100" autocomplete="off">
                            <div class="header__srh-box position-relative">
                                <div class="srh-left">
                                    <i class="fa fa-bars" aria-hidden="true"></i>
                                    <input type="text" id="global-search-input" placeholder="Search products or categories">
                                </div>
                                <div class="srh-rgt">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </div>

                                {{-- Live search dropdown --}}
                                <div id="global-search-results" class="wish-life d-none">
                                    <ul class="list-unstyled mb-0" id="global-search-list"></ul>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Right navigation -->
                    <div class="navbar-nav ms-lg-4">
                        <div class="navbar-nav mx-lg-auto">
                            <a class="nav-item nav-link cart" href="{{ route('cart') }}">
                                <img src="{{ asset('front-theme/images/cart.svg') }}" alt="">
                                <span id="headerCartCount">0</span>
                            </a>
                            <a class="nav-item nav-link cart notification-clk" href="#">
                                <img src="{{ asset('front-theme/images/notification.svg') }}" alt="">
                                <span>11</span>
                            </a>
                            <a class="nav-item nav-link user-admn" href="">
                                <span>
                                    <img src="{{ asset('front-theme/images/user-admin.svg') }}" alt="">
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <div class="menu-account">
            @if(auth()->guard('customer')->check())
            <div class="container">
                <h3 class="h-24 mb-3">{{ auth()?->guard('customer')?->name }}</h3>
                <div class="act-switch">
                    <div class="mb-3">
                        <a class="text-white" href="{{ route('switch-account') }}">Switch Accounts</a>
                    </div>
                    <div class="mb-3">
                        <form action="{{ route('logout') }}" method="POST"> @csrf
                            <button type="submit" class="text-white" style="border: none;background:transparent;">Sign Out</button>
                        </form>
                    </div>
                </div>
                <ul>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-1.svg') }}" alt="">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt="">
                            <span>Order History</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt="">
                            <span>Wishlist</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt="">
                            <span>Addresses</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt="">
                            <span>Profile Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-6.svg') }}" alt="">
                            <span>Invoices</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt="">
                            <span>Payment Methods</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-8.svg') }}" alt="">
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="btn">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt="">
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
            @else
            <!-- Without Login -->
            <div class="container">
                <a href="{{ route('login') }}" class="h-24 text-white"> Login </a>
            </div>
            <!-- Without Login -->
            @endif
        </div>
        <div class="notification">
            <div class="notification-block">
                <h3 class="h-24">Notifications</h3>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                            type="button" role="tab">
                            All
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread"
                            type="button" role="tab">
                            Unread
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <!-- All Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <ul class="notification-list">
                            <li class="notification-item">
                                <img src="{{ asset('front-theme/images/nitficatio9m.svg') }}" alt="">
                                <div>
                                    <div class="notification-text">Your order has been processed.</div>
                                    <div class="notification-time">5m</div>
                                </div>
                                <span class="notification-dot"></span>
                            </li>
                            <li class="notification-item">
                                <img src="{{ asset('front-theme/images/nitficatio9m.svg') }}" alt="">
                                <div>
                                    <div class="notification-text">Your order shipped.</div>
                                    <div class="notification-time">6d</div>
                                </div>
                            </li>
                            <li class="notification-item">
                                <img src="{{ asset('front-theme/images/nitficatio9m.svg') }}" alt="">
                                <div>
                                    <div class="notification-text">Check out our Summer Sale</div>
                                    <div class="notification-time">12d</div>
                                </div>
                            </li>
                            <li class="notification-item">
                                <img src="{{ asset('front-theme/images/nitficatio9m.svg') }}" alt="">
                                <div>
                                    <div class="notification-text">20% off all OKF products!</div>
                                    <div class="notification-time">5m</div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Unread Tab -->
                    <div class="tab-pane fade" id="unread" role="tabpanel">
                        <ul class="notification-list">
                            <li class="notification-item">
                                <img src="{{ asset('front-theme/images/nitficatio9m.svg') }}" alt="">
                                <div>
                                    <div class="notification-text">Your order has been processed.</div>
                                    <div class="notification-time">5m</div>
                                </div>
                                <span class="notification-dot"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="cart-panel" id="cartPanel">
        <div class="cart-content">
            <div class="cart-item">
                <h3>Subtotal</h3>
                <p class="inr-red">INR 116.45</p>
                <a href="" class="btn cart-btn d-block">Go to Cart</a>
            </div>
            <div class="border-cart">
                <div class="cart-product">
                    <img src="{{ asset('front-theme/images/food-img4.png') }}" alt="">
                    <p class="inr-blck">INR 116.45</p>
                </div>
                <div class="col-auto">
                    <div class="input-group quantity-group">
                        <button class="btn btn-outline-secondary btn-minus" type="button">−</button>
                        <input type="text" class="form-control text-center quantity-value" value="1" id="quantity"
                            readonly="">
                        <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- header content End-------------------------------------------->


    @yield('content')

    <footer>
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-lg-6 col-xl-3 col-md-6 col-xxl-3">
                        <div class="footer-box">
                            <h3>About Anjo Wholesale</h3>
                            <p class="mt-2 mb-2">Mon-Fri 8AM-4PM</p>
                            <div class="ftr-whl">
                                <p>Our goods are delivered at no <br> extra charge once our delivery <br> requirements
                                    are met! Give us a <br> call to find out more information.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-lg-6 col-xl-3 col-md-6 col-xxl-3">
                        <div class="footer-box">
                            <h3>Phone</h3>
                            <div class="ftor-call">
                                <p>(268) 480-3080 (AR)</p>
                                <p>(268) 736 5814 (Whatsapp)</p>
                                <p>(268) 480-3046/7 (Coolidge)</p>
                                <p>(268) 480-3086 (Fax)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-lg-6 col-xl-3 col-md-6 col-xxl-3">
                        <div class="footer-box">
                            <h3>Email</h3>
                            <ul class="ftr-ul">
                                <li><a href="">anjo.w@candw.ag</a></li>
                                <li><a href="">​HR@anjowholesale.com</a></li>
                                <li><a href="">info@anjowholesale.com</a></li>
                                <li><a href="">mario.winter@anjowholesale.com</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg- 3col-lg-6 col-xl-3 col-md-6 col-xxl-3">
                        <div class="footer-box">
                            <h3>P.O. Box</h3>
                            <div class="ftr-po">
                                Anjo Wholesale <br>
                                P.O. Box 104 St. John's, <br> Antigua & Barbuda
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xl-2">
                        <div class="ftr-social">
                            <a href=""><img src="{{ asset('front-theme/images/Facebook.svg') }}" alt=""></a>
                            <a href=""><img src="{{ asset('front-theme/images/Instagram.svg') }}" alt=""></a>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xl-10">
                        <ul>
                            <li>
                                <img src="{{ asset('front-theme/images/Subtract.svg') }}" alt="">
                                Anjo Wholesale
                            </li>
                            <li>American Road St. John's, Antigua & Barbuda</li>
                            <li>Coolidge, Corner of Sir George Walter Highway & Powells Main Road</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>


</body>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('front-theme/js/bootstrap-min.js') }}"></script>

<script>
    // Global header search (products + categories)
    (function () {
        const $input = $('#global-search-input');
        const $dropdown = $('#global-search-results');
        const $list = $('#global-search-list');
        let debounceTimer = null;
        let currentIndex = -1;
        var SEARCH_URL = "{{ route('search') }}";

        function hideDropdown() {
            $dropdown.addClass('d-none');
            currentIndex = -1;
            $list.empty();
        }

        function showDropdown() {
            if ($list.children().length === 0) {
                hideDropdown();
                return;
            }
            $dropdown.removeClass('d-none');
        }

        function renderResults(data) {
            data = data || {};
            $list.empty();

            const hasProducts = (data.products || []).length > 0;
            const hasCategories = (data.categories || []).length > 0;

            if (!hasProducts && !hasCategories) {
                $list.append(
                    '<li class="search-empty">No matching products or categories</li>'
                );
                showDropdown();
                return;
            }

            if (hasProducts) {
                $list.append('<li class="search-section-title">Products</li>');
                data.products.forEach(function (item) {
                    const meta = item.sku ? 'SKU: ' + item.sku : 'Product';
                    const li =
                        '<li class="search-item" data-url="' + item.url + '">' +
                        '  <div class="search-icon"><i class="fa fa-cube"></i></div>' +
                        '  <div class="search-text">' +
                        '    <div class="search-title">' + $('<div>').text(item.name).html() + '</div>' +
                        '    <div class="search-meta">' + meta + '</div>' +
                        '  </div>' +
                        '</li>';
                    $list.append(li);
                });
            }

            if (hasCategories) {
                $list.append('<li class="search-section-title">Categories</li>');
                data.categories.forEach(function (item) {
                    const li =
                        '<li class="search-item" data-url="' + item.url + '">' +
                        '  <div class="search-icon"><i class="fa fa-folder-open"></i></div>' +
                        '  <div class="search-text">' +
                        '    <div class="search-title">' + $('<div>').text(item.name).html() + '</div>' +
                        '    <div class="search-meta">Category</div>' +
                        '  </div>' +
                        '</li>';
                    $list.append(li);
                });
            }

            showDropdown();
        }

        function performSearch(term) {
            if (!term || term.length < 2) {
                hideDropdown();
                return;
            }

            var payload = {};
            payload.q = term;
            payload.ajax = 1;

            $.get(SEARCH_URL, payload, function (response) {
                renderResults(response);
            }, 'json').fail(function () {
                hideDropdown();
            });
        }

        if ($input.length) {
            $input.on('input', function () {
                const term = $(this).val();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    performSearch(term);
                }, 250);
            });

            $input.on('keydown', function (e) {
                const items = $list.find('.search-item');
                if (items.length === 0) {
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentIndex = (currentIndex + 1) % items.length;
                    items.removeClass('active');
                    $(items[currentIndex]).addClass('active');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
                    items.removeClass('active');
                    $(items[currentIndex]).addClass('active');
                } else if (e.key === 'Enter') {
                    if (currentIndex >= 0 && currentIndex < items.length) {
                        e.preventDefault();
                        const url = $(items[currentIndex]).data('url');
                        if (url) {
                            window.location.href = url;
                        }
                    }
                } else if (e.key === 'Escape') {
                    hideDropdown();
                }
            });

            $list.on('click', '.search-item', function (e) {
                e.preventDefault();
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.header__srh-box').length) {
                    hideDropdown();
                }
            });
        }
    })();

    document.addEventListener("DOMContentLoaded", function() {
        const userAdmin = document.querySelector(".user-admn");
        const menuAccount = document.querySelector(".menu-account");

        userAdmin.addEventListener("click", function(e) {
            e.preventDefault();
            menuAccount.classList.toggle("active");
        });

        document.addEventListener("click", function(e) {
            if (!menuAccount.contains(e.target) && !userAdmin.contains(e.target)) {
                menuAccount.classList.remove("active");
            }
        });
    });

    document.querySelectorAll('.product-card').forEach(card => {
        const wishlist = card.querySelector('.wish-list');

        card.addEventListener('mouseenter', () => {
            wishlist.classList.add('show');
            setTimeout(() => wishlist.classList.add('animate'), 10);
        });

        card.addEventListener('mouseleave', () => {
            wishlist.classList.remove('animate');
            wishlist.addEventListener('transitionend', function handler() {
                wishlist.classList.remove('show');
                wishlist.removeEventListener('transitionend', handler);
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const userAdmin = document.querySelector(".notification-clk");
        const menuAccount = document.querySelector(".notification");

        userAdmin.addEventListener("click", function(e) {
            e.preventDefault();
            menuAccount.classList.toggle("active");
        });

        document.addEventListener("click", function(e) {
            if (!menuAccount.contains(e.target) && !userAdmin.contains(e.target)) {
                menuAccount.classList.remove("active");
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const addToCartBtns = document.querySelectorAll(".cart-btn");
        const cartPanel = document.getElementById("cartPanel");
        const closeCart = document.getElementById("closeCart");

        if (!cartPanel.classList.contains('hidden') && !cartPanel.classList.contains('active')) {
            cartPanel.classList.add('hidden');
        }

        function showPanel() {
            cartPanel.classList.remove('hidden');
            void cartPanel.offsetWidth;
            cartPanel.classList.add('active');
        }

        function hidePanel() {
            if (cartPanel.classList.contains('hidden') || !cartPanel.classList.contains('active')) return;

            cartPanel.classList.remove('active');

            const onTransitionEnd = function(e) {
                if (e.target === cartPanel && (e.propertyName === 'transform' || e.propertyName === 'opacity')) {
                    cartPanel.classList.add('hidden');
                    cartPanel.removeEventListener('transitionend', onTransitionEnd);
                }
            };
            cartPanel.addEventListener('transitionend', onTransitionEnd);
        }

        addToCartBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                // showPanel();
            });
        });

        if (closeCart) {
            closeCart.addEventListener('click', (e) => {
                e.preventDefault();
                hidePanel();
            });
        }

        document.addEventListener('click', (e) => {
            if (cartPanel.classList.contains('hidden')) return;
            const clickedInside = cartPanel.contains(e.target);
            const clickedAddBtn = e.target.closest('.cart-btn');
            if (!clickedInside && !clickedAddBtn) {
                hidePanel();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !cartPanel.classList.contains('hidden')) {
                hidePanel();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const wrappers = document.querySelectorAll('.cart-toggle-wrapper');

        wrappers.forEach(wrapper => {
            const addBtn = wrapper.querySelector('.cart-btn');
            const cartHome = wrapper.querySelector('.cart-home');
            const deleteBtn = wrapper.querySelector('.cart-delete');
            const btnPlus = wrapper.querySelector('.btn-plus');
            const btnMinus = wrapper.querySelector('.btn-minus');
            const qtyInput = wrapper.querySelector('.quantity-value');

            if (!addBtn || !cartHome) return;

            addBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                wrapper.classList.add('active');
                cartHome.setAttribute('aria-hidden', 'false');
                if (btnPlus) btnPlus.focus();
            });

            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    wrapper.classList.remove('active');
                    cartHome.setAttribute('aria-hidden', 'true');
                });
            }

            document.addEventListener('click', (e) => {
                if (!wrapper.classList.contains('active')) return;

                if (wrapper.contains(e.target)) return;

                wrapper.classList.remove('active');
                cartHome.setAttribute('aria-hidden', 'true');
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && wrapper.classList.contains('active')) {
                    wrapper.classList.remove('active');
                    cartHome.setAttribute('aria-hidden', 'true');
                }
            });

            if (btnPlus && btnMinus && qtyInput) {
                btnPlus.addEventListener('click', (e) => {
                    e.stopPropagation();
                    let v = parseInt(qtyInput.value || '1', 10);
                    if (isNaN(v)) v = 1;
                    qtyInput.value = v + 1;
                });

                btnMinus.addEventListener('click', (e) => {
                    e.stopPropagation();
                    let v = parseInt(qtyInput.value || '1', 10);
                    if (isNaN(v)) v = 1;
                    if (v > 1) qtyInput.value = v - 1;
                });
            }
        });
    });
</script>


@stack('js')

</html>