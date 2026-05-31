<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) exit("Unauthorized");
$from = $_SESSION['user_id'];
$to = (int)$_POST['to_user'];
$msg = trim($_POST['message'] ?? '');
$image = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif'])) {
        $dir = DIR . '/uploads/';
        if (!is_dir($dir)) mkdir($dir);
        $name = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $name);
        $image = 'uploads/' . $name;
    }
}
$stmt = $pdo->prepare("INSERT INTO chat_messages (from_user, to_user, message, image_path) VALUES (?,?,?,?)");
$stmt->execute([$from, $to, $msg, $image]);
echo "ok";