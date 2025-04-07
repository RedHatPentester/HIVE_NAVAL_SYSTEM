<?php
session_start();
include 'includes/config.php';

// Clear any previous error message
unset($_SESSION['login_error']);

// Honeypot check: if the honeypot field is filled, redirect to the honeypot trap
if (isset($_POST['honeypot']) && $_POST['honeypot'] !== '') {
    header("Location: /honeypot_trap.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect raw input; leaving quotes intact for vulnerability purposes
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Set SQL mode to bypass some protections (intentional for this lab)
    mysqli_query($conn, "SET SESSION sql_mode='NO_BACKSLASH_ESCAPES,NO_ENGINE_SUBSTITUTION'");

    // Vulnerable SQL query (do not use in production)
    $sql = "SELECT * FROM officers WHERE username='$username' AND password='$password' LIMIT 1";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        // On SQL errors, silently reject the login attempt
        $_SESSION['login_error'] = true;
        header("Location: login.php");
        exit();
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Determine if this is an admin account (admin accounts are more secure)
        $is_admin = (stripos($row['username'], 'admin') !== false);

        // For admin accounts, enforce an exact match to prevent bypass
        if ($is_admin && ($username !== $row['username'] || $password !== $row['password'])) {
            $_SESSION['login_error'] = true;
            header("Location: login.php");
            exit();
        }

        // Set user session and cookie
        $_SESSION['user'] = ['username' => $row['username'], 'admin' => $is_admin];
        setcookie("naval_user", $row['username'], time() + 3600, "/", "", false, false);

        // Redirect user based on account type
        if ($is_admin) {
            header("Location: admin/index.php?welcome=" . rawurlencode($row['username']));
        } else {
            header("Location: /dashboard.php?greeting=" . rawurlencode($row['username']));
        }
        exit();
    } else {
        // Invalid credentials for non-existent or mismatched accounts
        $_SESSION['login_error'] = true;
        header("Location: login.php");
        exit();
    }
}

// Display a generic error message on the login page if needed
if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === true) {
    echo "<p>Invalid credentials.</p>";
}
?>








<!DOCTYPE html>
<html>
<head>
    <title>Naval System Login</title>
    <script src="/static/js/jquery-1.4.2.js"></script>
    <meta name="description" content="Naval Command Network Management System">
    <meta name="keywords" content="Naval, Command, Network, Management, System">
    <meta name="author" content="Naval Command Team">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        :root {
            --primary: #2a9d8f;
            --secondary: #264653;
            --accent: #e9c46a;
            --error: #e76f51;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('main.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            width: 350px;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s;
        }

        .login-box:hover {
            transform: translateY(-5px);
        }

        h2 {
            color: var(--accent);
            margin-bottom: 30px;
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 15px 20px;
            box-sizing: border-box;
            border: none;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        input:focus {
            outline: none;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        button {
            background: var(--primary);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
        }

        button:hover {
            background: #21867a;
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
        }

        .error {
            color: var(--error);
            margin: 15px 0;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .footer {
            margin-top: 30px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .logo {
            width: 80px;
            margin-bottom: 20px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="main.jpeg" alt="Naval Logo" class="logo">
                <h2>Naval System Login</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
            </div>
            <form method="POST" action="login.php">
                <!-- Single honeypot field with empty default -->
                <input type="text" name="honeypot" value="" style="display:none !important" tabindex="-1" autocomplete="off">
                
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small style="color:#aaa">Passwords are securely encrypted</small>
                </div>
                <button type="submit">ACCESS SYSTEM</button>
            </form>
            <div class="footer">
            Naval Command Network © 2023
        </div>
        </div>
    </div>
</body>
</html>
