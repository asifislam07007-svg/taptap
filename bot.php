<?php
// -------------------------------
// 1️⃣ Check if cURL is enabled
// -------------------------------
if (!extension_loaded('curl')) {
    die('❌ cURL is not enabled on this server. Telegram bot cannot work without cURL.');
}

// -------------------------------
// 2️⃣ Get bot token
// -------------------------------
// Option 1: From environment variable (recommended)
$BOT_TOKEN = getenv('TELEGRAM_BOT_TOKEN'); 

// Option 2: Hardcode temporarily for testing (uncomment to test)
// $BOT_TOKEN = "8381157079:AAHkbJoQ4BQ7GSbzgJKrgtWmSys0jbfVpvo";

// -------------------------------
// 3️⃣ Check if token is valid
// -------------------------------
if (empty($BOT_TOKEN)) {
    die('❌ Bot token is empty. Set TELEGRAM_BOT_TOKEN environment variable or hardcode it.');
}

// -------------------------------
// 4️⃣ Get the update sent by Telegram
// -------------------------------
$update = json_decode(file_get_contents('php://input'), true);

// Log the incoming update for debugging
file_put_contents('bot.log', "[" . date('Y-m-d H:i:s') . "] Incoming update:\n" . print_r($update, true) . "\n", FILE_APPEND);

// -------------------------------
// 5️⃣ Respond to messages
// -------------------------------
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'] ?? '';
    $reply = "You said: " . $text;

    $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage?chat_id=$chat_id&text=" . urlencode($reply);

    // Log the outgoing request
    file_put_contents('bot.log', "[" . date('Y-m-d H:i:s') . "] Sending URL: $url\n", FILE_APPEND);

    // -------------------------------
    // 6️⃣ Send message using cURL
    // -------------------------------
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // timeout for response
    $result = curl_exec($ch);
    
    if ($result === false) {
        // Log cURL error
        file_put_contents('bot.log', "[" . date('Y-m-d H:i:s') . "] cURL error: " . curl_error($ch) . "\n", FILE_APPEND);
    } else {
        // Log successful response
        file_put_contents('bot.log', "[" . date('Y-m-d H:i:s') . "] Telegram response: $result\n", FILE_APPEND);
    }

    curl_close($ch);
}
?>
