@extends('layouts.app_with_sidebar')
@section('title', 'Add products')
@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 shadow-md rounded">

            <h2 class="text-xl font-bold mb-4">Add New Product</h2>

            <form id="productForm">
                @csrf

                <div>
                    <label class="font-semibold">Name:</label>
                    <input type="text" name="name" id="name" class="border p-2 w-full">
                </div>

                <div class="mt-3">
                    <label class="font-semibold">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" class="border p-2 w-full">
                </div>

                <div class="mt-3">
                    <label class="font-semibold">Price:</label>
                    <input type="text" name="price" id="price" class="border p-2 w-full">
                </div>

                <div class="mt-4">
                    <button type="button" id="saveProductBtn" class="bg-blue-500 px-3 py-2 text-black rounded">
                        Save Product
                    </button>
                </div>
            </form>

    </div>
    
    <script>
        $(document).ready(function(){

            // Custom rule for letters only
            $.validator.addMethod("lettersOnly", function(value) {
                return /^[A-Za-z\s]+$/.test(value);
            }, "Only letters allowed");

            $.validator.addMethod("decimal", function(value, element) {
                return this.optional(element) || /^[0-9]+(\.[0-9]+)?$/.test(value);
            }, "Enter a valid number");



            // Apply validation rules
            $("#productForm").validate({
                rules: {
                    name: {
                        required: true,
                        lettersOnly: true
                    },
                    quantity: {
                        required: true,
                        digits: true,
                        min: 1
                    },
                    price: {
                        required: true,
                        decimal: true
                    }
                },

                messages: {
                    name: {
                        required: "Name is required",
                        lettersOnly: "Only alphabets allowed"
                    },
                    quantity: {
                        required: "Quantity is required",
                        digits: "Enter a valid number",
                        min: "Minimum value is 10"
                    },
                    price: {
                        required: "Price is required",
                        digits: "Only numbers allowed"
                    }
                }
            });

            // AJAX save button
            $("#saveProductBtn").click(function(e){
                e.preventDefault();

                // validate before AJAX
                if($("#productForm").valid()){

                    $.ajax({
                        url: "{{ route('products.store') }}",
                        type: "POST",
                        data: {
                            name: $("#name").val(),
                            quantity: $("#quantity").val(),
                            price: $("#price").val(),
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            alert(response.message);
                            window.location.href = "{{ route('products.index') }}";
                        }
                    });
                }
            });

        });
    </script>
@endsection