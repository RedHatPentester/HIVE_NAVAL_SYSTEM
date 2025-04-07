<?php
// Naval Cyber Unit - Diagnostic Console
session_start();
require_once __DIR__.'/../includes/config.php';

// Verify admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] == 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('<h1>ACCESS DENIED</h1><p>Administrator privileges required</p>');
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Only allow from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('<h1>ACCESS DENIED</h1><p>This tool is only available from localhost</p>');
}

// Process commands
$output = '';
if (isset($_GET['cmd'])) {
    $allowed_commands = [
        'ls' => ['-la', '-l'],
        'pwd' => [],
        'whoami' => [],
        'date' => ['+%Y-%m-%d', '+%c']
    ];
    
    $cmd = $_GET['cmd'];
    $parts = explode(' ', $cmd);
    $base_cmd = $parts[0];
    
    if (isset($allowed_commands[$base_cmd])) {
        $allowed = true;
        // Verify all arguments are allowed
        foreach(array_slice($parts, 1) as $arg) {
            if (!in_array($arg, $allowed_commands[$base_cmd])) {
                $allowed = false;
                break;
            }
        }
        
        if ($allowed) {
            $output = htmlspecialchars(shell_exec($cmd), ENT_QUOTES, 'UTF-8');
        } else {
            $output = "Error: Invalid arguments for command";
        }
    } else {
        $output = "Error: Command not allowed";
    }
}

// Process file uploads
$upload_result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $allowed_types = ['txt','log','json'];
    $ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed_types)) {
        $dest = 'mission_uploads/' . uniqid() . '_' . basename($_FILES['upload']['name']);
        if (move_uploaded_file($_FILES['upload']['tmp_name'], $dest)) {
            $upload_result = "File uploaded to: " . htmlspecialchars($dest);
        } else {
            $upload_result = "Error: File upload failed";
        }
    } else {
        $upload_result = "Error: Only " . implode(', ', $allowed_types) . " files allowed";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naval Cyber Test Console</title>
    <link rel="stylesheet" href="/static/css/font-awesome.css">
    <link rel="stylesheet" href="/assets/style-enhanced.css">
    <style>
        .console {
            background: #0a0a2a;
            color: #00ff00;
            padding: 20px;
            border-radius: 5px;
            font-family: monospace;
            margin: 20px 0;
            max-height: 400px;
            overflow-y: auto;
        }
        .command-form {
            margin: 20px 0;
        }
        .upload-form {
            margin: 20px 0;
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="nav">
        <h2>Naval Command :: Diagnostic Console</h2>
        <a href="/admin/index.php" class="logout-button">
            <i class="fa fa-arrow-left"></i> Back to Admin
        </a>
    </div>

    <div class="content">
        <h1><i class="fa fa-terminal"></i> Diagnostic Console</h1>
        
        <div class="command-form">
            <h3><i class="fa fa-code"></i> Command Execution</h3>
            <form method="get">
                <input type="text" name="cmd" placeholder="Enter command" required
                    pattern="(ls(\s+(-la|-l))?|pwd|whoami|date(\s+\+%[YmdHMS%c]+)?)">
                <button type="submit"><i class="fa fa-play"></i> Execute</button>
            </form>
            
            <?php if ($output): ?>
                <div class="console">
                    <pre><?= $output ?></pre>
                </div>
            <?php endif; ?>
        </div>

        <div class="upload-form">
            <h3><i class="fa fa-upload"></i> File Upload</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="upload" required>
                <button type="submit"><i class="fa fa-upload"></i> Upload</button>
            </form>
            
            <?php if ($upload_result): ?>
                <p><?= $upload_result ?></p>
            <?php endif; ?>
        </div>

        <div class="help-section">
            <h3><i class="fa fa-info-circle"></i> Usage</h3>
            <p><strong>Allowed commands:</strong> ls, pwd, whoami, date</p>
            <p><strong>Allowed arguments:</strong></p>
            <ul>
                <li>ls: -la, -l</li>
                <li>date: +%Y-%m-%d, +%c</li>
            </ul>
            <p><strong>Allowed file types:</strong> .txt, .log, .json</p>
        </div>
    </div>
</body>
