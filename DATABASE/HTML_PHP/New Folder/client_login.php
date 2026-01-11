<?php
session_start();

include("../DATABASE/database.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = trim($_POST['client_name']); // matches form field
    $password = md5(trim($_POST['password']));  // password encryption

    // ✅ Check credentials in the database
    $sql = "SELECT * FROM clients WHERE client_name = '$client_name' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // ✅ Store client name in session for use in dashboard
        $_SESSION['client_name'] = $client_name;

        // ✅ Redirect to client panel (matches your earlier file name)
        header("Location: client~panel.php");
        exit;
    } else {
        $error = "❌ Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Login | Secure Repair System</title>
  <link rel="stylesheet" href="../CSS/login.CSS">
  <style>
    .error-message {
      color: #b30000;
      text-align: center;
      width: 300px;
      margin: 15px auto 0;
      font-weight: bold;
      padding: 10px;
      border-radius: 6px;
      background: rgba(255, 200, 200, 0.2);
      opacity: 1;
      transition: opacity 0.5s ease;
    }
      </style>
</head>

<body>
  <div class="nav"></div>

  <div class="login-container">
    <h1>Client Login</h1>
    <p class="subtitle">Access your repair updates securely</p>

    <form method="POST" action="">
      <label for="client_name">Username</label>
      <input type="text" name="client_name" id="client_name" placeholder="Enter your username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>

      <?php if (!empty($error)) { ?>
        <p class="error-message"><?php echo $error; ?></p>
      <?php } ?>
    </form>
  </div>

  <script>
    // Hide error message after 3 seconds
    document.addEventListener("DOMContentLoaded", function () {
      const msg = document.querySelector(".error-message");
      if (msg) {
        setTimeout(() => {
          msg.style.opacity = "0";
          setTimeout(() => msg.remove(), 500);
        }, 3000);
      }
    });
  </script>
</body>
</html>
