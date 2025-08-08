<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batch;
use App\Models\Product;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // เพิ่มการ import Log facade

class CheckStockAndExpiration extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'inventory:check-stock-alerts';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Checks stock levels and expiration dates to send Line notifications via Messaging API.';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        $this->info('Starting stock and expiration check...');
        Log::info('Starting stock and expiration check via Artisan command.'); // บันทึก Log

        $channelAccessToken = env('LINE_MESSAGING_CHANNEL_ACCESS_TOKEN');
        $groupId = env('LINE_MESSAGING_GROUP_ID');

        // --- บรรทัดสำหรับ Debug: แสดงค่าที่อ่านได้จาก .env ---
        $this->info("Debug: LINE_MESSAGING_CHANNEL_ACCESS_TOKEN = " . ($channelAccessToken ? 'Set (length ' . strlen($channelAccessToken) . ')' : 'Not Set'));
        $this->info("Debug: LINE_MESSAGING_GROUP_ID = " . ($groupId ? 'Set' : 'Not Set'));
        Log::info("Debug: LINE_MESSAGING_CHANNEL_ACCESS_TOKEN = " . ($channelAccessToken ? 'Set (length ' . strlen($channelAccessToken) . ')' : 'Not Set')); // บันทึก Log
        Log::info("Debug: LINE_MESSAGING_GROUP_ID = " . ($groupId ? 'Set' : 'Not Set')); // บันทึก Log
        // --- สิ้นสุดบรรทัด Debug ---


        if (!$channelAccessToken || !$groupId) {
            $this->error('LINE_MESSAGING_CHANNEL_ACCESS_TOKEN or LINE_MESSAGING_GROUP_ID is not set in .env. Skipping Line notifications.');
            Log::error('LINE_MESSAGING_CHANNEL_ACCESS_TOKEN or LINE_MESSAGING_GROUP_ID is not set in .env. Skipping Line notifications.'); // บันทึก Log
            return Command::FAILURE;
        }

        $messages = [];

        // --- ตรวจสอบสินค้าใกล้หมดอายุ ---
        $expirationThresholdDays = 30;
        $expirationDateLimit = Carbon::now()->addDays($expirationThresholdDays)->endOfDay();

        $expiringBatches = Batch::with('product')
                                ->whereNotNull('expiration_date')
                                ->where('expiration_date', '<=', $expirationDateLimit)
                                ->where('quantity', '>', 0)
                                ->get();

        if ($expiringBatches->isNotEmpty()) {
            $messages[] = "⚠️ *แจ้งเตือนสินค้าใกล้หมดอายุ (ภายใน {$expirationThresholdDays} วัน):*";
            foreach ($expiringBatches as $batch) {
                $messages[] = "- สินค้า: {$batch->product->name} ({$batch->product->product_code})";
                $messages[] = "  ล็อต: {$batch->batch_number}, คงเหลือ: {$batch->quantity} {$batch->product->unit}";
                $messages[] = "  หมดอายุ: " . Carbon::parse($batch->expiration_date)->format('d/m/Y');
            }
            Log::info('Found expiring batches. Count: ' . $expiringBatches->count()); // บันทึก Log
        } else {
            Log::info('No expiring batches found.'); // บันทึก Log
        }

        // --- ตรวจสอบสินค้าสต็อกต่ำกว่าจุดต่ำสุด ---
        // แก้ไขส่วนนี้: ใช้ whereRaw เพื่อทำ Subquery ได้อย่างถูกต้อง
        $lowStockProducts = Product::where('minimum_stock_level', '>', 0)
                                    ->whereRaw('products.minimum_stock_level >= (SELECT COALESCE(SUM(batches.quantity), 0) FROM batches WHERE batches.product_id = products.id)')
                                    ->get();

        if ($lowStockProducts->isNotEmpty()) {
            $messages[] = "🚨 *แจ้งเตือนสินค้าสต็อกต่ำกว่าจุดต่ำสุด:*";
            foreach ($lowStockProducts as $product) {
                $currentStock = $product->batches()->sum('quantity');
                $messages[] = "- สินค้า: {$product->name} ({$product->product_code})";
                $messages[] = "  สต็อกปัจจุบัน: {$currentStock} {$product->unit}";
                $messages[] = "  จุดต่ำสุด: {$product->minimum_stock_level} {$product->unit}";
            }
            Log::info('Found low stock products. Count: ' . $lowStockProducts->count()); // บันทึก Log
        } else {
            Log::info('No low stock products found.'); // บันทึก Log
        }

        // --- ส่งข้อความไปยัง Line Messaging API ---
        if (!empty($messages)) {
            $fullMessage = implode("\n", $messages);
            $chunks = str_split($fullMessage, 1900);

            foreach ($chunks as $chunk) {
                $this->sendLineMessage($channelAccessToken, $groupId, $chunk);
            }
            $this->info('Line notifications sent successfully.');
            Log::info('Line notifications sent successfully.'); // บันทึก Log
        } else {
            $this->info('No alerts found. No Line notifications sent.');
            Log::info('No alerts found. No Line notifications sent.'); // บันทึก Log
        }

        $this->info('Stock and expiration check completed.');
        Log::info('Stock and expiration check completed.'); // บันทึก Log
        return Command::SUCCESS;
    }

    /**
     * Sends a push message to Line Messaging API.
     * @param string $channelAccessToken Channel Access Token
     * @param string $to User ID, Group ID, or Room ID
     * @param string $message The text message to send.
     * @return void
     */
    private function sendLineMessage(string $channelAccessToken, string $to, string $message)
    {
        $client = new Client();
        try {
            $response = $client->post('https://api.line.me/v2/bot/message/push', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $channelAccessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to' => $to,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                        ],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode === 200) {
                $this->info("Line message sent. Response: " . $body);
                Log::info("Line message sent. Status: {$statusCode}, Response: " . $body); // บันทึก Log
            } else {
                $this->error("Failed to send Line message. Status: {$statusCode}, Response: " . $body);
                Log::error("Failed to send Line message. Status: {$statusCode}, Response: " . $body); // บันทึก Log
            }
        } catch (\Exception $e) {
                $this->error("Error sending Line message: " . $e->getMessage());
                Log::error("Error sending Line message: " . $e->getMessage()); // บันทึก Log
        }
    }
}
