<?php
session_start();
require_once 'config.php';

// نمایش خطاها (برای دیباگ)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$therapist= trim($_POST['therapist'] ?? '');
$date     = $_POST['date'] ?? '';
$time     = $_POST['time'] ?? '';

if (empty($fullname) || empty($phone) || empty($therapist) || empty($date) || empty($time)) {
    $error = "❌ همه فیلدها باید پر شوند.";
    header("Location: index.php?error=" . urlencode($error));
    exit;
}

if (!preg_match('/^09[0-9]{9}$/', $phone)) {
    $error = "❌ شماره موبایل نامعتبر است.";
    header("Location: index.php?error=" . urlencode($error));
    exit;
}

// بررسی کنید که نام ستون‌ها دقیقاً با جدول مطابقت داشته باشد
// توجه: در test_insert.php از 'therapist_name' استفاده کردید. اگر ستون شما 'therapist' است، آن را اصلاح کنید.
$sql = "INSERT INTO appointments (fullname, phone, therapist_name, date, time) VALUES (?, ?, ?, ?, ?)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fullname, $phone, $therapist, $date, $time]);
    $success = "✅ نوبت شما با موفقیت ثبت شد. منتظر تماس ما باشید.";
    header("Location: index.php?msg=" . urlencode($success));
    exit;
} catch (PDOException $e) {
    // نمایش خطا در مرورگر (برای عیب‌یابی)
    die("خطای دیتابیس: " . $e->getMessage());
}
?>