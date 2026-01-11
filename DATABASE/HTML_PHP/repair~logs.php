<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit();
}

include("../DATABASE/database.php");

$msg = "";

/* ============ Helper ============ */
function esc($conn, $v) {
  return $conn->real_escape_string(trim($v));
}

/* ============ Unlock Client via OTP ============ */
if (isset($_POST['unlock'])) {
  $client = esc($conn, $_POST['client_name']);
  $otp = trim($_POST['otp']);

  $otpCheck = $conn->query("SELECT otp_hash, account_locked FROM client_preferences WHERE client_name='$client' LIMIT 1");
  if ($otpCheck && $otpCheck->num_rows > 0) {
    $clientData = $otpCheck->fetch_assoc();

    if ($clientData['account_locked'] == 1) {
      $msg = "🔒 Client account permanently locked.";
    } elseif (password_verify($otp, $clientData['otp_hash'])) {
      $_SESSION['unlocked'][$client] = true;
      $_SESSION['otp'][$client] = $otp;
      $msg = "🔓 Access granted for {$client}.";
    } else {
      $msg = "❌ Invalid OTP for {$client}.";
    }
  } else {
    $msg = "⚠️ Client OTP not found.";
  }
}

/* ============ Save Report & Lock Everything ============ */
if (isset($_POST['save_and_done'])) {
  $client = esc($conn, $_POST['client_name']);
  $tech = esc($conn, $_POST['technician_name']);
  $otp = $_SESSION['otp'][$client] ?? '';

  $steps = [];
  for ($i = 1; $i <= 10; $i++) {
    $steps[$i] = esc($conn, $_POST["step_$i"] ?? '');
  }

  $report_text = "";
  foreach ($steps as $num => $step) {
    if (!empty($step)) $report_text .= "Step $num: $step\n";
  }

  if (!empty($otp)) {
    $key = hash('sha256', $otp);
    $iv = substr(hash('sha256', 'fixed_iv'), 0, 16);
    $encrypted_report = openssl_encrypt($report_text, 'AES-256-CBC', $key, 0, $iv);

    // Store report
    $stmt = $conn->prepare("INSERT INTO reports (client_name, technician_name, encrypted_report, status)
                            VALUES (?, ?, ?, 'done')");
    $stmt->bind_param("sss", $client, $tech, $encrypted_report);
    $stmt->execute();
    $stmt->close();

    // Mark repair as done
    $conn->query("UPDATE repair_logs SET status='done', completed_at=NOW() WHERE client_name='$client'");

    // Lock client permanently in preferences
    $conn->query("UPDATE client_preferences SET account_locked=1 WHERE client_name='$client'");

    // Clear session
    unset($_SESSION['unlocked'][$client]);
    unset($_SESSION['otp'][$client]);

    $msg = "✅ Report saved and client permanently locked.";
  } else {
    $msg = "⚠️ Unlock client with OTP before saving.";
  }
}

/* ============ Fetch Clients ============ */
$clients = $conn->query("SELECT * FROM client_preferences ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Repair Logs | SecureRepair Admin</title>
<link rel="stylesheet" href="../CSS/admin~menu.CSS">
<style>
body { background:#0d0d0d; color:#fff; font-family:'Segoe UI',sans-serif; margin:0; padding:0; }
.content { padding:20px; }
.log-card { border:2px dotted #ff0033; margin:20px 0; padding:1rem; border-radius:10px; background:#111; }
.client-name { font-weight:bold; color:#ff0033; font-size:1.1rem; margin-bottom:8px; }
.msg { color:#ff0033; margin:10px 0; font-weight:bold; }
input, textarea { width:100%; background:#222; color:#fff; border:none; border-radius:5px; padding:8px; margin:6px 0; }
button { background:#ff0033; color:#fff; border:none; padding:8px 12px; border-radius:6px; font-weight:bold; cursor:pointer; }
button:hover { background:#ff1a1a; }
.details-box { background:#1a1a1a; padding:10px; border-radius:6px; margin-top:10px; }
.done { background:#333; color:#888; border:2px dashed #444; }
.step-box { border:1px solid #333; background:#1a1a1a; border-radius:8px; padding:8px; margin-bottom:6px; }
</style>
</head>
<body>

<header class="navbar">
  <div class="logo">⚡ SecureRepair Admin</div>
  <nav class="nav-links">
    <a href="technician.php">Dashboard</a>
    <a href="repair~logs.php" class="active">Repair Logs</a>
    <a href="reports.php">Reports</a>
    <a href="notifications.php">Notifications</a>
    <a href="../destroy session/tech_logout.php">Logout</a>
  </nav>
</header>

<section class="content">
<h1>📑 Repair Logs</h1>
<?php if (!empty($msg)) echo "<p class='msg'>{$msg}</p>"; ?>

<?php while ($row = $clients->fetch_assoc()):
  $client = $row['client_name'];

  $logRes = $conn->query("SELECT * FROM repair_logs WHERE client_name='".$conn->real_escape_string($client)."' LIMIT 1");
  $log = ($logRes && $logRes->num_rows>0) ? $logRes->fetch_assoc() : null;

  $done = ($log && $log['status'] === 'done') || ($row['account_locked'] == 1);
?>
<div class="log-card <?php echo $done ? 'done' : ''; ?>">
  <div class="client-name">
    <?php echo htmlspecialchars($client); ?>
    <?php 
      if ($done) echo " ✅ Completed";
      elseif ($log && $log['status'] === 'locked') echo " 🔒 Locked";
      else echo " 🛠️ Active";
    ?>
  </div>

  <?php if ($done): ?>
    <p>🔒 This repair log is permanently locked and completed.</p>

  <?php elseif (empty($_SESSION['unlocked'][$client])): ?>
    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <input type="password" name="otp" placeholder="Enter Client OTP" required>
      <button name="unlock" type="submit">Unlock Client</button>
    </form>

  <?php else: ?>
    <div class="details-box">
      <p><strong>Phone Model:</strong> <?php echo htmlspecialchars($row['phone_model']); ?></p>
      <p><strong>MAC Address:</strong> <?php echo htmlspecialchars($row['mac_address']); ?></p>
      <p><strong>Problem:</strong> <?php echo htmlspecialchars($row['problem_description']); ?></p>
      <p><strong>Restricted:</strong> <?php echo htmlspecialchars($row['restricted_data']); ?></p>
      <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></p>
      <p><strong>Tools:</strong> <?php echo htmlspecialchars($row['selected_tools']); ?></p>
    </div>

    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <input type="text" name="technician_name" placeholder="Technician Name" required>

      <?php for ($i=1; $i<=10; $i++): ?>
        <div class="step-box">
          <label>Step <?php echo $i; ?>:</label>
          <textarea name="step_<?php echo $i; ?>" placeholder="Describe step <?php echo $i; ?>"></textarea>
        </div>
      <?php endfor; ?>

      <button name="save_and_done" type="submit">Save Report & Done (Lock Permanently)</button>
    </form>
  <?php endif; ?>
</div>
<?php endwhile; ?>
</section>

<footer style="padding:15px;text-align:center;color:#888;">© 2025 SecureRepair Admin Panel</footer>
</body>
</html>
