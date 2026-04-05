<?php
// আপনার বর্তমান রেন্ডার আইপি জানতে এটি কল করুন
$current_ip = file_get_contents('https://api.ipify.org');
if (isset($_GET['check_ip'])) {
    die("আপনার বর্তমান রেন্ডার আইপি: " . $current_ip);
}
?>
