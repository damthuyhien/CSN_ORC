<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
if(!isset($_SESSION['last_image'])){ header("Location: upload.php"); exit; }

$image = $_SESSION['last_image'];

$output = shell_exec("tesseract ".escapeshellarg($image)." stdout -l vie+eng");

// lưu kết quả:
$db = new PDO('sqlite:db/database.sqlite');
$stmt = $db->prepare("INSERT INTO ocr_history (user_id, image_path, result, created_at) VALUES (?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $image, $output, date("Y-m-d H:i:s")]);
?>

<link rel="stylesheet" href="style.css">

<div class="header">
    <div class="logo">Scan2Text</div>
    <nav>
        <a href="upload.php">Tải ảnh OCR</a>
        <a href="history.php">Lịch sử OCR</a>
        <a href="index.php">Trang chủ</a>
        <a href="logout.php">Đăng xuất</a>
    </nav>
</div>

<div class="container">
    <h2>Kết quả OCR</h2>
    
    <div style="border-radius:12px; background:#fff; padding:15px; box-shadow:0 5px 15px rgba(0,0,0,0.05); margin-bottom:20px; text-align:center;">
        <img src="<?php echo $image; ?>" style="max-width:100%; border-radius:12px; margin-bottom:15px;">
        <p><strong>Text nhận dạng:</strong></p>
        <pre style="background:#f3f3f3; padding:15px; border-radius:12px; overflow-x:auto;"><?php echo htmlspecialchars($output); ?></pre>
    </div>

    <div style="display:flex; justify-content:center; gap:15px;">
        <a href="upload.php"><button>Tải ảnh khác</button></a>
        <a href="history.php"><button>Xem lịch sử</button></a>
        <a href="index.php"><button>Trang chủ</button></a>
    </div>
</div>
