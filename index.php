<?php
// ১. আপনার ক্রেডেনশিয়াল দিন
$api_username = "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX"; // e.g. mofh_12345678
$api_password = "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK"; // রিসেলার প্যানেলের API কী
$url = "https://panel.myownfreehost.net/xml-api/createacct.php";

// ২. ডাটা তৈরি (আপনার ফর্ম থেকে আসা ডাটা)
$data = array(
    'username' => $_POST['username'] ?? 'testuser' . rand(10,99),
    'password' => $_POST['password'] ?? 'pass123456',
    'contactemail' => $_POST['email'] ?? 'test@example.com',
    'domain' => ($_POST['username'] ?? 'testuser') . '.zhost.eu.org',
    'plan' => 'Freetest' // আপনার তৈরি করা প্ল্যান নাম
);

// ৩. CURL শুরু
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// ৪. গুরুত্বপূর্ণ হেডার (এটি ৪০৩ এরর কাটাতে সাহায্য করতে পারে)
$headers = array(
    "Authorization: Basic " . base64_encode($api_username . ":" . $api_password),
    "Content-Type: application/x-www-form-urlencoded",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// ৫. সিকিউরিটি বাইপাস
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ৬. রেজাল্ট দেখানো
echo "HTTP Code: " . $http_code . "<br>";
echo "Server Response: " . htmlspecialchars($response);
?>
