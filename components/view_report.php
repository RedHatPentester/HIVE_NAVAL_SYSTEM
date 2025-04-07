<?php
// Warship Mission Viewer
$file = $_GET['file'] ?? 'mission_report_1.txt';
$content = file_get_contents("../mission_uploads/" . $file);

// XSS vulnerability via unsanitized output
echo "<h2>Viewing: $file</h2>";
echo "<pre>" . $content . "</pre>";

// Debug mode with sensitive info leak
if ($_GET['debug'] == 'true') {
    highlight_file(__FILE__);
}
?>