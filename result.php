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

/* ===== SAVE DB ===== */
require __DIR__ . '../init_db.php';
if ($ocr_text !== '' && isset($_SESSION['ocr_id'])) {
    $stmt = $db->prepare("UPDATE ocr_history SET result=?, status='success' WHERE id=?");
    $stmt->execute([$ocr_text, $_SESSION['ocr_id']]);
}

unset($_SESSION['ocr_id'], $_SESSION['last_image']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả OCR | Scan2Text</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#eef2ff,#fdfdfd);
}
.header{
    background:#0b5ed7;
    color:#fff;
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.header a{color:#fff;margin-left:20px;font-weight:500}

.container{
    max-width:1000px;
    margin:40px auto;
    background:#fff;
    padding:35px;
    border-radius:20px;
    box-shadow:0 25px 60px rgba(0,0,0,.2);
    animation:fadeUp .6s ease;
}
@keyframes fadeUp{
    from{opacity:0;transform:translateY(30px)}
    to{opacity:1;transform:none}
}

h2{text-align:center;color:#0b5ed7}

.img-preview{
    text-align:center;
}
.img-preview img{
    max-width:260px;
    border-radius:14px;
    cursor:pointer;
    transition:.3s;
}
.img-preview img:hover{transform:scale(1.07)}

textarea{
    width:100%;
    min-height:260px;
    padding:18px;
    border-radius:14px;
    border:1px solid #ccc;
    background:#f6f8fc;
    font-size:14px;
}

.actions{
    margin-top:25px;
    display:flex;
    gap:15px;
    justify-content:center;
    flex-wrap:wrap;
}
.actions button{
    padding:12px 26px;
    border:none;
    border-radius:12px;
    background:#0b5ed7;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:.25s;
}
.actions button:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(11,94,215,.4);
}

/* ===== MODAL ===== */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.65);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:999;
}
.modal-content{
    background:#fff;
    padding:30px;
    border-radius:18px;
    max-width:90%;
    max-height:90%;
    overflow:auto;
    animation:zoom .4s ease;
}
@keyframes zoom{
    from{transform:scale(.8);opacity:0}
    to{transform:scale(1);opacity:1}
}
.modal img{
    max-width:100%;
    border-radius:14px;
}
.close{
    text-align:right;
    font-size:20px;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="header">
    <div><b>Scan2Text</b></div>
    <nav>
        <a href="upload.php">OCR</a>
        <a href="history.php">Lịch sử</a>
        <a href="index.php">Trang chủ</a>
        <a href="logout.php">Đăng xuất</a>
    </nav>
</div>

<div class="container">
    <h2>📄 KẾT QUẢ OCR</h2>

    <div class="img-preview">
        <p><b>Ảnh gốc (bấm để xem chi tiết)</b></p>
        <img src="<?= htmlspecialchars($image) ?>" onclick="openModal()">
    </div>

    <h4>📑 Văn bản nhận dạng:</h4>
    <textarea readonly><?= htmlspecialchars($ocr_text) ?></textarea>

    <div class="actions">
        <button onclick="translateText()">🌐 Dịch văn bản</button>
        <a href="upload.php"><button>📤 Ảnh khác</button></a>
        <a href="history.php"><button>📜 Lịch sử</button></a>
    </div>

    <div id="translateBox" style="display:none;margin-top:20px">
        <h4>📝 Bản dịch:</h4>
        <textarea id="translatedText" readonly></textarea>
    </div>
</div>

<!-- MODAL IMAGE -->
<div class="modal" id="modal">
    <div class="modal-content">
        <div class="close" onclick="closeModal()">✖</div>
        <img src="<?= htmlspecialchars($image) ?>">
    </div>
</div>

<script>
function openModal(){
    document.getElementById('modal').style.display='flex';
}
function closeModal(){
    document.getElementById('modal').style.display='none';
}

/* ===== TRANSLATE (DEMO) ===== */
async function translateText(){
    const text = <?= json_encode($ocr_text) ?>;
    if(!text.trim()) return alert("Không có nội dung để dịch");

    const res = await fetch("https://translate.googleapis.com/translate_a/single?client=gtx&sl=vi&tl=en&dt=t&q="+encodeURIComponent(text));
    const data = await res.json();

    let result = "";
    data[0].forEach(i => result += i[0]);

    document.getElementById("translateBox").style.display="block";
    document.getElementById("translatedText").value = result;
}
</script>

</body>
</html>
