<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

require __DIR__ . '/../init_db.php'; // kết nối DB
$stmt = $db->prepare("SELECT * FROM ocr_history WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử OCR | Scan2Text</title>
<link rel="stylesheet" href="style.css">
<style>
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
    font-size:1.6rem;
    font-weight:700;
}
.header nav a{
    margin-left:25px;
    color:#fff;
    font-weight:500;
}

/* ===== MAIN ===== */
.container{
    max-width:1000px;
    margin:40px auto 80px;
    padding:0 20px;
}
h2{
    text-align:center;
    color:#0b5ed7;
    margin-bottom:30px;
}

/* ===== HISTORY ITEMS ===== */
.history{
    background:#fff;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    transition:0.3s;
}
.history:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 30px rgba(0,0,0,0.12);
}
.history img{
    max-width:100%;
    border-radius:12px;
    margin-bottom:10px;
    transition:0.3s;
}
.history img:hover{
    transform:scale(1.03);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.history pre{
    background:#f4f6f9;
    padding:12px;
    border-radius:10px;
    overflow-x:auto;
    font-family: 'Courier New', monospace;
    white-space:pre-wrap;
}
.history small{
    display:block;
    margin-top:8px;
    color:#555;
    font-size:0.85rem;
}

/* ===== BUTTON ===== */
button{
    padding:12px 25px;
    background:#0b5ed7;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:1rem;
    font-weight:500;
    cursor:pointer;
    transition:0.25s;
}
button:hover{
    background:#094bb5;
    transform:translateY(-2px);
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
@media(max-width:700px){
    .history{
        padding:15px;
    }
    button{
        width:100%;
    }
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">Scan2Text</div>
    <nav>
        <a href="upload.php">Tải ảnh OCR</a>
        <a href="index.php">Trang chủ</a>
        <a href="\CN\logout.php">Đăng xuất</a>
    </nav>
</div>

<!-- MAIN -->
<div class="container">
<h2>Lịch sử OCR</h2>

<?php if($records): ?>
    <?php foreach($records as $r): ?>
        <div class="history">
            <img src="<?php echo $r['image_path']; ?>" alt="Ảnh OCR">
            <strong>Kết quả OCR:</strong>
            <pre><?php echo htmlspecialchars($r['result']); ?></pre>
            <small>Ngày: <?php echo $r['created_at']; ?></small>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center; color:#555;">Chưa có lịch sử OCR.</p>
<?php endif; ?>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text · Hệ thống OCR · PHP & SQLite
</div>

</body>
</html>
