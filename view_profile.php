<?php
session_start();
include 'includes/config.php';

// IDOR vulnerability preserved
$officer_id = $_GET['officer_id'];

// Using direct MySQLi query instead of PDO
$query = "SELECT * FROM officers WHERE id = " . (int)$officer_id;
$result = $conn->query($query);
$officer = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Officer Profile | HCNMS</title>
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
            padding: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--teal);
        }

        .profile-title {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--teal), var(--light-slate));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: var(--glow);
        }

        .profile-card {
            background: linear-gradient(145deg, var(--light-navy), var(--lightest-navy));
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            border: 1px solid rgba(100, 255, 218, 0.2);
            margin-bottom: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .profile-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            font-size: 2rem;
            font-weight: bold;
            color: var(--navy);
            box-shadow: var(--glow);
        }

        .profile-info h2 {
            margin: 0;
            color: var(--teal);
            font-size: 1.8rem;
        }

        .profile-info p {
            margin: 0.3rem 0;
            color: var(--light-slate);
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .detail-card {
            background: rgba(10, 25, 47, 0.5);
            padding: 1.2rem;
            border-radius: 10px;
            border-left: 3px solid var(--teal);
        }

        .detail-card h3 {
            margin-top: 0;
            color: var(--teal);
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-card p {
            margin: 0.5rem 0 0;
            font-size: 1rem;
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
            margin-top: 1rem;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-badge {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1 class="profile-title">Officer Profile</h1>
            <a href="/dashboard.php" class="back-button">Back to Dashboard</a>
        </header>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-badge">
                    <?= strtoupper(substr($officer['name'], 0, 1)) ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($officer['name']) ?></h2>
                    <p><?= htmlspecialchars($officer['rank']) ?></p>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-card">
                    <h3>Officer ID</h3>
                    <p><?= htmlspecialchars($officer['id']) ?></p>
                </div>
                <div class="detail-card">
                    <h3>Email</h3>
                    <p><?= htmlspecialchars($officer['email']) ?></p> <!-- PII leak preserved -->
                </div>
                <div class="detail-card">
                    <h3>Department</h3>
                    <p><?= htmlspecialchars($officer['department'] ?? 'N/A') ?></p>
                </div>
                <div class="detail-card">
                    <h3>Clearance Level</h3>
                    <p><?= htmlspecialchars($officer['clearance_level'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
