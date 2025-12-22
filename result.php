<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if(!isset($_SESSION['last_image'])){
    header("Location: upload.php");
    exit;
}

$image = $_SESSION['last_image'];
//ORC
$output = shell_exec(
    "tesseract " . escapeshellarg($image) .
    " stdout -l vie --psm 6 -c preserve_interword_spaces=1"
);



// Lưu DB
$db = new PDO('sqlite:db/database.sqlite');
$stmt = $db->prepare(
    "INSERT INTO ocr_history (user_id, image_path, result, created_at)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([
    $_SESSION['user_id'],
    $image,
    $output,
    date("Y-m-d H:i:s")
]);

$ocr_text = $output;
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

    <!-- Ảnh -->
    <div style="text-align:center; margin-bottom:25px;">
        <img src="<?php echo $image; ?>">
    </div>

    <!-- Header của văn bản + nút copy nhỏ -->
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:8px;
    ">
        <strong>Văn bản nhận dạng:</strong>

        <button onclick="copyOCR()"
            style="
                width:auto;
                padding:6px 14px;
                font-size:13px;
                border-radius:8px;
            ">
            Sao chép
        </button>
    </div>

    <!-- Text OCR -->
    <textarea
    class="ocr-text"
    readonly
    style="
        width:100%;
        min-height:520px;
        resize:none;
        overflow:hidden;

        font-family:'Times New Roman', Times, serif;
        font-size:13pt;
        color:#000;
        text-align:justify;
        line-height:1.6;

        padding:18px;
        box-sizing:border-box;
    "
><?php echo htmlspecialchars($ocr_text); ?></textarea>

    <!-- Nút điều hướng -->
    <div class="flex-center" style="margin-top:30px;">
        <a href="upload.php"><button class="main-btn">Tải ảnh khác</button></a>
        <a href="history.php"><button class="main-btn">Xem lịch sử</button></a>
        <a href="index.php"><button class="main-btn">Trang chủ</button></a>
    </div>
</div>

<script>
function copyOCR(){
    const textarea = document.querySelector('.ocr-text');
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Đã sao chép văn bản!");
}
const ta = document.querySelector('.ocr-text');
ta.style.height = 'auto';
ta.style.height = ta.scrollHeight + 'px';
</script>
