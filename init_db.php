<?php
/* ===============================
   THIẾT LẬP ĐƯỜNG DẪN DATABASE
================================ */
$dbDir  = __DIR__ . '/db';
$dbFile = $dbDir . '/database.sqlite';

/* ===============================
   TẠO THƯ MỤC DB
================================ */
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

/* ===============================
   TẠO FILE SQLITE
================================ */
if (!file_exists($dbFile)) {
    fopen($dbFile, 'w');
}

/* ===============================
   KẾT NỐI SQLITE
================================ */
try {
    $db = new PDO("sqlite:" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ⚠️ CỰC KỲ QUAN TRỌNG: BẬT KHÓA NGOẠI SQLITE
    $db->exec("PRAGMA foreign_keys = ON");

} catch (PDOException $e) {
    die("❌ Lỗi kết nối DB: " . $e->getMessage());
}

/* ===============================
   BẢNG USERS
================================ */
$db->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    status TEXT DEFAULT 'active',
    avatar TEXT DEFAULT 'default.png',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

/* ===============================
   BẢNG OCR HISTORY
================================ */
$db->exec("
CREATE TABLE IF NOT EXISTS ocr_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    image_path TEXT,
    invoice_type TEXT,
    result TEXT,
    status TEXT DEFAULT 'success',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
)
");

/* ===============================
   BẢNG INVALID DATA
================================ */
$db->exec("
CREATE TABLE IF NOT EXISTS invalid_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ocr_id INTEGER NOT NULL,
    issue TEXT NOT NULL,
    checked_by INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(ocr_id) REFERENCES ocr_history(id) ON DELETE CASCADE,
    FOREIGN KEY(checked_by) REFERENCES users(id) ON DELETE SET NULL
)
");

/* ===============================
   TẠO ADMIN MẶC ĐỊNH
================================ */
$checkAdmin = $db->query("
    SELECT COUNT(*) FROM users WHERE role = 'admin'
")->fetchColumn();

if ($checkAdmin == 0) {
    $adminPass = password_hash("admin123", PASSWORD_DEFAULT);
    $stmt = $db->prepare("
        INSERT INTO users (username, password, role, status, created_at)
        VALUES (?, ?, 'admin', 'active', datetime('now','localtime'))
    ");
    $stmt->execute(["admin", $adminPass]);
}
