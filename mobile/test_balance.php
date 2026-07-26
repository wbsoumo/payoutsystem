<?php
$ch = curl_init('https://taskbazi.xyz/api/v1/wallet/balance');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'x-api-key: nvx_pk_live_B13S1gJQ73anYvlS3JjAP2CVeCv1xjY5',
    'x-api-secret: nvx_sk_live_c5hdF5i8HSzQhY1KMjn7nqhpTkx8u9eUXNx2mO8TZhU4dJ6R',
    'x-merchant-id: 019f9dc9-a3aa-7076-b865-1f4ca42e790c',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Response: " . $response . "\n";
