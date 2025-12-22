<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// ===== HÀM XỬ LÝ ẢNH =====
function preprocessImage($srcPath){
    $info = getimagesize($srcPath);
    $mime = $info['mime'];

    if($mime == 'image/jpeg'){
        $img = imagecreatefromjpeg($srcPath);
    } elseif($mime == 'image/png'){
        $img = imagecreatefrompng($srcPath);
    } else {
        return false;
    }

    $width  = imagesx($img);
    $height = imagesy($img);

    // Tạo ảnh mới
    $newImg = imagecreatetruecolor($width, $height);

    // 1. Chuyển ảnh xám
    imagecopy($newImg, $img, 0, 0, 0, 0, $width, $height);
    imagefilter($newImg, IMG_FILTER_GRAYSCALE);

    // 2. Lọc nhiễu (blur)
    imagefilter($newImg, IMG_FILTER_GAUSSIAN_BLUR);

    // 3. Nhị phân hóa
    for($x = 0; $x < $width; $x++){
        for($y = 0; $y < $height; $y++){
            $rgb = imagecolorat($newImg, $x, $y);
            $gray = ($rgb >> 16) & 0xFF;

            $color = ($gray > 150) ? 255 : 0;
            $newColor = imagecolorallocate($newImg, $color, $color, $color);
            imagesetpixel($newImg, $x, $y, $newColor);
        }
    }

    // Lưu ảnh đã xử lý
    $processedPath = "uploads/processed_" . basename($srcPath);
    imagejpeg($newImg, $processedPath);

    imagedestroy($img);
    imagedestroy($newImg);

    return $processedPath;
}

// ===== XỬ LÝ UPLOAD =====
if(isset($_POST['submit'])){
    $target_dir = "uploads/";
    $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);

    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){

        // GỌI XỬ LÝ ẢNH
        $processed_image = preprocessImage($target_file);

        $_SESSION['last_image'] = $target_file;
        $_SESSION['processed_image'] = $processed_image;

        header("Location: result.php");
        exit;
    } else {
        $error = "Upload thất bại!";
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="header">
    <div class="logo">Scan2Text</div>
    <nav>
        <a href="history.php">Lịch sử OCR</a>
        <a href="index.php">Trang chủ</a>
        <a href="logout.php">Đăng xuất</a>
    </nav>
</div>

<div class="container">
    <h2>Tải ảnh để nhận dạng OCR</h2>

    <div class="upload-box">
        <form method="POST" enctype="multipart/form-data">
            <p>Nhấp hoặc kéo ảnh vào đây</p>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit" name="submit">Tải lên</button>
        </form>
    </div>

    <?php if(isset($error)): ?>
        <p style="color:red; text-align:center; margin-top:15px;">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>

    <p style="text-align:center; margin-top:20px;">
        <a href="index.php"><button>Quay lại trang chủ</button></a>
    </p>
</div>
