<?php
/**
 * ZHost API Bridge - FINAL DEBUG + STABLE VERSION
 */

// ================= CONFIG =================
$api_username = getenv('MOFH_USER') ?: "YOUR_API_USERNAME";
$api_password = getenv('MOFH_PASS') ?: "YOUR_API_PASSWORD";
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

    header('Content-Type: application/json');

    $raw_user = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$raw_user || !$email || !$password) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
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
        echo json_encode([
            'success' => false,
            'message' => $result['error']
        ]);
        exit;
    }

    // Return raw XML (important for your dashboard regex)
    echo $result['response'];
    exit;
}


// =====================================================
// 🔵 GET USER STATS
// =====================================================
if ($action === 'get_stats') {

    header('Content-Type: application/json');

    $user = $_GET['hosting_user'] ?? '';

    // 🔴 Debug if missing
    if (empty($user) || $user === 'N/A') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or empty hosting_user',
            'received' => [
                'action' => $action,
                'hosting_user' => $user,
                'method' => $_SERVER['REQUEST_METHOD']
            ]
        ]);
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

    // 🔴 If API returns empty
    if (empty($result['response'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Empty response from MOFH',
            'user' => $user
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
// ❌ INVALID REQUEST (WITH DEBUG)
// =====================================================
echo json_encode([
    'success' => false,
    'message' => 'Invalid request',
    'received' => [
        'action' => $action,
        'hosting_user' => $_GET['hosting_user'] ?? null,
        'method' => $_SERVER['REQUEST_METHOD']
    ]
]);
exit;
?>
