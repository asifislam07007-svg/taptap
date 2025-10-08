<?php
// ✅ --- STATIC FILE HANDLER (Fixes MIME Type issue) ---
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);

if ($ext) {
    $mimeTypes = [
        "css" => "text/css",
        "js" => "application/javascript",
        "png" => "image/png",
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "gif" => "image/gif",
        "svg" => "image/svg+xml",
        "ico" => "image/x-icon",
        "json" => "application/json",
        "woff" => "font/woff",
        "woff2" => "font/woff2",
        "ttf" => "font/ttf",
    ];
    if (isset($mimeTypes[$ext])) {
        header("Content-Type: {$mimeTypes[$ext]}");
        $file = __DIR__ . $path;
        if (file_exists($file)) {
            readfile($file);
            exit;
        } else {
            http_response_code(404);
            exit("File not found");
        }
    }
}

// ✅ --- API HANDLER (Backend Logic) ---
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(200);
    exit;
}

if (isset($_GET['api']) && $_GET['api'] === 'login') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // ✅ Example login validation
    if ($username === 'admin' && $password === '1234') {
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
    exit;
}
?>

<!-- ✅ --- FRONTEND (HTML + JS) --- -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Taptap Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .box {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      width: 320px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      text-align: center;
    }
    h2 {
      margin-bottom: 15px;
      color: #444;
    }
    input {
      width: 90%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      width: 95%;
      padding: 10px;
      background: #2575fc;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover {
      background: #1a5adb;
    }
    #message {
      margin-top: 10px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>🔐 Taptap Login</h2>
    <input id="username" type="text" placeholder="Username" />
    <input id="password" type="password" placeholder="Password" />
    <button onclick="login()">Login</button>
    <p id="message"></p>
  </div>

  <script>
    async function login() {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      const msg = document.getElementById('message');

      if (!username || !password) {
        msg.innerText = '⚠️ Please enter both fields';
        msg.style.color = 'red';
        return;
      }

      msg.innerText = '⏳ Logging in...';
      msg.style.color = 'black';

      try {
        const res = await fetch('/?api=login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, password })
        });

        const data = await res.json();

        msg.innerText = data.message;
        msg.style.color = data.status === 'success' ? 'green' : 'red';
      } catch (err) {
        msg.innerText = 'Network error: ' + err.message;
        msg.style.color = 'red';
      }
    }
  </script>
</body>
</html>
