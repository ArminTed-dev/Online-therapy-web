<?php
session_start();
require_once 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname_user'] ?? '');
    $number   = trim($_POST['number_user'] ?? '');
    $pass     = $_POST['pass_user'] ?? '';
   

    // فقط فیلدهای ضروری را بررسی کن (type_user دیگر از کاربر گرفته نمی‌شود)
    if (empty($fullname) || empty($number) || empty($pass)) {
        $error = "❌ لطفاً نام، شماره و رمز عبور را پر کنید.";
    } elseif (!preg_match('/^09[0-9]{9}$/', $number)) {
        $error = "❌ شماره موبایل باید با 09 شروع و 11 رقم باشد.";
    } elseif (strlen($pass) < 4) {
        $error = "❌ رمز عبور حداقل ۴ کاراکتر باشد.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT number_user FROM tb_users WHERE number_user = ?");
            $stmt->execute([$number]);
            if ($stmt->fetch()) {
                $error = "❌ این شماره قبلاً ثبت شده است.";
            } else {
                $hashed_pass = $pass;
                // مقدار type_user را 0 قرار بده (کاربر عادی)
                $type_user = 0;
                $sql = "INSERT INTO tb_users (fullname_user, number_user, pass_user, type_user) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$fullname, $number, $hashed_pass, $type_user])) {
                    $success = "✅ ثبت‌نام موفق. در حال انتقال به صفحه ورود...";
                    header("refresh:2; url=index.php");
                    exit;
                } else {
                    $error = "❌ خطا در درج اطلاعات.";
                }
            }
        } catch (PDOException $e) {
            $error = "❌ خطای دیتابیس: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نتیجه ثبت‌نام</title>
    <style>
        body{font-family:'Vazirmatn',sans-serif;text-align:center;padding:50px;background:#0f172a;color:white;}
        .error{color:#f87171;background:#2d2a2a;padding:15px;border-radius:20px;display:inline-block;}
        .success{color:#4ade80;background:#1e3a2f;padding:15px;border-radius:20px;display:inline-block;}
        a{color:#c084fc;}
    </style>
</head>
<body>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <br><br>
        <a href="register.php">◀ بازگشت به صفحه ثبت‌نام</a>
    <?php elseif ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php else: ?>
        <div>درخواست نامعتبر</div>
        <a href="register.php">رفتن به ثبت‌نام</a>
    <?php endif; ?>
</body>
</html>