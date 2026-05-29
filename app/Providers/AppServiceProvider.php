<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->ensureDomPdfFontsRegistered();
    }

    /**
     * Auto-register THSarabunNew fonts for DomPDF if the .ufm cache files are missing.
     * This handles the case where storage/fonts/*.ufm were deleted (gitignored files
     * removed after a git pull) or the app is deployed to a new server.
     */
    private function ensureDomPdfFontsRegistered(): void
    {
        try {
            $installedFontsPath = storage_path('fonts/installed-fonts.json');

            // Check whether the bold font's .ufm file actually exists on disk
            $ufmMissing = true;
            if (file_exists($installedFontsPath)) {
                $installed = json_decode(file_get_contents($installedFontsPath), true);
                if (!empty($installed['thsarabunnew']['bold'])) {
                    $ufmMissing = !file_exists($installed['thsarabunnew']['bold'] . '.ufm');
                }
            }

            if (!$ufmMissing) {
                return; // Fonts are already registered correctly — nothing to do
            }

            // .ufm file is missing: register all THSarabunNew variants
            $fontDir  = storage_path('fonts');
            $variants = [
                ['style' => 'normal', 'weight' => 'normal', 'file' => 'THSarabunNew.ttf'],
                ['style' => 'italic', 'weight' => 'normal', 'file' => 'THSarabunNew Italic.ttf'],
                ['style' => 'normal', 'weight' => 'bold',   'file' => 'THSarabunNew Bold.ttf'],
                ['style' => 'italic', 'weight' => 'bold',   'file' => 'THSarabunNew BoldItalic.ttf'],
            ];

            $dompdf      = new \Dompdf\Dompdf();
            $fontMetrics = new \Dompdf\FontMetrics($dompdf->getCanvas(), $dompdf->getOptions());

            foreach ($variants as $variant) {
                $ttfPath = $fontDir . '/' . $variant['file'];
                if (file_exists($ttfPath)) {
                    $fontMetrics->registerFont([
                        'family' => 'THSarabunNew',
                        'style'  => $variant['style'],
                        'weight' => $variant['weight'],
                    ], $ttfPath);
                }
            }

            Log::info('AppServiceProvider: THSarabunNew fonts registered successfully.');
        } catch (\Throwable $e) {
            Log::error('AppServiceProvider: DomPDF font registration failed — ' . $e->getMessage());
        }
    }
}
