<?php
function test_page($url, $noResultPhrase, $label) {
    $term = uniqid('s_');
    $full = $url . '?search=' . urlencode($term);
    $html = @file_get_contents($full);
    if ($html === false) {
        echo "$label:HTTP_FAIL\n";
        return;
    }
    $hasNoResults = strpos($html, $noResultPhrase) !== false;
    $hasTerm = strpos($html, $term) !== false;
    echo "$label:" . ($hasNoResults ? 'NORESULTS' : 'HASRESULTS') . ":term_present=" . ($hasTerm ? '1' : '0') . ":$term\n";
}

$base = 'http://127.0.0.1:8000';

test_page($base . '/products', 'ไม่พบข้อมูลสินค้า', 'PRODUCTS');
test_page($base . '/batches', 'ไม่พบข้อมูลล็อตสินค้า', 'BATCHES');
test_page($base . '/stock-transactions', 'ไม่พบข้อมูลรายการสต็อก', 'STOCK_TRANSACTIONS');
test_page($base . '/requisitions', 'ไม่พบข้อมูลใบขอเบิก', 'REQUISITIONS');
