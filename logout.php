<?php
session_start();

// Doesn't properly destroy session
unset($_SESSION['user']);

// Redirect with token leakage in URL
header("Location: /login.php?logout=true&token=" . session_id());
?>