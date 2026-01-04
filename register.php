<?php
session_start();

$db = new PDO('sqlite:db/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['username'], $_POST['password'], $_POST['password_confirm'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (strlen($password) < 6) {
        $error = "❌ Mật khẩu phải có ít nhất 6 ký tự!";
    } 
    else if ($password !== $password_confirm) {
        $error = "❌ Mật khẩu nhập lại không khớp!";
    } 
    else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $db->prepare("
                INSERT INTO users (username, password, status, created_at)
                VALUES (?, ?, 'active', datetime('now','localtime'))
            ");
            $stmt->execute([$username, $password_hash]);

            header("Location: login.php");
            exit;
        } 
        catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $error = "❌ Tên đăng nhập đã tồn tại!";
            } else {
                $error = "❌ Lỗi hệ thống!";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký | Scan2Text - Hệ thống OCR</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ===== RESET & GLOBAL ===== */
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family:'Inter', sans-serif; min-height:100vh; background: linear-gradient(135deg,#f5f7fa 0%,#e4edf5 100%); display:flex; flex-direction:column; position:relative; overflow-x:hidden;}

/* ===== BACKGROUND SHAPES ===== */
.bg-shapes {position:absolute; width:100%; height:100%; z-index:-1; overflow:hidden;}
.shape {position:absolute; border-radius:50%; background:linear-gradient(45deg, rgba(11, 94, 215,0.08), rgba(58,123,213,0.08)); animation:float 20s ease-in-out infinite;}
.shape-1 {width:250px; height:250px; top:10%; left:5%; border-radius:63% 37% 54% 46% / 55% 48% 52% 45%; animation-delay:0s;}
.shape-2 {width:180px; height:180px; bottom:15%; right:10%; border-radius:30% 70% 70% 30% / 30% 30% 70% 70%; animation-delay:5s;}
.shape-3 {width:120px; height:120px; top:50%; left:85%; border-radius:40% 60% 70% 30% / 40% 50% 60% 50%; animation-delay:10s;}
@keyframes float {0%,100%{transform:translate(0,0) rotate(0deg);}33%{transform:translate(20px,-20px) rotate(120deg);}66%{transform:translate(-15px,15px) rotate(240deg);}}

/* ===== HEADER ===== */
.header {background:white; padding:1.2rem 2.5rem; box-shadow:0 4px 12px rgba(0,0,0,0.06); display:flex; justify-content:space-between; align-items:center;}
.logo {display:flex; align-items:center; gap:10px; font-size:1.6rem; font-weight:700; background:linear-gradient(90deg,#0b5ed7 0%,#3a7bd5 100%); -webkit-background-clip:text; background-clip:text; color:transparent;}
.logo i {font-size:1.8rem;}
.header nav a {color:#0b5ed7; text-decoration:none; font-weight:500; padding:0.5rem 1rem; border-radius:8px; display:inline-flex; align-items:center; gap:8px; transition:all 0.3s ease;}
.header nav a:hover {background:#f0f7ff; transform:translateY(-2px);}

/* ===== MAIN ===== */
.main {flex:1; display:flex; justify-content:center; align-items:center; padding:2rem;}

/* ===== REGISTER CARD ===== */
.register-card {width:100%; max-width:480px; background:white; border-radius:24px; box-shadow:0 20px 50px rgba(11,94,215,0.12); overflow:hidden; animation:fadeIn 0.6s ease-out;}
.register-card:hover {transform:translateY(-5px); box-shadow:0 25px 60px rgba(11,94,215,0.18);}

/* ===== CARD HEADER ===== */
.card-header {background:linear-gradient(90deg,#0b5ed7 0%,#3a7bd5 100%); padding:2rem; text-align:center; color:white; position:relative; overflow:hidden;}
.card-header::before {content:''; position:absolute; top:-50%; left:-50%; width:200%; height:200%; background:radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size:30px 30px; animation:moveBackground 20s linear infinite;}
@keyframes moveBackground {0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
.card-header h2 {font-size:1.8rem; font-weight:600; margin-bottom:0.5rem; position:relative; z-index:1;}
.card-header p {opacity:0.9; font-size:0.95rem; position:relative; z-index:1;}

/* ===== CARD BODY ===== */
.card-body {padding:2.5rem;}

/* ===== FORM ===== */
.form-group {margin-bottom:1.5rem;}
.form-label {display:flex; align-items:center; gap:8px; font-weight:500; color:#333; margin-bottom:0.5rem; font-size:0.95rem;}
.form-label i {color:#0b5ed7; width:20px; text-align:center;}
.input-with-icon {position:relative;}
.input-icon {position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#888; font-size:1.1rem;}
.form-input {width:100%; padding:0.9rem 1rem 0.9rem 3rem; border:2px solid #e1e8f0; border-radius:12px; font-size:1rem; transition:all 0.3s ease; background:#f8fafc;}
.form-input:focus {outline:none; border-color:#0b5ed7; background:white; box-shadow:0 0 0 3px rgba(11,94,215,0.1);}
.form-input::placeholder {color:#a0aec0;}

/* ===== BUTTON ===== */
.register-btn {width:100%; padding:1rem; background:linear-gradient(90deg,#0b5ed7 0%,#3a7bd5 100%); color:white; border:none; border-radius:12px; font-size:1rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; margin-top:1rem; transition:all 0.3s ease;}
.register-btn:hover {transform:translateY(-2px); box-shadow:0 10px 20px rgba(11,94,215,0.25);}
.register-btn:active {transform:translateY(0);}

/* ===== ERROR MESSAGE ===== */
.error-message {margin-top:1.2rem; padding:0.9rem 1rem; background:#fee; border-left:4px solid #f56565; border-radius:8px; color:#c53030; font-size:0.9rem; display:flex; align-items:center; gap:10px; animation:shake 0.5s ease-in-out;}
@keyframes shake {0%,100%{transform:translateX(0);}25%{transform:translateX(-5px);}75%{transform:translateX(5px);}}

/* ===== LOGIN LINK ===== */
.login-link {text-align:center; margin-top:1.8rem; padding-top:1.5rem; border-top:1px solid #eef2f7; font-size:0.9rem; color:#555;}
.login-link a {color:#0b5ed7; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:5px; transition:color 0.3s ease;}
.login-link a:hover {color:#094db3; text-decoration:underline;}

/* ===== FOOTER ===== */
.footer {background:white; padding:1.2rem; text-align:center; font-size:0.85rem; color:#666; border-top:1px solid #eef2f7; margin-top:auto;}
.footer-links {display:flex; justify-content:center; gap:1.5rem; margin-top:0.5rem;}
.footer-links a {color:#666; text-decoration:none; transition:color 0.3s ease;}
.footer-links a:hover {color:#0b5ed7;}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){.header{padding:1rem; flex-direction:column; gap:0.8rem; text-align:center;}.card-body{padding:2rem 1.5rem;}.shape-1,.shape-2,.shape-3{display:none;}.register-card{max-width:95%;}}
@media(max-width:480px){.register-card{border-radius:16px;}.card-header{padding:1.5rem;}.card-body{padding:1.5rem;}}

/* ===== ANIMATION ===== */
@keyframes fadeIn{from{opacity:0; transform:translateY(20px);}to{opacity:1; transform:translateY(0);}}
</style>
</head>
<body>

<div class="bg-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
</div>

<div class="header">
    <div class="logo"><i class="fas fa-scanner"></i> Scan2Text</div>
    <nav><a href="login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a></nav>
</div>

<div class="main">
    <div class="register-card">
        <div class="card-header">
            <h2>Tạo tài khoản mới</h2>
            <p>Đăng ký để sử dụng đầy đủ tính năng của Scan2Text</p>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Tên đăng nhập</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" class="form-input" placeholder="Nhập tên đăng nhập" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock"></i> Mật khẩu</label>
                    <div class="input-with-icon">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required minlength="6">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock"></i> Xác nhận mật khẩu</label>
                    <div class="input-with-icon">
                        <i class="fas fa-check-circle input-icon"></i>
                        <input type="password" name="password_confirm" class="form-input" placeholder="Nhập lại mật khẩu" required minlength="6">
                    </div>
                </div>

                <button type="submit" class="register-btn"><i class="fas fa-user-plus"></i> Đăng ký tài khoản</button>

                <?php if(isset($error)): ?>
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="login-link">
                    Đã có tài khoản? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập ngay</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="footer">
    © <?= date('Y') ?> Scan2Text - Hệ thống OCR chuyên nghiệp
    <div class="footer-links">
        <a href="#"><i class="fas fa-question-circle"></i> Hỗ trợ</a>
        <a href="#"><i class="fas fa-shield-alt"></i> Chính sách bảo mật</a>
        <a href="#"><i class="fas fa-file-contract"></i> Điều khoản sử dụng</a>
    </div>
</div>

</body>
</html>
