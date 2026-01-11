<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}


include("../DATABASE/database.php");

/* ============= ADD TECHNICIAN ============= */
if (isset($_POST['add_technician'])) {
  $name = $_POST['technician_name'];
  $specialization = $_POST['specialization'];
 $password = $_POST['password'];

  // Hash password with MD5
  $hashed_password = md5($password);
  $email_phone = $_POST['email_phone'];

  $sql = "INSERT INTO technicians (technician_name, password, email_phone, specialization)
          VALUES ('$name', '$email_phone', $password, '$specialization')";
  $conn->query($sql);
  $_SESSION['msg'] = "✅ Technician added successfully!";
  header("Location: manage~technician.php");
  exit();
}

/* ============= DELETE TECHNICIAN ============= */
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM technicians WHERE id=$id");
  $_SESSION['msg'] = "🗑️ Technician deleted!";
  header("Location: manage~technician.php");
  exit();
}

/* ============= UPDATE STATUS ============= */
if (isset($_GET['update_status']) && isset($_GET['new_status'])) {
  $id = $_GET['update_status'];
  $new = $_GET['new_status'];
  $conn->query("UPDATE technicians SET status='$new' WHERE id=$id");
  $_SESSION['msg'] = "🔄 Technician marked as $new!";
  header("Location: manage~technician.php");
  exit();
}

/* ============= FETCH ALL TECHNICIANS ============= */
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
    }
    .msg {
      margin-top: 10px;
      color: #ff0033;
      font-weight: bold;
    }
    .btn {
      padding: 0.3rem 0.8rem;
      border-radius: 5px;
      text-decoration: none;
      color: #fff;
      font-weight: bold;
    }
    .status {
      background: #ffaa00;
      border: none;
      cursor: pointer;
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
      <a href="dashboard.php">Dashboard</a>
      <a href="manage~technicians" class="active" >Technicians</a>
      <a href="../destroy session/ad_logout.php">Logout</a>

    </nav>
  </header>

  <section class="content">
    <h1>🛠️ Manage Technicians</h1>

    <!-- Add Technician -->
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

    <!-- Technician List -->
    <div class="card" style="border: 2px dotted #ff0033;">
      <h2>👷 Registered Technicians</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email / Phone</th>
          <th>Specialization</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>

        <?php while ($row = $techs->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['technician_name']) ?></td>
          <td><?= htmlspecialchars($row['email_phone']) ?></td>
          <td><?= htmlspecialchars($row['specialization']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
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

    // Fade out messages after 3 seconds
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
