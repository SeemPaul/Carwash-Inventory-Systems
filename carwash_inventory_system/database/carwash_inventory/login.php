<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, full_name, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
    } else {
        $error = "Please enter your username and password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kleen Ooto | Login</title>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #07111f, #0f172a, #1e3a8a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .login-wrapper {
            width: 900px;
            max-width: 95%;
            display: grid;
            grid-template-columns: 1fr 420px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
        }

        .login-info {
            padding: 50px;
            background: linear-gradient(135deg, rgba(37,99,235,.35), rgba(15,23,42,.95));
        }

        .login-info h1 {
            font-size: 38px;
            margin-bottom: 12px;
        }

        .login-info p {
            color: #cbd5e1;
            line-height: 1.6;
            font-size: 16px;
        }

        .feature-row {
            display: flex;
            gap: 18px;
            margin-top: 35px;
        }

        .feature-box {
            text-align: center;
        }

        .feature-box i {
            width: 54px;
            height: 54px;
            background: #2563eb;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 22px;
            margin: 0 auto 10px;
        }

        .login-card {
            background: #ffffff;
            color: #0f172a;
            padding: 42px 34px;
            text-align: center;
        }

        .login-logo {
            width: 125px;
            margin-bottom: 10px;
        }

        .login-card h2 {
            margin: 5px 0;
            font-size: 30px;
            color: #0f172a;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 28px;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
        }

        .input-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: #2563eb;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s ease;
            font-size: 15px;
        }

        .login-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .login-note {
            margin-top: 18px;
            color: #64748b;
            font-size: 13px;
        }

        .error {
            background: rgba(220,38,38,0.12);
            color: #dc2626;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        @media (max-width: 850px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-info {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-info">
        <h1>Kleen Ooto</h1>
        <p>
            Carwash Inventory Management System designed to monitor stocks,
            track usage, manage inventory records, and improve operational control.
        </p>

        <div class="feature-row">
            <div class="feature-box">
                <i class="fas fa-boxes-stacked"></i>
                <span>Inventory</span>
            </div>

            <div class="feature-box">
                <i class="fas fa-chart-line"></i>
                <span>Forecasting</span>
            </div>

            <div class="feature-box">
                <i class="fas fa-bell"></i>
                <span>Alerts</span>
            </div>
        </div>
    </div>

    <div class="login-card">
        <img src="assets/Carwash Logo Circle.png" class="login-logo">

        <h2>Kleen Ooto</h2>
        <p class="subtitle">Carwash Inventory Management System</p>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-right-to-bracket"></i> Login
            </button>
        </form>

        <div class="login-note">
            Login as Admin or Staff
        </div>
    </div>

</div>

</body>
</html>