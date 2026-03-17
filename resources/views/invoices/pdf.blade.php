<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>

    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <h2>INVOICE</h2>

    <p>
        <strong>Invoice No:</strong> {{ $invoice->invoice_number }} <br>
        <strong>Date:</strong> {{ $invoice->invoice_date }} <br>
        <strong>Customer:</strong> {{ $invoice->customer_name }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="text-right"><strong>Sub Total</strong></td>
            <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Tax</strong></td>
            <td class="text-right">{{ number_format($invoice->tax, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Discount</strong></td>
            <td class="text-right">{{ number_format($invoice->discount, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Grand Total</strong></td>
            <td class="text-right"><strong>{{ number_format($invoice->grand_total, 2) }}</strong></td>
        </tr>
    </table>

</body>
</html>
