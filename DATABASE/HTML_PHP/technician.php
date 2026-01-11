<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include("../DATABASE/database.php");

// ==================== PHPMailer SETUP =====================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// ==================== CLIENT REGISTRATION =====================
if (isset($_POST['register_client'])) {
  $name = $_POST['client_name'];
  $email_phone = $_POST['email_phone'];
  $password = $_POST['password'];

  $hashed_password = md5($password);

  $sql = "INSERT INTO clients (client_name, email_phone, password)
          VALUES ('$name', '$email_phone', '$hashed_password')";

  if ($conn->query($sql)) {
    $client_message = "✅ Client registered successfully!";
  } else {
    $client_message = "❌ Error: " . $conn->error;
  }
}

// ==================== MESSAGE SENDING =====================
if (isset($_POST['send_message'])) {
  $technician_email = $_POST['technician_email'];
  $client_email = $_POST['client_email'];
  $title = $_POST['title'];
  $message_body = $_POST['message_body'];

  // Save message in DB
  $sql = "INSERT INTO messages (technician_email, client_email, title, message_body)
          VALUES ('$technician_email', '$client_email', '$title', '$message_body')";
  $conn->query($sql);

  // Send email
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'oweneshuchi77@gmail.com'; // your Gmail
    $mail->Password = 'zpifeqpnmmlnbzyd';       // your Gmail app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($technician_email, 'SecureRepair Technician');
    $mail->addAddress($client_email);
    $mail->Subject = $title;
    $mail->Body = $message_body;

    $mail->send();
    $email_message = "✅ Message sent successfully to $client_email!";
  } catch (Exception $e) {
    $email_message = "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
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
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="../destroy session/ad_logout.php">Logout</a>
    </nav>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
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
      <div id="registerClient" class="section active">
        <h1>📋 Register Client</h1>
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

          <!-- Email/SMS Form -->
          <form class="card" method="POST">
            <h2>📲 Send SMS/Email to Client</h2>

            <label>Technician Email:</label>
            <input type="email" name="technician_email" placeholder="Technician email" required>

            <label>Client Email:</label>
            <input type="email" name="client_email" placeholder="Client email" required>

            <label>Message Title:</label>
            <input type="text" name="title" placeholder="Message Title" required>

            <label>Message Body:</label>
            <textarea name="message_body" placeholder="Write message..." required></textarea>

            <button type="submit" name="send_message">Send Message</button>
            <?php if (!empty($email_message)) echo "<p>$email_message</p>"; ?>
          </form>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 SecureRepair Admin Panel</p>
  </footer>
  <script src="../JS/messageTimeout.js"></script>
</body>
</html>
