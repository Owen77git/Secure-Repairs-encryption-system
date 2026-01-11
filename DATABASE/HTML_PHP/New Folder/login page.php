<?php
session_start();

include("../DATABASE/database.php");


$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = trim($_POST['name']);
    $password = md5(trim($_POST['password'])); // ✅ Encrypt with MD5

    $sql = "SELECT * FROM clients WHERE client_name = '$client_name' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['client'] = $client_name;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid email/phone or password!";
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
      margin: 10px auto;
      font-weight: bold;
      animation: fadeOut 3s forwards;
      animation-delay: 0s;
    }

  </style>
</head>
<body>

  

  <div class="login-container">
    <h1> Client Login</h1>
    <p class="subtitle">Access your repair updates securely</p>

    <form method="POST" action="">
      <label for="email">username</label>
      <input type="text" name="client_name" id="name" placeholder="Enter your username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>
      <p class="forgot"><a href="forgot.html">Forgot Password?</a></p>
    </form>
  </div>
 
</body>
<footer>
   <?php if (!empty($error)) { ?>
    <p class="error-message"><?php echo $error; ?></p>
  <?php } ?>
  </footer>
<script>
  // messageTimeout.js
document.addEventListener("DOMContentLoaded", function () {
  const msg = document.querySelector(".error-message");
  if (msg) {
    setTimeout(() => {
      msg.style.transition = "opacity 0.5s ease";
      msg.style.opacity = "0";
      setTimeout(() => msg.remove(), 500);
    }, 3000); // 3 seconds
  }
});
</script>
</html>
