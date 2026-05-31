<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 2) {
    exit;
}
$therapist_id = $_SESSION['user_id'];

// شمارش مراجعین آنلاین (کسانی که در ۲ دقیقه اخیر فعالیت داشته‌اند و با این مشاور چت داشته‌اند)
$sql = "SELECT COUNT(DISTINCT u.id) as online FROM tb_users u 
        JOIN chat_messages m ON (m.from_user = u.id OR m.to_user = u.id)
        WHERE (m.from_user = ? OR m.to_user = ?) 
        AND u.last_activity > DATE_SUB(NOW(), INTERVAL 2 MINUTE)
        AND u.type_user = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([$therapist_id, $therapist_id]);
$online = $stmt->fetchColumn();

// شمارش پیام‌های خوانده نشده خطاب به این مشاور
$stmt = $pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE to_user = ? AND is_read = 0");
$stmt->execute([$therapist_id]);
$unread = $stmt->fetchColumn();

echo json_encode(['online' => (int)$online, 'unread' => (int)$unread]);
?>