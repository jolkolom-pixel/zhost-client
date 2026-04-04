<?php
/**
 * ZHost API Bridge - FINAL STABLE VERSION
 * Handles Auto-Endpoint Detection for MOFH API
 */

// ================= CONFIG =================
$api_username = "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX";
$api_password = "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK";
$plan_name    = "plan1";

// ================= HELPER FUNCTION =================
function mofh_request($url, $post_data = null, $api_username, $api_password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($post_data !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode($api_username . ":" . $api_password)
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['response' => $response, 'http_code' => $http_code];
}

$action = $_GET['action'] ?? null;

// ================= CREATE ACCOUNT =================
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_user = $_POST['username'] ?? '';
    $clean_user = substr(preg_replace("/[^A-Za-z0-9]/", '', $raw_user), 0, 8);
    $url = "https://panel.myownfreehost.net/xml-api/createacct.php";
    $data = [
        'username' => $clean_user,
        'password' => $_POST['password'] ?? '',
        'contactemail' => $_POST['email'] ?? '',
        'domain' => $clean_user . ".zhost.eu.org",
        'plan' => $plan_name
    ];
    $result = mofh_request($url, $data, $api_username, $api_password);
    echo $result['response'];
    exit;
}

// ================= GET USER STATS (Auto-Detect Logic) =================
if ($action === 'get_stats') {
    header('Content-Type: application/json');
    $user = $_GET['hosting_user'] ?? '';

    if (empty($user) || $user === 'N/A') {
        echo json_encode(['success' => false, 'message' => 'Invalid hosting_user']);
        exit;
    }

    // ট্রাই করার জন্য সম্ভাব্য ৩টি এন্ডপয়েন্ট
    $endpoints = [
        "https://panel.myownfreehost.net/xml-api/getstats.php?user=" . urlencode($user),
        "https://panel.myownfreehost.net/getstats.php?user=" . urlencode($user),
        "https://panel.myownfreehost.net:2087/xml-api/getstats?user=" . urlencode($user)
    ];

    $final_response = "";
    $success = false;

    foreach ($endpoints as $url) {
        $res = mofh_request($url, null, $api_username, $api_password);
        // যদি রেসপন্সে quotalimit থাকে, তবে এটিই সঠিক ডাটা
        if ($res['http_code'] == 200 && strpos($res['response'], '<quotalimit>') !== false) {
            $final_response = $res['response'];
            $success = true;
            break;
        }
    }

    if ($success) {
        echo json_encode([
            'success' => true,
            'raw_xml' => $final_response
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Could not fetch stats from any endpoint. Check MOFH reseller status.'
        ]);
    }
    exit;
}

// ❌ BLOCK DIRECT ACCESS
http_response_code(403);
echo json_encode(['success' => false, 'message' => 'Direct access forbidden']);
exit;
