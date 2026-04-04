<?php
/**
 * ZHost API Bridge - FINAL VERIFIED VERSION
 */

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
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    return ['res' => $response, 'code' => $http_code, 'error' => $curl_error];
}

$action = $_GET['action'] ?? null;

if ($action === 'get_stats' && isset($_GET['hosting_user'])) {
    header('Content-Type: application/json');
    $user = $_GET['hosting_user'];

// ২০৮৭ পোর্ট সরিয়ে শুধু HTTPS ট্রাই করা
$url = "https://198.251.88.119/xml-api/getstats?user=" . urlencode($user);

    $result = mofh_request($url, $api_username, $api_password);

    if ($result['code'] == 200 && strpos($result['res'], '<quotalimit>') !== false) {
        echo json_encode(['success' => true, 'raw_xml' => $result['res']]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Connection Failed',
            'http_code' => $result['code'],
            'curl_error' => $result['error'],
            'your_render_ip' => file_get_contents('https://api.ipify.org') // আইপি শনাক্ত করতে সাহায্য করবে
        ]);
    }
    exit;
}
// আপনার বর্তমান রেন্ডার আইপি জানতে এটি কল করুন
$current_ip = file_get_contents('https://api.ipify.org');
if (isset($_GET['check_ip'])) {
    die("আপনার বর্তমান রেন্ডার আইপি: " . $current_ip);
}
?>
