<?php
session_start();
require_once __DIR__.'/../includes/config.php';

// Initialize database connection
global $conn;

// Verify admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] == 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('<h1>ACCESS DENIED</h1><p>Administrator privileges required</p>');
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Get all officers except the last one, set default rank if empty
$users = [];
$result = $conn->query("SELECT id, name, 
                       COALESCE(rank, 'officer') AS rank 
                       FROM officers 
                       WHERE id < (SELECT MAX(id) FROM officers)");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $error = "Failed to load users: " . $conn->error;
}

// Process role updates - vulnerable direct query
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    
    // No input validation
    $conn->query("UPDATE officers SET rank = COALESCE('$new_role', 'officer') WHERE id = $user_id");
    if ($conn->error) {
        $error = "Update failed: " . $conn->error;
    } else {
        $_SESSION['flash'] = "Role updated";
        header("Location: access_control.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Access Control | HCNMS</title>
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

        .user-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--light-navy);
            border-radius: 8px;
            overflow: hidden;
        }

        .user-table th,
        .user-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--lightest-navy);
        }

        .user-table th {
            background: var(--lightest-navy);
            color: var(--teal);
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .role-select {
            background: var(--lightest-navy);
            border: 1px solid var(--teal);
            color: var(--white);
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .role-select:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--teal);
        }

        button {
            background: var(--teal);
            color: var(--navy);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        button:hover {
            background: var(--white);
            transform: translateY(-1px);
        }

        .flash-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: 1px solid var(--teal);
            background: var(--light-navy);
        }

        .back-button {
            background: var(--teal);
            color: var(--navy);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Access Control Panel</h1>
            <a href="/admin/index.php" class="back-button">Back to Admin</a>
        </header>

        <?php if (isset($error)): ?>
            <div class="flash-message" style="border-color: #ff0000;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-message">
                <?= htmlspecialchars($_SESSION['flash']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Current Rank</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['rank']) ?></td>
                    <td>
                        <form method="POST" class="role-form">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                            <select name="new_role" class="role-select">
                                <option value="admiral">Admiral</option>
                                <option value="commander">Commander</option>
                                <option value="captain">Captain</option>
                                <option value="lieutenant">Lieutenant</option>
                                <option value="ensign">Ensign</option>
                                <option value="cadet">Cadet</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
