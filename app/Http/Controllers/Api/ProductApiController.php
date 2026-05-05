<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Batch;

class ProductApiController extends Controller
{
    /**
     * 🔍 Live Search Product
     */
    public function search(Request $request)
    {
        $query = trim($request->get('search', $request->get('query', '')));
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('product_code', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'product_code', 'unit']);

        return response()->json($products);
    }

    /**
     * 📦 Get all available batches for a specific product
     */
    public function getBatches(Product $product)
    {
        $batches = Batch::where('product_id', $product->id)
            ->where('current_quantity', '>', 0)
            ->orderBy('expiration_date') // ✅ ใช้ expiration_date
            ->get(['id', 'batch_number', 'expiration_date', 'current_quantity']);

        $total_stock = $batches->sum('current_quantity');

        return response()->json([
            'unit_name' => $product->unit ?? '', // ✅ ป้องกัน null
            'total_stock' => $total_stock,
            'batches' => $batches,
        ]);
    }
}
