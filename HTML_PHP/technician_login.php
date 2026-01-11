<?php
session_start();
include("../DATABASE/database.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $technician_name = trim($_POST['technician_name']);
    $password = md5(trim($_POST['password']));

    // Prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM technicians WHERE technician_name = ? AND password = ?");
    $stmt->bind_param("ss", $technician_name, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['status'] === 'cleared') {
            $_SESSION['technician'] = $technician_name;
            header("Location: technician.php");
            exit;
        } else {
            $error = "⚠️ Sorry, you are currently on a blacklist (status: MONITORED).";
        }
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
      color: #ff4444;
      text-align: center;
      width: 320px;
      margin-top: 15px;
      font-weight: bold;
      padding: 10px;
      border-radius: 6px;
      background: rgba(255, 50, 50, 0.1);
      opacity: 1;
      transition: opacity 0.5s ease;
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
      <p class="forgot"><a href="tech_forgot.php">Forgot Password?</a></p>

      <?php if (!empty($error)) { ?>
        <p class="error-message"><?php echo $error; ?></p>
      <?php } ?>
    </form>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const msg = document.querySelector(".error-message");
      if (msg) {
        setTimeout(() => {
          msg.style.opacity = "0";
          setTimeout(() => msg.remove(), 500);
        }, 3500);
      }
    });
  </script>

</body>
</html>
