<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
    $product = Product::findOrFail($id);
    return view('products.show', compact('product'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());

        // Log product creation
        ActivityLogService::logCrud('created', 'Product', $product);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added successfully',
            'product' => $product
        ]);
    }



    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $oldData = $product->only(['name', 'quantity', 'price']);

        $product->update([
            'name'     => $request->name,
            'quantity' => $request->quantity,
            'price'    => $request->price,
        ]);

        // Log product update
        $changes = array_diff_assoc($request->only(['name', 'quantity', 'price']), $oldData);
        ActivityLogService::logCrud('updated', 'Product', $product, $changes ? ['changes' => $changes] : null);

        return response()->json(['success' => true, 'message' => 'Product Updated']);
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        
        // Log product deletion
        ActivityLogService::logCrud('deleted', 'Product', $product);
        
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product Deleted']);
    }

    public function exportExcel(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;

        return Excel::download(new ProductsExport($ids), 'products.xlsx');
    }

}
