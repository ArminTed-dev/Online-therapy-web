<?php
session_start();
require_once 'config.php';

// ذخیره تیکت در دیتابیس
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_ticket'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO support_tickets (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $_SESSION['ticket_sent'] = "پیام شما با موفقیت دریافت شد. به زودی پاسخ می‌دهیم.";
    } else {
        $_SESSION['ticket_error'] = "لطفاً تمام فیلدها را پر کنید.";
    }
    header("Location: support.php");
    exit;
}

// دریافت سوالات متداول از دیتابیس (مرتب‌سازی بر اساس order_index)
$faqs = $pdo->query("SELECT * FROM faqs ORDER BY order_index ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پشتیبانی | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 1300px; margin: 0 auto; }
        .page-header { text-align: center; margin-bottom: 40px; }
        .page-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #1e1b4b, #4f46e5);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .support-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .support-grid > div {
            flex: 1;
            min-width: 300px;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(16px);
            border-radius: 48px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.4);
        }
        .faq-item {
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .faq-question {
            background: rgba(255,255,255,0.5);
            padding: 15px 20px;
            border-radius: 40px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 20px;
            color: #1e293b;
        }
        .faq-item.open .faq-answer {
            max-height: 300px;
            padding: 15px 20px;
        }
        .faq-question i { transition: transform 0.3s; }
        .faq-item.open .faq-question i { transform: rotate(180deg); }
        .contact-info p { margin: 15px 0; display: flex; align-items: center; gap: 12px; }
        .contact-info i { width: 30px; color: #4f46e5; }
        .social-links { display: flex; gap: 15px; margin-top: 20px; }
        .social-links a {
            background: rgba(255,255,255,0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e1b4b;
            transition: 0.2s;
        }
        .social-links a:hover { background: #4f46e5; color: white; }
        .form-group { margin-bottom: 20px; }
        input, textarea, select {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 40px;
            font-family: inherit;
            font-size: 1rem;
        }
        input:focus, textarea:focus { outline: none; border-color: #4f46e5; background: white; }
        button {
            background: linear-gradient(90deg, #4f46e5, #a855f7);
            border: none;
            padding: 14px 28px;
            border-radius: 40px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        .message { background: #10b981; color: white; padding: 12px; border-radius: 30px; margin-bottom: 20px; text-align: center; }
        .error { background: #ef4444; color: white; padding: 12px; border-radius: 30px; margin-bottom: 20px; text-align: center; }
        h2 { font-size: 1.6rem; margin-bottom: 20px; color: #1e1b4b; display: flex; align-items: center; gap: 10px; }
        @media (max-width: 768px) { .support-grid > div { min-width: 100%; } }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-headset"></i> پشتیبانی دکتریار</h1>
        <p>ما همیشه کنار شما هستیم | پاسخگویی ۲۴ ساعته</p>
    </div>

    <?php if (isset($_SESSION['ticket_sent'])): ?>
        <div class="message"><?= htmlspecialchars($_SESSION['ticket_sent']); unset($_SESSION['ticket_sent']); ?></div>
    <?php elseif (isset($_SESSION['ticket_error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['ticket_error']); unset($_SESSION['ticket_error']); ?></div>
    <?php endif; ?>

    <div class="support-grid">
        <!-- بخش سوالات متداول (از دیتابیس) -->
        <div>
            <h2><i class="fas fa-question-circle"></i> سوالات متداول</h2>
            <div class="faq-list">
                <?php if (empty($faqs)): ?>
                    <p>هنوز سوالی ثبت نشده است.</p>
                <?php else: ?>
                    <?php foreach ($faqs as $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question">
                                <span><?= htmlspecialchars($faq['question']) ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- اطلاعات تماس (بدون تغییر) -->
        <div>
            <h2><i class="fas fa-address-card"></i> اطلاعات تماس</h2>
            <div class="contact-info">
                <p><i class="fas fa-phone-alt"></i> تلفن پشتیبانی: ۰۲۱-۱۲۳۴۵۶۷۸</p>
                <p><i class="fas fa-mobile-alt"></i> همراه: ۰۹۱۲۳۴۵۶۷۸۹</p>
                <p><i class="fas fa-envelope"></i> ایمیل: support@drsite.com</p>
                <p><i class="fas fa-map-marker-alt"></i> آدرس: تهران، خیابان ولیعصر، پلاک ۱۲۳</p>
                <p><i class="fas fa-clock"></i> ساعات پاسخگویی: ۸ صبح تا ۱۰ شب (همه روزه)</p>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-telegram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <div style="margin-top: 30px;">
                <a href="chat-advanced.html" style="display: inline-block; background:#4f46e5; padding:10px 20px; border-radius:40px; color:white; text-decoration:none; text-align:center;">
                    <i class="fas fa-comments"></i> شروع چت زنده با پشتیبانی
                </a>
            </div>
        </div>

        <!-- فرم ارسال تیکت (بدون تغییر) -->
        <div>
            <h2><i class="fas fa-paper-plane"></i> ارسال تیکت پشتیبانی</h2>
            <form method="post">
                <div class="form-group">
                    <input type="text" name="name" placeholder="نام و نام خانوادگی" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="آدرس ایمیل" required>
                </div>
                <div class="form-group">
                    <input type="text" name="subject" placeholder="موضوع" required>
                </div>
                <div class="form-group">
                    <textarea name="message" rows="4" placeholder="پیام خود را بنویسید..." required></textarea>
                </div>
                <button type="submit" name="send_ticket">ارسال پیام</button>
            </form>
            <p style="margin-top: 16px; font-size:0.7rem; color:#475569;">
                <i class="fas fa-lock"></i> اطلاعات شما نزد ما محفوظ است.
            </p>
        </div>
    </div>
</div>

<script>
    // باز و بسته شدن سوالات متداول
    document.querySelectorAll('.faq-item').forEach(item => {
        const questionDiv = item.querySelector('.faq-question');
        questionDiv.addEventListener('click', () => {
            item.classList.toggle('open');
        });
    });
</script>
</body>
</html>