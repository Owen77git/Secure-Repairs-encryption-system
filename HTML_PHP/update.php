<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../DATABASE/database.php");



$email = $_SESSION['reset_email'];
$msg = "";

if (isset($_POST['reset_password'])) {
  $new_pass = $_POST['new_password'];
  $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);

  $update = $conn->query("UPDATE clients SET password='$hashed_pass' WHERE email_phone='$email'");

  if ($update) {
    $msg = "✅ Password updated successfully! You can now log in.";
    session_unset();
    session_destroy();
  } else {
    $msg = "Error updating password.";
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
<body>
  <div class="container">
    <h2> Reset Password</h2>
    <?php if(!empty($msg)) echo "<p style='color:#ff0033;'>$msg</p>"; ?>
    <form method="POST">
      <label>New Password:</label>
      <input type="password" name="new_password" placeholder="Enter new password" required>
      <button type="submit" name="reset_password">Update Password</button>
    </form>
  </div>
</body>
</html>
