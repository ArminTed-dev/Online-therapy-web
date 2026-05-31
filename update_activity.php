<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE tb_users SET last_activity = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    echo "ok";
} else {
    echo "no";
}
?>