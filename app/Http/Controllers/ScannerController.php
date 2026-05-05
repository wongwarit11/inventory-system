<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    public function index(Request $request)
    {
        $product = null;
        $productId = $request->query('product_id');
        if ($productId) {
            $product = Product::with('batches')->find($productId);
        }

        return view('scanner.index', compact('product'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        $barcode = trim($request->input('barcode'));

        $product = Product::with(['batches.location'])->where('product_code', $barcode)->first();
        if ($product) {
            $stock = $product->batches->sum('quantity');
            $locations = $product->batches->pluck('location.full_location')->filter()->unique()->values();
            $locationText = $locations->count() ? $locations->implode(', ') : 'ยังไม่กำหนด';

            return response()->json([
                'type' => 'product',
                'id' => $product->id,
                'code' => $product->product_code,
                'name' => $product->name,
                'unit' => $product->unit,
                'stock' => $stock,
                'location' => $locationText,
                'batch_id' => null,
                'batch_number' => null,
            ]);
        }

        $batch = Batch::with('product', 'location')->where('batch_number', $barcode)->first();
        if ($batch) {
            $location = $batch->location ? "{$batch->location->full_location}" : 'ยังไม่กำหนด';
            return response()->json([
                'type' => 'batch',
                'id' => $batch->id,
                'code' => $batch->batch_number,
                'name' => $batch->product->name ?? null,
                'product_id' => $batch->product_id,
                'product_code' => $batch->product->product_code ?? null,
                'stock' => $batch->quantity,
                'location' => $location,
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
            ]);
        }

        return response()->json(['message' => 'ไม่พบบาร์โค้ดนี้ในระบบ'], 404);
    }
}
