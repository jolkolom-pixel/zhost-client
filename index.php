<?php
/**
 * ZHost API Bridge - Secure Version
 * This file handles requests only from your Dashboard.
 */

// ১. রিসেলার ক্রেডেনশিয়াল
$api_username = "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX"; 
$api_password = "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK"; 

// ২. ডাটা ফেচ করা (Get Statistics) - ড্যাশবোর্ডের জন্য
if (isset($_GET['action']) && $_GET['action'] == 'get_stats' && isset($_GET['hosting_user'])) {
    header('Content-Type: application/json');
    $user = $_GET['hosting_user'];
    $stats_url = "https://panel.myownfreehost.net/xml-api/getstats.php?user=" . urlencode($user);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $stats_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . base64_encode($api_username . ":" . $api_password)]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    curl_close($ch);

    echo json_encode(['raw_xml' => $response]);
    exit;
}

// ৩. অ্যাকাউন্ট তৈরি করা (Create Account) - শুধুমাত্র POST রিকোয়েস্টের জন্য
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] == 'create') {
    $create_url = "https://panel.myownfreehost.net/xml-api/createacct.php";
    
    // ড্যাশবোর্ড থেকে আসা ডাটা রিসিভ করা
    $data = array(
        'username'     => substr(preg_replace("/[^A-Za-z0-9]/", '', $_POST['username']), 0, 8),
        'password'     => $_POST['password'],
        'contactemail' => $_POST['email'],
        'domain'       => $_POST['username'] . ".zhost.eu.org",
        'plan'         => "plan1"
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $create_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . base64_encode($api_username . ":" . $api_password)]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response; // ড্যাশবোর্ডকে Raw XML ব্যাক করবে
    exit;
}

// যদি কেউ সরাসরি ব্রাউজারে লিঙ্ক ওপেন করে, তাকে খালি পেজ বা এরর দেখাবে
header("HTTP/1.1 403 Forbidden");
echo "Direct access is not allowed.";
exit;
?>
