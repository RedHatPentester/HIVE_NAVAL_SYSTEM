<?php
session_start();
include '../includes/config.php';

// Check if admin (vulnerability: no real authorization check)
$is_admin = isset($_SESSION['user']) && $_SESSION['user']['rank'] === 'Admiral';

// Get all officers using MySQLi
$query = "SELECT * FROM officers";
$result = mysqli_query($conn, $query);
$officers = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html>

<head>
    <title>All Officers | HCNMS</title>
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
            background: var(--navy) url('/main.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: var(--white);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            background-color: rgba(10, 25, 47, 0.85);
            backdrop-filter: blur(5px);
            min-height: 100vh;
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
            font-size: 2rem;
            background: linear-gradient(135deg, var(--teal), var(--light-slate));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: var(--glow);
        }

        .officers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .officer-card {
            background: linear-gradient(145deg, var(--light-navy), var(--lightest-navy));
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(100, 255, 218, 0.2);
            transition: all 0.3s ease;
        }

        .officer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            border-color: var(--teal);
        }

        .officer-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .officer-badge {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: bold;
            color: var(--navy);
            box-shadow: var(--glow);
        }

        .officer-info h3 {
            margin: 0;
            color: var(--teal);
        }

        .officer-info p {
            margin: 0.2rem 0;
            color: var(--light-slate);
            font-size: 0.9rem;
        }

        .officer-details {
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .officer-details div {
            margin-bottom: 0.5rem;
        }

        .detail-label {
            color: var(--teal);
            display: inline-block;
            width: 100px;
        }

        .admin-buttons {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .admin-buttons a {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .edit-btn {
            background: var(--teal);
            color: var(--navy);
        }

        .edit-btn:hover {
            background: var(--white);
        }

        .delete-btn {
            background: #ff4757;
            color: white;
        }

        .delete-btn:hover {
            background: #ff6b81;
        }

        .back-button {
            display: inline-block;
            background: var(--teal);
            color: var(--navy);
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .officers-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>All Naval Officers</h1>
            <a href="/admin/index.php" class="back-button">Back to Dashboard</a>
        </header>

        <div class="officers-grid">
            <?php foreach ($officers as $officer): ?>
                <div class="officer-card">
                    <div class="officer-header">
                        <div class="officer-badge">
                            <?= strtoupper(substr($officer['name'], 0, 1)) ?>
                        </div>
                        <div class="officer-info">
                            <h3><?= htmlspecialchars($officer['name']) ?></h3>
                            <p><?= htmlspecialchars($officer['rank']) ?></p>
                        </div>
                    </div>

                    <div class="officer-details">
                        <div>
                            <span class="detail-label">ID:</span>
                            <?= htmlspecialchars($officer['id']) ?>
                        </div>
                        <div>
                            <span class="detail-label">Email:</span>
                            <?= htmlspecialchars($officer['email']) ?>
                        </div>
                        <div>
                            <span class="detail-label">Department:</span>
                            <?= htmlspecialchars($officer['department'] ?? 'N/A') ?>
                        </div>
                        <div>
                            <span class="detail-label">Clearance:</span>
                            <?= htmlspecialchars($officer['clearance_level'] ?? 'N/A') ?>
                        </div>
                    </div>

                    <?php if ($is_admin): ?>
                        <div class="admin-buttons">
                            <a href="/view_profile.php?officer_id=<?= $officer['id'] ?>" class="edit-btn">View Profile</a>
                            <a href="#" class="delete-btn">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>