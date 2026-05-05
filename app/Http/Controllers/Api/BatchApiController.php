<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchApiController extends Controller
{
    public function getBatchesByProduct($productId)
    {
        $batches = Batch::where('product_id', $productId)
            ->orderBy('expiry_date', 'asc')
            ->get(['id', 'batch_number', 'quantity', 'expiry_date']);

        if ($batches->isEmpty()) {
            return response()->json(['message' => 'ไม่พบข้อมูล batch ของสินค้านี้'], 404);
        }

        return response()->json($batches);
    }
}
