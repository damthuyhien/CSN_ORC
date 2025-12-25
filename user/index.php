<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Scan2Text - OCR System</title>
<link rel="stylesheet" href="style.css">
<style>
/* ===== GLOBAL ===== */
body{
    margin:0;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #eef2f7;
    color:#333;
}
a{text-decoration:none;}

/* ===== HEADER ===== */
.header{
    background:#0b5ed7;
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 40px;
    box-shadow:0 4px 8px rgba(0,0,0,0.05);
    position:sticky;
    top:0;
    z-index:100;
}
.header .logo{
    font-size:1.8rem;
    font-weight:700;
}
.header nav a{
    margin-left:25px;
    color:#fff;
    font-weight:500;
}
.header nav span{
    margin-right:15px;
}

/* ===== MAIN HERO ===== */
.container{
    max-width:1200px;
    margin:40px auto 80px; /* margin-bottom đủ cho footer */
    padding:0 20px;
}
.hero{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:40px;
    justify-content:space-between;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,0.08);
    transition:0.3s;
}
.hero:hover{
    box-shadow:0 20px 50px rgba(0,0,0,0.12);
}
.hero-text{
    flex:1 1 450px;
}
.hero-text h1{
    font-size:2.4rem;
    color:#0b5ed7;
    margin-bottom:20px;
}
.hero-text p{
    font-size:1.1rem;
    margin-bottom:25px;
    line-height:1.7;
}
.flex-center{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}
.main-btn{
    padding:12px 30px;
    background:#0b5ed7;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:1rem;
    font-weight:600;
    cursor:pointer;
    transition:0.25s;
    box-shadow:0 4px 12px rgba(11,94,215,0.3);
}
.main-btn:hover{
    background:#094bb5;
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(11,94,215,0.4);
}

/* HERO IMAGE */
.hero-image{
    flex:1 1 400px;
    text-align:center;
}
.hero-image img{
    max-width:100%;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    transition:0.4s;
}
.hero-image img:hover{
    transform:scale(1.03);
    box-shadow:0 15px 40px rgba(0,0,0,0.15);
}

/* ===== FOOTER ===== */
.footer{
    background:#0b5ed7;
    color:#fff;
    padding:20px 40px;
    text-align:center;
    font-size:0.9rem;
    box-shadow:0 -4px 8px rgba(0,0,0,0.05);
}

/* ===== RESPONSIVE ===== */
@media(max-width:900px){
    .hero{
        flex-direction:column;
        text-align:center;
    }
    .hero-text, .hero-image{
        flex:1 1 100%;
    }
    .flex-center{
        justify-content:center;
    }
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">Scan2Text</div>
    <nav>
        <?php if($logged_in): ?>
            <span>Xin chào, <?php echo $_SESSION['username']; ?></span>
            <a href="\CN\logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="login.php">Đăng nhập</a>
            <a href="register.php">Đăng ký</a>
        <?php endif; ?>
    </nav>
</div>

<!-- MAIN -->
<div class="container">
    <div class="hero">
        <div class="hero-text">
            <h1>Chào mừng đến Scan2Text!</h1>
            <p>Nhận dạng ký tự từ hình ảnh OCR nhanh chóng, hỗ trợ tiếng Việt và tiếng Anh. Lưu lịch sử quét, quản lý kết quả thông minh và xuất dữ liệu dễ dàng.</p>
            <div class="flex-center">
                <a href="<?php echo $logged_in ? 'upload.php' : 'login.php'; ?>"><button class="main-btn">Tải ảnh OCR</button></a>
                <a href="<?php echo $logged_in ? 'history.php' : 'login.php'; ?>"><button class="main-btn">Xem lịch sử OCR</button></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="R.png" alt="OCR Illustration">
        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text · Hệ thống OCR · PHP & SQLite
</div>

</body>
</html>
