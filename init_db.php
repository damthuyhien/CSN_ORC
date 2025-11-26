<?php

// Thư mục DB
$dbDir = __DIR__ . '/db';
$dbFile = $dbDir . '/database.sqlite';

// Tạo thư mục db nếu chưa tồn tại
if(!is_dir($dbDir)){
    if(!mkdir($dbDir, 0777, true)){
        die("❌ Không thể tạo thư mục db. Hãy kiểm tra quyền ghi.");
    }
}

// Tạo file SQLite nếu chưa tồn tại
if(!file_exists($dbFile)){
    $createFile = fopen($dbFile, 'w');
    if(!$createFile){
        die("❌ Không thể tạo file database.sqlite. Hãy kiểm tra quyền ghi của thư mục db.");
    }
    fclose($createFile);
    echo "✅ File database.sqlite đã được tạo thành công.<br>";
} else {
    echo "ℹ️ File database.sqlite đã tồn tại.<br>";
}

// Kết nối SQLite
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Kết nối SQLite thành công.<br>";
} catch (PDOException $e) {
    die("❌ Lỗi kết nối SQLite: " . $e->getMessage());
}

// Tạo bảng users
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
)");
echo "✅ Bảng 'users' đã sẵn sàng.<br>";

// Tạo bảng ocr_history
$db->exec("CREATE TABLE IF NOT EXISTS ocr_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    image_path TEXT,
    result TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
echo "✅ Bảng 'ocr_history' đã sẵn sàng.<br>";

echo "<br>🎉 CSN - CN Database đã được khởi tạo hoàn chỉnh!";
?>
