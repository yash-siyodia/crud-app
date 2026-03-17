<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET: Product list
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => ProductResource::collection(
                Product::latest()->get()
            )
        ]);
    }

    // POST: Create product
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price'    => 'required|numeric|min:0',
        ]);

        $product = Product::create($request->only([
            'name', 'quantity', 'price'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ], 201);
    }

    // GET: Single product
    public function show(Product $product)
    {
        return response()->json([
            'status' => true,
            'data' => new ProductResource($product)
        ]);
    }

    // PUT: Update product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'     => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price'    => 'required|numeric|min:0',
        ]);

        $product->update($request->only([
            'name', 'quantity', 'price'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product)
        ]);
    }

    // DELETE: Soft delete
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
