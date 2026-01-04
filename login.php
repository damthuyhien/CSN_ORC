<?php
session_start();

$db = new PDO('sqlite:db/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['username'], $_POST['password'])) {

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "❌ Tên đăng nhập hoặc mật khẩu không đúng";
    }
    else if ($user['status'] === 'blocked') {
        $error = "⛔ Tài khoản đã bị khóa";
    }
    else if (!password_verify($_POST['password'], $user['password'])) {
        $error = "❌ Tên đăng nhập hoặc mật khẩu không đúng";
    }
    else {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | Scan2Text - Hệ thống OCR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET & GLOBAL STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* ===== BACKGROUND DECORATION ===== */
        .bg-shape {
            position: absolute;
            z-index: -1;
        }

        .bg-shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(45deg, rgba(11, 94, 215, 0.08) 0%, rgba(58, 123, 213, 0.08) 100%);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            top: 5%;
            left: 5%;
            animation: float 20s ease-in-out infinite;
        }

        .bg-shape-2 {
            width: 250px;
            height: 250px;
            background: linear-gradient(45deg, rgba(11, 94, 215, 0.05) 0%, rgba(58, 123, 213, 0.05) 100%);
            border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
            bottom: 10%;
            right: 8%;
            animation: float 25s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* ===== HEADER ===== */
        .header {
            background: white;
            padding: 1.2rem 2.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(90deg, #0b5ed7 0%, #3a7bd5 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .header-tagline {
            font-size: 0.9rem;
            color: #555;
            font-weight: 500;
        }

        /* ===== MAIN CONTENT ===== */
        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        /* ===== LOGIN CARD ===== */
        .login-card {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(11, 94, 215, 0.12);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 60px rgba(11, 94, 215, 0.18);
        }

        .card-header {
            background: linear-gradient(90deg, #0b5ed7 0%, #3a7bd5 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .card-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 2.5rem;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: #0b5ed7;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.1rem;
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 3rem;
            border: 2px solid #e1e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-input:focus {
            outline: none;
            border-color: #0b5ed7;
            background: white;
            box-shadow: 0 0 0 3px rgba(11, 94, 215, 0.1);
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        /* ===== BUTTON ===== */
        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(90deg, #0b5ed7 0%, #3a7bd5 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 0.5rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(11, 94, 215, 0.25);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* ===== ERROR MESSAGE ===== */
        .error-message {
            margin-top: 1.2rem;
            padding: 0.9rem 1rem;
            background: #fee;
            border-left: 4px solid #f56565;
            border-radius: 8px;
            color: #c53030;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* ===== REGISTER LINK ===== */
        .register-link {
            text-align: center;
            margin-top: 1.8rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eef2f7;
            font-size: 0.9rem;
            color: #555;
        }

        .register-link a {
            color: #0b5ed7;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #094db3;
            text-decoration: underline;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: white;
            padding: 1.2rem;
            text-align: center;
            font-size: 0.85rem;
            color: #666;
            border-top: 1px solid #eef2f7;
            margin-top: auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }

        .footer-links a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #0b5ed7;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 0.8rem;
                text-align: center;
            }

            .card-body {
                padding: 2rem 1.5rem;
            }

            .bg-shape-1, .bg-shape-2 {
                display: none;
            }

            .login-card {
                max-width: 95%;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                border-radius: 16px;
            }

            .card-header {
                padding: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body>

<!-- BACKGROUND DECORATION -->
<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<!-- HEADER -->
<div class="header">
    <div class="logo">
        <i class="fas fa-scanner"></i>
        Scan2Text
    </div>
    <div class="header-tagline">Hệ thống nhận dạng văn bản OCR thông minh</div>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="login-card">
        <div class="card-header">
            <h2>Đăng nhập hệ thống</h2>
            <p>Nhập thông tin tài khoản để tiếp tục</p>
        </div>

        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Tên đăng nhập
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               name="username" 
                               class="form-input" 
                               placeholder="Nhập tên đăng nhập" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i>
                        Mật khẩu
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" 
                               name="password" 
                               class="form-input" 
                               placeholder="Nhập mật khẩu" 
                               required>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>

                <?php if(isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="register-link">
                    Chưa có tài khoản? 
                    <a href="register.php">
                        <i class="fas fa-user-plus"></i>
                        Đăng ký ngay
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text - Hệ thống OCR chuyên nghiệp
    <div class="footer-links">
        <a href="#"><i class="fas fa-question-circle"></i> Trợ giúp</a>
        <a href="#"><i class="fas fa-shield-alt"></i> Bảo mật</a>
        <a href="#"><i class="fas fa-envelope"></i> Liên hệ</a>
    </div>
</div>

</body>
</html>