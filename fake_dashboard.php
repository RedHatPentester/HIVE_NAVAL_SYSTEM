<?php
session_start();
if (!isset($_SESSION['fake_user'])) {
    header("Location: /login.php");
    exit();
}

// Log the SQLi attempt with timestamp
error_log("[".date('Y-m-d H:i:s')."] FAKE DASHBOARD ACCESS - SQLi attempt from: ".$_SERVER['REMOTE_ADDR']." - User-Agent: ".$_SERVER['HTTP_USER_AGENT']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Naval Command Dashboard</title>
    <style>
        body {
            background: #000033;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
        }
        .terminal {
            border: 2px dashed #ff0000;
            padding: 20px;
            margin-top: 30px;
            background: rgba(0,0,0,0.7);
        }
        .blink {
            animation: blink 0.5s step-end infinite;
        }
        @keyframes blink {
            50% { opacity: 0; }
        }
        .ascii-art {
            color: #ffff00;
            font-family: monospace;
            white-space: pre;
        }
    </style>
</head>
<body>
    <div class="terminal">
        <h1 class="blink">! WARNING !</h1>
        <p>You've reached a <strong>FAKE</strong> dashboard</p>
        
        <div class="ascii-art">
   ____
  /    \
 | STOP | 
  \____/
   |  |
  /    \
 | TRY  | 
  \____/
   HARDER
        </div>

        <h2>Your Hacking Attempt:</h2>
        <ul>
            <li>IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></li>
            <li>Time: <?php echo date('Y-m-d H:i:s'); ?></li>
            <li>Method: Basic SQL Injection</li>
        </ul>

        <h3>Fake Data Exposed:</h3>
        <p>• Nuclear Codes: 12345</p>
        <p>• Admin Password: password123</p>
        <p>• Secret Base: 42.3647° N, 71.1042° W</p>

        <p class="blink">This is a trap! Your attempt has been logged.</p>
    </div>
</body>
</html>
