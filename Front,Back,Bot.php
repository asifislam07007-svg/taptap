<?php
// ===================================================
// 🔹 BACKEND API SECTION
// ===================================================

// Allow CORS for all requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle CORS preflight requests immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Handle API route: /?api=login
if (isset($_GET['api']) && $_GET['api'] === 'login') {
    header("Content-Type: application/json");

    // Read POST body
    $data = json_decode(file_get_contents('php://input'), true);
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    // ✅ Example login logic (you can replace with database check)
    if ($username === 'admin' && $password === '1234') {
        echo json_encode(['status' => 'success', 'message' => 'Login successful ✅']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password ❌']);
    }
    exit();
}

// ===================================================
// 🔹 FRONTEND (HTML + JS)
// ===================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Taptap Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f6fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .box {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      width: 320px;
      text-align: center;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #007bff;
      border: none;
      color: white;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover {
      background: #0069d9;
    }
    #message {
      margin-top: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Taptap Login</h2>
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
      msg.innerText = "🔄 Logging in...";

      try {
        const res = await fetch(`${window.location.origin}/?api=login`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, password }),
        });

        const data = await res.json();
        msg.innerText = data.message;
        msg.style.color = data.status === "success" ? "green" : "red";
      } catch (err) {
        msg.innerText = "⚠️ Network error: " + err.message;
        msg.style.color = "red";
      }
    }
  </script>
</body>
</html>
