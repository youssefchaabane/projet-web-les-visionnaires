<?php
$apiKey = 'AIzaSyDo2mI1MTMlmPNsPs14_EMaGMlDpCmNfB8';
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$data = json_decode($res, true);
foreach($data['models'] as $m) {
    echo $m['name'] . "\n";
}
?>
