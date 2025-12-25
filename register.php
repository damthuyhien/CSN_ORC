<?php
session_start();

$db = new PDO('sqlite:db/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['username'], $_POST['password'], $_POST['password_confirm'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = "❌ Mật khẩu nhập lại không khớp!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $db->prepare("
                INSERT INTO users (username, password, role, status, created_at)
                VALUES (?, ?, 'user', 'active', datetime('now','localtime'))
            ");
            $stmt->execute([$username, $password_hash]);

            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
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
<title>Đăng ký | Scan2Text</title>
<link rel="stylesheet" href="style.css">

<style>
*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    min-height:100vh;
    display:flex;
    flex-direction:column;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
}

/* ===== HEADER ===== */
.header{
    background:#0b5ed7;
    color:#fff;
    padding:16px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header .logo{
    font-size:1.4rem;
    font-weight:600;
}

.header a{
    color:#fff;
    text-decoration:none;
    font-weight:500;
}

/* ===== MAIN ===== */
.main{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 15px;
}

/* ===== AUTH BOX ===== */
.auth-box{
    width:100%;
    max-width:420px;
    background:#fff;
    padding:32px 30px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.15);
}

/* tiêu đề */
.auth-box h2{
    text-align:center;
    margin-bottom:22px;
    color:#0b5ed7;
}

/* form */
.auth-box form{
    display:flex;
    flex-direction:column;
    gap:16px;
}

/* input */
.auth-box input{
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:0.95rem;
}

.auth-box input:focus{
    outline:none;
    border-color:#0b5ed7;
    box-shadow:0 0 0 2px rgba(11,94,215,0.15);
}

/* button */
.auth-box button{
    width:100%;
    padding:12px;
    border-radius:12px;
    border:none;
    background:#0b5ed7;
    color:#fff;
    font-size:1rem;
    font-weight:600;
    cursor:pointer;
    transition:0.25s;
}

.auth-box button:hover{
    background:#094bb5;
    transform:translateY(-1px);
}

/* error */
.auth-box .error{
    margin-top:14px;
    text-align:center;
    color:#dc3545;
    font-size:0.9rem;
}

/* link dưới */
.auth-box p{
    margin-top:18px;
    text-align:center;
    font-size:0.9rem;
}

.auth-box a{
    color:#0b5ed7;
    font-weight:600;
    text-decoration:none;
}

/* ===== FOOTER ===== */
.footer{
    text-align:center;
    padding:14px;
    background:#f1f3f5;
    color:#666;
    font-size:0.85rem;
}

</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">Scan2Text</div>
    <nav>
        <a href="login.php">Đăng nhập</a>
    </nav>
</div>

<!-- MAIN -->
<div class="main">
    <div class="auth-box">
        <h2>📝 Đăng ký tài khoản</h2>

        <form method="POST" style="display:flex; flex-direction:column; gap:16px;">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <input type="password" name="password_confirm" placeholder="Nhập lại mật khẩu" required>
            <button type="submit">Đăng ký</button>
        </form>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <p style="text-align:center; margin-top:20px;">
            Đã có tài khoản?
            <a href="login.php">Đăng nhập ngay</a>
        </p>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text • OCR System
</div>

</body>
</html>
