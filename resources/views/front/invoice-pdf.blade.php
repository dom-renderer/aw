<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.5;
            background-color: #fff;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 30px;
        }
        .header-section {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .order-header {
            margin-bottom: 15px;
        }
        .order-header h1 {
            font-size: 18px;
            font-weight: 400;
            color: #111;
            margin-bottom: 8px;
        }
        .order-meta {
            font-size: 11px;
            color: #565959;
            margin-bottom: 5px;
        }
        .order-meta strong {
            color: #111;
            font-weight: 700;
        }
        .address-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .address-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .address-box:last-child {
            padding-right: 0;
            padding-left: 20px;
        }
        .address-title {
            font-size: 11px;
            font-weight: 700;
            color: #111;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .address-content {
            font-size: 11px;
            color: #111;
            line-height: 1.6;
        }
        .address-content p {
            margin: 2px 0;
        }
        .items-section {
            margin-bottom: 25px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        .items-table thead {
            background-color: #f3f3f3;
        }
        .items-table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #111;
            border-bottom: 1px solid #ddd;
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table th.text-center {
            text-align: center;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            color: #111;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .items-table tbody tr:hover {
            background-color: #fafafa;
        }
        .item-name {
            font-weight: 700;
            color: #111;
            margin-bottom: 3px;
        }
        .item-details {
            font-size: 10px;
            color: #565959;
            margin-top: 3px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-section {
            margin-top: 20px;
            margin-left: auto;
            width: 350px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 0;
            font-size: 11px;
            color: #111;
        }
        .summary-table td:first-child {
            text-align: left;
            color: #565959;
        }
        .summary-table td:last-child {
            text-align: right;
            color: #111;
        }
        .summary-table .total-row {
            border-top: 2px solid #111;
            padding-top: 12px;
            margin-top: 8px;
        }
        .summary-table .total-row td {
            font-weight: 700;
            font-size: 13px;
            color: #111;
        }
        .payment-section {
            margin-top: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .payment-section h3 {
            font-size: 12px;
            font-weight: 700;
            color: #111;
            margin-bottom: 10px;
        }
        .payment-section p {
            font-size: 11px;
            color: #111;
            margin: 4px 0;
        }
        .payment-section strong {
            color: #111;
        }
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .notes-section h3 {
            font-size: 12px;
            font-weight: 700;
            color: #111;
            margin-bottom: 8px;
        }
        .notes-section p {
            font-size: 11px;
            color: #111;
        }
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .footer-section p {
            font-size: 10px;
            color: #565959;
            margin: 5px 0;
        }
        .footer-section strong {
            color: #111;
        }
        .status-info {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 3px;
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
        .status-shipped {
            background-color: #d4edda;
            color: #155724;
        }
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header-section">
            <div class="order-header">
                <h1>Order Confirmation</h1>
                <div class="order-meta">
                    <strong>Order Number:</strong> {{ $order->order_number }}<br>
                    {{-- <strong>Order Date:</strong> {{ $order->order_date->format('F d, Y') }}<br> --}}
                    <strong>Order Status:</strong> <span class="status-info status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    @if($payment)
                    <br><strong>Payment Status:</strong> <span class="status-info status-{{ $payment->status === 'completed' ? 'confirmed' : 'pending' }}">{{ ucfirst($payment->status) }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="address-section">
            <div class="address-box">
                <div class="address-title">Ship To</div>
                <div class="address-content">
                    <p><strong>{{ $order->customer->name ?? 'N/A' }}</strong></p>
                    @if($order->location)
                        <p>{{ $order->location->address_line_1 }}</p>
                        @if($order->location->address_line_2)
                            <p>{{ $order->location->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->location->city->name ?? '' }}, {{ $order->location->state->name ?? '' }} {{ $order->location->zipcode }}</p>
                        <p>{{ $order->location->country->name ?? '' }}</p>
                        <p style="margin-top: 8px;">Phone: {{ $order->location->contact_number }}</p>
                        <p>Email: {{ $order->location->email }}</p>
                    @else
                        <p>{{ $order->shipping_address_line_1 }}</p>
                        @if($order->shipping_address_line_2)
                            <p>{{ $order->shipping_address_line_2 }}</p>
                        @endif
                        <p>{{ $order->shipping_city_id ? \App\Models\City::find($order->shipping_city_id)->name ?? '' : '' }}, {{ $order->shipping_state_id ? \App\Models\State::find($order->shipping_state_id)->name ?? '' : '' }} {{ $order->shipping_zipcode }}</p>
                        <p>{{ $order->shipping_country_id ? \App\Models\Country::find($order->shipping_country_id)->name ?? '' : '' }}</p>
                        <p style="margin-top: 8px;">Phone: {{ $order->shipping_phone }}</p>
                        <p>Email: {{ $order->shipping_email }}</p>
                    @endif
                </div>
            </div>
            <div class="address-box">
                <div class="address-title">Bill To</div>
                <div class="address-content">
                    <p><strong>{{ $order->customer->name ?? 'N/A' }}</strong></p>
                    @if($order->location)
                        <p>{{ $order->location->address_line_1 }}</p>
                        @if($order->location->address_line_2)
                            <p>{{ $order->location->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->location->city->name ?? '' }}, {{ $order->location->state->name ?? '' }} {{ $order->location->zipcode }}</p>
                        <p>{{ $order->location->country->name ?? '' }}</p>
                        <p style="margin-top: 8px;">Phone: {{ $order->location->contact_number }}</p>
                        <p>Email: {{ $order->location->email }}</p>
                    @else
                        <p>{{ $order->billing_address_line_1 }}</p>
                        @if($order->billing_address_line_2)
                            <p>{{ $order->billing_address_line_2 }}</p>
                        @endif
                        <p>{{ $order->billing_city_id ? \App\Models\City::find($order->billing_city_id)->name ?? '' : '' }}, {{ $order->billing_state_id ? \App\Models\State::find($order->billing_state_id)->name ?? '' : '' }} {{ $order->billing_zipcode }}</p>
                        <p>{{ $order->billing_country_id ? \App\Models\Country::find($order->billing_country_id)->name ?? '' : '' }}</p>
                        <p style="margin-top: 8px;">Phone: {{ $order->billing_phone }}</p>
                        <p>Email: {{ $order->billing_email }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            <div class="item-details">
                                @if($item->product_sku)
                                    SKU: {{ $item->product_sku }}
                                @endif
                                @if($item->variant_name)
                                    <br>Variant: {{ $item->variant_name }}
                                @endif
                                <br>Unit: {{ $item->unit_name ?? 'Unit' }}
                            </div>
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">${{ number_format($item->price_per_unit, 2) }}</td>
                        <td class="text-right"><strong>${{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td>Items Subtotal:</td>
                    <td class="text-right">${{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Shipping & Handling:</td>
                    <td class="text-right">${{ number_format($order->shipping_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">${{ number_format($order->tax_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Grand Total:</td>
                    <td class="text-right">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @if($payment)
                <tr>
                    <td>Amount Paid:</td>
                    <td class="text-right">${{ number_format($order->paid_amount, 2) }}</td>
                </tr>
                @if($order->due_amount > 0)
                <tr>
                    <td>Amount Due:</td>
                    <td class="text-right">${{ number_format($order->due_amount, 2) }}</td>
                </tr>
                @endif
                @endif
            </table>
        </div>

        @if($payment)
        <div class="payment-section">
            <h3>Payment Information</h3>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            @if($payment->payment_gateway)
                <p><strong>Payment Gateway:</strong> {{ $payment->payment_gateway }}</p>
            @endif
            @if($payment->transaction_id)
                <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
            @endif
            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('F d, Y g:i A') }}</p>
        </div>
        @endif

        @if($order->customer_notes)
        <div class="notes-section">
            <h3>Order Notes</h3>
            <p>{{ $order->customer_notes }}</p>
        </div>
        @endif

        <div class="footer-section">
            <p><strong>Anjo Wholesale</strong></p>
            <p>American Road St. John's, Antigua & Barbuda</p>
            <p>Phone: (268) 480-3080 | Email: info@anjowholesale.com</p>
            <p style="margin-top: 15px;">Thank you for your business!</p>
            <p style="font-size: 9px; color: #767676; margin-top: 10px;">This is a computer-generated invoice. No signature required.</p>
        </div>
    </div>
</body>
</html>
