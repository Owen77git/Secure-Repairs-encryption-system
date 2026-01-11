<?php
session_start();
include("../DATABASE/database.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_phone = trim($_POST['email_phone']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM clients WHERE email_phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();

        if (password_verify($password, $client['password'])) {
            $_SESSION['client_name'] = $client['client_name'];
            $_SESSION['email_phone'] = $client['email_phone'];
            header("Location: client~panel.php");
            exit;
        } else {
            $error = "❌ Incorrect password!";
        }
    } else {
        $error = "⚠️ No client found with that email or phone!";
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
      color: #ff0033;
      text-align: center;
      margin-top: 10px;
      font-weight: bold;
    }
 
  </style>
</head>

<body>
  <div class="login-container">
    <h1>Client Login</h1>
    <p style="color:#aaa;font-size:14px;">Access your SecureRepair dashboard</p>

    <form method="POST" action="">
      <label for="email_phone">Email / Phone</label>
      <input type="text" name="email_phone" id="email_phone" placeholder="Enter your email or phone" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>

      <?php if (!empty($error)) { ?>
        <p class="error-message"><?php echo $error; ?></p>
      <?php } ?>
    </form>

    <a href="forgot_password.php">Forgot Password?</a>
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
