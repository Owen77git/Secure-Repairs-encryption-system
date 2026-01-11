<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['technician']) || empty($_SESSION['technician'])) {
  header("Location: technician_login.php");
  exit();
}


include("../DATABASE/database.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

$msg = "";
$decrypted = [];

/* ============ UNLOCK REPORT ============ */
if (isset($_POST['unlock_report'])) {
  $client_name = $_POST['client_name'];
  $otp = $_POST['otp'];

  $query = $conn->query("SELECT * FROM reports WHERE client_name='$client_name'");
  if ($query && $query->num_rows > 0) {
    $row = $query->fetch_assoc();
    $otpCheck = $conn->query("SELECT otp_hash FROM client_preferences WHERE client_name='$client_name'");
    $otpData = $otpCheck->fetch_assoc();

    if (password_verify($otp, $otpData['otp_hash'])) {
      $key = hash('sha256', $otp);
      $iv = substr(hash('sha256', 'fixed_iv'), 0, 16);
      $decrypted_report = openssl_decrypt($row['encrypted_report'], "AES-256-CBC", $key, 0, $iv);
      $decrypted[$client_name] = [
        'report' => $decrypted_report,
        'technician' => $row['technician_name']
      ];
    } else {
      $msg = "Invalid OTP for $client_name.";
    }
  } else {
    $msg = "No encrypted report found for $client_name.";
  }
}

if (isset($_POST['send_email'])) {
  $client_email = $_POST['client_email'];
  $client_name = $_POST['client_name'];
  $technician = $_POST['technician_name'];
  $report_text = $_POST['report_content'];

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
    $mail->addAddress($client_email);
    $mail->isHTML(true);
    $mail->Subject = "⚡ SecureRepair Report for $client_name";

    $mail->Body = "
    <html>
    <head>
    <style>
      body {
        background-color: #0d0d0d;
        color: #f2f2f2;
        font-family: 'Segoe UI', sans-serif;
        padding: 40px;
      }
      .container {
        background: #111;
        border: 2px solid #ff0033;
        border-radius: 10px;
        box-shadow: 0 0 25px rgba(255, 0, 51, 0.3);
        padding: 30px;
      }
      .header {
        text-align: center;
        color: #ff0033;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
      }
      .divider {
        height: 2px;
        background: #ff0033;
        border: none;
        margin: 20px 0;
      }
      .info p {
        font-size: 16px;
        color: #ccc;
        margin: 5px 0;
      }
      pre {
        background: #1a1a1a;
        color: #ddd;
        padding: 15px;
        border-radius: 6px;
        font-size: 14px;
        white-space: pre-wrap;
      }
      .footer {
        margin-top: 25px;
        text-align: center;
        color: #888;
        font-size: 13px;
        border-top: 1px solid #ff0033;
        padding-top: 10px;
      }
      .footer strong {
        color: #ff0033;
      }
    </style>
    </head>
    <body>
      <div class='container'>
        <div class='header'>⚡ SecureRepair Service Report</div>
        <div class='info'>
          <p><strong>Client:</strong> $client_name</p>
          <p><strong>Technician:</strong> $technician</p>
        </div>
        <hr class='divider'>
        <h3 style='color:#ff0033;'>Repair Summary</h3>
        <pre>$report_text</pre>
        <div class='footer'>
          <p>© 2025 <strong>SecureRepair Systems</strong> | Confidential Communication</p>
        </div>
      </div>
    </body>
    </html>";

    $mail->send();
    $msg = "✅ Report sent successfully to $client_email.";
  } catch (Exception $e) {
    $msg = "Email failed: {$mail->ErrorInfo}";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Reports | SecureRepair Admin</title>
<link rel="stylesheet" href="../CSS/admin~menu.CSS">
<style>
body{background:#0d0d0d;color:#fff;font-family:'Segoe UI',sans-serif;}
.content{padding:20px;}
.card{background:#111;padding:20px;border:2px solid #ff0033;border-radius:10px;margin-top:20px;}
.msg{color:#ff0033;font-weight:bold;}
pre{background:#1a1a1a;padding:10px;border-radius:8px;color:#ccc;}
button{background:#ff0033;color:#fff;border:none;padding:8px 15px;border-radius:6px;font-weight:bold;cursor:pointer;}
button:hover{background:#fff;color:#ff0033;}
input{background:#222;color:#fff;border:none;border-radius:5px;padding:8px;margin-top:5px;width:100%;}
</style>
</head>
<body>
<header class="navbar">
  <div class="logo">⚡ SecureRepair Admin</div>
  <nav class="nav-links">
    <a href="technician.php">Dashboard</a>
    <a href="reports.php" class="active">Reports</a>
    <a href="../destroy session/tech_logout.php">Logout</a>
  </nav>
</header>

<section class="content">
<h1>Reports</h1>
<?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>

<?php
$clients = $conn->query("SELECT client_name, technician_name, encrypted_report FROM reports ORDER BY id DESC");
while ($c = $clients->fetch_assoc()):
$client = $c['client_name'];
?>
<div class="card">
<h3> <?php echo htmlspecialchars($client); ?> — Encrypted Report</h3>

<?php if (!isset($decrypted[$client])): ?>
<form method="POST">
  <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
  <label>Enter OTP to Unlock:</label>
  <input type="password" name="otp" placeholder="Enter OTP" required>
  <button type="submit" name="unlock_report">Unlock Report</button>
</form>
<?php else: ?>
  <div style="margin-top:15px;">
    <h4> Decrypted Report (by <?php echo htmlspecialchars($decrypted[$client]['technician']); ?>):</h4>
    <pre><?php echo htmlspecialchars($decrypted[$client]['report']); ?></pre>

    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <input type="hidden" name="technician_name" value="<?php echo htmlspecialchars($decrypted[$client]['technician']); ?>">
      <input type="hidden" name="report_content" value="<?php echo htmlspecialchars($decrypted[$client]['report']); ?>">
      <label>Client Email:</label>
      <input type="email" name="client_email" placeholder="Enter Client Email" required>
      <button type="submit" name="send_email">Send Report to Client</button>
    </form>
  </div>
<?php endif; ?>
</div>
<?php endwhile; ?>
</section>

<footer style="text-align:center;color:#888;padding:20px 0;">© 2025 SecureRepair Admin Panel</footer>
</body>
</html>
