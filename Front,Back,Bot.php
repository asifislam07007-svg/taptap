<?php
// -------------------------------
// 1️⃣ CORS & Preflight Handling
// -------------------------------
header("Access-Control-Allow-Origin: *"); // allow all origins (or restrict to your domain)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// -------------------------------
// 2️⃣ Telegram Bot Token
// -------------------------------
$BOT_TOKEN = getenv('TELEGRAM_BOT_TOKEN'); // Set this in Render environment variables
if (!$BOT_TOKEN) {
    $BOT_TOKEN = "YOUR_BOT_TOKEN_HERE"; // fallback for testing
}

// -------------------------------
// 3️⃣ Handle Telegram updates
// -------------------------------
$update = json_decode(file_get_contents('php://input'), true);
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'] ?? '';
    $reply = "You said: " . $text;

    $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage?chat_id=$chat_id&text=" . urlencode($reply);

    // Send message using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result = curl_exec($ch);
    curl_close($ch);
}

// -------------------------------
// 4️⃣ Handle API requests
// -------------------------------
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    header('Content-Type: application/json');

    // Read JSON body
    $input = json_decode(file_get_contents('php://input'), true);

    // Simple in-memory "database" for demo
    $users = [
        ['username' => 'testuser', 'password' => '123456', 'role' => 'user'],
        ['username' => 'admin', 'password' => 'admin123', 'role' => 'admin']
    ];

    switch ($action) {
        case 'login':
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';
            $found = false;
            foreach ($users as $user) {
                if ($user['username'] === $username && $user['password'] === $password) {
                    $found = true;
                    $response = [
                        'status' => 'success',
                        'message' => 'Login successful',
                        'user' => $user
                    ];
                    break;
                }
            }
            if (!$found) {
                $response = [
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ];
            }
            echo json_encode($response);
            exit();

        case 'register':
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';
            $response = [
                'status' => 'success',
                'message' => 'Registration successful',
                'user' => ['username' => $username, 'role' => 'user']
            ];
            echo json_encode($response);
            exit();

        case 'fetch':
            echo json_encode([
                'status' => 'success',
                'users' => $users
            ]);
            exit();

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid endpoint']);
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All-in-One PHP App + Telegram Bot</title>
</head>
<body>
    <h2>Login</h2>
    <input type="text" id="username" placeholder="Username"><br>
    <input type="password" id="password" placeholder="Password"><br>
    <button id="loginBtn">Login</button>
    <p id="loginResult"></p>

    <h2>Register</h2>
    <input type="text" id="regUsername" placeholder="Username"><br>
    <input type="password" id="regPassword" placeholder="Password"><br>
    <button id="regBtn">Register</button>
    <p id="regResult"></p>

    <h2>Fetch Users</h2>
    <button id="fetchBtn">Fetch All Users</button>
    <pre id="fetchResult"></pre>

<script>
const baseUrl = window.location.href; // Same PHP file

// Login
document.getElementById('loginBtn').addEventListener('click', () => {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    fetch(baseUrl + '?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('loginResult').innerText = data.message;
    });
});

// Register
document.getElementById('regBtn').addEventListener('click', () => {
    const username = document.getElementById('regUsername').value;
    const password = document.getElementById('regPassword').value;

    fetch(baseUrl + '?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('regResult').innerText = data.message;
    });
});

// Fetch users
document.getElementById('fetchBtn').addEventListener('click', () => {
    fetch(baseUrl + '?action=fetch')
        .then(res => res.json())
        .then(data => {
            document.getElementById('fetchResult').innerText = JSON.stringify(data, null, 2);
        });
});
</script>
</body>
</html>
