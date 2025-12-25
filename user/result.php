<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['last_image'])) {
    header("Location: upload.php");
    exit;
}

$image = $_SESSION['last_image'];

/* ===== OCR ===== */
$cmd = "tesseract " . escapeshellarg($image) .
       " stdout -l vie --psm 6 -c preserve_interword_spaces=1";

$output = shell_exec($cmd);
$ocr_text = trim($output);

/* ===== LƯU DB ===== */
require __DIR__ . '/../init_db.php';

if ($ocr_text !== '' && isset($_SESSION['ocr_id'])) {
    $stmt = $db->prepare("
        UPDATE ocr_history
        SET result = ?, status = 'success'
        WHERE id = ?
    ");
    $stmt->execute([
        $ocr_text,
        $_SESSION['ocr_id']
    ]);
}

// tránh insert lại
unset($_SESSION['ocr_id']);
unset($_SESSION['last_image']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả OCR | Scan2Text</title>
<link rel="stylesheet" href="style.css">
<style>
body{
    margin:0;
    font-family:'Segoe UI', Tahoma, sans-serif;
    background:#eef2f7;
    color:#333;
}

/* ===== HEADER ===== */
.header{
    background:#0b5ed7;
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 40px;
    box-shadow:0 4px 8px rgba(0,0,0,0.05);
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

/* ===== CONTAINER ===== */
.container{
    max-width:900px;
    margin:40px auto 80px;
    background:#fff;
    padding:30px;
    border-radius:16px;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}
h2{
    text-align:center;
    color:#0b5ed7;
    margin-bottom:30px;
}
h4{
    margin-top:20px;
    margin-bottom:12px;
}

/* ===== IMAGE ===== */
.img-container{
    text-align:center;
    margin-bottom:20px;
}
.img-container img{
    max-width:100%;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
    transition:0.3s;
}
.img-container img:hover{
    transform:scale(1.03);
}

/* ===== TEXTAREA ===== */
textarea{
    width:100%;
    min-height:300px;
    padding:18px;
    font-size:14px;
    line-height:1.6;
    border-radius:12px;
    border:1px solid #ccc;
    background:#f4f6f9;
    resize:none;
}

/* ===== ACTION BUTTONS ===== */
.actions{
    margin-top:25px;
    display:flex;
    gap:15px;
    justify-content:center;
    flex-wrap:wrap;
}
.actions button{
    padding:12px 28px;
    border:none;
    border-radius:10px;
    background:#0b5ed7;
    color:#fff;
    font-weight:600;
    font-size:1rem;
    cursor:pointer;
    transition:0.25s;
}
.actions button:hover{
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
    .container{
        padding:20px;
    }
    .actions{
        flex-direction:column;
    }
    .actions button{
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
        <a href="history.php">Lịch sử</a>
        <a href="index.php">Trang chủ</a>
        <a href="\CN\logout.php">Đăng xuất</a>
    </nav>
</div>

<!-- MAIN CONTAINER -->
<div class="container">
    <h2>📄 Kết quả OCR</h2>

    <div class="img-container">
        <img src="<?= htmlspecialchars($image) ?>" alt="Ảnh OCR">
    </div>

    <h4>Văn bản nhận dạng:</h4>
    <textarea readonly><?= htmlspecialchars($ocr_text) ?></textarea>

    <div class="actions">
        <a href="upload.php"><button>📤 Tải ảnh khác</button></a>
        <a href="history.php"><button>📜 Lịch sử</button></a>
        <a href="index.php"><button>🏠 Trang chủ</button></a>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text · Hệ thống OCR · PHP & SQLite
</div>

</body>
</html>
