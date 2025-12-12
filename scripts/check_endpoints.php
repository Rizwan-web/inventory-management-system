<?php
// Simple script to login and fetch endpoints with cookies
$base = 'http://127.0.0.1:8000';
$cookieFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'laravel_test_cookie.txt';
// Login
$login = curl_init($base . '/login');
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_setopt($login, CURLOPT_HEADER, true);
curl_setopt($login, CURLOPT_POST, true);
curl_setopt($login, CURLOPT_POSTFIELDS, http_build_query(['email' => 'admin@mail.com', 'password' => 'admin@mail.com']));
curl_setopt($login, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($login, CURLOPT_FOLLOWLOCATION, false);
$resp = curl_exec($login);
$info = curl_getinfo($login);
curl_close($login);
echo "--- LOGIN STATUS: {$info['http_code']} ---\n";
// Fetch DC numbers
$ch = curl_init($base . '/transaksi/dc-numbers');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$dc = curl_exec($ch);
$info2 = curl_getinfo($ch);
curl_close($ch);
echo "--- DC NUMBERS HTTP: {$info2['http_code']} ---\n";
echo $dc . "\n";
// Fetch sales data
$ch = curl_init($base . '/sales/data');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$sd = curl_exec($ch);
$info3 = curl_getinfo($ch);
curl_close($ch);
echo "--- SALES.DATA HTTP: {$info3['http_code']} ---\n";
echo substr($sd,0,2000) . "\n";
// If we have a DC from dc numbers, try dc-sales
$decoded = json_decode($dc, true);
if (is_array($decoded) && count($decoded)>0) {
    $first = urlencode($decoded[0]);
    $ch = curl_init($base . '/transaksi/dc-sales?dc=' . $first);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $dcsales = curl_exec($ch);
    $info4 = curl_getinfo($ch);
    curl_close($ch);
    echo "--- DC-SALES HTTP: {$info4['http_code']} ---\n";
    echo $dcsales . "\n";
} else {
    echo "--- NO DC NUMBERS FOUND ---\n";
}
?>
