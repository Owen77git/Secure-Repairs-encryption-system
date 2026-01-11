<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../DATABASE/database.php");

if (!isset($_SESSION['client_name'])) {
    header("Location: client_login.php");
    exit();
}

$client_name = $_SESSION['client_name'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_preferences'])) {
    $phone_model = $_POST['phone_model'];
    $mac_address = $_POST['mac_address'];
    $problem_description = $_POST['problem_description'];
    $restricted_data = $_POST['restricted_data'];
    $contact_number = $_POST['contact_number'];
    $tools = isset($_POST['tools']) ? implode(", ", $_POST['tools']) : "";

    // Hash OTP (if provided)
    $otp = trim($_POST['otp']);
    $otp_hash = !empty($otp) ? password_hash($otp, PASSWORD_DEFAULT) : null;

    $stmt = $conn->prepare("
        INSERT INTO client_preferences 
        (client_name, phone_model, mac_address, problem_description, restricted_data, contact_number, selected_tools, otp_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        phone_model = VALUES(phone_model),
        mac_address = VALUES(mac_address),
        problem_description = VALUES(problem_description),
        restricted_data = VALUES(restricted_data),
        contact_number = VALUES(contact_number),
        selected_tools = VALUES(selected_tools),
        otp_hash = VALUES(otp_hash)
    ");
    $stmt->bind_param("ssssssss", $client_name, $phone_model, $mac_address, $problem_description, $restricted_data, $contact_number, $tools, $otp_hash);
    $stmt->execute();
    $stmt->close();

    $msg = "✅ Preferences and OTP saved successfully!";
}

if (isset($_GET['download']) && $_GET['download'] == "word") {
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=Client_Preferences_Report.doc");

    $query = $conn->query("SELECT * FROM client_preferences WHERE client_name='$client_name' ORDER BY id DESC LIMIT 1");
    $data = $query->fetch_assoc();

    echo "<html><body style='font-family:Calibri, sans-serif;'>";
    echo "<h1 style='color:#ff0033;'>Client Repair Preferences Report</h1>";
    echo "<p><strong>Client Name:</strong> {$data['client_name']}</p>";
    echo "<p><strong>Phone Model:</strong> {$data['phone_model']}</p>";
    echo "<p><strong>MAC Address:</strong> {$data['mac_address']}</p>";
    echo "<p><strong>Problem Description:</strong> {$data['problem_description']}</p>";
    echo "<p><strong>Restricted Data:</strong> {$data['restricted_data']}</p>";
    echo "<p><strong>Contact Number:</strong> {$data['contact_number']}</p>";
    echo "<p><strong>Selected Tools:</strong> {$data['selected_tools']}</p>";
    echo "<p><strong>Created At:</strong> {$data['created_at']}</p>";
    echo "</body></html>";
    exit();
}

$tools = $conn->query("SELECT * FROM repair_tools WHERE status='active' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Dashboard | SecureRepair</title>
<style>
/* Reset */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
body { background: #0d0d0d; color: #f2f2f2; }

/* Header */
.header {
  text-align: center;
  padding: 2rem;
  background: #111;
  border-bottom: 3px solid #ff0033;
}
.header h1 { color: #ff0033; margin-bottom: 0.5rem; }
.header p { color: #aaa; }

/* Dashboard Layout */
.dashboard {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  padding: 2rem 10%;
}

/* Left Panel */
.left-panel, .right-panel {
  background: #111;
  padding: 1.5rem;
  border: 2px solid #ff0033;
  border-radius: 10px;
}
.left-panel h2, .right-panel h2 { color: #ff0033; margin-bottom: 1rem; }
label { display: block; margin: 0.5rem 0 0.2rem; }
input, textarea {
  width: 100%; padding: 0.8rem; margin-bottom: 1rem;
  border: none; border-radius: 5px;
  background: #222; color: #fff;
}
input:focus, textarea:focus { outline: 2px solid #ff0033; }

/* Tools List */
.tools-list {
  display: flex; flex-direction: column;
  gap: 0.8rem;
}
.tools-list label {
  background: #1a1a1a;
  padding: 0.6rem;
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}
.tools-list label:hover { background: #ff0033; color: #fff; }

/* Footer Actions */
.actions {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  padding: 1.5rem;
  background: #111;
  border-top: 3px solid #ff0033;
}
button {
  background: #ff0033; color: #fff; padding: 0.8rem 1.5rem;
  border: none; border-radius: 5px; font-weight: bold; cursor: pointer;
  transition: 0.3s;
}
button:hover { background: #fff; color: #ff0033; box-shadow: 0 0 15px #ff0033; }

/* Responsive */
@media (max-width: 900px) {
  .dashboard { grid-template-columns: 1fr; }
}


.tools-list label {
  display: block;
  background: #1a1a1a;
  border-radius: 6px;
  padding: 8px;
  margin: 6px 0;
  cursor: pointer;
}
.actions {
  text-align: center;
  margin: 20px 0;
}
.msg {
  color: #ff0033;
  font-weight: bold;
  margin-top: 10px;
}
</style>
</head>
<body>

<header class="header">
  <h1>⚡ Client Control Dashboard — <strong><?php echo htmlspecialchars($client_name); ?></strong></h1>
  <p>Manage your repair details and security preferences</p>
</header>

<main class="dashboard">
  <!-- Left Panel -->
  <section class="left-panel">
    <h2>📱 Device Information</h2>
    <form method="POST">
      <label>Phone Model</label>
      <input type="text" name="phone_model" placeholder="e.g. Samsung S23 Ultra" required>

      <label>MAC Address</label>
      <input type="text" name="mac_address" placeholder="e.g. 00:1B:44:11:3A:B7" required>

      <label>Problem Description</label>
      <textarea name="problem_description" placeholder="Describe the issue..." required></textarea>

      <label>Data NOT to Access</label>
      <textarea name="restricted_data" placeholder="e.g. Gallery, WhatsApp, Banking Apps" required></textarea>

      <label>Contact Number</label>
      <input type="text" name="contact_number" placeholder="+254700000000" required>

      <label>Create Your OTP</label>
      <input type="password" name="otp" placeholder="Create secure OTP" required>
  </section>

  <!-- Right Panel -->
  <section class="right-panel">
    <h2>Repair Tools</h2>
    <p>Select which tools can be used during repair:</p>
    <div class="tools-list">
      <?php
      if ($tools && $tools->num_rows > 0) {
        while ($tool = $tools->fetch_assoc()) {
          echo "<label><input type='checkbox' name='tools[]' value='{$tool['tool_name']}'> {$tool['tool_name']} — <small>{$tool['tool_description']}</small></label>";
        }
      } else {
        echo "<p style='color:#aaa;'>No tools available right now.</p>";
      }
      ?>
    </div>
  </section>
</main>

<!-- Footer Actions -->
<footer class="actions">
  <a href="../destroy session/cli_logout.php"><button type="button">LogOut</button>
  <button type="submit" name="save_preferences">Save Preferences</button>
  <a href="?download=word"><button type="button">Download My Choices</button></a>
  <a href="extra.php"><button type="button">Additional Context</button></a>
  <?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>
</footer>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const msg = document.querySelector('.msg');
  if (msg) {
    setTimeout(() => {
      msg.style.transition = 'opacity 0.5s';
      msg.style.opacity = '0';
      setTimeout(() => msg.remove(), 500);
    }, 3000);
  }
});
</script>

</body>
</html>
