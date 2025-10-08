<?php
// Get bot token from environment variable (or hardcode temporarily)
$BOT_TOKEN = getenv('TELEGRAM_BOT_TOKEN'); // or $BOT_TOKEN = "123:ABC";

// Get the update sent by Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Log update for debugging
file_put_contents('bot.log', print_r($update, true), FILE_APPEND);

if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    $reply = "You said: " . $text;

    $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage?chat_id=$chat_id&text=" . urlencode($reply);
    
    // Log outgoing URL
    file_put_contents('bot.log', "Sending URL: $url\n", FILE_APPEND);

    // Send message using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $result = curl_exec($ch);
    if($result === false){
        file_put_contents('bot.log', "cURL error: ".curl_error($ch)."\n", FILE_APPEND);
    }
    curl_close($ch);
}
?>
