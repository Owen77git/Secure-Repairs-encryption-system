<?php
session_start();
include("../DATABASE/database.php");

$msg = "";
$show_reset = false;

// STEP 1: Verify Technician Info
if (isset($_POST['verify_info'])) {
    $name = trim($_POST['technician_name']);
    $email_phone = trim($_POST['email_phone']);
    $specialization = trim($_POST['specialization']);

    $stmt = $conn->prepare("SELECT * FROM technicians WHERE technician_name=? AND email_phone=? AND specialization=?");
    $stmt->bind_param("sss", $name, $email_phone, $specialization);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result && $result->num_rows > 0) {
        $_SESSION['reset_tech'] = $name;
        $show_reset = true;
    } else {
        $msg = "Technician details not found!";
    }
}

// STEP 2: Reset Password
if (isset($_POST['reset_pass'])) {
    $new_pass = trim($_POST['new_password']);
    $confirm_pass = trim($_POST['confirm_password']);
    $name = $_SESSION['reset_tech'] ?? null;

    if (!$name) {
        $msg = "Session expired. Please verify your details again.";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "Passwords do not match!";
        $show_reset = true;
    } else {
        $hashed_pass = md5($new_pass);
        $stmt = $conn->prepare("UPDATE technicians SET password=? WHERE technician_name=?");
        $stmt->bind_param("ss", $hashed_pass, $name);
        $stmt->execute();
        $stmt->close();

        unset($_SESSION['reset_tech']);
        $msg = " Password updated successfully! You can now log in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Technician Password Recovery</title>
<style>
/* Reset + Base */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }

body {
  background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
              url(../IMAGES/CIRCUIT.jpg) no-repeat center/cover;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: #fff;
}

/* Container */
.container {
  background: rgba(17,17,17,0.9);
  padding: 2rem;
  width: 420px;
  border: 2px solid #ff0033;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(255,0,51,0.3);
  backdrop-filter: blur(8px);
}

h2 {
  color: #ff0033;
  text-align: center;
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin: 0.5rem 0 0.2rem;
}

input {
  width: 100%;
  padding: 0.8rem;
  margin-bottom: 1rem;
  border: none;
  border-radius: 5px;
  background: #222;
  color: #fff;
}

input:focus {
  outline: 2px solid #ff0033;
}

button {
  background: #ff0033;
  color: #fff;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
  width: 100%;
}

button:hover {
  background: #fff;
  color: #ff0033;
  box-shadow: 0 0 15px #ff0033;
}

.error-text {
  color: #ff8080;
  text-align: center;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.success-text {
  color: #9eff9e;
  text-align: center;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}

.back-link {
  text-align: center;
  margin-top: 1rem;
}

.back-link a {
  color: #ff0033;
  text-decoration: none;
  font-weight: bold;
}

.back-link a:hover {
  color: #fff;
}

.fade {
  animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="container fade">
  <?php if (!$show_reset && empty($_SESSION['reset_tech'])): ?>
  <!-- Step 1: Verify Technician -->
  <h2>Technician Verification</h2>
  <form method="POST">
    <label for="technician_name">Technician Name</label>
    <input type="text" name="technician_name" placeholder="Enter your name" required>

    <label for="email_phone">Email or Phone</label>
    <input type="text" name="email_phone" placeholder="Enter your email or phone" required>

    <label for="specialization">Specialization</label>
    <input type="text" name="specialization" placeholder="e.g., Electronics Repair" required>

    <button name="verify_info" type="submit">Verify Details</button>
     <p class="forgot"><a href="technician_login.php">-Back to Login</a></p>
    <p class="error-text"><?php echo $msg; ?></p>
  </form>

  <?php elseif ($show_reset || isset($_SESSION['reset_tech'])): ?>
  <!-- Step 2: Reset Password -->
  <h2>Reset Your Password</h2>
  <form method="POST">
    <label for="new_password">New Password</label>
    <input type="password" name="new_password" placeholder="Enter new password" required>

    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" placeholder="Confirm password" required>

    <button name="reset_pass" type="submit">Update Password</button>
    <?php if (strpos($msg, 'successfully') !== false): ?>
      <p class="success-text"><?php echo $msg; ?></p>
      <div class="back-link"><a href="technician_login.php">Return to Login</a></div>
    <?php else: ?>
      <p class="error-text"><?php echo $msg; ?></p>
    <?php endif; ?>
  </form>
  <?php endif; ?>
</div>

</body>
</html>
