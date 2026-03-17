@extends('layouts.app_with_sidebar')

@section('title', 'Edit Invoice')

@section('content')

<div class="container mt-4">

    <h2>Edit Invoice</h2>

    <!--<form id="invoiceForm">
        @csrf-->
    <form method="POST" id="invoiceForm">
        @csrf
        @method('PUT') 
        <!-- Invoice Header -->
        <input type="hidden" id="invoice_id" value="{{ $invoice->id }}">
        <div class="card p-3 mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label>Customer Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $invoice->customer_name }}">
                </div>

                <div class="col-md-4">
                    <label>Invoice Date</label>
                    <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{ $invoice->invoice_date }}">
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="card p-3">
            <h5>Products</h5>

            <table class="table table-bordered" id="productTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th width="120">Qty</th>
                        <th width="120">Price</th>
                        <th width="120">Total</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                @foreach($invoice->items as $item)
                    <tbody>
                        <tr>
                            <td><input type="text" name="product_name[]" class="form-control product_name" value="{{ $item->product_name }}"></td>
                            <td><input type="text" name="description[]" class="form-control" value="{{ $item->description }}"></td>
                            <td><input type="number" name="qty[]" class="form-control qty" value="{{ $item->quantity }}"></td>
                            <td><input type="number" name="price[]" class="form-control price" value="{{ $item->price }}"></td>
                            <td><input type="number" name="total[]" class="form-control total" readonly value="{{ $item->total }}"></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            </table>

            <button class="btn btn-primary btn-sm" id="addRow" type="button">
                + Add Product
            </button>
        </div>

        <!-- Summary -->
         
        <div class="card p-3 mt-4">
            <div class="row">
                <div class="col-md-4 offset-md-8">

                    <div class="mb-2">
                        <label>Subtotal</label>
                        <input type="text" class="form-control" name="sub_total" id="subTotal" readonly>
                    </div>

                    <div class="mb-2">
                        <label>Tax (%)</label>
                        <input type="number" class="form-control" name="tax" id="tax" value="0">
                    </div>

                    <div class="mb-2">
                        <label>Discount</label>
                        <input type="number" class="form-control" name="discount" id="discount" value="0">
                    </div>

                    <div class="mb-2">
                        <label>Grand Total</label>
                        <input type="text" class="form-control" name="grand_total" id="grandTotal" readonly>
                    </div>

                    <button type="submit" class="btn btn-success mt-3">
                        Update Invoice
                    </button>

                </div>
            </div>
        </div>

    </form>
</div>


@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            //console.log('Invoice JS loaded');
            //calculateGrandTotal();
            // Add new row
            $('#addRow').click(function(){
                let row = `
                <tr>
                    <td>
                        <input type="text" name="product_name[]" class="form-control product_name">
                    </td>
                    <td>
                        <input type="text" name="description[]" class="form-control">
                    </td>
                    <td>
                        <input type="number" name="qty[]" class="form-control qty">
                    </td>
                    <td>
                        <input type="number" name="price[]" class="form-control price">
                    </td>
                    <td>
                        <input type="number" name="total[]" class="form-control total" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                    </td>
                </tr>
                `;

                $('#productTable tbody').append(row);
            });

            // Remove row
            $(document).on('click', '.removeRow', function(){
                $(this).closest('tr').remove();
                calculateTotals();
            });

            // Qty or Price change
            $(document).on('input', '.qty, .price, #tax, #discount', function(){
                calculateTotals();
            });

            function calculateTotals() {
                let subTotal = 0;

                $('#productTable tbody tr').each(function () {

                    let qty = parseFloat($(this).find('.qty').val());
                    let price = parseFloat($(this).find('.price').val());

                    qty = isNaN(qty) ? 0 : qty;
                    price = isNaN(price) ? 0 : price;

                    let rowTotal = qty * price;

                    $(this).find('.total').val(rowTotal.toFixed(2));
                    subTotal += rowTotal;
                });

                $('#subTotal').val(subTotal.toFixed(2));

                let taxPercent = parseFloat($('#tax').val()) || 0;
                let discount = parseFloat($('#discount').val()) || 0;

                let taxAmount = (subTotal * taxPercent) / 100;
                let grandTotal = subTotal + taxAmount - discount;

                $('#grandTotal').val(grandTotal.toFixed(2));
            }


            $('#invoiceForm').submit(function(e){

                e.preventDefault();
                e.stopImmediatePropagation();
                let valid = true;
                $('.text-danger').remove();

                // Customer Name
                if($('#customer_name').val() == ''){
                    $('#customer_name').after('<span class="text-danger">Customer name required</span>');
                    valid = false;
                }

                // Invoice Date
                if($('#invoice_date').val() == ''){
                    $('#invoice_date').after('<span class="text-danger">Invoice date required</span>');
                    valid = false;
                }

                // Product rows count
                if($('#productTable tbody tr').length == 0){
                    alert('Please add at least one product');
                    valid = false;
                }

                // Validate each product row
                $('#productTable tbody tr').each(function(){

                    let product = $(this).find('.product_name');
                    let qty = $(this).find('.qty');
                    let price = $(this).find('.price');

                    if(product.val() == ''){
                        product.after('<span class="text-danger">Required</span>');
                        valid = false;
                    }

                    if(qty.val() == '' || qty.val() <= 0){
                        qty.after('<span class="text-danger">Invalid Qty</span>');
                        valid = false;
                    }

                    if(price.val() == '' || price.val() < 0){
                        price.after('<span class="text-danger">Invalid Price</span>');
                        valid = false;
                    }

                });

                if(!valid){
                    e.preventDefault(); // Stop submit
                }

                $.ajax({
                    url: "/invoices/" + $('#invoice_id').val(),
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res){
                        if(res.status){
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: res.message
                            }).then(() => {
                                window.location.href = "{{ route('invoices.index') }}";
                            });
                        }
                    },

                    //error: function(){
                    //    Swal.fire('Error', 'Something went wrong!', 'error');
                    //}

                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            console.log(errors);
                        } else {
                            Swal.fire('Error', 'Server error occurred', 'error');
                        }
                    }
                });

            });

        });
    </script>
@endpush
