@extends('layouts.app_with_sidebar')

@section('title', 'Invoice List')

@section('content')

<h3 class="mb-3">Invoice List</h3>

<a href="{{ route('invoices.create') }}" class="btn btn-primary mb-3">
    Create Invoice
</a>

<table class="table table-bordered" id="invoiceTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

@endsection

@push('scripts')
<script>
$(function () {
    $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('invoices.datatable') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false },
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'grand_total', name: 'grand_total' },
            { data: 'invoice_date', name: 'invoice_date' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush
