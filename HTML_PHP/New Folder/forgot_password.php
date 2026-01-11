<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../DATABASE/database.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

$msg = "";

if (isset($_POST['send_otp'])) {
  $email = trim($_POST['email']);
  $check = $conn->query("SELECT * FROM clients WHERE email_phone='$email'");

  if ($check && $check->num_rows > 0) {
    $otp = rand(100000, 999999);
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_otp'] = $otp;
    $_SESSION['otp_expire'] = time() + 600; 

    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'oweneshuchi77@gmail.com';
      $mail->Password = 'zpifeqpnmmlnbzyd'; 
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->setFrom('oweneshuchi77@gmail.com', 'SecureRepair Systems');
      $mail->addAddress($email);
      $mail->isHTML(true);
      $mail->Subject = " SecureRepair Password Reset OTP";
      $mail->Body = "
        <div style='background:#0d0d0d;color:#fff;padding:25px;font-family:Segoe UI;border-radius:10px;'>
          <h2 style='color:#ff0033;text-align:center;'>SecureRepair Password Reset</h2>
          <p style='font-size:16px;'>Hello! Use the OTP below to reset your password. It expires in <b>10 minutes</b>.</p>
          <h1 style='color:#ff0033;text-align:center;'>$otp</h1>
          <p style='text-align:center;color:#888;'>© 2025 SecureRepair Systems</p>
        </div>";
      $mail->send();

      header("Location: verify_otp.php");
      exit();

    } catch (Exception $e) {
      $msg = " Failed to send OTP: {$mail->ErrorInfo}";
    }
  } else {
    $msg = "No client found with that email.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password | SecureRepair</title>
<link rel="stylesheet" href="../CSS/client.CSS">
</head>
<body style="background:#0d0d0d;color:#fff;font-family:'Segoe UI',sans-serif;">
  <div class="container" style="max-width:400px;margin:100px auto;background:#111;padding:25px;border-radius:10px;border:2px solid #ff0033;">
    <h2 style="color:#ff0033;text-align:center;">Forgot Password</h2>
    <?php if(!empty($msg)) echo "<p style='color:#ff0033;text-align:center;'>$msg</p>"; ?>
    <form method="POST">
      <label>Email Address:</label>
      <input type="email" name="email" placeholder="Enter your email" required style="width:100%;padding:10px;background:#222;color:#fff;border:none;border-radius:5px;margin-bottom:10px;">
      <button type="submit" name="send_otp" style="width:100%;background:#ff0033;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer;font-weight:bold;">Send OTP</button>
    </form>
    <p style="text-align:center;margin-top:10px;"><a href="client_login.php" style="color:#ff0033;text-decoration:none;">Back to Login</a></p>
  </div>
</body>
</html>
