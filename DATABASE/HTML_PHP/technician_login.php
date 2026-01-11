<?php
session_start();

include("../DATABASE/database.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $technician_name = trim($_POST['technician_name']); // ✅ fixed variable name
    $password = md5(trim($_POST['password'])); // ✅ MD5 encryption

    // ✅ Corrected column name in SQL query
    $sql = "SELECT * FROM technicians WHERE technician_name = '$technician_name' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['technician'] = $technician_name;
        header("Location: technician.php");
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
  <title>Technician Login | Secure Repair System</title>
  <link rel="stylesheet" href="../CSS/login.CSS">
  <style>
    .error-message {
      color: #b30000;
      text-align: center;
      width: 300px;
      margin-top: 15px;
      font-weight: bold;
      padding: 10px;
      border-radius: 6px;
      background: rgba(255, 200, 200, 0.2);
      opacity: 1;
      transition: opacity 0.5s ease;
      margin-right:5px;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <h1>Technician Login</h1>
    <p class="subtitle">Access repair records and updates securely</p>

    <form method="POST" action="">
      <label for="technician_name">Username</label>
      <input type="text" name="technician_name" id="technician_name" placeholder="Enter your username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>
      <p class="forgot"><a href="forgot.html">Forgot Password?</a></p>

      <!-- ✅ Error message -->
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
