<?php
function getUserIP() {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}

$ip = getUserIP();
$webhookurl = "https://discord.com/api/webhooks/1241024482894811218/0buUUaGvO9IqhnA4fI422Y5YYD6Yrur_wXD_tnavuMxbioo4EdjezBsTU-Tg-of1a8bg";

// Create the data array
$timestamp = date("c", strtotime("now"));
$json_data = json_encode([
    "content" => "IP Address Logged: $ip",
    "username" => "IP Logger",
    "tts" => false,
    "embeds" => [
        [
            "title" => "New IP Logged",
            "type" => "rich",
            "description" => "An IP address has been logged.",
            "timestamp" => $timestamp,
            "color" => hexdec("3366ff"),
            "fields" => [
                [
                    "name" => "IP Address",
                    "value" => $ip,
                    "inline" => true
                ]
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

// Use cURL to send the webhook
$ch = curl_init($webhookurl);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

echo "IP logged successfully.";
?>
