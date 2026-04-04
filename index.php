<?php
/**
 * ZHost Client Area - MOFH API Bridge
 * This file handles both Account Creation (POST) and Statistics Fetching (GET)
 */

// ১. রিসেলার ক্রেডেনশিয়াল
$api_username = "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX"; 
$api_password = "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK"; 

// --- ২. ড্যাশবোর্ড থেকে স্ট্যাটাস (Stats) কল হ্যান্ডেল করা ---
if (isset($_GET['action']) && $_GET['action'] == 'get_stats' && isset($_GET['hosting_user'])) {
    header('Content-Type: application/json');
    $user = $_GET['hosting_user'];
    
    // MOFH XML API URL for Stats
    $stats_url = "https://panel.myownfreehost.net/xml-api/getstats.php?user=" . urlencode($user);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $stats_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $headers = [
        "Authorization: Basic " . base64_encode($api_username . ":" . $api_password),
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    curl_close($ch);

    // ড্যাশবোর্ডের জন্য JSON ফরম্যাটে পাঠানো
    echo json_encode(['raw_xml' => $response]);
    exit;
}

// --- ৩. অ্যাকাউন্ট তৈরি করা (POST Request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = "plan1";               
    $create_url = "https://panel.myownfreehost.net/xml-api/createacct.php";

    $raw_user = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($raw_user) || empty($email)) {
        die("Invalid Input");
    }

    $clean_user = substr(preg_replace("/[^A-Za-z0-9]/", '', $raw_user), 0, 8);
    $domain     = $clean_user . ".zhost.eu.org";

    $data = array(
        'username'     => $clean_user,
        'password'     => $password,
        'contactemail' => $email,
        'domain'       => $domain,
        'plan'         => $plan_name
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $create_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $headers = array(
        "Authorization: Basic " . base64_encode($api_username . ":" . $api_password),
        "Content-Type: application/x-www-form-urlencoded"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    // সাইন-আপের পর রেসপন্স দেখানো
    echo "<h2>Registration Result for $domain</h2>";
    if (strpos($response, '<status>1</status>') !== false) {
        echo "<b style='color:green;'>Success! Account created. Check your email.</b>";
    } else {
        echo "<b style='color:red;'>Error!</b><pre>" . htmlspecialchars($response) . "</pre>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZHost Client Registration</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; background: #f0f2f5; text-align: center; }
        .form-container { background: white; padding: 30px; border-radius: 12px; max-width: 400px; margin: auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h3 { color: #1a73e8; margin-bottom: 25px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 16px; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>ZHost Free Hosting</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username (max 8 chars)" required maxlength="8">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create My Account</button>
        </form>
    </div>
</body>
</html>
