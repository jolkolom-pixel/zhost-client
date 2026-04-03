<?php
/**
 * ZHost Client Area - MOFH API Integration
 * Target Domain: client.zhost.eu.org
 */

// ১. আপনার রিসেলার ক্রেডেনশিয়াল (আপনার প্যানেল থেকে নিশ্চিত হয়ে নিন)
$api_username = "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX"; // উদাহরণ: mofh_12345678
$api_password = "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK"; // রিসেলার প্যানেলের API Key
$plan_name    = "plan1";               // আপনার দেয়া প্ল্যান নাম
$url          = "https://panel.myownfreehost.net/xml-api/createacct.php";

// ২. ইনপুট হ্যান্ডেল করা (ইউজার ফর্ম থেকে আসলে)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_user = $_POST['username'] ?? 'user' . rand(10, 99);
    $email    = $_POST['email'] ?? 'test' . rand(10, 99) . '@gmail.com';
    $password = $_POST['password'] ?? 'pass' . rand(1000, 9999);

    // MOFH-এর জন্য ইউজারনেম অবশ্যই ৮ অক্ষরের নিচে হতে হবে
    $clean_user = substr(preg_replace("/[^A-Za-z0-9]/", '', $raw_user), 0, 8);
    $domain     = $clean_user . ".zhost.eu.org";

    // ৩. API ডাটা প্রস্তুত করা
    $data = array(
        'username'     => $clean_user,
        'password'     => $password,
        'contactemail' => $email,
        'domain'       => $domain,
        'plan'         => $plan_name
    );

    // ৪. CURL সেটআপ
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // হেডার সেটআপ (Auth এবং User-Agent)
    $headers = array(
        "Authorization: Basic " . base64_encode($api_username . ":" . $api_password),
        "Content-Type: application/x-www-form-urlencoded",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ৫. রেজাল্ট ডিসপ্লে
    echo "<h2>Registration Result for $domain</h2>";
    if (strpos($response, '<status>1</status>') !== false) {
        echo "<b style='color:green;'>Success! Your account is being created. Please check your email.</b>";
    } else {
        echo "<b style='color:red;'>Error!</b><br>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ZHost Client Registration</title>
    <style>
        body { font-family: Arial; padding: 50px; background: #f4f4f4; }
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Create Your Free Hosting</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username (max 8 chars)" required maxlength="8">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Account</button>
        </form>
    </div>
</body>
</html>
