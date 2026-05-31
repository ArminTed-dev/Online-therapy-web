<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 1) {
    header("Location: login.php");
    exit;
}

$msg = '';
$error = '';

// دریافت مقادیر فعلی
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_title'");
$stmt->execute();
$current_title = $stmt->fetchColumn() ?: 'دکتریار | مشاوره آنلاین روانشناسی';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'h1_text'");
$stmt->execute();
$current_h1 = $stmt->fetchColumn() ?: 'بهترین جرعه آرامش را بنوش';

// ذخیره تغییرات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = trim($_POST['site_title'] ?? '');
    $new_h1 = trim($_POST['h1_text'] ?? '');

    if (empty($new_title) || empty($new_h1)) {
        $error = "هیچ فیلدی نمی‌تواند خالی باشد.";
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'site_title'");
            $stmt->execute([$new_title]);
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'h1_text'");
            $stmt->execute([$new_h1]);
            $pdo->commit();
            $msg = "تنظیمات با موفقیت ذخیره شد.";
            $current_title = $new_title;
            $current_h1 = $new_h1;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "خطا در ذخیره: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تنظیمات سایت | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Vazirmatn',sans-serif;background:linear-gradient(135deg,#0f172a,#1e1b4b);min-height:100vh;padding:20px;color:#f1f5f9}
        .container{max-width:800px;margin:0 auto;background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border-radius:48px;padding:30px}
        h2{margin-bottom:20px;display:flex;align-items:center;gap:10px}
        .form-group{margin-bottom:25px}
        label{display:block;margin-bottom:8px;font-weight:bold}
        input,textarea{width:100%;padding:12px;border-radius:30px;border:none;background:rgba(255,255,255,0.2);color:white;font-family:inherit;font-size:1rem}
        input:focus,textarea:focus{outline:1px solid #4f46e5}
        button{background:#4f46e5;border:none;padding:12px 28px;border-radius:40px;color:white;font-weight:bold;cursor:pointer;font-size:1rem}
        .msg{background:#10b981;padding:12px;border-radius:30px;margin-bottom:20px}
        .error{background:#ef4444;padding:12px;border-radius:30px;margin-bottom:20px}
        a{color:#c084fc;text-decoration:none;display:inline-block;margin-top:20px}
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-cog"></i> تنظیمات ظاهری سایت</h2>
    <?php if($msg): ?>
        <div class="msg"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div>
    <?php elseif($error): ?>
        <div class="error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label><i class="fas fa-tag"></i> تایتل صفحه (عنوان مرورگر)</label>
            <input type="text" name="site_title" value="<?= htmlspecialchars($current_title) ?>" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-heading"></i> متن اصلی H1 (عنوان بزرگ صفحه)</label>
            <input type="text" name="h1_text" value="<?= htmlspecialchars($current_h1) ?>" required>
        </div>
        <button type="submit"><i class="fas fa-save"></i> ذخیره تغییرات</button>
    </form>
    <a href="adminpanel.php"><i class="fas fa-arrow-left"></i> بازگشت به پنل مدیریت</a>
</div>
</body>
</html>