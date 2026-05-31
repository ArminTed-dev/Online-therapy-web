<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) exit();
$user = (int)$_SESSION['user_id'];
$with = (int)$_GET['with'];
$last = (int)($_GET['last_id'] ?? 0);
header('Cache-Control: no-cache');
header('Content-Type: application/json');
$stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE ((from_user=? AND to_user=?) OR (from_user=? AND to_user=?)) AND id>? ORDER BY id");
$stmt->execute([$user, $with, $with, $user, $last]);
$msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($msgs as &$m) {
    if ($m['to_user'] == $user && !$m['is_read']) {
        $up = $pdo->prepare("UPDATE chat_messages SET is_read=1 WHERE id=?");
        $up->execute([$m['id']]);
    }
}
echo json_encode($msgs);