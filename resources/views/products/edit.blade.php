<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <style>
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">

    {{-- Optional navigation --}}
    @include('layouts.navigation')

    <div class="max-w-3xl mx-auto bg-white p-6 shadow-md rounded">

        <h2 class="text-xl font-bold mb-4">Edit Product</h2>

        <form id="updateProductForm" action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="font-semibold">Name:</label>
                <input type="text" name="name" id="name" value="{{ $product->name }}" class="border p-2 w-full">
            </div>

            <div class="mt-3">
                <label class="font-semibold">Quantity:</label>
                <input type="number" name="quantity" id="quantity" value="{{ $product->quantity }}" class="border p-2 w-full">
            </div>

            <div class="mt-3">
                <label class="font-semibold">Price:</label>
                <input type="text" name="price" id="price" value="{{ $product->price }}" class="border p-2 w-full">
            </div>

            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-black px-3 py-2 rounded">
                    Update
                </button>
            </div>
        </form>

    </div>

    <script>
        $(document).ready(function(){

            // Custom rule for alphabets only
            $.validator.addMethod("lettersOnly", function(value) {
                return /^[A-Za-z\s]+$/.test(value);
            }, "Only letters allowed");

            $.validator.addMethod("decimal", function(value, element) {
                return this.optional(element) || /^[0-9]+(\.[0-9]+)?$/.test(value);
            }, "Enter a valid number");


            // Apply jQuery validation
            $("#updateProductForm").validate({

                rules: {
                    name: {
                        required: true,
                        lettersOnly: true
                    },
                    quantity: {
                        required: true,
                        digits: true,
                        min: 10
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
                },

                // If validation passes → run AJAX
                submitHandler: function(form){
                    var $form = $(form);
                    var actionUrl = $form.attr("action");

                    $.ajax({
                        url: actionUrl,
                        type: "POST",
                        data: $form.serialize(),
                        success: function(response){
                            alert(response.message);
                            window.location.href = "{{ route('products.index') }}";
                        },
                        error:function(xhr){
                            console.log(xhr.responseText);
                        }
                    });
                }
            });

        });
    </script>




</body>
</html>
