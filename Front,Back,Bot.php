<?php
// ✅ --- BACKEND (API HANDLER) ---
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight CORS requests
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

    // ✅ Example login logic
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Taptap Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      width: 300px;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin: 5px 0;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Login</h2>
    <input id="username" type="text" placeholder="Username" />
    <input id="password" type="password" placeholder="Password" />
    <button onclick="login()">Login</button>
    <p id="message"></p>
  </div>

  <script>
    async function login() {
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;
      const msg = document.getElementById('message');

      msg.innerText = 'Logging in...';

      try {
        const res = await fetch(window.location.origin + '/?api=login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, password })
        });

        const data = await res.json();
        msg.innerText = data.message;
      } catch (err) {
        msg.innerText = 'Network error: ' + err.message;
      }
    }
  </script>
</body>
</html>
