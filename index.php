<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan2Text - Hệ thống OCR </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET & GLOBAL STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .header {
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, #0b5ed7 0%, #3a7bd5 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 1.5rem;
        }

        .header nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header nav span {
            color: #555;
            font-weight: 500;
        }

        .header nav a {
            text-decoration: none;
            color: #0b5ed7;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .header nav a:hover {
            background-color: #f0f7ff;
            transform: translateY(-2px);
        }

        /* ===== MAIN CONTAINER ===== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            padding: 4rem 0;
        }

        @media (max-width: 992px) {
            .hero {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }

        .hero-text h1 {
            font-size: 2.8rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #1a237e;
        }

        .hero-text p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
            max-width: 95%;
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s ease;
        }

        .hero-image img:hover {
            transform: perspective(1000px) rotateY(0deg);
        }

        /* ===== BUTTONS ===== */
        .flex-center {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .main-btn {
            background: linear-gradient(90deg, #0b5ed7 0%, #3a7bd5 100%);
            color: white;
            border: none;
            padding: 0.9rem 1.8rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(11, 94, 215, 0.3);
        }

        .main-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(11, 94, 215, 0.4);
        }

        .main-btn i {
            font-size: 1.1rem;
        }

        /* ===== CONTENT SECTION ===== */
        .section {
            max-width: 1000px;
            margin: 60px auto;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #0b5ed7 0%, #3a7bd5 100%);
        }

        .section h2 {
            font-size: 1.8rem;
            color: #1a237e;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f0f4f8;
            position: relative;
        }

        .section h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #0b5ed7 0%, #3a7bd5 100%);
        }

        .section p {
            margin-bottom: 1.2rem;
            color: #444;
            line-height: 1.7;
        }

        .section ul, .section ol {
            margin-bottom: 1.5rem;
            padding-left: 1.8rem;
        }

        .section li {
            margin-bottom: 0.8rem;
            color: #444;
        }

        .section b {
            color: #1a237e;
        }

        .feature-list, .usage-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .feature-card {
            background: #f8faff;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid #0b5ed7;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(11, 94, 215, 0.1);
        }

        .feature-card i {
            font-size: 1.5rem;
            color: #0b5ed7;
            margin-bottom: 1rem;
        }

        .faq-item {
            background: #f9fbfd;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid #eef2f7;
        }

        .faq-item:hover {
            border-color: #d0e1ff;
        }

        .faq-question {
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-question i {
            color: #0b5ed7;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            padding: 2rem;
            background: #1a237e;
            color: white;
            margin-top: 4rem;
            font-size: 0.95rem;
        }

        .footer a {
            color: #a5c8ff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .hero-text h1 {
                font-size: 2.2rem;
            }

            .hero-text p {
                max-width: 100%;
            }

            .section {
                padding: 25px;
                margin: 40px 15px;
            }

            .feature-list, .usage-list {
                grid-template-columns: 1fr;
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section, .hero, .header {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">
        <i class="fas fa-scanner"></i>
        Scan2Text
    </div>
    <nav>
        <?php if($logged_in): ?>
            <span><i class="fas fa-user-circle"></i> Xin chào, <?php echo $_SESSION['username']; ?></span>
            <a href="\CN\logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
        <?php endif; ?>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="container">
    <div class="hero">
        <div class="hero-text">
            <h1>Chuyển đổi hình ảnh thành văn bản với độ chính xác cao</h1>
            <p>Scan2Text sử dụng công nghệ OCR tiên tiến để nhận dạng ký tự từ hình ảnh. Hỗ trợ tiếng Việt và tiếng Anh, lưu lịch sử quét, quản lý kết quả thông minh và xuất dữ liệu dễ dàng.</p>
            <div class="flex-center">
                <a href="<?php echo $logged_in ? 'upload.php' : 'login.php'; ?>">
                    <button class="main-btn">
                        <i class="fas fa-cloud-upload-alt"></i> Tải ảnh OCR
                    </button>
                </a>
                <a href="<?php echo $logged_in ? 'history.php' : 'login.php'; ?>">
                    <button class="main-btn">
                        <i class="fas fa-history"></i> Xem lịch sử OCR
                    </button>
                </a>
            </div>
        </div>
        <div class="hero-image">
            <img src="R.png" alt="OCR Illustration">
        </div>
    </div>
</div>

<!-- CONTENT SECTION -->
<div class="section">
    <h2>Bạn có mệt mỏi với việc gõ toàn bộ văn bản từ hình ảnh?</h2>
    <p>Trong thời đại công nghệ này, thật lãng phí thời gian khi phải chuyển đổi thủ công các tệp hình ảnh thành văn bản. Với Scan2Text, việc chuyển đổi chỉ mất vài giây!</p>
    <p>Công cụ chuyển đổi ảnh sang văn bản của chúng tôi sử dụng công nghệ nhận dạng ký tự quang học (OCR) tiên tiến để trích xuất văn bản từ hình ảnh với độ chính xác cao.</p>

    <h2>Hướng dẫn sử dụng</h2>
    <div class="usage-list">
        <div class="feature-card">
            <i class="fas fa-upload"></i>
            <h3>1. Tải lên hình ảnh</h3>
            <p>Tải lên hình ảnh của bạn hoặc kéo và thả trực tiếp vào trình duyệt.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-link"></i>
            <h3>2. Hoặc nhập URL</h3>
            <p>Nhập URL của hình ảnh nếu bạn có liên kết trực tiếp đến hình ảnh.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-paper-plane"></i>
            <h3>3. Nhấn nút Gửi</h3>
            <p>Hệ thống sẽ tự động xử lý và trích xuất văn bản từ hình ảnh của bạn.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-copy"></i>
            <h3>4. Sao chép kết quả</h3>
            <p>Sao chép văn bản vào clipboard hoặc tải xuống dưới dạng tài liệu.</p>
        </div>
    </div>

    <h2>Các tính năng nổi bật</h2>
    <div class="feature-list">
        <div class="feature-card">
            <i class="fas fa-free-code-camp"></i>
            <h3>Hoàn toàn miễn phí</h3>
            <p>Không cần đăng ký, trích xuất văn bản hoàn toàn miễn phí với mọi người dùng.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-robot"></i>
            <h3>Công nghệ AI tiên tiến</h3>
            <p>Sử dụng Tesseract OCR mã nguồn mở được cải tiến với độ chính xác cao.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-language"></i>
            <h3>Đa ngôn ngữ</h3>
            <p>Hỗ trợ nhiều ngôn ngữ: Tiếng Việt, Anh, Tây Ban Nha, Ý, Hà Lan và hơn thế nữa.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-download"></i>
            <h3>Tải xuống văn bản</h3>
            <p>Sau khi OCR, bạn có thể lưu kết quả dưới dạng file TXT hoặc DOC.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-file-image"></i>
            <h3>Đa định dạng ảnh</h3>
            <p>Hỗ trợ JPG, JPEG, PNG, BMP, TIFF, PDF và nhiều định dạng khác.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-shield-alt"></i>
            <h3>Bảo mật dữ liệu</h3>
            <p>Hình ảnh của bạn được xử lý an toàn, không lưu trữ hoặc chia sẻ với bên thứ ba.</p>
        </div>
    </div>

    <h2>Bạn có thể dùng Scan2Text để:</h2>
    <ul>
        <li>Sao chép văn bản từ sách, báo chí, tạp chí hoặc tài liệu in.</li>
        <li>Số hóa ghi chú lớp học, tài liệu giấy thành văn bản kỹ thuật số.</li>
        <li>Nhập dữ liệu từ hình ảnh sang định dạng văn bản dễ chỉnh sửa.</li>
        <li>Trích xuất caption từ hình ảnh trên mạng xã hội.</li>
        <li>Chuyển đổi biên lai, hóa đơn giấy thành dữ liệu số.</li>
    </ul>

    <h2>Câu hỏi thường gặp (FAQ)</h2>
    <div class="faq-list">
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-question-circle"></i>
                Làm thế nào để sao chép chữ trong ảnh miễn phí?
            </div>
            <p>Chỉ cần tải lên ảnh của bạn, nhấn nút "Gửi", văn bản sẽ được trích xuất ngay lập tức. Sau đó bạn có thể sao chép hoặc tải file TXT về máy.</p>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-shield-alt"></i>
                Scan2Text có an toàn không?
            </div>
            <p>Hình ảnh của bạn được xử lý an toàn và không được lưu trữ lâu dài hoặc chia sẻ với bên thứ ba. Chúng tôi tôn trọng quyền riêng tư của người dùng.</p>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-file-image"></i>
                Scan2Text hỗ trợ định dạng ảnh nào?
            </div>
            <p>Chúng tôi hỗ trợ hầu hết các định dạng hình ảnh phổ biến: PNG, JPG, JPEG, TIFF, BMP và cả file PDF.</p>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-user-check"></i>
                Có cần đăng ký tài khoản không?
            </div>
            <p>Không bắt buộc! Bạn có thể sử dụng miễn phí mà không cần tài khoản. Tuy nhiên, đăng ký giúp bạn lưu lịch sử quét và truy cập từ nhiều thiết bị.</p>
        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © <?= date('Y') ?> Scan2Text - Hệ thống OCR chuyên nghiệp · Được phát triển với PHP & SQLite
    <br>
    <small>Mọi hình ảnh được xử lý đều được bảo mật và không chia sẻ với bên thứ ba.</small>
</div>

</body>
</html>