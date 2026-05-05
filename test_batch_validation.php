<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Batch;

// Test 1: Check if batches are correctly related to products
$product = Product::with('batches')->first();
if ($product) {
    echo "Product ID: {$product->id}\n";
    echo "Product Name: {$product->name}\n";
    echo "Batch Count: " . $product->batches()->count() . "\n\n";
    
    $batches = $product->batches()->take(3)->get();
    foreach ($batches as $batch) {
        echo "Batch ID: {$batch->id}, Batch Number: {$batch->batch_number}, Product ID: {$batch->product_id}, Quantity: {$batch->quantity}\n";
    }
    echo "\n";
} else {
    echo "No products found\n";
}

// Test 2: Check if there are any orphaned batches (batches pointing to non-existent products)
$orphanedBatches = Batch::whereNotIn('product_id', Product::pluck('id'))->get();
if ($orphanedBatches->count() > 0) {
    echo "Found orphaned batches:\n";
    foreach ($orphanedBatches as $batch) {
        echo "Batch ID: {$batch->id}, Batch Number: {$batch->batch_number}, Product ID: {$batch->product_id}\n";
    }
} else {
    echo "No orphaned batches found.\n";
}

// Test 3: Verify status column exists and is set
$batch = Batch::first();
if ($batch) {
    echo "\nStatus of first batch: {$batch->status}\n";
    echo "Batch fillable fields: " . json_encode($batch->getFillable()) . "\n";
}
?>
