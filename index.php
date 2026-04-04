<?php
/**
 * ZHost API Bridge - FINAL VERIFIED VERSION
 */

// আপনার প্যানেল থেকে পাওয়া ক্রেডেনশিয়াল
$api_username = "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX";
$api_password = "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK";

function mofh_request($url, $api_username, $api_password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode($api_username . ":" . $api_password)
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['res' => $response, 'code' => $http_code];
}

$action = $_GET['action'] ?? null;

if ($action === 'get_stats' && isset($_GET['hosting_user'])) {
    header('Content-Type: application/json');
    $user = $_GET['hosting_user'];

    // প্যানেলের দেওয়া সরাসরি আইপি এবং পোর্ট ব্যবহার করছি
    $url = "https://198.251.88.119:2087/xml-api/getstats?user=" . urlencode($user);

    $result = mofh_request($url, $api_username, $api_password);

    if (strpos($result['res'], '<quotalimit>') !== false) {
        echo json_encode(['success' => true, 'raw_xml' => $result['res']]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to fetch data',
            'http_code' => $result['code'],
            'tip' => 'Make sure API Allowed IP Address in MOFH panel is EMPTY.'
        ]);
    }
    exit;
}

// Create Account পার্ট আগের মতোই থাকবে...
