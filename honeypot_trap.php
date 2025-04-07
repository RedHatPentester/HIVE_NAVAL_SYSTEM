<?php
// Log the bot attempt with full details
error_log("HONEYPOT TRIGGERED - Bot detected from IP: ".$_SERVER['REMOTE_ADDR'].
          " User-Agent: ".$_SERVER['HTTP_USER_AGENT']);

// Set cookie to mark them as a bot
setcookie("bot_detected", "1", time()+3600, "/", "", false, true);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bot Detected</title>
    <style>
        body {
            background: #000;
            color: #0f0;
            font-family: monospace;
            text-align: center;
            padding: 50px;
        }
        .blink {
            animation: blink 1s step-end infinite;
        }
        @keyframes blink {
            50% { opacity: 0; }
        }
    </style>
</head>
<body>
    <h1 class="blink">BOT DETECTED</h1>
    <p>Your automated attack tools have been identified. (If you didn't use one then you are a bot in human form)</p>
    <p>We've logged your IP and user agent.(Just Joking)</p>
    <p>Real hackers don't use scripts - try harder!</p>
    
    <pre>
  _____
 /     \
 | () () |
 \  ^  /
  |||||
  |||||
    </pre>
    
    <p>This is not the real system. Go back and try again.</p>
</body>
</html>
