<?php
// ✅ Enable CORS for all requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ✅ Handle OPTIONS (CORS preflight) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Connect to Telegram Bot API (optional sample)
$botToken = "YOUR_BOT_TOKEN_HERE"; // <-- Replace this with your Telegram bot token
$apiUrl = "https://api.telegram.org/bot$botToken/";

// ✅ Example API endpoint handling
$requestUri = $_SERVER['REQUEST_URI'];

// --- /api/login ---
if (strpos($requestUri, '/api/login') !== false) {
    // Example login logic
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['username']) || !isset($data['password'])) {
        echo json_encode(["status" => "error", "message" => "Missing username or password"]);
        exit();
    }

    // Simple mock login success (you can connect to Firebase/MySQL here)
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "user" => [
            "username" => $data['username'],
            "points" => 100,
        ]
    ]);
    exit();
}

// --- /api/sendMessage ---
if (strpos($requestUri, '/api/sendMessage') !== false) {
    $data = json_decode(file_get_contents("php://input"), true);
    $chat_id = $data['chat_id'] ?? null;
    $text = $data['text'] ?? null;

    if (!$chat_id || !$text) {
        echo json_encode(["status" => "error", "message" => "chat_id and text required"]);
        exit();
    }

    $url = $apiUrl . "sendMessage?chat_id=" . urlencode($chat_id) . "&text=" . urlencode($text);
    $response = file_get_contents($url);

    echo json_encode(["status" => "sent", "telegram_response" => json_decode($response, true)]);
    exit();
}

// --- Default route ---
echo json_encode([
    "status" => "ok",
    "message" => "Telegram bot backend running successfully on Render!",
    "time" => date("Y-m-d H:i:s")
]);
exit();
?>
