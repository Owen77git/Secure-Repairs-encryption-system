<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include("../DATABASE/database.php");

// Fetch all sent messages
$messages = $conn->query("SELECT * FROM messages ORDER BY sent_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications | SecureRepair Admin</title>
  <link rel="stylesheet" href="../CSS/admin~menu.CSS">
  <style>
    body {
      background: #0d0d0d;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .content {
      padding: 20px;
    }
    h1 {
      color: #ff0033;
      margin-bottom: 15px;
    }
    .card {
      background: #111;
      padding: 20px;
      border-radius: 8px;
      border: 2px solid #ff0033;
      box-shadow: 0 0 10px rgba(255, 0, 51, 0.3);
      margin-top: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border-bottom: 1px dotted #444;
      padding: 10px;
      text-align: left;
    }
    th {
      color: #ff0033;
      font-size: 1rem;
      border-bottom: 2px solid #ff0033;
    }
    tr:hover {
      background-color: #1a1a1a;
    }
    p {
      color: #aaa;
    }
  </style>
</head>
<body>
 <header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
      <a href="technician.php" >Dashboard</a>
      <a href="repair~logs.php" >Repair Logs</a>
      <a href="reports.php">Reports</a>
      <a href="notifications.php" class="active" >Notifications</a>
      <a href="../destroy session/tech_logout.php">Logout</a>
    </nav>
  </header>


  <section class="content">
    <h1>📲 Client Notifications</h1>

    <div class="card">
      <h2>📧 Sent Messages Log</h2>

      <?php if ($messages && $messages->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Technician Email</th>
              <th>Client Email</th>
              <th>Title</th>
              <th>Message</th>
              <th>Sent On</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $messages->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['technician_email']); ?></td>
                <td><?= htmlspecialchars($row['client_email']); ?></td>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <td><?= nl2br(htmlspecialchars($row['message_body'])); ?></td>
                <td><?= htmlspecialchars($row['sent_at']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No notifications found in the database.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer>
    <p>© 2025 SecureRepair Admin Panel</p>
  </footer>
</body>
</html>
