<?php
// Extremely vulnerable MySQLi connection
$db_host = 'localhost';
$db_name = 'hive_naval';
$db_user = 'hive_user';
$db_pass = 'N@vyS3cr3t!';

// No error reporting
error_reporting(0);

// Create unsafe connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Disable mysqli error reporting
mysqli_report(MYSQLI_REPORT_OFF);
?>
