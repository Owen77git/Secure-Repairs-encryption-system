<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../DATABASE/database.php");

if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
  header("Location: forgot_password.php");
  exit();
}

$email = $_SESSION['reset_email'];
$msg = "";

if (isset($_POST['reset_password'])) {
  $new_pass = $_POST['new_password'];
  $confirm_pass = $_POST['confirm_password'];

  if ($new_pass !== $confirm_pass) {
    $msg = "Passwords do not match.";
  } else {
    $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
    $update = $conn->query("UPDATE clients SET password='$hashed_pass' WHERE email_phone='$email'");
    if ($update) {
      $msg = "✅ Password updated successfully! You can now log in.";
      session_unset();
      session_destroy();
    } else {
      $msg = "Database error updating password.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password | SecureRepair</title>
<link rel="stylesheet" href="../CSS/client.CSS">
</head>
<body style="background:#0d0d0d;color:#fff;font-family:'Segoe UI',sans-serif;">
  <div class="container" style="max-width:400px;margin:100px auto;background:#111;padding:25px;border-radius:10px;border:2px solid #ff0033;">
    <h2 style="color:#ff0033;text-align:center;">Reset Password</h2>
    <?php if(!empty($msg)) echo "<p style='color:#ff0033;text-align:center;'>$msg</p>"; ?>
    <form method="POST">
      <label>New Password:</label>
      <input type="password" name="new_password" placeholder="Enter new password" required style="width:100%;padding:10px;background:#222;color:#fff;border:none;border-radius:5px;margin-bottom:10px;">
      <label>Confirm Password:</label>
      <input type="password" name="confirm_password" placeholder="Confirm password" required style="width:100%;padding:10px;background:#222;color:#fff;border:none;border-radius:5px;margin-bottom:10px;">
      <button type="submit" name="reset_password" style="width:100%;background:#ff0033;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer;font-weight:bold;">Update Password</button>
    </form>
    <p style="text-align:center;margin-top:10px;"><a href="client_login.php" style="color:#ff0033;text-decoration:none;">Return to Login</a></p>
  </div>
</body>
</html>
