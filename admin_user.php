
<?php
session_start();
require_once 'config.php';

// بررسی دسترسی ادمین (type_user = 1)
if (!isset($_SESSION['type_user']) || $_SESSION['type_user'] != 1) {
    header("Location: login.html");
    exit;
}

// ======================== حذف کاربر ========================
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    // جلوگیری از حذف ادمین اصلی (با شماره admin یا id=1)
    $stmt = $pdo->prepare("SELECT number_user FROM tb_users WHERE id = ?");
    $stmt->execute([$delete_id]);
    $user = $stmt->fetch();
    if ($user && $user['number_user'] == 'admin') {
        $msg = "امکان حذف کاربر ادمین اصلی وجود ندارد.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM tb_users WHERE id = ?");
        $stmt->execute([$delete_id]);
        $msg = "کاربر با موفقیت حذف شد.";
    }
    header("Location: admin_user.php?msg=" . urlencode($msg));
    exit;
}

// ======================== افزودن کاربر جدید (دستی) ========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $type     = (int)$_POST['type_user'];
    $password = $_POST['password'];

    $error = '';
    if (empty($fullname) || empty($phone) || empty($password)) {
        $error = "نام، شماره و رمز عبور الزامی است.";
    } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
        $error = "شماره موبایل نامعتبر است (باید با 09 شروع شود و 11 رقم باشد).";
    } elseif (strlen($password) < 4) {
        $error = "رمز عبور حداقل ۴ کاراکتر باشد.";
    } else {
        // بررسی تکراری نبودن شماره
        $stmt = $pdo->prepare("SELECT id FROM tb_users WHERE number_user = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $error = "این شماره قبلاً ثبت شده است.";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO tb_users (fullname_user, number_user, pass_user, type_user) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$fullname, $phone, $hashed_pass, $type])) {
                $msg = "کاربر جدید با موفقیت اضافه شد.";
            } else {
                $error = "خطا در افزودن کاربر.";
            }
        }
    }
    if ($error) {
        header("Location: admin_user.php?error=" . urlencode($error));
    } else {
        header("Location: admin_user.php?msg=" . urlencode($msg));
    }
    exit;
}

// ======================== ویرایش کاربر ========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id       = (int)$_POST['user_id'];
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $type     = (int)$_POST['type_user'];

    $error = '';
    if (empty($fullname) || empty($phone)) {
        $error = "نام و شماره الزامی است.";
    } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
        $error = "شماره موبایل نامعتبر است.";
    } else {
        // بررسی تکراری نبودن شماره به جز خود کاربر
        $stmt = $pdo->prepare("SELECT id FROM tb_users WHERE number_user = ? AND id != ?");
        $stmt->execute([$phone, $id]);
        if ($stmt->fetch()) {
            $error = "این شماره قبلاً توسط کاربر دیگری ثبت شده است.";
        } else {
            $stmt = $pdo->prepare("UPDATE tb_users SET fullname_user = ?, number_user = ?, type_user = ? WHERE id = ?");
            if ($stmt->execute([$fullname, $phone, $type, $id])) {
                $msg = "اطلاعات کاربر بروزرسانی شد.";
            } else {
                $error = "خطا در بروزرسانی.";
            }
        }
    }
    if ($error) {
        header("Location: admin_users.php?error=" . urlencode($error));
    } else {
        header("Location: admin_users.php?msg=" . urlencode($msg));
    }
    exit;
}


// ======================== دریافت لیست کاربران با جستجو ========================
$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM tb_users";
if ($search !== '') {
    $sql .= " WHERE fullname_user LIKE :search OR number_user LIKE :search";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt = $pdo->query($sql);
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت کاربران | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            padding: 20px;
            color: white;
        }
        .container {
            max-width: 1300px;
            margin: 0 auto;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 24px;
        }
        h1 { text-align: center; margin-bottom: 20px; }
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }
        .search-box input {
            padding: 12px 20px;
            border-radius: 40px;
            border: none;
            width: 300px;
            font-family: inherit;
        }
        .search-box button {
            background: #4f46e5;
            border: none;
            padding: 0 24px;
            border-radius: 40px;
            color: white;
            cursor: pointer;
        }
        .btn {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-danger { background: #ef4444; }
        .btn-warning { background: #f59e0b; }
        .btn-sm { padding: 4px 12px; font-size: 0.8rem; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 8px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        th { background: rgba(79,70,229,0.4); }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
        }
        .badge-admin { background: #ef4444; }
        .badge-therapist { background: #f59e0b; }
        .badge-client { background: #10b981; }
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #1e293b;
            padding: 24px;
            border-radius: 32px;
            width: 400px;
            color: white;
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 30px;


border: none;
        }
        .close-modal {
            float: left;
            cursor: pointer;
            font-size: 1.5rem;
        }
        .message {
            background: #10b981;
            padding: 10px;
            border-radius: 30px;
            margin-bottom: 20px;
        }
        .error {
            background: #ef4444;
            padding: 10px;
            border-radius: 30px;
            margin-bottom: 20px;
        }
        .add-form {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 32px;
            margin-top: 30px;
        }
        .add-form input, .add-form select {
            padding: 10px;
            margin: 5px;
            border-radius: 30px;
            border: none;
            width: 200px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><i class="fas fa-users"></i> مدیریت کاربران</h1>

    <?php if ($msg): ?>
        <div class="message"><?= htmlspecialchars($msg) ?></div>
    <?php elseif ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- جستجو -->
    <form method="get" class="search-box">
        <input type="text" name="search" placeholder="جستجو بر اساس نام یا شماره..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i> جستجو</button>
        <?php if ($search): ?>
            <a href="admin_users.php" class="btn btn-sm">نمایش همه</a>
        <?php endif; ?>
    </form>

    <!-- جدول کاربران -->
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr><th>ID</th><th>نام کامل</th><th>شماره تماس</th><th>نقش</th><th>عملیات</th></tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): 
                        if ($user['type_user'] == 1) { $role = "ادمین"; $class="badge-admin"; }
                        elseif ($user['type_user'] == 2) { $role = "مشاور"; $class="badge-therapist"; }
                        else { $role = "مراجعه‌کننده"; $class="badge-client"; }
                    ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['fullname_user']) ?></td>
                        <td><?= htmlspecialchars($user['number_user']) ?></td>
                        <td><span class="badge <?= $class ?>"><?= $role ?></span></td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['fullname_user']) ?>" data-phone="<?= htmlspecialchars($user['number_user']) ?>" data-type="<?= $user['type_user'] ?>">ویرایش</button>
                            <a href="?delete_id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('آیا از حذف این کاربر اطمینان دارید؟')">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">هیچ کاربری یافت نشد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- فرم افزودن کاربر (دستی) -->
    <div class="add-form">
        <h3><i class="fas fa-user-plus"></i> افزودن کاربر جدید</h3>
        <form method="post">
            <input type="text" name="fullname" placeholder="نام کامل" required>
            <input type="tel" name="phone" placeholder="شماره تماس (مثال 09123456789)" required>
            <select name="type_user">
                <option value="0">مراجعه‌کننده</option>
                <option value="2">مشاور</option>
                <option value="1">ادمین</option>
            </select>
            <input type="password" name="password" placeholder="رمز عبور (حداقل ۴ رقم)" required>
            <button type="submit" name="add_user" class="btn">افزودن کاربر</button>
        </form>
    </div>
</div>


<!-- مودال ویرایش کاربر -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3>ویرایش کاربر</h3>
        <form method="post" id="editForm">
            <input type="hidden" name="user_id" id="edit_id">
            <input type="text" name="fullname" id="edit_fullname" placeholder="نام کامل" required>
            <input type="tel" name="phone" id="edit_phone" placeholder="شماره تماس" required>
            <select name="type_user" id="edit_type">
                <option value="0">مراجعه‌کننده</option>
                <option value="2">مشاور</option>
                <option value="1">ادمین</option>
            </select>
            <button type="submit" name="edit_user" class="btn">ذخیره تغییرات</button>
        </form>
    </div>
</div>

<script>
    // مودال ویرایش
    const modal = document.getElementById('editModal');
    const closeModal = document.querySelector('.close-modal');
    const editBtns = document.querySelectorAll('.edit-btn');
    const editId = document.getElementById('edit_id');
    const editName = document.getElementById('edit_fullname');
    const editPhone = document.getElementById('edit_phone');
    const editType = document.getElementById('edit_type');

    editBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            editId.value = btn.getAttribute('data-id');
            editName.value = btn.getAttribute('data-name');
            editPhone.value = btn.getAttribute('data-phone');
            editType.value = btn.getAttribute('data-type');
            modal.style.display = 'flex';
        });
    });

    function closeModalFunc() {
        modal.style.display = 'none';
    }
    closeModal.onclick = closeModalFunc;
    window.onclick = function(e) {
        if (e.target == modal) closeModalFunc();
    }
</script>
</body>
</html>