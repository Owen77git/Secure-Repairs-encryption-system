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
$stage = "forgot"; // default stage

// ---------------------- STEP 1: SEND OTP ----------------------
if (isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);
    $check = $conn->query("SELECT * FROM clients WHERE email_phone='$email'");

    if ($check && $check->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_expire'] = time() + 600;
        $stage = "verify";

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
            $mail->Subject = "SecureRepair Password Reset OTP";
            $mail->Body = "
            <div style='background:#0d0d0d;color:#fff;padding:25px;font-family:Segoe UI;border-radius:10px;'>
                <h2 style='color:#ff0033;text-align:center;'>SecureRepair Password Reset</h2>
                <p style='font-size:16px;'>Use the OTP below to reset your password. It expires in <b>10 minutes</b>.</p>
                <h1 style='color:#ff0033;text-align:center;'>$otp</h1>
                <p style='text-align:center;color:#888;'>© 2025 SecureRepair Systems</p>
            </div>";
            $mail->send();
            $msg = "✅ OTP sent to your email.";
        } catch (Exception $e) {
            $msg = "Failed to send OTP: {$mail->ErrorInfo}";
            $stage = "forgot";
        }
    } else {
        $msg = "No client found with that email.";
    }
}

// ---------------------- STEP 2: VERIFY OTP ----------------------
if (isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);
    if (time() > $_SESSION['otp_expire']) {
        $msg = "OTP expired. Please request a new one.";
        $stage = "forgot";
    } elseif ($otp == $_SESSION['reset_otp']) {
        $_SESSION['otp_verified'] = true;
        $stage = "reset";
    } else {
        $msg = "Invalid OTP entered.";
        $stage = "verify";
    }
}

// ---------------------- STEP 3: RESET PASSWORD ----------------------
if (isset($_POST['reset_password'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if ($new_pass !== $confirm_pass) {
        $msg = "Passwords do not match.";
        $stage = "reset";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
        $update = $conn->query("UPDATE clients SET password='$hashed_pass' WHERE email_phone='$email'");
        if ($update) {
            $msg = "✅ Password updated successfully! You can now log in.";
            session_unset();
            session_destroy();
            $stage = "done";
        } else {
            $msg = "Database error updating password.";
            $stage = "reset";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot / Reset Password | SecureRepair</title>
<style>
/* ========== CYBER STYLE ========== */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
body {
  background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.9)),
              url(../IMAGES/CIRCUIT.jpg) no-repeat center/cover;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: #fff;
}
.container {
  background: rgba(17,17,17,0.9);
  padding: 2rem;
  width: 420px;
  border: 2px solid #ff0033;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(255,0,51,0.3);
  backdrop-filter: blur(8px);
}
h2 {
  color: #ff0033;
  text-align: center;
  margin-bottom: 1.5rem;
}
label { display: block; margin: 0.5rem 0 0.2rem; }
input {
  width: 100%;
  padding: 0.8rem;
  margin-bottom: 1rem;
  border: none;
  border-radius: 5px;
  background: #222;
  color: #fff;
}
input:focus { outline: 2px solid #ff0033; }
button {
  background: #ff0033;
  color: #fff;
  padding: 0.8rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  width: 100%;
  transition: 0.3s;
}
button:hover { background: #fff; color: #ff0033; box-shadow: 0 0 15px #ff0033; }
.message {
  text-align: center;
  font-size: 0.9rem;
  color: #ff8080;
  margin-top: 0.5rem;
}
.success { color: #9eff9e; }
.link { text-align: center; margin-top: 1rem; }
.link a { color: #ff0033; text-decoration: none; font-weight: bold; }
.link a:hover { color: #fff; }
.fade { animation: fadeIn 0.8s ease; }
@keyframes fadeIn { from {opacity:0; transform:translateY(15px);} to {opacity:1; transform:translateY(0);} }
</style>
</head>
<body>

<div class="container fade">

  <?php if ($stage == "forgot"): ?>
  <!-- STEP 1: FORGOT PASSWORD -->
  <h2>Forgot Password</h2>
  <form method="POST">
    <label>Email Address:</label>
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit" name="send_otp">Send OTP</button>
    <?php if(!empty($msg)) echo "<p class='message'>$msg</p>"; ?>
  </form>
  <div class="link"><a href="client_login.php">Back to Login</a></div>

  <?php elseif ($stage == "verify"): ?>
  <!-- STEP 2: VERIFY OTP -->
  <h2>Verify OTP</h2>
  <form method="POST">
    <label>Enter OTP:</label>
    <input type="text" name="otp" placeholder="6-digit code" required>
    <button type="submit" name="verify_otp">Verify</button>
    <?php if(!empty($msg)) echo "<p class='message'>$msg</p>"; ?>
  </form>
  <div class="link"><a href="client_forgot_reset.php">Resend OTP</a></div>

  <?php elseif ($stage == "reset"): ?>
  <!-- STEP 3: RESET PASSWORD -->
  <h2>Reset Password</h2>
  <form method="POST">
    <label>New Password:</label>
    <input type="password" name="new_password" placeholder="Enter new password" required>
    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" placeholder="Confirm password" required>
    <button type="submit" name="reset_password">Update Password</button>
    <?php if(!empty($msg)) echo "<p class='message'>$msg</p>"; ?>
  </form>

  <?php elseif ($stage == "done"): ?>
  <!-- STEP 4: SUCCESS -->
  <h2>Password Reset Successful</h2>
  <p class="message success"><?php echo $msg; ?></p>
  <div class="link"><a href="client_login.php">Return to Login</a></div>

  <?php endif; ?>

</div>
</body>
</html>
