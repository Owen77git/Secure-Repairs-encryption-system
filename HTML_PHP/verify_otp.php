<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = "";

if (!isset($_SESSION['reset_email'])) {
  header("Location: forgot_password.php");
  exit();
}

if (isset($_POST['verify_otp'])) {
  $otp = trim($_POST['otp']);

  if (time() > $_SESSION['otp_expire']) {
    $msg = "OTP expired. Please request a new one.";
  } elseif ($otp == $_SESSION['reset_otp']) {
    $_SESSION['otp_verified'] = true;
    header("Location: reset_password.php");
    exit();
  } else {
    $msg = " Invalid OTP entered.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP | SecureRepair</title>
<link rel="stylesheet" href="../CSS/client.CSS">
</head>
<style>
  body {
  background: linear-gradient(rgba(0,0,0,0.9), rgba(0,0,0,0.9)),
              url(../IMAGES/CIRCUIT.jpg) no-repeat center/cover;
  }
  </style>
<body style="background:#0d0d0d;color:#fff;font-family:'Segoe UI',sans-serif;">
  <div class="container" style="max-width:400px;margin:100px auto;background:#111;padding:25px;border-radius:10px;border:2px solid #ff0033;">
    <h2 style="color:#ff0033;text-align:center;">Verify OTP</h2>
    <?php if(!empty($msg)) echo "<p style='color:#ff0033;text-align:center;'>$msg</p>"; ?>
    <form method="POST">
      <label>Enter OTP:</label>
      <input type="text" name="otp" placeholder="6-digit code" required style="width:100%;padding:10px;background:#222;color:#fff;border:none;border-radius:5px;margin-bottom:10px;">
      <button type="submit" name="verify_otp" style="width:100%;background:#ff0033;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer;font-weight:bold;">Verify</button>
    </form>
    <p style="text-align:center;margin-top:10px;"><a href="forgot_password.php" style="color:#ff0033;text-decoration:none;">Resend OTP</a></p>
  </div>
</body>
</html>
