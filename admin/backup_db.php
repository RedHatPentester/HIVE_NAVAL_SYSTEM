<?php
include '../includes/config.php';

// Check if CSV download requested
if (isset($_GET['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="naval_db_dump_' . date('Y-m-d') . '.csv"');

    $tables = ['officers', 'warship_deployments', 'mission_reports', 'users', 'system_config'];
    $output = fopen('php://output', 'w');

    foreach ($tables as $table) {
        fputcsv($output, ["=== TABLE $table ==="]);
        $query = "SELECT * FROM `$table`"; // Backticks to prevent SQL errors with reserved words
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            // Get column names from the first row
            $firstRow = mysqli_fetch_assoc($result);
            fputcsv($output, array_keys($firstRow));
            fputcsv($output, $firstRow);

            // Fetch remaining rows
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, $row);
            }
        } else {
            fputcsv($output, ["No data found in $table"]);
        }
        fputcsv($output, []);
    }
    fclose($output);
    exit();
}

// Regular HTML output
?><!DOCTYPE html>
<html>

<head>
    <title>Database Backup Portal</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #0a0a2a;
            color: #00ff00;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border: 1px solid #00aa00;
        }

        h1 {
            color: #00ffff;
            border-bottom: 1px dashed #00ff00;
            padding-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #00aa00;
            color: #000;
            text-decoration: none;
            font-weight: bold;
            margin: 10px 0;
            border-radius: 5px;
        }

        .btn:hover {
            background: #00ff00;
        }

        pre {
            background: #000;
            padding: 15px;
            border: 1px solid #00aa00;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Naval Database Backup</h1>
        <a href="?download=1" class="btn">DOWNLOAD FULL DATABASE (CSV)</a>

        <h2>Raw Data Preview:</h2>
        <pre>
        <?php
        // Database dump preview
        $tables = ['officers', 'warship_deployments', 'mission_reports', 'users', 'system_config'];
        echo "Database Preview:\n\n";

        foreach ($tables as $table) {
            echo "=== TABLE $table ===\n";
            $query = "SELECT * FROM `$table`";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Avoid exposing passwords
                    if (isset($row['password'])) {
                        $row['password'] = '[REDACTED]';
                    }
                    print_r($row);
                }
            } else {
                echo "No data or error in $table\n";
            }
            echo "\n";
        }

        // Secure logging (optional, but improved)
        file_put_contents(
            '../logs/backup_access.log',
            date('Y-m-d H:i:s') . " - Accessed by: " . $_SERVER['REMOTE_ADDR'] . "\n",
            FILE_APPEND
        );
        ?>
        </pre>
    </div>
</body>

</html>