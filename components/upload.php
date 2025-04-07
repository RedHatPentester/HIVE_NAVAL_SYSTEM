<?php
// Naval Intelligence Upload Portal
header('X-Powered-By: HCNMS/1.0');

// Include the database configuration (adjusted path)
require_once '../includes/config.php';

// Fetch officer profiles from the database using the connection from config.php
$officer_profiles = [];
$result = mysqli_query($conn, "SELECT * FROM officers");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $officer_profiles[] = $row;
    }
    mysqli_free_result($result);
} else {
    // Since error reporting is disabled in config.php, we won't see the error
    echo "<p>Error fetching officer profiles. Please contact the administrator.</p>";
}

// Handle file uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES["mission_file"])) {
    $target_dir = "../mission_uploads/";

    // Ensure the upload directory exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["mission_file"]["name"]);

    // No file validation! (Vulnerable: allows any file type and doesn't check contents)
    if (move_uploaded_file($_FILES["mission_file"]["tmp_name"], $target_file)) {
        echo "<p>Mission report uploaded to: <a href='$target_file'>$target_file</a></p>";

        // Logging with command injection vulnerability (safer: deletes a dummy file)
        $log_cmd = "echo 'Uploaded: " . $_FILES["mission_file"]["name"] . "' >> ../logs/upload.log";
        system($log_cmd);

        // Create a dummy file that can be safely deleted by command injection
        $dummy_file = "../logs/dummy_report.txt";
        file_put_contents($dummy_file, "This is a dummy report file for testing command injection.\n");
    } else {
        echo "<p>Error uploading mission report.</p>";
    }
}

// Handle viewing reports (both pre-existing mission reports and uploaded files)
$view_reports = false;
$mission_files = [];
$uploaded_files = [];
if (isset($_GET['action']) && $_GET['action'] == 'view-reports') {
    $view_reports = true;

    // Load pre-existing mission reports from the missions folder
    $missions_dir = "../missions/";
    if (file_exists($missions_dir)) {
        $mission_files = array_diff(scandir($missions_dir), array('.', '..'));
    }

    // Load uploaded files from the mission_uploads folder
    $target_dir = "../mission_uploads/";
    if (file_exists($target_dir)) {
        $uploaded_files = array_diff(scandir($target_dir), array('.', '..'));
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>HCNMS - Classified Document Upload</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background-color: #121212;
            color: #e0e0e0;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            line-height: 1.6;
        }

        h2 {
            color: #4a8fe7;
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        h3 {
            color: #5cb85c;
            margin: 1rem 0;
        }

        .section {
            background-color: #1e1e1e;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 4px;
            border-left: 4px solid #2c3e50;
        }

        .warning {
            color: #d9534f;
            background-color: rgba(217, 83, 79, 0.1);
            padding: 0.5rem;
            border-radius: 4px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 1rem 0;
            font-size: 0.9rem;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #2c3e50;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(74, 143, 231, 0.1);
        }

        input[type="file"] {
            display: block;
            margin: 1rem 0;
            padding: 0.5rem;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
        }

        button {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .back-button {
            display: inline-block;
            background: #64ffda;
            color: #0a192f;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            margin-bottom: 20px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(100, 255, 218, 0.3);
        }


        ul {
            list-style-type: none;
            padding: 0;
            margin: 1rem 0;
        }

        li {
            margin: 0.5rem 0;
            padding: 0.5rem;
            background-color: rgba(44, 62, 80, 0.3);
            border-radius: 4px;
        }

        li:hover {
            background-color: rgba(44, 62, 80, 0.5);
        }

        a {
            color: #4a8fe7;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        small {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .section {
                padding: 1rem;
            }

            th,
            td {
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body>
    <h2>HCNMS Classified Document Upload Portal</h2>
    <a href="../dashboard.php" class="back-button">Back to Dashboard</a>

    <!-- Section 1: Display Officer Profiles (Fetched from MySQL) -->
    <div class="section">
        <h3>Officer Profiles (Classified)</h3>
        <p><small>This data is accessible to authorized personnel only. Do not include in mission reports.</small></p>
        <table>
            <tr>
                <th>UUID</th>
                <th>Name</th>
                <th>Rank</th>
                <th>Email</th>
            </tr>
            <?php foreach ($officer_profiles as $profile): ?>
                <tr>
                    <td><?php echo htmlspecialchars($profile['uuid']); ?></td>
                    <td><?php echo htmlspecialchars($profile['name']); ?></td>
                    <td><?php echo htmlspecialchars($profile['rank']); ?></td>
                    <td><?php echo htmlspecialchars($profile['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Section 2: Upload Mission Report -->
    <div class="section">
        <h3>Upload Mission Report</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="mission_file" required>
            <button type="submit">Upload to Naval Database</button>
        </form>
        <p class="warning"><small>For operational security, encrypt files before upload (NOT ENFORCED)</small></p>
    </div>

    <!-- Section 3: View Reports (Pre-existing and Uploaded) -->
    <div class="section">
        <h3>View Mission Reports</h3>
        <p><a href="?action=view-reports">Click here to view mission reports</a></p>
        <?php if ($view_reports): ?>
            <h4>Pre-existing Mission Reports (from missions folder)</h4>
            <?php if (count($mission_files) > 0): ?>
                <ul>
                    <?php foreach ($mission_files as $file): ?>
                        <li><a href="../missions/<?php echo htmlspecialchars($file); ?>"
                                target="_blank"><?php echo htmlspecialchars($file); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No pre-existing mission reports found in the missions folder.</p>
            <?php endif; ?>

            <h4>Uploaded Mission Reports (from mission_uploads folder)</h4>
            <?php if (count($uploaded_files) > 0): ?>
                <ul>
                    <?php foreach ($uploaded_files as $file): ?>
                        <li><a href="../mission_uploads/<?php echo htmlspecialchars($file); ?>"
                                target="_blank"><?php echo htmlspecialchars($file); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No reports uploaded yet.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>