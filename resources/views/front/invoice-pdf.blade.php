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
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .company-info h1 {
            font-size: 24px;
            color: #1a73e8;
            margin-bottom: 10px;
        }
        .company-info p {
            margin: 3px 0;
            color: #666;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-info p {
            margin: 3px 0;
        }
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .billing-box, .shipping-box {
            width: 48%;
        }
        .billing-box h3, .shipping-box h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .billing-box p, .shipping-box p {
            margin: 3px 0;
            color: #666;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #1a73e8;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            margin-top: 20px;
            margin-left: auto;
            width: 300px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals-table td:first-child {
            text-align: left;
            font-weight: bold;
        }
        .totals-table td:last-child {
            text-align: right;
        }
        .totals-table .total-row {
            background-color: #1a73e8;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .totals-table .total-row td {
            border-bottom: none;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #ffc107;
            color: #333;
        }
        .status-confirmed {
            background-color: #17a2b8;
            color: white;
        }
        .status-processing {
            background-color: #007bff;
            color: white;
        }
        .status-shipped {
            background-color: #28a745;
            color: white;
        }
        .status-delivered {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>Anjo Wholesale</h1>
                <p>American Road St. John's, Antigua & Barbuda</p>
                <p>Phone: (268) 480-3080</p>
                <p>Email: info@anjowholesale.com</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                {{-- <p><strong>Order Date:</strong> {{ $order->order_date->format('M d, Y') }}</p> --}}
                <p><strong>Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
                @if($payment)
                <p><strong>Payment Status:</strong> <span class="status-badge status-{{ $payment->status === 'completed' ? 'confirmed' : 'pending' }}">{{ ucfirst($payment->status) }}</span></p>
                @endif
            </div>
        </div>

        <div class="billing-section">
            <div class="billing-box">
                <h3>Bill To:</h3>
                <p><strong>{{ $order->customer->name ?? 'N/A' }}</strong></p>
                @if($order->location)
                    <p>{{ $order->location->address_line_1 }}</p>
                    @if($order->location->address_line_2)
                        <p>{{ $order->location->address_line_2 }}</p>
                    @endif
                    <p>{{ $order->location->city->name ?? '' }}, {{ $order->location->state->name ?? '' }} {{ $order->location->zipcode }}</p>
                    <p>{{ $order->location->country->name ?? '' }}</p>
                    <p>Phone: {{ $order->location->contact_number }}</p>
                    <p>Email: {{ $order->location->email }}</p>
                @else
                    <p>{{ $order->billing_address_line_1 }}</p>
                    @if($order->billing_address_line_2)
                        <p>{{ $order->billing_address_line_2 }}</p>
                    @endif
                    <p>{{ $order->billing_city_id ? \App\Models\City::find($order->billing_city_id)->name ?? '' : '' }}, {{ $order->billing_state_id ? \App\Models\State::find($order->billing_state_id)->name ?? '' : '' }} {{ $order->billing_zipcode }}</p>
                    <p>{{ $order->billing_country_id ? \App\Models\Country::find($order->billing_country_id)->name ?? '' : '' }}</p>
                    <p>Phone: {{ $order->billing_phone }}</p>
                    <p>Email: {{ $order->billing_email }}</p>
                @endif
            </div>
            <div class="shipping-box">
                <h3>Ship To:</h3>
                @if($order->location)
                    <p><strong>{{ $order->location->name }}</strong></p>
                    <p>{{ $order->location->address_line_1 }}</p>
                    @if($order->location->address_line_2)
                        <p>{{ $order->location->address_line_2 }}</p>
                    @endif
                    <p>{{ $order->location->city->name ?? '' }}, {{ $order->location->state->name ?? '' }} {{ $order->location->zipcode }}</p>
                    <p>{{ $order->location->country->name ?? '' }}</p>
                    <p>Phone: {{ $order->location->contact_number }}</p>
                @else
                    <p>{{ $order->shipping_address_line_1 }}</p>
                    @if($order->shipping_address_line_2)
                        <p>{{ $order->shipping_address_line_2 }}</p>
                    @endif
                    <p>{{ $order->shipping_city_id ? \App\Models\City::find($order->shipping_city_id)->name ?? '' : '' }}, {{ $order->shipping_state_id ? \App\Models\State::find($order->shipping_state_id)->name ?? '' : '' }} {{ $order->shipping_zipcode }}</p>
                    <p>{{ $order->shipping_country_id ? \App\Models\Country::find($order->shipping_country_id)->name ?? '' : '' }}</p>
                    <p>Phone: {{ $order->shipping_phone }}</p>
                @endif
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>SKU</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->variant_name)
                            <br><small>Variant: {{ $item->variant_name }}</small>
                        @endif
                        <br><small>Unit: {{ $item->unit_name ?? 'Unit' }}</small>
                    </td>
                    <td>{{ $item->product_sku ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">${{ number_format($item->price_per_unit, 2) }}</td>
                    <td class="text-right">${{ number_format($item->discount_amount, 2) }}</td>
                    <td class="text-right">${{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right"><strong>${{ number_format($item->total, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">${{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Shipping:</td>
                    <td class="text-right">${{ number_format($order->shipping_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">${{ number_format($order->tax_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total:</td>
                    <td class="text-right">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @if($payment)
                <tr>
                    <td>Paid Amount:</td>
                    <td class="text-right">${{ number_format($order->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Due Amount:</td>
                    <td class="text-right">${{ number_format($order->due_amount, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($payment)
        <div style="margin-top: 30px; padding: 15px; background-color: #f9f9f9; border-radius: 5px;">
            <h3 style="margin-bottom: 10px; font-size: 14px;">Payment Information</h3>
            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            <p><strong>Payment Gateway:</strong> {{ $payment->payment_gateway }}</p>
            @if($payment->transaction_id)
                <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
            @endif
            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('M d, Y H:i') }}</p>
        </div>
        @endif

        @if($order->customer_notes)
        <div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <h3 style="margin-bottom: 10px; font-size: 14px;">Customer Notes:</h3>
            <p>{{ $order->customer_notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice. No signature required.</p>
            <p>For any inquiries, please contact us at info@anjowholesale.com or call (268) 480-3080</p>
        </div>
    </div>
</body>
</html>

