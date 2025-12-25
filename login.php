<?php
session_start();
$db = new PDO('sqlite:db/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if(isset($_POST['username'], $_POST['password'])){
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($_POST['password'], $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 👉 PHÂN QUYỀN
        if($user['role'] === 'admin'){
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/index.php");
        }
        exit;
    } else {
        $error = "❌ Tên đăng nhập hoặc mật khẩu không đúng";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập | Scan2Text</title>

<style>
*{box-sizing:border-box}
body{
    margin:0;
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
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header .logo{
    font-size:1.6rem;
    font-weight:700;
}
.header span{
    font-size:0.95rem;
    opacity:0.9;
}

/* ===== MAIN ===== */
.main{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 15px;
}

/* ===== LOGIN CARD ===== */
.login-card{
    width:100%;
    max-width:420px;
    background:#fff;
    padding:35px 32px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,0.15);
}

.login-card h2{
    text-align:center;
    margin-bottom:8px;
    color:#0b5ed7;
}

.login-card p{
    text-align:center;
    font-size:0.95rem;
    color:#555;
    margin-bottom:25px;
}

form{
    display:flex;
    flex-direction:column;
    gap:16px;
}

input{
    padding:12px 14px;
    border-radius:10px;
    border:1px solid #ddd;
    font-size:0.95rem;
}

input:focus{
    outline:none;
    border-color:#0b5ed7;
    box-shadow:0 0 0 2px rgba(11,94,215,0.15);
}

button{
    margin-top:6px;
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

button:hover{
    background:#094db3;
    transform:translateY(-1px);
}

.error{
    margin-top:12px;
    color:#dc3545;
    text-align:center;
    font-size:0.9rem;
}

.bottom-text{
    margin-top:22px;
    text-align:center;
    font-size:0.9rem;
}
.bottom-text a{
    color:#0b5ed7;
    font-weight:600;
    text-decoration:none;
}

/* ===== FOOTER ===== */
.footer{
    background:#fff;
    border-top:1px solid #e5e7eb;
    padding:14px;
    text-align:center;
    font-size:0.85rem;
    color:#555;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">Scan2Text</div>
    <span>Hệ thống nhận dạng hóa đơn OCR</span>
</div>

<!-- MAIN -->
<div class="main">
    <div class="login-card">
        <h2>Đăng nhập</h2>
        <p>Vui lòng đăng nhập để tiếp tục</p>

        <form method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">🔐 Đăng nhập</button>
        </form>

        <?php if(isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <div class="bottom-text">
            Chưa có tài khoản?
            <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text · Đồ án OCR · PHP & SQLite
</div>

</body>
</html>
