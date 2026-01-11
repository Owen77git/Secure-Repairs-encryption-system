<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

include("../DATABASE/database.php");

// Add Tool
if (isset($_POST['add_tool'])) {
    $tool_name = $_POST['tool_name'];
    $tool_description = $_POST['tool_description'];

    $sql = "INSERT INTO repair_tools (tool_name, tool_description) 
            VALUES ('$tool_name', '$tool_description')";
    $conn->query($sql);
    $_SESSION['msg'] = "✅ Tool added successfully!";
    header("Location: tools.php");
    exit();
}

// Update Tool Status
if (isset($_GET['update_status']) && isset($_GET['new_status'])) {
    $id = $_GET['update_status'];
    $new = $_GET['new_status'];
    $conn->query("UPDATE repair_tools SET status='$new' WHERE id=$id");
    $_SESSION['msg'] = "Tool marked as $new!";
    header("Location: tools.php");
    exit();
}

// Delete Tool
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM repair_tools WHERE id=$id");
    $_SESSION['msg'] = "Tool deleted!";
    header("Location: tools.php");
    exit();
}

$tools = $conn->query("SELECT * FROM repair_tools ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Tools | SecureRepair Admin</title>
<link rel="stylesheet" href="../CSS/admin~menu.CSS" />
<style>
    body { background: #0d0d0d; color: #fff; font-family: 'Segoe UI', sans-serif; margin:0; padding:0; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { padding: 0.8rem; border: 1px solid #ff0033; text-align: left; }
    th { background: #111; color: #ff0033; }
    td { background: #1a1a1a; }
    .card { margin-bottom: 20px; border: 2px solid #ff0033; padding: 15px; border-radius: 8px; }
    .msg { margin-top: 10px; color: #ff0033; font-weight: bold; }
    .btn { padding: 0.4rem 0.8rem; border-radius: 5px; text-decoration: none; color: #fff; font-weight: bold; border: none; cursor: pointer; }
    .options-btn { background: #ff0033; }
    .options-btn:hover { background: #ff1a1a; }
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
    .dropdown a:hover { background: #ff0033; }
    .dropdown a.delete { color: #ff4444; }
</style>
</head>

<body>
<header class="navbar">
    <div class="logo">⚡ SecureRepair Admin</div>
    <nav class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="tools.php" class="active">Tools</a>
        <a href="../destroy session/ad_logout.php">Logout</a>
    </nav>
</header>

<section class="content">
    <h1>Manage Repair Tools</h1>

    <!-- Add Tool -->
    <div class="card">
        <form method="POST">
            <label>Tool Name:</label>
            <input type="text" name="tool_name" placeholder="Enter tool name" required>

            <label>Tool Description:</label>
            <textarea name="tool_description" placeholder="Describe the tool..." required></textarea>

            <button type="submit" name="add_tool">Add Tool</button>
            <?php
                if (!empty($_SESSION['msg'])) {
                    echo "<p class='msg'>{$_SESSION['msg']}</p>";
                    unset($_SESSION['msg']);
                }
            ?>
        </form>
    </div>

    <!-- Tool List -->
    <div class="card" style="border: 2px dotted #ff0033;">
        <h2> Available Tools</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Tool Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $tools->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['tool_name']) ?></td>
                <td><?= htmlspecialchars($row['tool_description']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td style="position: relative;">
                    <button type="button" class="btn options-btn" onclick="toggleOptions(this)">Options</button>
                    <div class="dropdown">
                        <a href="?update_status=<?= $row['id'] ?>&new_status=active">Set Active</a>
                        <a href="?update_status=<?= $row['id'] ?>&new_status=inactive">Set Inactive</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this tool?')" class="delete"> Delete</a>
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
        if (!e.target.matches('.options-btn')) {
            document.querySelectorAll('.dropdown').forEach(d => d.style.display = 'none');
        }
    });
</script>
</body>
</html>
