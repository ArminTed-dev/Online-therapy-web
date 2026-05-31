
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['type_user']) || $_SESSION['type_user'] != 1) {
    header("Location: login.html");
    exit;
}

// عملیات تأیید
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $pdo->prepare("UPDATE appointments SET status = 'approved' WHERE id = ?")->execute([$id]);
    header("Location: reservations_admin.php?msg=تأیید شد");
    exit;
}
// عملیات لغو
if (isset($_GET['cancel'])) {
    $id = (int)$_GET['cancel'];
    $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?")->execute([$id]);
    header("Location: reservations_admin.php?msg=لغو شد");
    exit;
}
// عملیات حذف
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$id]);
    header("Location: reservations_admin.php?msg=حذف شد");
    exit;
}
// افزودن رزرو جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $therapist = $_POST['therapist'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $stmt = $pdo->prepare("INSERT INTO appointments (fullname, phone, therapist_name, date, time) VALUES (?,?,?,?,?)");
    $stmt->execute([$fullname, $phone, $therapist, $date, $time]);
    header("Location: reservations_admin.php?msg=رزرو جدید اضافه شد");
    exit;
}

$msg = $_GET['msg'] ?? '';
$appointments = $pdo->query("SELECT * FROM appointments ORDER BY date DESC, time DESC")->fetchAll();

?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <title>مدیریت رزرو نوبت</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; background: #0f172a; color: white; padding: 20px; direction: rtl; }
        .container { max-width: 1200px; margin: auto; background: rgba(255,255,255,0.1); border-radius: 32px; padding: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: center; border-bottom: 1px solid #334155; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 20px; }
        .pending { background: #f59e0b; }
        .approved { background: #10b981; }
        .cancelled { background: #ef4444; }
        .btn { display: inline-block; padding: 4px 12px; margin: 2px; border-radius: 20px; text-decoration: none; color: white; font-size: 0.8rem; }
        .btn-approve { background: #10b981; }
        .btn-cancel { background: #ef4444; }
        .btn-delete { background: #6b7280; }
        .btn-add { background: #4f46e5; padding: 8px 20px; display: inline-block; margin-top: 20px; }
        form input, form select { padding: 8px; margin: 5px; border-radius: 20px; border: none; width: 180px; }
        .message { background: #10b981; padding: 10px; border-radius: 30px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-calendar-check"></i> مدیریت رزرو نوبت</h2>
    <?php if ($msg): ?>
        <div class="message"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <h3>لیست درخواست‌ها</h3>
    <table>
        <thead>
            <tr><th>نام</th><th>شماره</th><th>مشاور</th><th>تاریخ</th><th>ساعت</th><th>وضعیت</th><th>عملیات</th></tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['therapist_name']) ?></td>

<td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['time']) ?></td>
                    <td>
                        <span class="status <?= $row['status'] ?>">
                            <?= $row['status'] == 'pending' ? 'در انتظار' : ($row['status'] == 'approved' ? 'تأیید شده' : 'لغو شد') ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <a href="?approve=<?= $row['id'] ?>" class="btn btn-approve">تأیید</a>
                            <a href="?cancel=<?= $row['id'] ?>" class="btn btn-cancel">لغو</a>
                        <?php elseif ($row['status'] == 'approved'): ?>
                            <a href="?cancel=<?= $row['id'] ?>" class="btn btn-cancel">لغو</a>
                        <?php endif; ?>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('حذف شود؟')">حذف</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>افزودن رزرو جدید</h3>
    <form method="post">
        <input type="text" name="fullname" placeholder="نام کامل" required>
        <input type="tel" name="phone" placeholder="شماره تماس" required>
        <input type="text" name="therapist" placeholder="نام مشاور" required>
        <input type="date" name="date" required>
        <input type="time" name="time" required>
        <button type="submit" name="add" class="btn btn-add"><i class="fas fa-plus"></i> افزودن رزرو</button>
    </form>
</div>
</body>
</html>