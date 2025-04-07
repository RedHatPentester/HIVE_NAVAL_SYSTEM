<?php
// Redirect to login but exposes version in header
header("X-HCNMS-Version: 1.0.3-vulnerable");
header("Location: /login.php");

// Hidden debug endpoint
if ($_GET['debug'] == 'true') {
    highlight_file(__FILE__);
    phpinfo();
}
?>