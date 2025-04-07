<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['username'] !== 'admin') {
    header("Location: /login.php");
    exit();
}

$allowed_logs = ['access', 'admin_activity'];
$log = $_GET['log'] ?? '';

if (!in_array($log, $allowed_logs)) {
    die('Invalid log specified');
}

$log_file = "/logs/{$log}.log";
if (!file_exists($log_file)) {
    die('Log file not found');
}

// Only show last 1000 lines for performance
$lines = file($log_file);
$lines = array_slice($lines, -1000);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Viewer | <?= ucfirst($log) ?> Log</title>
    <link rel="stylesheet" href="/static/css/font-awesome.min.css">
    <link rel="stylesheet" href="/assets/style-enhanced.css">
</head>
<body>
    <div class="nav">
        <h2>Naval Command :: Log Viewer</h2>
        <a href="/admin/index.php" class="back-button">
            <i class="fa fa-arrow-left"></i> Back to Admin
        </a>
    </div>

    <div class="content">
        <h1><?= ucfirst($log) ?> Log</h1>
        <div class="log-container">
            <pre><?= htmlspecialchars(implode('', $lines)) ?></pre>
        </div>
    </div>
</body>
</html>
