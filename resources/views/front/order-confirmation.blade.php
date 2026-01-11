@extends('front.layout', [
    'metaInfo' => [
        'title' => 'Order Confirmation - ' . ($order->order_number ?? ''),
        'content' => 'Your order has been placed successfully',
    ],
])

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
@endpush

@section('content')
<section class="order-hero">
    <div class="odr-block bg-sky">
        <div class="odr-w-hero">
            <div class="text-center">
                <img src="{{ asset('front-theme/images/order-hero1.png') }}" alt="">
                <h1 class="h-48 mt-4">Thank You for Your Order!</h1>
                <p class="p-25 mt-3">Your wholesale order has been successfully placed and is being processed.</p>
            </div>
            <div class="odr-date-num">
                <div class="row">
                    <div class="col-lg-6 col-xl-6 col-md-6 col-sm-6 mr-576">
                        <p class="p-18">Order Number</p>
                        <p class="p-25">#{{ $order->order_number }}</p>
                    </div>
                    <div class="col-lg-6 col-xl-6 col-md-6 col-sm-6">
                        <p class="p-18">Order Date</p>
                        <p class="p-25">{{ $order->order_date->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="invoice-download d-flex justify-content-center gap-3">
                <a href="#" class="btn" onclick="window.print(); return false;">Download Invoice</a>
                @if(auth()->guard('customer')->check())
                <a href="#" class="btn"><img src="{{ asset('front-theme/images/order-view.png') }}" alt="">View My Orders</a>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="odr-sum">
    <div class="odr-sum-block">
        <div class="container-1200 container">
            <h2 class="h-30 mb-4">Order Summary</h2>
            <div class="row">
                <div class="col-lg-8 col-md-7">
                    <div class="odr-sm-left">
                        <div class="rd-ltop c-lft-box bdr-clr">
                            <h3 class="h-24 mb-4">Items Ordered ({{ $order->items->sum('quantity') }} items)</h3>
                            @foreach($order->items as $item)
                            <div class="odr-collection d-flex justify-content-between align-items-center gap-4 mb-3">
                                <div class="odr-clect-left d-flex justify-content-between align-items-center gap-4">
                                    <div>
                                        @php
                                            $product = $item->product;
                                            $variant = $item->variant;
                                        @endphp
                                        @if($item->product_type == 'variable' && $variant && $variant->variantImage)
                                            <img src="{{ asset('storage/' . $variant->variantImage->file) }}" alt="" style="width: 80px; height: 80px; object-fit: contain;">
                                        @elseif($product && $product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->file) }}" alt="" style="width: 80px; height: 80px; object-fit: contain;">
                                        @else
                                            <img src="{{ asset('front-theme/images/collection1.png') }}" alt="" style="width: 80px; height: 80px; object-fit: contain;">
                                        @endif
                                    </div>
                                    <div class="colcet-detail">
                                        <h3 class="h-24 mb-2">{{ $item->product_name }}</h3>
                                        @if($item->product_sku)
                                            <p class="p-18">SKU: {{ $item->product_sku }}</p>
                                        @endif
                                        @if($item->variant_name)
                                            <p class="p-18">Variant: {{ $item->variant_name }}</p>
                                        @endif
                                        <p class="p-18">Quantity: {{ number_format($item->quantity, 2) }} {{ $item->unit_name ?? 'Unit' }}</p>
                                    </div>
                                </div>
                                <div class="colect-rates">
                                    ${{ number_format($item->total, 2) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="oder-middle cart-summery bdr-clr crt-pading mt-30">
                            <h3 class="h-24 mb-4">Order Total</h3>
                            <ul>
                                <li>
                                    <span>Subtotal ({{ $order->items->sum('quantity') }} items)</span>
                                    <span>${{ number_format($order->subtotal, 2) }}</span>
                                </li>
                                @if($order->discount_amount > 0)
                                <li>
                                    <span>Bulk Discount</span>
                                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                </li>
                                @endif
                                <li>
                                    <span>Shipping</span>
                                    <span>${{ number_format($order->shipping_amount, 2) }}</span>
                                </li>
                                <li>
                                    <span>Tax Estimate</span>
                                    <span>${{ number_format($order->tax_amount, 2) }}</span>
                                </li>
                            </ul>
                            <div class="crt-total d-flex justify-content-between gap-2">
                                <p class="h-24">Total Paid</p>
                                <p class="h-24">${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="odr-right">
                        <div class="ord-shoping shop-info bdr-clr crt-pading mt-30">
                            <h2 class="h-24 mb-3">Shipping Information</h2>
                            <p class="addr-odr blue-20">Delivery Address</p>
                            <p>
                                {{ $order->location->name ?? ($order->customer->name ?? '') }}<br>
                                @if($order->location)
                                    {{ $order->location->address_line_1 }}<br>
                                    @if($order->location->address_line_2)
                                        {{ $order->location->address_line_2 }}<br>
                                    @endif
                                    {{ $order->location->city->name ?? '' }}, {{ $order->location->state->name ?? '' }} {{ $order->location->zipcode }}<br>
                                    {{ $order->location->country->name ?? '' }}
                                @else
                                    {{ $order->shipping_address_line_1 }}<br>
                                    @if($order->shipping_address_line_2)
                                        {{ $order->shipping_address_line_2 }}<br>
                                    @endif
                                    {{ $order->shipping_city_id ? \App\Models\City::find($order->shipping_city_id)->name ?? '' : '' }}, {{ $order->shipping_state_id ? \App\Models\State::find($order->shipping_state_id)->name ?? '' : '' }} {{ $order->shipping_zipcode }}<br>
                                    {{ $order->shipping_country_id ? \App\Models\Country::find($order->shipping_country_id)->name ?? '' : '' }}
                                @endif
                            </p>
                            <p class="mt-2">
                                <span class="blue-20">Phone</span> <br>
                                {{ $order->shipping_phone }}
                            </p>
                            <p class="mt-2">
                                <span class="blue-20">Email</span> <br>
                                {{ $order->shipping_email }}
                            </p>
                        </div>
                        <div class="ord-shoping method shop-info bdr-clr crt-pading mt-30">
                            <h2 class="h-24 mb-3">Delivery Method</h2>
                            <p><img class="me-2" src="{{ asset('front-theme/images/truct.svg') }}" alt="">Standard Shipping</p>
                            <div class="estimate-odr">
                                <h3 class="h-20">
                                    <img src="{{ asset('front-theme/images/clock.svg') }}" class="me-2" alt="">Estimated Delivery:
                                </h3>
                                <div>
                                    {{ $order->order_date->addDays(5)->format('M d') }} - {{ $order->order_date->addDays(7)->format('M d, Y') }}
                                </div>
                                <p class="p-16">Your order will be dispatched within 24 hours</p>
                            </div>
                        </div>
                        <div class="ord-shoping payment-method shop-info bdr-clr crt-pading mt-30">
                            <h2 class="h-24 mb-3">Payment Method</h2>
                            <div class="debit-odr">
                                @if($payment && $payment->payment_method == 'credit_card')
                                    <img src="{{ asset('front-theme/images/credit.png') }}" alt="">
                                    <span class="h-24">Credit/Debit Card</span>
                                    @if(isset($payment->gateway_response['card_last_four']))
                                        <p class="odr-num p-16">**** **** **** {{ $payment->gateway_response['card_last_four'] }}</p>
                                    @endif
                                @else
                                    <span class="h-24">Cash on Delivery</span>
                                @endif
                                <p class="p-18 my-2">
                                    @if($payment && $payment->status == 'completed')
                                        Payment processed successfully
                                    @elseif($payment && $payment->status == 'pending')
                                        Payment pending
                                    @else
                                        Payment will be collected on delivery
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="odr-status">
        <div class="odr-status-block">
            <div class="container-1200 container">
                <h2 class="h-30 text-center">Order Status</h2>
                <div class="status-box">
                    <div class="my-5">
                        <div class="box-track">
                            <div class="order-tracker d-flex justify-content-between align-items-center text-center">
                                <div class="step active">
                                    <div class="icon">
                                        <img src="{{ asset('front-theme/images/status-1.svg') }}" alt="">
                                    </div>
                                    <p class="title">Order Placed</p>
                                    <p class="small">{{ $order->order_date->format('M d') }}</p>
                                </div>
                                <div class="line"></div>
                                <div class="step {{ in_array($order->status, ['confirmed', 'processing', 'packed', 'shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="icon">
                                        <img src="{{ asset('front-theme/images/status-2.svg') }}" alt="">
                                    </div>
                                    <p class="title">Processing</p>
                                    <p class="small">{{ in_array($order->status, ['confirmed', 'processing', 'packed', 'shipped', 'delivered']) ? 'In Progress' : 'Pending' }}</p>
                                </div>
                                <div class="line"></div>
                                <div class="step {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="icon">
                                        <img src="{{ asset('front-theme/images/status-3.svg') }}" alt="">
                                    </div>
                                    <p class="title">Shipping</p>
                                    <p class="small">{{ in_array($order->status, ['shipped', 'delivered']) ? 'Shipped' : 'Pending' }}</p>
                                </div>
                                <div class="line"></div>
                                <div class="step {{ $order->status == 'delivered' ? 'active' : '' }}">
                                    <div class="icon">
                                        <img src="{{ asset('front-theme/images/status-4.svg') }}" alt="">
                                    </div>
                                    <p class="title">Delivered</p>
                                    <p class="small">{{ $order->status == 'delivered' ? 'Delivered' : 'Pending' }}</p>
                                </div>
                            </div>
                            <div class="proces-odr text-center">
                                <p class="h-24 mb-2">Your order is being processed</p>
                                <p>We'll send you tracking information once your order ships</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="next-odr">
    <div class="next-block pd-65">
        <div class="container-1200 container">
            <h2 class="h-30 text-center">What's Next?</h2>
            <div class="next-box">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="next_inbox c-lft-box bdr-clr">
                            <div class="nxt-icon">
                                <img src="{{ asset('front-theme/images/next-1.svg') }}" alt="">
                            </div>
                            <h3 class="h-24 mb-3 mt-3">Email Confirmation</h3>
                            <p class="p-18">A detailed order confirmation has been sent to your email address</p>
                            @if(auth()->guard('customer')->check())
                            <a href="#" class="btn next-btn">View My Orders</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="next_inbox c-lft-box bdr-clr">
                            <div class="nxt-icon">
                                <img src="{{ asset('front-theme/images/next-2.svg') }}" alt="">
                            </div>
                            <h3 class="h-24 mb-3 mt-3">Order Updates</h3>
                            <p class="p-18">We'll notify you when your order ships and provide tracking information</p>
                            @if(auth()->guard('customer')->check())
                            <a href="#" class="btn next-btn">View My Orders</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="next_inbox c-lft-box bdr-clr">
                            <div class="nxt-icon">
                                <img src="{{ asset('front-theme/images/next-3.svg') }}" alt="">
                            </div>
                            <h3 class="h-24 mb-3 mt-3">Need Help?</h3>
                            <p class="p-18">Our customer service team is here to help with any questions</p>
                            @if(auth()->guard('customer')->check())
                            <a href="#" class="btn next-btn">View My Orders</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.order-tracker .step').each(function(index) {
            if ($(this).hasClass('active')) {
                $(this).prevAll('.line').addClass('active');
            }
        });
    });
</script>
@endpush

