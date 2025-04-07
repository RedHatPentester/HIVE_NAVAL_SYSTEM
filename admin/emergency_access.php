<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Easter egg - self-destruct sequence
    if ($_POST['code'] === '000-DESTRUCT-0') {
        echo "<pre>";
        echo "SELF DESTRUCT SEQUENCE INITIATED\n";
        for ($i=10; $i>0; $i--) {
            echo "$i...\n";
            sleep(1);
        }
        echo "Just kidding! This is a test system.\n";
        echo "But you found the Easter egg!\n";
        echo "</pre>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Emergency Access</title>
    <style>
        body { 
            background: #000; 
            color: red; 
            font-family: monospace;
            text-align: center;
            padding: 50px;
        }
        input {
            padding: 10px;
            font-size: 20px;
            background: #111;
            color: red;
            border: 1px solid red;
        }
        button {
            padding: 10px 20px;
            background: red;
            color: black;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>EMERGENCY ACCESS PORTAL</h1>
    <p>Enter destruct code:</p>
    <form method="POST">
        <input type="password" name="code" placeholder="DESTRUCT CODE">
        <button type="submit">ACTIVATE</button>
    </form>
</body>
</html>
