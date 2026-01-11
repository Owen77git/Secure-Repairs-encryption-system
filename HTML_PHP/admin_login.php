<?php
session_start();

include("../DATABASE/database.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); 
    $password = md5(trim($_POST['password'])); 

    $sql = "SELECT * FROM admins WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = " Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Secure Repair System</title>
  <link rel="stylesheet" href="../CSS/login.CSS">

  <style>

    .error-message {
      color: #b30000;
      text-align: center;
      width: 300px;
      margin-top: 15px;
      font-weight: bold;
      background: #ffe6e6;
      padding: 10px;
      border-radius: 6px;
      opacity: 1;
      transition: opacity 0.5s ease;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <h1>Admin Login</h1>
    <p class="subtitle">Access your repair management securely</p>

    <form method="POST" action="">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" placeholder="Enter your username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>
    </form>

    <?php if (!empty($error)) { ?>
      <p class="error-message"><?php echo $error; ?></p>
    <?php } ?>
  </div>

  <script>
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
