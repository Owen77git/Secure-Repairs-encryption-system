<?php
include("../DATABASE/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $_POST['client_name'] ?? 'Unknown_Client';
    $report_content = $_POST['report_content'] ?? '';

    // Fetch technician name & timestamp
    $stmt = $conn->prepare("SELECT technician_name, created_at FROM repair_logs WHERE client_name = ? LIMIT 1");
    $stmt->bind_param("s", $client_name);
    $stmt->execute();
    $stmt->bind_result($technician_name, $created_at);
    $stmt->fetch();
    $stmt->close();

    if (empty($technician_name)) $technician_name = "N/A";
    if (empty($created_at)) $created_at = date("Y-m-d H:i:s");

    $safe_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $client_name);

    header("Content-Type: application/msword");
    header("Content-Disposition: attachment; filename={$safe_name}_Repair_Report.doc");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "
    <html xmlns:o='urn:schemas-microsoft-com:office:office'
          xmlns:w='urn:schemas-microsoft-com:office:word'
          xmlns='http://www.w3.org/TR/REC-html40'>
    <head>
      <meta charset='utf-8'>
      <title>SecureRepair Report</title>
      <style>
        body {
          font-family: 'Segoe UI', Calibri, sans-serif;
          color: #111;
          background: #fff;
          margin: 40px;
        }
        h1 {
          color: #ff0033;
          text-align: center;
          border-bottom: 2px solid #ff0033;
          padding-bottom: 10px;
          margin-bottom: 25px;
        }
        .logo {
          text-align: center;
          font-size: 28px;
          font-weight: bold;
          color: #ff0033;
          margin-bottom: 10px;
        }
        .info-box {
          margin-bottom: 20px;
          border: 1px solid #ddd;
          border-left: 4px solid #ff0033;
          padding: 10px 15px;
          border-radius: 6px;
          background: #fafafa;
        }
        .label {
          color: #ff0033;
          font-weight: bold;
        }
        pre {
          background: #f8f8f8;
          border-left: 4px solid #ff0033;
          padding: 12px;
          border-radius: 6px;
          font-size: 14px;
          white-space: pre-wrap;
          word-wrap: break-word;
        }
        footer {
          text-align: center;
          color: #777;
          font-size: 12px;
          margin-top: 40px;
          border-top: 1px solid #ddd;
          padding-top: 10px;
        }
      </style>
    </head>
    <body>
      <div class='logo'>⚡ SecureRepair System</div>
      <h1>Client Repair Report</h1>

      <div class='info-box'>
        <p><span class='label'>Client Name:</span> " . htmlspecialchars($client_name) . "</p>
        <p><span class='label'>Technician:</span> " . htmlspecialchars($technician_name) . "</p>
        <p><span class='label'>Date & Time:</span> " . htmlspecialchars($created_at) . "</p>
      </div>

      <div class='info-box'>
        <p><span class='label'>Repair Details:</span></p>
        <pre>" . htmlspecialchars($report_content) . "</pre>
      </div>

      <footer>© 2025 SecureRepair Admin Panel — Confidential & Encrypted Report</footer>
    </body></html>
    ";
    exit;
} else {
    echo "❌ Invalid request.";
}
?>
