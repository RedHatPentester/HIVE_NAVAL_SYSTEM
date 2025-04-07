<?php
session_start();
require_once __DIR__.'/../includes/config.php';

// Initialize database connection
global $conn;

// Verify admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] = 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('<h1>ACCESS DENIED</h1><p>Administrator privileges required</p>');
}

// Easter egg - view anyone's logs by changing the user parameter
if (isset($_GET['debug_user'])) {
    $logfile = "logs/".$_GET['debug_user']."_activity.log";
    if (file_exists($logfile)) {
        highlight_file($logfile);
        exit();
    }
}

// Get audit logs - vulnerable direct query
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM audit_log WHERE action LIKE '%$search%' ORDER BY timestamp DESC LIMIT 100";
$result = $conn->query($query);
$logs = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Trail | HCNMS</title>
    <style>
        :root {
            --navy: #0a192f;
            --teal: #64ffda;
            --light-navy: #172a45;
            --lightest-navy: #303C55;
            --slate: #8892b0;
            --light-slate: #a8b2d1;
            --white: #e6f1ff;
            --glow: 0 0 15px rgba(100, 255, 218, 0.7);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--navy);
            color: var(--white);
            margin: 0;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--teal);
        }

        h1 {
            color: var(--teal);
            text-shadow: var(--glow);
        }

        .audit-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--light-navy);
            border-radius: 8px;
            overflow: hidden;
        }

        .audit-table th,
        .audit-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--lightest-navy);
        }

        .audit-table th {
            background: var(--lightest-navy);
            color: var(--teal);
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .back-button {
            background: var(--teal);
            color: var(--navy);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .search-form {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
        }

        .search-input {
            background: var(--lightest-navy);
            border: 1px solid var(--teal);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            flex: 1;
        }

        .search-button {
            background: var(--teal);
            color: var(--navy);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .search-button:hover {
            background: var(--white);
            transform: translateY(-1px);
        }

        .critical {
            color: #ff6b6b;
        }

        .warning {
            color: #feca57;
        }

        .info {
            color: var(--teal);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Audit Trail</h1>
            <a href="/admin/index.php" class="back-button">Back to Admin</a>
        </header>

        <form method="GET" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search actions..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-button">Search</button>
        </form>

        <table class="audit-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    <td><?= htmlspecialchars($log['username']) ?></td>
                    <td class="<?= 
                        strpos($log['action'], 'failed') !== false ? 'critical' : 
                        (strpos($log['action'], 'warning') !== false ? 'warning' : 'info') 
                    ?>">
                        <?= htmlspecialchars($log['action']) ?>
                    </td>
                    <td><?= htmlspecialchars($log['details']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
