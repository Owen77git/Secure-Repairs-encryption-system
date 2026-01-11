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

/* =========================
   ADD NEW TOOL
   ========================= */
if (isset($_POST['add_tool'])) {
    $name = $conn->real_escape_string($_POST['tool_name']);
    $desc = $conn->real_escape_string($_POST['tool_description']);

    if (!empty($name) && !empty($desc)) {
        $sql = "INSERT INTO repair_tools (tool_name, tool_description, status)
                VALUES ('$name', '$desc', 'active')";
        if ($conn->query($sql)) {
            $msg = "✅ Tool '$name' added successfully!";
        } else {
            $msg = "❌ Database error: " . $conn->error;
        }
    } else {
        $msg = "⚠️ Please fill in all fields.";
    }
}

/* =========================
   DELETE TOOL
   ========================= */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM repair_tools WHERE id=$id");
    $msg = "🗑️ Tool deleted successfully!";
}

/* =========================
   TOGGLE TOOL STATUS
   ========================= */
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $tool = $conn->query("SELECT status FROM repair_tools WHERE id=$id")->fetch_assoc();
    $newStatus = ($tool['status'] === 'active') ? 'inactive' : 'active';
    $conn->query("UPDATE repair_tools SET status='$newStatus' WHERE id=$id");
    $msg = "🔄 Tool status changed to $newStatus!";
}

/* =========================
   FETCH TOOLS
   ========================= */
$tools = $conn->query("SELECT * FROM repair_tools ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Tools | SecureRepair Admin</title>
  <link rel="stylesheet" href="../CSS/dashboard.CSS">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
      <a href="dashboard.php">Dashboard</a>
      <a href="manage_tools.php" class="active">Manage Tools</a>
      <a href="repair~logs.php">Repair Logs</a>
      <a href="notifications.php">Notifications</a>
      <a href="../destroy session/ad_logout.php">Logout</a>
    </nav>
  </header>

  <main class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Menu</h2>
      <ul>
        <a href="dashboard.php"><li>Home</li></a>
        <a href="manage~technician.php"><li>Technicians</li></a>
        <a href="manage_tools.php"><li>Tools</li></a>
        <a href="repair~logs.php"><li>Repair Logs</li></a>
        <a href="reports.php"><li>Reports</li></a>
      </ul>
    </aside>

    <!-- Content -->
    <section class="content">
      <h1>🛠️ Manage Repair Tools</h1>
      <?php if (!empty($msg)) echo "<p style='color:#ff0033;font-weight:bold;'>$msg</p>"; ?>

      <!-- Add New Tool -->
      <div class="card">
        <h2>Add New Tool</h2>
        <form method="POST">
          <label for="tool_name">Tool Name:</label>
          <input type="text" name="tool_name" placeholder="Enter tool name" required>

          <label for="tool_description">Description:</label>
          <textarea name="tool_description" placeholder="Describe the tool..." required></textarea>

          <button type="submit" name="add_tool">Add Tool</button>
        </form>
      </div>

      <!-- Tool List -->
      <div class="card">
        <h2>Existing Tools</h2>
        <table style="width:100%;border-collapse:collapse;margin-top:1rem;">
          <tr style="background:#111;color:#ff0033;">
            <th style="padding:10px;border-bottom:1px solid #ff0033;">ID</th>
            <th style="padding:10px;border-bottom:1px solid #ff0033;">Name</th>
            <th style="padding:10px;border-bottom:1px solid #ff0033;">Description</th>
            <th style="padding:10px;border-bottom:1px solid #ff0033;">Status</th>
            <th style="padding:10px;border-bottom:1px solid #ff0033;">Actions</th>
          </tr>

          <?php while ($t = $tools->fetch_assoc()): ?>
          <tr style="border-bottom:1px solid #333;">
            <td style="padding:10px;"><?php echo $t['id']; ?></td>
            <td style="padding:10px;"><?php echo htmlspecialchars($t['tool_name']); ?></td>
            <td style="padding:10px;"><?php echo htmlspecialchars($t['tool_description']); ?></td>
            <td style="padding:10px;"><?php echo $t['status'] === 'active' ? '✅ Active' : '⛔ Inactive'; ?></td>
            <td style="padding:10px;">
              <a href="?toggle=<?php echo $t['id']; ?>" style="color:#ffaa00;text-decoration:none;margin-right:10px;">Toggle</a>
              <a href="?delete=<?php echo $t['id']; ?>" style="color:#ff4444;text-decoration:none;" onclick="return confirm('Delete this tool?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 SecureRepair Admin Panel</p>
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded",()=>{
      const m=document.querySelector("p[style*='color:#ff0033']");
      if(m){setTimeout(()=>{m.style.opacity='0';m.remove();},3000);}
    });
  </script>

</body>
</html>
