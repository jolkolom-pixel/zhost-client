<?php
/**
 * ZHost API Bridge (Render)
 * FINAL STABLE VERSION
 */

// ================= CONFIG =================
$api_username = getenv('MOFH_USER') ?: "RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX";
$api_password = getenv('MOFH_PASS') ?: "ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK";
$plan_name    = "plan1";

// ================= HELPER FUNCTION =================
function mofh_request($url, $post_data = null, $api_username, $api_password) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($post_data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode($api_username . ":" . $api_password)
    ]);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }

    curl_close($ch);
    return ['response' => $response];
}

// ================= ROUTER =================
$action = $_GET['action'] ?? null;


// =====================================================
// 🟢 CREATE ACCOUNT
// =====================================================
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $raw_user = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$raw_user || !$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Missing fields']);
        exit;
    }

    // Clean username
    $clean_user = substr(preg_replace("/[^A-Za-z0-9]/", '', $raw_user), 0, 8);
    $domain     = $clean_user . ".zhost.eu.org";

    $url = "https://panel.myownfreehost.net/xml-api/createacct.php";

    $data = [
        'username'     => $clean_user,
        'password'     => $password,
        'contactemail' => $email,
        'domain'       => $domain,
        'plan'         => $plan_name
    ];

    $result = mofh_request($url, $data, $api_username, $api_password);

    if (isset($result['error'])) {
        echo json_encode(['success' => false, 'message' => $result['error']]);
        exit;
    }

    // Return raw XML (dashboard will parse)
    echo $result['response'];
    exit;
}


// =====================================================
// 🔵 GET USER STATS
// =====================================================
if ($action === 'get_stats' && isset($_GET['hosting_user'])) {

    header('Content-Type: application/json');

    $user = $_GET['hosting_user'];

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'No username']);
        exit;
    }

    $url = "https://panel.myownfreehost.net/xml-api/panel.php?" . http_build_query([
        'action'   => 'stats',
        'username' => $user
    ]);

    $result = mofh_request($url, null, $api_username, $api_password);

    if (isset($result['error'])) {
        echo json_encode([
            'success' => false,
            'message' => $result['error']
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'raw_xml' => $result['response']
    ]);

    exit;
}


// =====================================================
// ❌ BLOCK DIRECT ACCESS
// =====================================================
http_response_code(403);
echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]);
exit;
?>
