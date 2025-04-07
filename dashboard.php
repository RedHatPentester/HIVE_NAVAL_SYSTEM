<?php
// No proper session validation
session_start();

// XSS vulnerability in greeting
$greeting = isset($_GET['greeting']) ? $_GET['greeting'] : '';

// Command injection vulnerability
if (isset($_GET['check_status'])) {
    $output = shell_exec("ping -c 2 " . $_GET['check_status']);
}

// IDOR vulnerability
$officer_id = isset($_GET['officer_id']) ? (int)$_GET['officer_id'] : 0;

// Debug mode that exposes session
if (isset($_GET['debug'])) {
    var_dump($_SESSION);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HCNMS Dashboard</title>
    <style>
        :root {
            --navy: #0a192f;
            --teal: #64ffda;
            --light-navy: #172a45;
            --lightest-navy: #303C55;
            --slate: #8892b0;
            --light-slate: #a8b2d1;
            --white: #e6f1ff;
            --glow: 0 0 15px rgba(100, 255, 218, 0.7);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--navy);
            color: var(--white);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(10, 25, 47, 0.8) 0%, rgba(10, 25, 47, 1) 100%),
                url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 L0,100 Z" fill="none" stroke="%2364ffda" stroke-width="0.5" stroke-dasharray="5,5"/></svg>');
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--teal);
            position: relative;
        }

        .welcome {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--teal), var(--light-slate), var(--teal));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: var(--glow);
            position: relative;
            display: inline-block;
        }

        .welcome::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--teal), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { background-position: -100% 0; }
            100% { background-position: 100% 0; }
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .card {
            background: linear-gradient(145deg, var(--light-navy), var(--lightest-navy));
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3), 
                        inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(100, 255, 218, 0.2);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(100, 255, 218, 0) 0%,
                rgba(100, 255, 218, 0.1) 50%,
                rgba(100, 255, 218, 0) 100%
            );
            transform: rotate(30deg);
            transition: all 0.6s ease;
        }

        .card:hover::before {
            animation: shine 1.5s infinite;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border-color: var(--teal);
        }

        .card h3 {
            color: var(--teal);
            margin-top: 0;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .card h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--teal);
            transition: width 0.3s ease;
        }

        .card:hover h3::after {
            width: 100px;
        }

        input {
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 2px solid var(--light-navy);
            font-size: 1rem;
            width: 90%;
            margin-bottom: 1rem;
            background: var(--lightest-navy);
            color: var(--white);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        input:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px var(--teal);
            transform: scale(1.01);
        }

        button {
            background: var(--teal);
            padding: 0.8rem 1rem;
            border-radius: 8px;
            color: var(--navy);
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        button:hover {
            background: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        button:hover::before {
            left: 100%;
        }

        .logout-button {
            position: absolute;
            top: 20px;
            right: 40px;
            background: #ff4757;
            border: none;
            border-radius: 8px;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(255, 71, 87, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-button:hover {
            background: #ff6b81;
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(255, 71, 87, 0.8);
        }

        pre {
            background: #011627;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid var(--teal);
            box-shadow: inset 0 0 15px rgba(0,0,0,0.5);
            position: relative;
            margin-top: 1rem;
        }

        pre::before {
            content: 'terminal';
            position: absolute;
            top: 0;
            left: 0;
            background: var(--teal);
            color: var(--navy);
            padding: 0.2rem 0.8rem;
            font-size: 0.8rem;
            border-bottom-right-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-light {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--teal);
            box-shadow: var(--glow);
            margin-right: 0.8rem;
            position: relative;
        }

        .status-light::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: var(--teal);
            opacity: 0.4;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.7; }
            50% { transform: scale(1.2); opacity: 0.3; }
            100% { transform: scale(0.8); opacity: 0.7; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .welcome {
                font-size: 1.8rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .logout-button {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1 class="welcome">Welcome, <?= htmlspecialchars($_SESSION['user']['rank']) ?> <?= htmlspecialchars($_SESSION['user']['name']) ?></h1>
            <a href="/logout.php" class="logout-button">Logout</a>
        </header>

        <script>
function showAdminHint() {
    const hintDiv = document.createElement('div');
    hintDiv.innerHTML = `
        <div style="position:fixed; top:20%; left:20%; right:20%; 
            background:var(--light-navy); padding:20px; border:2px solid var(--teal);
            z-index:1000; text-align:center; border-radius:10px; color:var(--white);">
            <h3 style="color:var(--teal)">Admin Access Hint</h3>
            <p>Default admin credentials:</p>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> <span id="pwd">********</span></p>
            <button onclick="document.getElementById('pwd').textContent='navy12345'" 
                style="background:var(--teal); color:var(--navy); margin:5px;">
                Reveal Password
            </button>
            <button onclick="this.parentNode.parentNode.removeChild(this.parentNode)"
                style="background:var(--lightest-navy); color:var(--white); margin:5px;">
                Close
            </button>
        </div>
    `;
    document.body.appendChild(hintDiv);
}

// Easter egg: trigger on typing "please admin" into any input
document.addEventListener('input', function(e) {
    if (e.target.tagName.toLowerCase() === 'input' && e.target.value.trim().toLowerCase() === 'please admin') {
        showAdminHint();
    }
});
</script>


        <div class="dashboard-grid">
            <!-- Mission Reports -->
            <div class="card">
                <h3><span class="status-light"></span>Mission Reports</h3>
                <p>Upload classified documents and mission reports</p>
                <a href="/components/upload.php" style="display: inline-block; margin-top: 1rem;">
                    <button style="width: 100%;">Upload Reports</button>
                </a>
            </div>

            <!-- IDOR Vulnerability -->
            <div class="card">
                <h3><span class="status-light"></span>Officer Profiles</h3>
                <form method="GET" action="/view_profile.php">
                    <input type="number" name="officer_id" placeholder="Officer ID">
                    <button type="submit">View Profile</button>
                </form>
            </div>

            <!-- Command Injection in "System Status" -->
            <div class="card">
                <h3><span class="status-light"></span>Warship Status</h3>
                <form method="GET">
                    <input type="text" name="check_status" placeholder="Enter IP or hostname">
                    <button type="submit">Check Status</button>
                </form>
                <?php if ($_GET['check_status']): ?>
                <pre>
                <?php system("ping -c 2 " . $_GET['check_status']); ?>
                </pre>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
