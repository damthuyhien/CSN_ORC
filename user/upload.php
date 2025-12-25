<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/../init_db.php';

/* ===== HÀM XỬ LÝ ẢNH ===== */
function preprocessImage($srcPath){
    $info = getimagesize($srcPath);
    if (!$info) return false;

    $mime = $info['mime'];
    if ($mime == 'image/jpeg') {
        $img = imagecreatefromjpeg($srcPath);
    } elseif ($mime == 'image/png') {
        $img = imagecreatefrompng($srcPath);
    } else {
        return false;
    }

    $w = imagesx($img);
    $h = imagesy($img);

    $new = imagecreatetruecolor($w, $h);
    imagecopy($new, $img, 0, 0, 0, 0, $w, $h);

    imagefilter($new, IMG_FILTER_GRAYSCALE);
    imagefilter($new, IMG_FILTER_GAUSSIAN_BLUR);

    for ($x=0;$x<$w;$x++){
        for ($y=0;$y<$h;$y++){
            $rgb = imagecolorat($new,$x,$y);
            $gray = ($rgb>>16)&0xFF;
            $c = ($gray>150)?255:0;
            imagesetpixel($new,$x,$y,imagecolorallocate($new,$c,$c,$c));
        }
    }

    $out = "uploads/processed_" . basename($srcPath);
    imagejpeg($new,$out);
    imagedestroy($img);
    imagedestroy($new);
    return $out;
}

/* ===== UPLOAD ===== */
$error = '';

if (isset($_POST['submit'])) {

    if (empty($_POST['invoice_type'])) {
        $error = "❌ Vui lòng chọn loại hóa đơn";
    } else {

        $invoiceType = $_POST['invoice_type'];

        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

        $fileName = time().'_'.basename($_FILES['image']['name']);
        $realPath = $uploadDir.$fileName;
        $publicPath = 'uploads/'.$fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $realPath)) {

$stmt = $db->prepare("
    INSERT INTO ocr_history (user_id, image_path, invoice_type, status, created_at)
    VALUES (?, ?, ?, 'processing', datetime('now','localtime'))
");
$stmt->execute([
    $_SESSION['user_id'],
    $publicPath,
    $invoiceType
]);

// ⭐ LẤY ID DÒNG VỪA TẠO
$_SESSION['ocr_id'] = $db->lastInsertId();

// nhớ ảnh để OCR
$_SESSION['last_image'] = $publicPath;


            // xử lý ảnh (chưa OCR)
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
<link rel="stylesheet" href="style.css">
<style>
body{background:linear-gradient(135deg,#eef2ff,#f8fafc);font-family:Arial}
.header{background:#0b5ed7;color:#fff;padding:15px 30px;display:flex;justify-content:space-between}
.header a{color:#fff;margin-left:15px;text-decoration:none}
.container{max-width:800px;margin:30px auto;background:#fff;padding:30px;border-radius:16px;box-shadow:0 15px 35px rgba(0,0,0,.15)}
.upload-box{border:2px dashed #0b5ed7;border-radius:16px;padding:25px;text-align:center}
.invoice-type{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:15px;margin:25px 0}
.type-card{background:#f4f7fb;border-radius:14px;padding:18px;text-align:center;cursor:pointer;border:2px solid transparent}
.type-card input{display:none}
.type-card:has(input:checked){border-color:#0b5ed7;background:#e8f0ff;font-weight:600}
button{padding:10px 25px;border:none;border-radius:10px;background:#0b5ed7;color:#fff}
</style>
</head>

<body>
<div class="header">
    <div class="logo">Scan2Text</div>
    <nav>
        <a href="history.php">Lịch sử</a>
        <a href="index.php">Trang chủ</a>
        <a href="\CN\logout.php">Đăng xuất</a>
    </nav>
</div>

<div class="container">
<h2 style="text-align:center">📄 Tải ảnh hóa đơn</h2>

<form method="post" enctype="multipart/form-data" class="upload-box">
<input type="file" name="image" required>

<div class="invoice-type">
<label class="type-card"><input type="radio" name="invoice_type" value="an_uong">🍜<br>Ăn uống</label>
<label class="type-card"><input type="radio" name="invoice_type" value="mua_sam">🛍️<br>Mua sắm</label>
<label class="type-card"><input type="radio" name="invoice_type" value="di_chuyen">🚕<br>Di chuyển</label>
<label class="type-card"><input type="radio" name="invoice_type" value="y_te">🏥<br>Y tế</label>
<label class="type-card"><input type="radio" name="invoice_type" value="khac">📄<br>Khác</label>
</div>

<button name="submit">📤 Tải lên</button>
</form>

<?php if($error): ?>
<p style="color:red;text-align:center;margin-top:15px"><?= $error ?></p>
<?php endif; ?>
</div>
</body>
</html>
