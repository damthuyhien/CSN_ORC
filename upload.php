<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '../init_db.php';

/* ===============================
   XỬ LÝ ẢNH TRƯỚC KHI OCR
   Mục tiêu: giảm nhiễu, rõ chữ Việt
================================ */
function preprocessImage($srcPath){
    $info = getimagesize($srcPath);
    if (!$info) return false;

    switch ($info['mime']) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($srcPath);
            break;
        case 'image/png':
            $img = imagecreatefrompng($srcPath);
            break;
        default:
            return false;
    }

    $w = imagesx($img);
    $h = imagesy($img);

    $new = imagecreatetruecolor($w, $h);
    imagecopy($new, $img, 0, 0, 0, 0, $w, $h);

    // Chuyển xám
    imagefilter($new, IMG_FILTER_GRAYSCALE);

    // Giảm nhiễu nhẹ
    imagefilter($new, IMG_FILTER_SMOOTH, 6);

    // Tăng tương phản
    imagefilter($new, IMG_FILTER_CONTRAST, -15);

    $out = __DIR__ . "/uploads/processed_" . basename($srcPath);
    imagepng($new, $out);

    imagedestroy($img);
    imagedestroy($new);

    return 'uploads/' . basename($out);
}

/* ===============================
   UPLOAD & LƯU DB
================================ */
$error = '';

if (isset($_POST['submit'])) {

    if (empty($_POST['invoice_type'])) {
        $error = "❌ Vui lòng chọn loại hóa đơn";
    } else {

        $invoiceType = $_POST['invoice_type'];
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time().'_'.basename($_FILES['image']['name']);
        $realPath = $uploadDir.$fileName;
        $publicPath = 'uploads/'.$fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $realPath)) {

            // Chuyển giá trị từ form thành ID bảng invoice_types
            $invoiceTypeStmt = $db->prepare("SELECT id FROM invoice_types WHERE name = ?");
            $invoiceTypeStmt->execute([$invoiceType]);
            $invoiceTypeId = $invoiceTypeStmt->fetchColumn();

            if (!$invoiceTypeId) {
                // Nếu chưa có loại hóa đơn trong DB, thêm mới
                $insertType = $db->prepare("INSERT INTO invoice_types (name) VALUES (?)");
                $insertType->execute([$invoiceType]);
                $invoiceTypeId = $db->lastInsertId();
            }

            // Lưu lịch sử OCR
            $stmt = $db->prepare("
                INSERT INTO ocr_history (user_id, image_path, invoice_type_id, status, created_at)
                VALUES (?, ?, ?, 'processing', datetime('now','localtime'))
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $publicPath,
                $invoiceTypeId
            ]);

            $_SESSION['ocr_id'] = $db->lastInsertId();
            $_SESSION['last_image'] = $publicPath;

            // Tiền xử lý ảnh
            $_SESSION['processed_image'] = preprocessImage($realPath);

            header("Location: result.php");
            exit;

        } else {
            $error = "❌ Upload thất bại";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Upload OCR</title>

<style>
*{
    box-sizing:border-box;
    font-family:'Segoe UI','Roboto','Helvetica Neue',Arial,sans-serif;
}
body{
    margin:0;
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    color:#1f2937;
}

/* HEADER */
.header{
    background:#0b5ed7;
    color:#fff;
    padding:16px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header .logo{
    font-size:20px;
    font-weight:700;
}
.header a{
    color:#fff;
    margin-left:18px;
    text-decoration:none;
    font-weight:500;
}

/* CONTAINER */
.container{
    max-width:860px;
    margin:40px auto;
    background:#fff;
    padding:40px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,.12);
}

/* UPLOAD BOX */
.upload-box{
    border:2px dashed #0b5ed7;
    border-radius:18px;
    padding:35px;
    text-align:center;
}
.upload-box input[type=file]{
    font-size:15px;
    margin-bottom:25px;
}

/* INVOICE TYPE */
.invoice-type{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(130px,1fr));
    gap:16px;
    margin:30px 0;
}
.type-card{
    background:#f4f7fb;
    border-radius:16px;
    padding:18px 10px;
    text-align:center;
    cursor:pointer;
    border:2px solid transparent;
    transition:.25s;
    font-size:15px;
}
.type-card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 18px rgba(0,0,0,.08);
}
.type-card input{
    display:none;
}
.type-card:has(input:checked){
    border-color:#0b5ed7;
    background:#e8f0ff;
    font-weight:600;
}

/* BUTTON */
button{
    padding:12px 34px;
    border:none;
    border-radius:14px;
    background:#0b5ed7;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:.25s;
}
button:hover{
    background:#094cb8;
    transform:scale(1.03);
}

/* ERROR */
.error{
    color:#dc2626;
    text-align:center;
    margin-top:18px;
    font-weight:500;
}
</style>
</head>

<body>

<div class="header">
    <div class="logo">📸 Scan2Text</div>
    <nav>
        <a href="history.php">Lịch sử</a>
        <a href="index.php">Trang chủ</a>
        <a href="logout.php">Đăng xuất</a>
    </nav>
</div>

<div class="container">
    <h2 style="text-align:center;margin-bottom:25px">📄 Tải ảnh hóa đơn</h2>

    <form method="post" enctype="multipart/form-data" class="upload-box">
        <input type="file" name="image" accept="image/*" required>

        <div class="invoice-type">
            <label class="type-card"><input type="radio" name="invoice_type" value="Ăn uống">🍜<br>Ăn uống</label>
            <label class="type-card"><input type="radio" name="invoice_type" value="Mua sắm">🛍️<br>Mua sắm</label>
            <label class="type-card"><input type="radio" name="invoice_type" value="Di chuyển">🚕<br>Di chuyển</label>
            <label class="type-card"><input type="radio" name="invoice_type" value="Y tế">🏥<br>Y tế</label>
            <label class="type-card"><input type="radio" name="invoice_type" value="Khác">📄<br>Khác</label>
        </div>

        <button name="submit">📤 Tải lên & Xử lý</button>
    </form>

    <?php if($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>

</body>
</html>
