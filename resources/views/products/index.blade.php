@extends('layouts.app_with_sidebar')

@section('title', 'product list')

@section('content')
    <div class=" w-full p-6">

        <h2 class="text-xl font-bold mb-4">Product List</h2>

        <div class="flex justify-between items-center mb-4">

            <!-- Left Button -->
            <a href="{{ route('products.create') }}"
            class="bg-green-500 text-white px-3 py-2 rounded">
                Add New Product
            </a>

            <!-- Right Button -->
            <button onclick="exportFilteredData()"
                class="bg-green-500 text-white px-3 py-2 rounded">
                Export Filtered Records
            </button>

        </div>



        <!-- Updated Table with table-auto -->
        <div class="overflow-x-auto w-full mt-4">
            <table id="productsTable" class="table-auto w-full border display">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Quantity</th>
                        <th class="border px-4 py-2">Price</th>
                        <th class="border px-4 py-2">Action</th>
                    </tr>
                </thead>

                <tbody id="productTableBody">
                    @foreach($products as $product)
                        <tr id="row_{{ $product->id }}">
                            <td class="border px-4 py-2">{{ $product->id }}</td>
                            <td class="border px-4 py-2">{{ $product->name }}</td>
                            <td class="border px-4 py-2">{{ $product->quantity }}</td>
                            <td class="border px-4 py-2">{{ $product->price }}</td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('products.edit', $product->id) }}"
                                class="bg-yellow-500 text-black px-2 py-1 rounded">
                                    Edit
                                </a>

                                <button onclick="deleteProduct({{ $product->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded">
                                    Delete
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>

    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function () {
            $('#productsTable').DataTable();
        });

        function deleteProduct(id){
            if(confirm("Are you sure to delete?")){

                $.ajax({
                    url: "/products/" + id,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(){
                        alert("Deleted Successfully");
                        location.reload();
                    }
                });

            }
        }

        function exportFilteredData() {
            let table = $('#productsTable').DataTable();

            let filteredRows = table.rows({ filter: 'applied' }).data();

            let ids = [];

            filteredRows.each(function (row) {
                ids.push(row[0]); // Assuming column 0 contains ID
            });

            if (ids.length === 0) {
                alert("No filtered records found.");
                return;
            }

            // Redirect server with filtered IDs
            window.location.href = "/products/export?ids=" + ids.join(",");
        }

    </script>

@endsection
