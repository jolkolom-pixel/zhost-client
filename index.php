<?php
// --- আপনার API তথ্য এখানে দিন ----
$api_username = 'RRpY9SSrfznkOjDoXwy48ycfKXXZYdXKdoACCshwRVgvwMnaDAZYGiAZ0aZLAdxdIOJHGSqMAGmQ7Q4UnZeWsTK2AaA03y8XY41CfWY3mpkjGM3BBnEISlDJ4HghwrGKM7Nl6fsEaUBJj2wuMBdhr10uqjFERnbthtpmYV8dEdY8enw4UTdx4rEassydMjwARuj0xzyY5Zh3lZFbuARRUbXI9AoPuDssuinePArcncmkdWdR9oUpNv9e1LOKYuX'; 
$api_password = 'ijdh3FqGKllb1JOGNwrK93lSnvVXDxRBCU4WFDoNLXibvaIb41FjYvb927fyLlvgZb3i4fKtNWtLF5xFmqttTkPSeL2T23BpJZoczW7FecdlVt29aLqY0uju1ln87TDYYwQyDEMQRB0eIpr28hcFfKMVpRSsOhceDIHLrXzgdrAuas2JHWpJtU9y6Vs70sxEWPKmThTfuNbwZrGCieGmDrDraXP1OymYGFOBK2P4GcOXh6TS2fxq7KMkTmylECK'; 
$plan_name    = 'Default'; 
// -----------------------------

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email    = $_POST['email'];
    $domain   = $_POST['domain'] . ".zhost.eu.org";

    $url = "http://panel.myownfreehost.net/xml-api/createacct.php";

    $data = array(
        'username' => $username,
        'password' => $password,
        'contactemail' => $email,
        'domain' => $domain,
        'plan' => $plan_name,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_REFERER, 'https://panel.myownfreehost.net/');
    curl_setopt($ch, CURLOPT_USERPWD, "$api_username:$api_password");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    $response = curl_exec($ch);
    curl_close($ch);

    // এটি আপনাকে আসল এরর মেসেজটি দেখাবে
    $message = "<div class='alert alert-info'><strong>Server Response:</strong> " . htmlspecialchars($response) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Debug zHost Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <div class="card p-4 mx-auto" style="max-width: 500px;">
            <?php echo $message; ?>
            <form method="POST">
                <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                <div class="input-group mb-3">
                    <input type="text" name="domain" class="form-control" placeholder="domain" required>
                    <span class="input-group-text">.zhost.eu.org</span>
                </div>
                <button type="submit" class="btn btn-danger w-100">Debug & Create</button>
            </form>
        </div>
    </div>
</body>
</html>
