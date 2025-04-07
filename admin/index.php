<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['username'] !== 'admin') {
    header("Location: /login.php");
    exit();
}

// CSRF vulnerability
if (isset($_POST['command'])) {
    system($_POST['command']);
}

// XSS in welcome message
$welcome = $_GET['welcome'] ?? 'Admin';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Portal | Naval Command System</title>
    <link rel="stylesheet" href="/static/css/font-awesome.css">
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const konamiCode = [38,38,40,40,37,39,37,39,66,65]; // ↑↑↓↓←→←→BA
        let inputSequence = [];
        
        // Create progress indicator
        const progress = document.createElement('div');
        progress.style = 'position:fixed;bottom:20px;right:20px;color:#0f0;font-family:monospace;';
        document.body.appendChild(progress);

        document.addEventListener('keydown', function(e) {
            inputSequence.push(e.keyCode);

            // Keep only the last 10 entries
            if (inputSequence.length > konamiCode.length) {
                inputSequence.shift();
            }

            // Show progress
            progress.textContent = `Code progress: ${inputSequence.length}/${konamiCode.length}`;
            
            // Check if input matches Konami code
            if (inputSequence.toString() === konamiCode.toString()) {
                // Secret action triggered
                document.body.style.background = 'url("https://i.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.webp") no-repeat center center fixed';
                document.body.style.backgroundSize = 'cover';
                
                // Fancy alert
                const alertBox = document.createElement('div');
                alertBox.style = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#000;border:2px solid #0f0;padding:20px;z-index:9999;text-align:center;';
                alertBox.innerHTML = `
                    <h2 style="color:#0f0;margin-top:0;">🔓 SECRET MODE ACTIVATED!</h2>
                    <p>Welcome to the inner circle.</p>
                    <p>Hidden admin features unlocked!</p>
                `;
                document.body.appendChild(alertBox);
                
                // Unlock hidden admin features
                document.getElementById('secretConsole').style.display = 'block';
                
                // Remove progress indicator
                progress.remove();
            }
        });
    });
    </script>
    <style>
        :root {
            <?php 
            if (isset($_COOKIE['h4x0r_theme'])) {
                echo '--hacker-accent: '.$_COOKIE['h4x0r_theme'].';';
            } else {
                echo '--hacker-accent: #00ff00;';
            }
            ?>
        }
        /* Original preserved styles */
        body { 
            font-family: 'Courier New', monospace; 
            background: #0a0a2a; 
            color: #00ff00; 
            text-align: center; 
            align-items: center; 
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .nav { 
            background: #000; 
            padding: 20px;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,255,0,0.3);
            position: relative;
        }
        .logout-button {
            position: absolute;
            top: 20px;
            right: 40px;
            background: #ff0000;
            border: 2px solid #ff0000;
            border-radius: 6px;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 0 10px rgba(255,0,0,0.5);
        }
        .logout-button:hover {
            background: #cc0000;
            box-shadow: 0 0 20px rgba(255,0,0,0.7);
        }
        .content { 
            padding: 40px 20px;
            flex: 1;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 { 
            font-size: 3em; 
            color: #ff0000;
            margin: 20px 0;
            text-shadow: 0 0 10px rgba(255,0,0,0.5);
            letter-spacing: 2px;
        }

        /* Modern dashboard layout */
        .admin-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px auto;
            max-width: 1200px;
            padding: 20px;
        }

        .admin-section {
            background: rgba(0,0,0,0.2);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(0,255,0,0.2);
            box-shadow: 0 0 20px rgba(0,255,0,0.1);
            transition: all 0.3s ease;
        }

        .admin-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,255,0,0.2);
        }

        .admin-section h4 {
            color: #00ffff;
            font-size: 1.2em;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #00ff00;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .admin-section li {
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .admin-section li:hover {
            transform: translateX(5px);
        }

        .admin-section a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #00ff00;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            background: rgba(0,0,0,0.3);
            transition: all 0.2s ease;
        }

        .admin-section a:hover {
            background: rgba(0,255,0,0.1);
            color: #00ffff;
        }

        .admin-section i {
            width: 20px;
            text-align: center;
        }
        
        /* Section-specific colors */
        .fleet-section {
            border-left: 4px solid #00aaff;
        }
        .tools-section {
            border-left: 4px solid #00ffaa;
        }
        .security-section {
            border-left: 4px solid #ff5555;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-dashboard {
                grid-template-columns: 1fr;
            }
            .admin-section {
                padding: 15px;
            }
            .admin-section h4 {
                font-size: 1em;
            }
        }
        form {
            margin: 40px auto;
            padding: 30px;
            background: rgba(0,0,0,0.5);
            border: 2px solid #00aa00;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,255,0,0.2);
            max-width: 600px;
        }
        input {
            width: 100%;
            max-width: 400px;
            padding: 12px 15px;
            border: 1px solid #00aa00;
            background: rgba(0,0,0,0.7);
            color: #00ff00;
            border-radius: 6px;
            font-size: 16px;
            margin: 0 auto 15px;
            display: block;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #00ff00;
            box-shadow: 0 0 10px rgba(0,255,0,0.5);
        }
        button {
            font-size: 16px;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            background: linear-gradient(to bottom, #005500, #002200);
            color: #00ff00;
            border: 1px solid #00aa00;
            font-weight: bold;
            transition: all 0.3s;
        }
        button:hover {
            background: linear-gradient(to bottom, #007700, #004400);
            box-shadow: 0 0 10px rgba(0,255,0,0.5);
        }
    </style>
</head>
<body>
    <div class="nav">
        <h2>Naval Command :: ADMIN PORTAL</h2>
        <a href="/logout.php" class="logout-button">Logout</a>
    </div>

    <div class="content">
        <h1>Welcome, <?= $welcome ?></h1>
        <h3>The Dashboard of the Admiral <?= $_SESSION['user']['name'] ?></h3>
        
        <div class="admin-dashboard">
            <div class="admin-section">
                <h4><i class="fas fa-ship"></i> Fleet Management</h4>
                <ul>
                    <li><a href="/admin/deployments.json"><i class="fa fa-map-marked-alt"></i> Deployed Warships</a></li>
                    <li><a href="/admin/view_all_officers.php"><i class="fa fa-users"></i> Officer Roster</a></li>
                    <li><a href="/admin/backup_db.php"><i class="fa fa-database"></i> Database Backup</a></li>
                </ul>
            </div>

            <div class="admin-section">
                <h4><i class="fas fa-tools"></i> System Tools</h4>
                <ul>
                    <li><a href="/missions/poc.php"><i class="fa fa-terminal"></i> Diagnostic Console</a></li>
                    <li><a href="/logs/access.log"><i class="fa fa-file-alt"></i> Access Logs</a></li>
                    <li><a href="/logs/admin_activity.log"><i class="fa fa-clipboard-list"></i> Admin Activity</a></li>
                </ul>
            </div>

            <div class="admin-section security-section">
                <h4><i class="fa fa-shield-alt"></i> Security</h4>
                <ul>
                    <li><a href="/admin/access_control.php"><i class="fa fa-lock"></i> Access Control</a></li>
                    <li><a href="/admin/audit_trail.php"><i class="fa fa-history"></i> Audit Trail</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Hidden admin console (right-click to reveal) -->
    <div id="secretConsole" style="display:none;position:fixed;bottom:0;left:0;right:0;background:#000;padding:10px;border-top:2px solid red;">
        <form method="POST">
            <input type="text" name="command" placeholder="Enter system command" style="width:70%">
            <button type="submit">Execute</button>
        </form>
    </div>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            document.getElementById('secretConsole').style.display = 'block';
        });
    </script>
</body>
</html>
