# 🚢 Hive Naval Command System - The Admiral's Playground ⚓

Ahoy there, cadet! Welcome aboard the most *secure* naval management system in the seven seas! 🏴‍☠️

## 🎯 System Overview
A PHP web application that manages naval officers with more holes than Swiss cheese! Features include:
- Admin dashboard with ✨easter eggs✨
- Officer database (with built-in IDOR vulnerabilities)
- Mission reporting system (perfect for leaking classified docs)
- "Security" logging (that logs to /dev/null)

## ⚠️ Critical Vulnerabilities - Your Treasure Map to Root

### 1. 🎣 Phishy Admin Access
The dashboard has a secret handshake! Type "admin please" in any input field to reveal:
```
Username: admin
Password: *********
```

### 2. 💉 SQL Injection Party
`includes/config.php` contains:
```php
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// No prepared statements? No problem! 
```

### 3. 🎮 Remote Code Execution
Check out the "Warship Status" panel - it'll happily run any command you type:
```php
system("ping -c 2 " . $_GET['check_status']);
```

### 4. 🕵️‍♂️ IDOR (Insecure Direct Object Reference)
View any officer's profile by guessing IDs:
```php
$officer_id = (int)$_GET['officer_id']; // No authorization checks!
```

### 5. 🎭 Session Hijacking
Debug mode exposes all session data:
```php
if (isset($_GET['debug'])) {
    var_dump($_SESSION); // Yarrr! Session variables for all!
}
```

## 🏴‍☠️ How to Become Admiral

1. **Find the Easter Egg**  
   Type "admin please" in any input field to reveal credentials

2. **Command Injection**  
   Use the Warship Status panel to execute commands

3. **Session Stealing**  
   Add `?debug=1` to any URL to dump session data

4. **Database Dump**  
   Use SQLi in search fields to extract all officer data

## 🛠️ "Fix" These Issues (If You Must)

1. **For config.php**:
```php
// Enable proper error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Use prepared statements
$stmt = $conn->prepare("SELECT * FROM officers WHERE id = ?");
$stmt->bind_param("i", $id);
```

2. **For command injection**:
```php
// Use escapeshellarg()
system("ping -c 2 " . escapeshellarg($_GET['check_status']));
```

3. **For IDOR**:
```php
// Verify user has permission
if ($_SESSION['user']['id'] != $officer_id && !$_SESSION['user']['is_admin']) {
    die("Permission denied!");
}
```

## Default Credential (Shhh! 🤫)
```
Officers: carl/ilovemywife
```

> ⚠️ Warning: This system is more vulnerable than a submarine with screen doors! Use only in controlled environments.
