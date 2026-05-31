<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 1) {
    header("Location: login.php");
    exit;
}

$msg = '';
$error = '';

// افزودن
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_disorder'])) {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    $description = trim($_POST['description']);
    $symptoms = trim($_POST['symptoms']);
    if (empty($name) || empty($description) || empty($symptoms)) {
        $error = "نام، توضیحات و علائم الزامی است.";
    } else {
        $maxOrder = $pdo->query("SELECT IFNULL(MAX(order_index), 0) FROM disorders")->fetchColumn();
        $newOrder = $maxOrder + 1;
        $stmt = $pdo->prepare("INSERT INTO disorders (name, icon, description, symptoms, order_index) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $icon, $description, $symptoms, $newOrder]);
        $msg = "اختلال جدید اضافه شد.";
    }
    header("Location: admin_disorders.php?msg=" . urlencode($msg) . "&error=" . urlencode($error));
    exit;
}

// ویرایش
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_disorder'])) {
    $id = (int)$_POST['disorder_id'];
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    $description = trim($_POST['description']);
    $symptoms = trim($_POST['symptoms']);
    if ($id && !empty($name) && !empty($description) && !empty($symptoms)) {
        $stmt = $pdo->prepare("UPDATE disorders SET name = ?, icon = ?, description = ?, symptoms = ? WHERE id = ?");
        $stmt->execute([$name, $icon, $description, $symptoms, $id]);
        $msg = "اختلال ویرایش شد.";
    } else {
        $error = "خطا در ویرایش.";
    }
    header("Location: admin_disorders.php?msg=" . urlencode($msg) . "&error=" . urlencode($error));
    exit;
}

// حذف
if (isset($_GET['delete_disorder'])) {
    $id = (int)$_GET['delete_disorder'];
    $pdo->prepare("DELETE FROM disorders WHERE id = ?")->execute([$id]);
    header("Location: admin_disorders.php?msg=اختلال حذف شد.");
    exit;
}

// مرتب‌سازی
if (isset($_GET['move_disorder']) && isset($_GET['dir'])) {
    $id = (int)$_GET['move_disorder'];
    $dir = $_GET['dir'];
    $current = $pdo->prepare("SELECT order_index FROM disorders WHERE id = ?");
    $current->execute([$id]);
    $current = $current->fetchColumn();
    if ($dir == 'up') {
        $new = $current - 1;
        $pdo->prepare("UPDATE disorders SET order_index = order_index + 1 WHERE order_index = ?")->execute([$new]);
    } else {
        $new = $current + 1;
        $pdo->prepare("UPDATE disorders SET order_index = order_index - 1 WHERE order_index = ?")->execute([$new]);
    }
    $pdo->prepare("UPDATE disorders SET order_index = ? WHERE id = ?")->execute([$new, $id]);
    header("Location: admin_disorders.php?msg=ترتیب به‌روز شد.");
    exit;
}

$disorders = $pdo->query("SELECT * FROM disorders ORDER BY order_index ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت اختلالات</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Vazirmatn',sans-serif;background:linear-gradient(135deg,#0f172a,#1e1b4b);padding:30px;color:#f1f5f9}
        .container{max-width:1200px;margin:0 auto;background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border-radius:48px;padding:30px}
        .card{background:rgba(0,0,0,0.2);border-radius:32px;padding:20px;margin-bottom:30px}
        .form-group{margin-bottom:20px}
        input,textarea{width:100%;padding:12px;border-radius:30px;border:none;background:rgba(255,255,255,0.9);font-family:inherit}
        button{background:#4f46e5;border:none;padding:10px 20px;border-radius:30px;color:white;cursor:pointer}
        table{width:100%;border-collapse:collapse;background:rgba(0,0,0,0.3);border-radius:32px;overflow:hidden}
        th,td{padding:12px 8px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.1);word-wrap:break-word}
        th{background:#4f46e5}
        .btn-icon{background:none;border:none;color:white;cursor:pointer;margin:0 4px}
        .btn-edit{color:#f59e0b}
        .btn-delete{color:#ef4444}
        .btn-move{color:#10b981}
        .message{background:#10b981;padding:10px;border-radius:30px;margin-bottom:20px}
        .error{background:#ef4444;padding:10px;border-radius:30px;margin-bottom:20px}
        .back-link{display:inline-block;margin-top:20px;color:#c084fc;text-decoration:none}
        td{max-width:300px;overflow-wrap:break-word}
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-database"></i> مدیریت اختلالات</h2>
    <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="message"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error']) && $_GET['error']): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="card">
        <h3>➕ افزودن اختلال جدید</h3>
        <form method="post">
            <div class="form-group">
                <input type="text" name="name" placeholder="نام اختلال (مثال: اسکیزوفرنی)" required>
            </div>
            <div class="form-group">
                <input type="text" name="icon" placeholder="کلاس آیکون FontAwesome (مثال: fa-brain)">
            </div>
            <div class="form-group">
                <textarea name="description" rows="3" placeholder="توضیحات کامل" required></textarea>
            </div>
            <div class="form-group">
                <textarea name="symptoms" rows="3" placeholder="علائم اصلی" required></textarea>
            </div>
            <button type="submit" name="add_disorder">ذخیره اختلال</button>
        </form>
    </div>

    <h3>📋 لیست اختلالات</h3>
    <table>
        <thead>
            <th>ترتیب</th><th>نام</th><th>آیکون</th><th>توضیحات</th><th>علائم</th><th>عملیات</th>
        </thead>
        <tbody>
            <?php foreach ($disorders as $index => $d): ?>
            <tr>
                <td>
                    <?= $d['order_index'] ?>
                    <?php if ($index > 0): ?>
                        <a href="?move_disorder=<?= $d['id'] ?>&dir=up" class="btn-icon btn-move"><i class="fas fa-arrow-up"></i></a>
                    <?php endif; ?>
                    <?php if ($index < count($disorders)-1): ?>
                        <a href="?move_disorder=<?= $d['id'] ?>&dir=down" class="btn-icon btn-move"><i class="fas fa-arrow-down"></i></a>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td><i class="fas <?= htmlspecialchars($d['icon']) ?>"></i> <?= htmlspecialchars($d['icon']) ?></td>
                <td><?= nl2br(htmlspecialchars($d['description'])) ?></td>
                <td><?= nl2br(htmlspecialchars($d['symptoms'])) ?></td>
                <td>
                    <button class="btn-icon btn-edit" onclick="openEditModal(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['name'])) ?>', '<?= htmlspecialchars(addslashes($d['icon'])) ?>', '<?= htmlspecialchars(addslashes($d['description'])) ?>', '<?= htmlspecialchars(addslashes($d['symptoms'])) ?>')"><i class="fas fa-edit"></i></button>
                    <a href="?delete_disorder=<?= $d['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('حذف شود؟')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($disorders)): ?>
                <tr><td colspan="6">هیچ اختلالی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="adminpanel.php" class="back-link">← بازگشت به پنل مدیریت</a>
</div>

<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); backdrop-filter:blur(5px); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:#1e293b; padding:30px; border-radius:48px; width:500px; color:white;">
        <span style="float:left; font-size:1.8rem; cursor:pointer;" onclick="closeEditModal()">&times;</span>
        <h3>ویرایش اختلال</h3>
        <form method="post">
            <input type="hidden" name="disorder_id" id="edit_id">
            <div class="form-group"><label>نام</label><input type="text" name="name" id="edit_name" required></div>
            <div class="form-group"><label>آیکون</label><input type="text" name="icon" id="edit_icon"></div>
            <div class="form-group"><label>توضیحات</label><textarea name="description" id="edit_description" rows="3" required></textarea></div>
            <div class="form-group"><label>علائم</label><textarea name="symptoms" id="edit_symptoms" rows="3" required></textarea></div>
            <button type="submit" name="edit_disorder">ذخیره تغییرات</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, icon, description, symptoms) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_icon').value = icon;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_symptoms').value = symptoms;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
</body>
</html>