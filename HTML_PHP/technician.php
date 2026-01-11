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

if (isset($_GET['delete_id'])) {
  $delete_id = intval($_GET['delete_id']);
  $conn->query("DELETE FROM clients WHERE id = $delete_id");
}

if (isset($_POST['register_client'])) {
  $name = trim($_POST['client_name']);
  $email_phone = trim($_POST['email_phone']);
  $password = $_POST['password'];
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  $sql = "INSERT INTO clients (client_name, email_phone, password)
          VALUES ('$name', '$email_phone', '$hashed_password')";

  if ($conn->query($sql)) {
    $client_message = "✅ Client registered successfully!";
  } else {
    $client_message = "Error: " . $conn->error;
  }
}

if (isset($_POST['send_message'])) {
  $technician_email = $_POST['technician_email'];
  $client_email = $_POST['client_email'];
  $title = $_POST['title'];
  $message_body = $_POST['message_body'];

  $sql = "INSERT INTO messages (technician_email, client_email, title, message_body)
          VALUES ('$technician_email', '$client_email', '$title', '$message_body')";
  $conn->query($sql);

  $styledBody = "
  <html>
  <head>
  <style>
  body {
    background-color: #0d0d0d;
    color: #f2f2f2;
    font-family: 'Segoe UI', sans-serif;
    padding: 30px;
  }
  .container {
    border: 2px solid #ff0033;
    border-radius: 10px;
    padding: 20px;
    background: #111;
  }
  h2 {
    color: #ff0033;
    text-align: center;
  }
  p {
    color: #ccc;
    font-size: 15px;
  }
  .footer {
    border-top: 1px solid #ff0033;
    margin-top: 20px;
    text-align: center;
    font-size: 13px;
    color: #888;
  }
  </style>
  </head>
  <body>
  <div class='container'>
    <h2>⚡ SecureRepair Report</h2>
    <p><strong>From:</strong> $technician_email</p>
    <p><strong>Subject:</strong> $title</p>
    <hr>
    <p>$message_body</p>
    <div class='footer'>
      <p>© 2025 SecureRepair Systems | Confidential Communication</p>
    </div>
  </div>
  </body>
  </html>";

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'oweneshuchi77@gmail.com'; 
    $mail->Password = 'zpifeqpnmmlnbzyd';       
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($technician_email, 'SecureRepair Technician');
    $mail->addAddress($client_email);
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body = $styledBody;

    $mail->send();
    $email_message = "✅ Message sent successfully to $client_email!";
  } catch (Exception $e) {
    $email_message = "Email could not be sent. Error: {$mail->ErrorInfo}";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Secure Repair System</title>
  <link rel="stylesheet" href="../CSS/dashboard.CSS">
  <style>
    .client-list {
      margin-top: 30px;
      border-top: 2px solid #ff0033;
      padding-top: 20px;
    }
    .client-card {
      background: #111;
      border: 1px solid #ff0033;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 10px;
    }
    .client-card h3 {
      color: #ff0033;
      margin-bottom: 5px;
    }
    .client-card small {
      color: #aaa;
    }
    .options {
      margin-top: 10px;
    }
    .options a {
      text-decoration: none;
      color: #ff0033;
      font-weight: bold;
      margin-right: 15px;
    }
    .options a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="../destroy session/tech_logout.php">Logout</a>
    </nav>
  </header>

  <!-- Dashboard -->
  <main class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Menu</h2>
      <ul>
        <a href="repair~logs.php"><li>Repair Logs</li></a>
        <a href="notifications.php"><li>Notifications</li></a>
        <a href="reports.php"><li>Reports</li></a>
      </ul>
    </aside>

    <!-- Main Content -->
    <section class="content">
      <h1>Register Client</h1>
      <div class="grid-2">
        <!-- Client Registration -->
        <form class="card" method="POST">
          <label>Client Name:</label>
          <input type="text" name="client_name" placeholder="Enter full name" required>

          <label>Phone/Email:</label>
          <input type="text" name="email_phone" placeholder="Enter contact info" required>

          <label>Password:</label>
          <input type="password" name="password" placeholder="Enter password" required>

          <button type="submit" name="register_client">Register Client</button>
          <?php if (!empty($client_message)) echo "<p>$client_message</p>"; ?>
        </form>

        <!-- Email Form -->
        <form class="card" method="POST">
          <h2>Send Secure Report</h2>
          <label>Technician Email:</label>
          <input type="email" name="technician_email" placeholder="Technician email" required>

          <label>Client Email:</label>
          <input type="email" name="client_email" placeholder="Client email" required>

          <label>Message Title:</label>
          <input type="text" name="title" placeholder="Subject" required>

          <label>Message Body:</label>
          <textarea name="message_body" placeholder="Write message..." required></textarea>

          <button type="submit" name="send_message">Send Secure Email</button>
          <?php if (!empty($email_message)) echo "<p>$email_message</p>"; ?>
        </form>
      </div>

      <div class="client-list">
        <h2> Registered Clients</h2>
        <?php
        $clients = $conn->query("SELECT * FROM clients ORDER BY id DESC");
        if ($clients && $clients->num_rows > 0) {
          while ($c = $clients->fetch_assoc()) {
            echo "
              <div class='client-card'>
                <h3>{$c['client_name']}</h3>
                <small>{$c['email_phone']}</small>
                <div class='options'>
                  <a href='?delete_id={$c['id']}' onclick=\"return confirm('Delete this client?')\">Delete</a>
                </div>
              </div>
            ";
          }
        } else {
          echo "<p style='color:#888;'>No clients registered yet.</p>";
        }
        ?>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 SecureRepair Admin Panel</p>
  </footer>
</body>
</html>
