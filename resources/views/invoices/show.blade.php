@extends('layouts.app_with_sidebar')

@section('content')
<div class="container mt-4">

    <h3>Invoice Details</h3>

    <div class="card p-3 mb-3">
        <p><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
        <p><strong>Date:</strong> {{ $invoice->invoice_date }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-end mt-3">
        <p><strong>Subtotal:</strong> {{ $invoice->sub_total }}</p>
        <p><strong>Tax:</strong> {{ $invoice->tax }}</p>
        <p><strong>Discount:</strong> {{ $invoice->discount }}</p>
        <h4><strong>Grand Total:</strong> {{ $invoice->grand_total }}</h4>
    </div>

    <a href="{{ route('invoices.index') }}" class="btn btn-secondary mt-3">
        Back
    </a>

</div>
@endsection
