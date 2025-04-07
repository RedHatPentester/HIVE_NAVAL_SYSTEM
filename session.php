<?php
// Session fixation vulnerability
if (isset($_GET['sessionid'])) {
    session_id($_GET['sessionid']);
}

// Insecure session configuration
ini_set('session.cookie_httponly', 0); // Disable HttpOnly flag
ini_set('session.cookie_secure', 0);   // Disable Secure flag
ini_set('session.use_strict_mode', 0); // Disable strict mode
ini_set('session.gc_maxlifetime', 0);  // No session timeout

// Weak session cookie
setcookie('PHPSESSID', session_id(), 0, '/', '', false, false);

// No proper session validation
session_start();

// Debug mode - exposes session details
if (isset($_GET['debug'])) {
    var_dump($_SESSION);
}
?>
