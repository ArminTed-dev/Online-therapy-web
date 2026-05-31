<?php
session_start();
require_once 'config.php';

// نمایش خطاها (برای عیب‌یابی - در محیط تولید غیرفعال کنید)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

$number = trim($_POST['number_user'] ?? '');
$password = $_POST['pass_user'] ?? '';

if (empty($number) || empty($password)) {
    $_SESSION['login_error'] = "لطفاً شماره و رمز عبور را وارد کنید.";
    header("Location: login.php");
    exit;
}

// جستجوی کاربر بر اساس number_user (شماره تلفن یا نام کاربری)
$stmt = $pdo->prepare("SELECT * FROM tb_users WHERE number_user = ?");
$stmt->execute([$number]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// مقایسه مستقیم رمز (بدون هش)
if ($user && $password == $user['pass_user']) {
    // ذخیره اطلاعات کاربر در جلسه
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname_user'];
    $_SESSION['type_user'] = $user['type_user'];

    // هدایت بر اساس type_user
    if ($user['type_user'] == 1) {
        header("Location: adminpanel.php");
    }
    elseif ($user['type_user']==2) {
    header("location:therapist_chat.php");
}
elseif ($user['type_user']==3) {
    header("location:chat-admin2.php");

}
  
    else {
        header("Location: index.php");
    }
    exit;
} else {
    $_SESSION['login_error'] = "شماره تماس یا رمز عبور اشتباه است.";
    header("Location: login.php");
    exit;
}
?>