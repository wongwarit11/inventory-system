<?php
function curl_get($url, $cookieFile) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
function curl_post($url, $postFields, $cookieFile) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

$base = 'http://127.0.0.1:8000';
$cookie = __DIR__ . '/cookies.txt';
@unlink($cookie);

// Fetch login page to get CSRF token
$loginPage = curl_get($base . '/login', $cookie);
if (!$loginPage) { echo "LOGIN_PAGE_FAIL\n"; exit(1); }

if (!preg_match('/name="_token" value="([^"]+)"/', $loginPage, $m)) {
    echo "CSRF_TOKEN_NOT_FOUND\n";
    // still continue trying to post without token
    $token = '';
} else {
    $token = $m[1];
}

// Post credentials (seeded admin/password)
$post = [
    'username' => 'admin',
    'password' => 'password',
    '_token' => $token,
];
$loginResp = curl_post($base . '/login', $post, $cookie);

// Verify login by requesting dashboard
$dash = curl_get($base . '/', $cookie);
if (strpos($dash, 'เข้าสู่ระบบ') !== false) {
    echo "LOGIN_FAILED\n";
    // continue anyway
} else {
    echo "LOGIN_OK\n";
}

function test_page($path, $noResultPhrase, $label, $cookie) {
    $term = uniqid('s_');
    $url = 'http://127.0.0.1:8000' . $path . '?search=' . urlencode($term);
    $html = curl_get($url, $cookie);
    if ($html === false) { echo "$label:HTTP_FAIL\n"; return; }
    $hasNoResults = strpos($html, $noResultPhrase) !== false;
    $hasTerm = strpos($html, $term) !== false;
    echo "$label:" . ($hasNoResults ? 'NORESULTS' : 'HASRESULTS') . ":term_present=" . ($hasTerm ? '1' : '0') . ":$term\n";
}

// Run tests

test_page('/products', 'ไม่พบข้อมูลสินค้า', 'PRODUCTS', $cookie);
test_page('/batches', 'ไม่พบข้อมูลล็อตสินค้า', 'BATCHES', $cookie);
test_page('/stock-transactions', 'ไม่พบข้อมูลรายการสต็อก', 'STOCK_TRANSACTIONS', $cookie);
test_page('/requisitions', 'ไม่พบข้อมูลใบขอเบิก', 'REQUISITIONS', $cookie);

unlink($cookie);
