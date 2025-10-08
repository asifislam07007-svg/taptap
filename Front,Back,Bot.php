<?php
// ✅ --- UNIVERSAL TELEGRAM BOT BACKEND + API LOGIN HANDLER ---
// Works perfectly on Render.com

// --------------------
// 1️⃣  BASIC CORS FIX
// --------------------
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --------------------
// 2️⃣  TELEGRAM BOT CONFIG
// --------------------
$apiKey = getenv('BOT_API_KEY') ?: 'YOUR_TELEGRAM_BOT_TOKEN';
$botUsername = getenv('BOT_USERNAME') ?: 'YOUR_BOT_USERNAME';
$web_app = getenv('WEB_URL') ?: 'https://your-app.onrender.com';

// --------------------
// 3️⃣  DATABASE CONFIG
// --------------------
$DB = [
  'dbname' => getenv('DB_NAME') ?: 'your_database',
  'username' => getenv('DB_USER') ?: 'your_user',
  'password' => getenv('DB_PASS') ?: 'your_password',
  'host' => getenv('DB_HOST') ?: 'localhost'
];

$mysqli = new mysqli($DB['host'], $DB['username'], $DB['password'], $DB['dbname']);
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// --------------------
// 4️⃣  SIMPLE ROUTING SYSTEM
// --------------------
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Example route: /api/login
if ($uri === 'api/login') {
    handleLogin($mysqli);
    exit();
}

// Example route: /bot/message (for Telegram webhook)
if ($uri === 'bot/message') {
    handleTelegramUpdate($apiKey, $mysqli);
    exit();
}

// Default landing page
echo json_encode(["status" => "Backend active", "time" => date('Y-m-d H:i:s')]);
exit();

// --------------------
// 5️⃣  API: LOGIN HANDLER
// --------------------
function handleLogin($mysqli) {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $mysqli->real_escape_string($data['username'] ?? '');
    $password = $mysqli->real_escape_string($data['password'] ?? '');

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing username or password"]);
        return;
    }

    $result = $mysqli->query("SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["success" => true, "user" => $user]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
}

// --------------------
// 6️⃣  TELEGRAM BOT HANDLER
// --------------------
function handleTelegramUpdate($apiKey, $mysqli) {
    $update = json_decode(file_get_contents("php://input"), true);
    if (!$update) return;

    $chat_id = $update['message']['chat']['id'] ?? null;
    $text = $update['message']['text'] ?? '';

    if (!$chat_id) return;

    // Simple command example
    if ($text == '/start') {
        sendTelegramMessage($apiKey, $chat_id, "👋 Welcome to the bot!");
    } else {
        sendTelegramMessage($apiKey, $chat_id, "You said: $text");
    }
}

function sendTelegramMessage($apiKey, $chat_id, $message) {
    $url = "https://api.telegram.org/bot$apiKey/sendMessage";
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>
