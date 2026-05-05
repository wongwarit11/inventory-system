<?php

require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\FontMetrics;

$basePath = realpath(__DIR__ . '/../');
$fontDir = $basePath . '/storage/fonts';

$fonts = [
    ['style' => 'normal', 'weight' => 'normal', 'file' => 'THSarabunNew.ttf'],
    ['style' => 'italic', 'weight' => 'normal', 'file' => 'THSarabunNew Italic.ttf'],
    ['style' => 'normal', 'weight' => 'bold', 'file' => 'THSarabunNew Bold.ttf'],
    ['style' => 'italic', 'weight' => 'bold', 'file' => 'THSarabunNew BoldItalic.ttf'],
];

$dompdf = new Dompdf();
$fontMetrics = new FontMetrics($dompdf->getCanvas(), $dompdf->getOptions());

foreach ($fonts as $font) {
    $fontPath = $fontDir . '/' . $font['file'];
    if (!file_exists($fontPath)) {
        echo "Missing font file: {$font['file']}\n";
        exit(1);
    }

    echo "Registering {$font['file']}...\n";
    $fontMetrics->registerFont([
        'family' => 'THSarabunNew',
        'style' => $font['style'],
        'weight' => $font['weight'],
    ], $fontPath);
}

echo "Font registration complete.\n";
