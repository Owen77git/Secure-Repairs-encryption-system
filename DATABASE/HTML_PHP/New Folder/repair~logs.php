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

// Helper
function esc($conn, $v) { return $conn->real_escape_string(trim($v)); }

// ===== CREATE OTP =====
if (isset($_POST['create_otp'])) {
  $client = esc($conn, $_POST['client_name']);
  $otp = $_POST['otp'];

  if (empty($otp)) {
    $msg = "⚠️ OTP cannot be empty.";
  } else {
    $cp = $conn->query("SELECT * FROM client_preferences WHERE client_name='{$client}'");
    if ($cp && $cp->num_rows > 0) {
      $info = $cp->fetch_assoc();
      $hash = password_hash($otp, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("INSERT INTO repair_logs 
        (client_name, phone_model, mac_address, problem_description, restricted_data, contact_number, selected_tools, encryption_key_hash, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'locked')
        ON DUPLICATE KEY UPDATE encryption_key_hash=VALUES(encryption_key_hash), status='locked'");
      $stmt->bind_param('ssssssss', $client, $info['phone_model'], $info['mac_address'], $info['problem_description'], $info['restricted_data'], $info['contact_number'], $info['selected_tools'], $hash);
      $stmt->execute();
      $stmt->close();

      $msg = "🔒 OTP created and info locked for {$client}.";
    } else {
      $msg = "⚠️ Client record not found.";
    }
  }
}

// ===== UNLOCK =====
$unlockedClient = null;
if (isset($_POST['unlock'])) {
  $client = esc($conn, $_POST['client_name']);
  $otp = $_POST['otp'];

  $q = $conn->query("SELECT * FROM repair_logs WHERE client_name='{$client}' AND status!='done'");
  if ($q && $q->num_rows > 0) {
    $row = $q->fetch_assoc();
    if (password_verify($otp, $row['encryption_key_hash'])) {
      $unlockedClient = $row;
      $_SESSION['unlocked'][$client] = true;
      $_SESSION['otp'][$client] = $otp;
      $msg = "🔓 Access granted for {$client}.";
    } else {
      $msg = "❌ Invalid OTP for {$client}.";
    }
  } else {
    $msg = "⚠️ No repair log found or already completed.";
  }
}

// ===== SAVE REPORT & LOCK =====
if (isset($_POST['save_and_lock'])) {
  $client = esc($conn, $_POST['client_name']);
  $tech = esc($conn, $_POST['technician_name']);
  $report = esc($conn, $_POST['report']);
  $otp = $_SESSION['otp'][$client] ?? '';

  if (!empty($otp)) {
    $key = hash('sha256', $otp);
    $iv = substr(hash('sha256', 'fixed_iv'), 0, 16);
    $enc = openssl_encrypt($report, 'AES-256-CBC', $key, 0, $iv);

    $conn->query("UPDATE repair_logs 
      SET report=NULL, encrypted_report='{$enc}', technician_name='{$tech}', status='locked'
      WHERE client_name='{$client}'");

    unset($_SESSION['unlocked'][$client]);
    unset($_SESSION['otp'][$client]);
    $msg = "🔐 Report saved and locked for {$client}.";
  } else {
    $msg = "⚠️ Missing OTP session for {$client}.";
  }
}

// ===== DONE =====
if (isset($_POST['done'])) {
  $client = esc($conn, $_POST['client_name']);
  $conn->query("UPDATE repair_logs SET status='done', completed_at=NOW() WHERE client_name='{$client}'");
  unset($_SESSION['unlocked'][$client]);
  unset($_SESSION['otp'][$client]);
  $msg = "✅ {$client} marked as done and permanently locked.";
}

// Fetch clients
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
</style>
</head>
<body>

<header class="navbar">
  <div class="logo">⚡ SecureRepair Admin</div>
  <nav class="nav-links">
    <a href="technician.php" >Dashboard</a>
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
  $locked = ($log && $log['status'] === 'locked');
  $done = ($log && $log['status'] === 'done');
?>
<div class="log-card <?php echo $done ? 'done' : ''; ?>">
  <div class="client-name">
    <?php echo htmlspecialchars($client); ?>
    <?php 
      if ($done) echo " ✅ Completed";
      elseif ($locked) echo " 🔒 Locked";
      else echo " 🛠️ Active";
    ?>
  </div>

  <?php if ($done): ?>
    <p>🔒 This repair log is permanently locked and cannot be reopened.</p>

  <?php elseif (!$log): ?>
    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <input type="password" name="otp" placeholder="Create OTP" required>
      <button name="create_otp" type="submit">Create OTP & Lock</button>
    </form>

  <?php elseif ($locked && empty($_SESSION['unlocked'][$client])): ?>
    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <input type="password" name="otp" placeholder="Enter OTP to Unlock" required>
      <button name="unlock" type="submit">Unlock</button>
    </form>

  <?php elseif (!empty($_SESSION['unlocked'][$client])): ?>
    <div class="details-box">
      <p><strong>Phone Model:</strong> <?php echo htmlspecialchars($log['phone_model']); ?></p>
      <p><strong>MAC Address:</strong> <?php echo htmlspecialchars($log['mac_address']); ?></p>
      <p><strong>Problem:</strong> <?php echo htmlspecialchars($log['problem_description']); ?></p>
      <p><strong>Restricted:</strong> <?php echo htmlspecialchars($log['restricted_data']); ?></p>
      <p><strong>Contact:</strong> <?php echo htmlspecialchars($log['contact_number']); ?></p>
      <p><strong>Tools:</strong> <?php echo htmlspecialchars($log['selected_tools']); ?></p>
    </div>

    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <input type="text" name="technician_name" placeholder="Technician Name" required>
      <textarea name="report" placeholder="Write repair report..." required></textarea>
      <button name="save_and_lock" type="submit">Save Report & Lock</button>
    </form>

    <form method="POST">
      <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($client); ?>">
      <button name="done" type="submit">Mark Done</button>
    </form>
  <?php endif; ?>
</div>
<?php endwhile; ?>
</section>

<footer style="padding:15px;text-align:center;color:#888;">© 2025 SecureRepair Admin Panel</footer>
</body>
</html>
