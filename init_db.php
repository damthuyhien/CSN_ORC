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
    $db->exec("PRAGMA foreign_keys = ON");
} catch (PDOException $e) {
    die("❌ Lỗi kết nối DB: " . $e->getMessage());
}

/* ===============================
   BẢNG USERS (KHÔNG ADMIN)
================================ */
$db->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    status TEXT DEFAULT 'active',
    avatar TEXT DEFAULT 'default.png',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

/* ===============================
   BẢNG INVOICE TYPES
================================ */
$db->exec("
CREATE TABLE IF NOT EXISTS invoice_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    description TEXT
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
    invoice_type_id INTEGER,
    result TEXT,
    status TEXT DEFAULT 'success',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(invoice_type_id) REFERENCES invoice_types(id) ON DELETE SET NULL
)
");

/* ===============================
   BẢNG INVALID DATA (KHÔNG CHECKER)
================================ */
$db->exec("
CREATE TABLE IF NOT EXISTS invalid_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ocr_id INTEGER NOT NULL,
    issue TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(ocr_id) REFERENCES ocr_history(id) ON DELETE CASCADE
)
");

// echo "✅ Database đã được thiết lập (không có admin)";
