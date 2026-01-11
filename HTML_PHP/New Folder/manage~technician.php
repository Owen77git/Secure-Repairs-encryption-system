<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Redirect if not logged in
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include("../DATABASE/database.php");

// ================== ADD TECHNICIAN ==================
if (isset($_POST['add_technician'])) {
    $name = trim($_POST['technician_name']);
    $email_phone = trim($_POST['email_phone']);
    $specialization = trim($_POST['specialization']);
    $password = trim($_POST['password']);

    // ✅ Hash password securely (MD5)
    $hashed_password = md5($password);

    // ✅ Insert data properly
    $sql = "INSERT INTO technicians (technician_name, password, email_phone, specialization)
            VALUES ('$name', '$hashed_password', '$email_phone', '$specialization')";

    if ($conn->query($sql)) {
        $_SESSION['msg'] = "✅ Technician added successfully!";
    } else {
        $_SESSION['msg'] = "❌ Error: " . $conn->error;
    }

    header("Location: manage~technician.php");
    exit();
}

// ================== DELETE TECHNICIAN ==================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM technicians WHERE id = $id");
    $_SESSION['msg'] = "🗑️ Technician deleted!";
    header("Location: manage~technician.php");
    exit();
}

// ================== UPDATE STATUS ==================
if (isset($_GET['update_status']) && isset($_GET['new_status'])) {
    $id = intval($_GET['update_status']);
    $new_status = $_GET['new_status'];
    $conn->query("UPDATE technicians SET status = '$new_status' WHERE id = $id");
    $_SESSION['msg'] = "🔄 Technician marked as $new_status!";
    header("Location: manage~technician.php");
    exit();
}

// ================== FETCH TECHNICIANS ==================
$techs = $conn->query("SELECT * FROM technicians ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Technicians | SecureRepair Admin</title>
  <link rel="stylesheet" href="../CSS/admin~menu.CSS" />
  <style>
    body { background: #0d0d0d; color: #fff; font-family: 'Segoe UI', sans-serif; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 0.8rem;
      border: 1px solid #ff0033;
      text-align: left;
    }
    th {
      background: #111;
      color: #ff0033;
    }
    td {
      background: #1a1a1a;
    }
    .card {
      margin-bottom: 20px;
      border: 2px solid #ff0033;
      border-radius: 8px;
      padding: 20px;
      background: #111;
    }
    .msg {
      margin-top: 10px;
      color: #ff0033;
      font-weight: bold;
    }
    input, button {
      display: block;
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 5px;
      border: 1px solid #ff0033;
      background: #1a1a1a;
      color: white;
    }
    button {
      background: #ff0033;
      cursor: pointer;
      font-weight: bold;
    }
    .btn {
      padding: 0.3rem 0.8rem;
      border-radius: 5px;
      text-decoration: none;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }
    .status {
      background: #ffaa00;
      border: none;
    }
    .dropdown {
      display: none;
      flex-direction: column;
      background: #111;
      border: 1px solid #ff0033;
      border-radius: 6px;
      position: absolute;
      top: 35px;
      right: 0;
      z-index: 10;
      padding: 5px;
      min-width: 130px;
    }
    .dropdown a {
      padding: 6px 10px;
      text-decoration: none;
      color: #fff;
      display: block;
      font-size: 0.9rem;
      border-bottom: 1px solid #333;
      transition: 0.2s;
    }
    .dropdown a:hover {
      background: #ff0033;
    }
    .dropdown a.delete {
      color: #ff4444;
    }
  </style>
</head>

<body>
  <header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
      <a href="cctv~monitoring.HTML">Live~CCTV</a>
      <a href="tools.php"><li>manage tools</li></a>
      <a href="dashboard.php">Dashboard</a>
      <a href="manage~technician.php" class="active">Technicians</a>
      <a href="../destroy session/ad_logout.php">Logout</a>
    </nav>
  </header>

  <section class="content">
    <h1>🛠️ Manage Technicians</h1>

    <!-- ADD TECHNICIAN -->
    <div class="card">
      <form method="POST">
        <label>Technician Name:</label>
        <input type="text" name="technician_name" placeholder="Enter technician name" required />

        <label>Email / Phone:</label>
        <input type="text" name="email_phone" placeholder="Enter contact info" required />

        <label>Specialization:</label>
        <input type="text" name="specialization" placeholder="e.g. Hardware, Software, Networking" required />

        <label>Password:</label>
        <input type="text" name="password" placeholder="!2gf6jA" required />

        <button type="submit" name="add_technician">Add Technician</button>

        <?php
          if (!empty($_SESSION['msg'])) {
            echo "<p class='msg'>{$_SESSION['msg']}</p>";
            unset($_SESSION['msg']);
          }
        ?>
      </form>
    </div>

    <!-- TECHNICIAN LIST -->
    <div class="card">
      <h2>👷 Registered Technicians</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email / Phone</th>
          <th>Specialization</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>

        <?php while ($row = $techs->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['technician_name']) ?></td>
          <td><?= htmlspecialchars($row['email_phone']) ?></td>
          <td><?= htmlspecialchars($row['specialization']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td style="position: relative;">
            <button type="button" class="btn status" onclick="toggleOptions(this)">Options</button>
            <div class="dropdown">
              <a href="?update_status=<?= $row['id'] ?>&new_status=cleared">✅ Cleared</a>
              <a href="?update_status=<?= $row['id'] ?>&new_status=monitored">👁️ Monitored</a>
              <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this technician?')" class="delete">🗑️ Delete</a>
            </div>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </section>

  <footer>
    <p>© 2025 SecureRepair Admin Panel</p>
  </footer>

  <script>
    // Toggle dropdown visibility
    function toggleOptions(button) {
      const dropdown = button.nextElementSibling;
      const allDropdowns = document.querySelectorAll('.dropdown');
      allDropdowns.forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
      });
      dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
    }

    // Close dropdown if clicked outside
    document.addEventListener('click', function(e) {
      if (!e.target.matches('.btn.status')) {
        document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none');
      }
    });

    // Fade out messages
    document.addEventListener('DOMContentLoaded', function() {
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
