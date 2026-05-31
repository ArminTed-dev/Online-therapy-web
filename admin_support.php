<?php
session_start();
require_once 'config.php';

// بررسی دسترسی ادمین
if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 1) {
    header("Location: login.php");
    exit;
}

$msg = '';
$error = '';

// ======================== مدیریت FAQ ========================
// افزودن سوال جدید
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_faq'])) {
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    if (!empty($question) && !empty($answer)) {
        // دریافت بیشترین مقدار order_index به صورت جداگانه
        $maxOrder = $pdo->query("SELECT IFNULL(MAX(order_index), 0) FROM faqs")->fetchColumn();
        $newOrder = $maxOrder + 1;
        $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, order_index) VALUES (?, ?, ?)");
        $stmt->execute([$question, $answer, $newOrder]);
        $msg = "سوال جدید با موفقیت اضافه شد.";
    } else {
        $error = "لطفاً هر دو فیلد را پر کنید.";
    }
    header("Location: admin_support.php?tab=faq&msg=" . urlencode($msg) . "&error=" . urlencode($error));
    exit;
}

// ویرایش سوال
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_faq'])) {
    $id = (int)$_POST['faq_id'];
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    if ($id && !empty($question) && !empty($answer)) {
        $stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ? WHERE id = ?");
        $stmt->execute([$question, $answer, $id]);
        $msg = "سوال با موفقیت ویرایش شد.";
    } else {
        $error = "خطا در ویرایش.";
    }
    header("Location: admin_support.php?tab=faq&msg=" . urlencode($msg) . "&error=" . urlencode($error));
    exit;
}

// حذف سوال
if (isset($_GET['delete_faq'])) {
    $id = (int)$_GET['delete_faq'];
    $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_support.php?tab=faq&msg=سوال حذف شد.");
    exit;
}

// مرتب‌سازی (بالا/پایین)
if (isset($_GET['move_faq']) && isset($_GET['dir'])) {
    $id = (int)$_GET['move_faq'];
    $dir = $_GET['dir'];
    // دریافت order_index فعلی
    $stmt = $pdo->prepare("SELECT order_index FROM faqs WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    if ($dir == 'up') {
        $new = $current - 1;
        $pdo->prepare("UPDATE faqs SET order_index = order_index + 1 WHERE order_index = ?")->execute([$new]);
    } else {
        $new = $current + 1;
        $pdo->prepare("UPDATE faqs SET order_index = order_index - 1 WHERE order_index = ?")->execute([$new]);
    }
    $pdo->prepare("UPDATE faqs SET order_index = ? WHERE id = ?")->execute([$new, $id]);
    header("Location: admin_support.php?tab=faq&msg=ترتیب به‌روز شد.");
    exit;
}

// ======================== مدیریت تیکت‌ها ========================
// تغییر وضعیت تیکت
if (isset($_GET['ticket_status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['ticket_status'];
    $pdo->prepare("UPDATE support_tickets SET status = ? WHERE id = ?")->execute([$status, $id]);
    header("Location: admin_support.php?tab=tickets&msg=وضعیت تیکت تغییر کرد.");
    exit;
}
// حذف تیکت
if (isset($_GET['delete_ticket'])) {
    $id = (int)$_GET['delete_ticket'];
    $pdo->prepare("DELETE FROM support_tickets WHERE id = ?")->execute([$id]);
    header("Location: admin_support.php?tab=tickets&msg=تیکت حذف شد.");
    exit;
}

// دریافت داده‌ها
$faqs = $pdo->query("SELECT * FROM faqs ORDER BY order_index ASC, id ASC")->fetchAll();
$tickets = $pdo->query("SELECT * FROM support_tickets ORDER BY created_at DESC")->fetchAll();

$active_tab = $_GET['tab'] ?? 'faq';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت پشتیبانی | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e1b4b);
            padding: 30px;
            color: #f1f5f9;
        }
        .container {
            max-width: 1300px;
            margin: 0 auto;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border-radius: 48px;
            padding: 30px;
        }
        .tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 10px;
        }
        .tab-btn {
            background: transparent;
            border: none;
            color: #cbd5e1;
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
        }
        .tab-btn.active {
            background: #4f46e5;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0,0,0,0.3);
            border-radius: 32px;
            overflow: hidden;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 8px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            background: #4f46e5;
        }
        .btn-icon {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 4px;
        }
        .btn-edit { color: #f59e0b; }
        .btn-delete { color: #ef4444; }
        .btn-move { color: #10b981; }
        .card {
            background: rgba(0,0,0,0.2);
            border-radius: 32px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 30px;
            border: none;
            background: rgba(255,255,255,0.9);
            font-family: inherit;
        }
        button {
            background: #4f46e5;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            cursor: pointer;
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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
        }
        .status-new { background: #f59e0b; color: black; }
        .status-read { background: #10b981; color: white; }
        .status-replied { background: #3b82f6; color: white; }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #c084fc;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-headset"></i> مدیریت پشتیبانی</h2>
    
    <div class="tabs">
        <button class="tab-btn <?= $active_tab == 'faq' ? 'active' : '' ?>" data-tab="faq">سوالات متداول (FAQ)</button>
        <button class="tab-btn <?= $active_tab == 'tickets' ? 'active' : '' ?>" data-tab="tickets">تیکت‌های پشتیبانی</button>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg']): ?>
        <div class="message"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error']) && $_GET['error']): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- بخش مدیریت FAQ -->
    <div id="faq-tab" class="tab-content <?= $active_tab == 'faq' ? 'active' : '' ?>">
        <div class="card">
            <h3>افزودن سوال جدید</h3>
            <form method="post">
                <div class="form-group">
                    <input type="text" name="question" placeholder="سوال" required>
                </div>
                <div class="form-group">
                    <textarea name="answer" rows="3" placeholder="پاسخ" required></textarea>
                </div>
                <button type="submit" name="add_faq"><i class="fas fa-plus"></i> افزودن</button>
            </form>
        </div>

        <h3>لیست سوالات موجود</h3>
        <table>
            <thead>
                <tr><th>ترتیب</th><th>سوال</th><th>پاسخ</th><th>عملیات</th></tr>
            </thead>
            <tbody>
                <?php foreach ($faqs as $index => $faq): ?>
                <tr>
                    <td>
                        <?= $faq['order_index'] ?>
                        <?php if ($index > 0): ?>
                            <a href="?move_faq=<?= $faq['id'] ?>&dir=up&tab=faq" class="btn-icon btn-move"><i class="fas fa-arrow-up"></i></a>
                        <?php endif; ?>
                        <?php if ($index < count($faqs)-1): ?>
                            <a href="?move_faq=<?= $faq['id'] ?>&dir=down&tab=faq" class="btn-icon btn-move"><i class="fas fa-arrow-down"></i></a>
                        <?php endif; ?>
                     </td>
                    <td><?= htmlspecialchars($faq['question']) ?></td>
                    <td style="max-width: 400px;"><?= nl2br(htmlspecialchars($faq['answer'])) ?></td>
                    <td>
                        <button class="btn-icon btn-edit" onclick="openEditFaq(<?= $faq['id'] ?>, '<?= htmlspecialchars(addslashes($faq['question'])) ?>', '<?= htmlspecialchars(addslashes($faq['answer'])) ?>')"><i class="fas fa-edit"></i></button>
                        <a href="?delete_faq=<?= $faq['id'] ?>&tab=faq" class="btn-icon btn-delete" onclick="return confirm('حذف شود؟')"><i class="fas fa-trash"></i></a>
                     </td>
                 </tr>
                <?php endforeach; ?>
                <?php if (empty($faqs)): ?>
                <tr><td colspan="4">هیچ سوالی ثبت نشده است.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- بخش مدیریت تیکت‌ها -->
    <div id="tickets-tab" class="tab-content <?= $active_tab == 'tickets' ? 'active' : '' ?>">
        <table>
            <thead>
                <tr><th>ID</th><th>نام</th><th>ایمیل</th><th>موضوع</th><th>پیام</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <tr><?= $ticket['id'] ?> </td>
                    <td><?= htmlspecialchars($ticket['name']) ?> </td>
                    <td><?= htmlspecialchars($ticket['email']) ?> </td>
                    <td><?= htmlspecialchars($ticket['subject']) ?> </td>
                    <td style="max-width: 300px;"><?= nl2br(htmlspecialchars($ticket['message'])) ?> </td>
                    <td>
                        <span class="status-badge status-<?= $ticket['status'] ?>">
                            <?= $ticket['status'] == 'new' ? 'جدید' : ($ticket['status'] == 'read' ? 'خوانده شده' : 'پاسخ داده شده') ?>
                        </span>
                     </td>
                    <td><?= date('Y-m-d H:i', strtotime($ticket['created_at'])) ?> </td>
                    <td>
                        <a href="?ticket_status=read&id=<?= $ticket['id'] ?>&tab=tickets" class="btn-icon" title="علامت خوانده شده"><i class="fas fa-check-circle"></i></a>
                        <a href="?ticket_status=replied&id=<?= $ticket['id'] ?>&tab=tickets" class="btn-icon" title="پاسخ داده شد"><i class="fas fa-reply-all"></i></a>
                        <a href="?delete_ticket=<?= $ticket['id'] ?>&tab=tickets" class="btn-icon btn-delete" onclick="return confirm('حذف شود؟')"><i class="fas fa-trash"></i></a>
                     </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                <tr><td colspan="8">هیچ تیکتی دریافت نشده است.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="admin_panel.php" class="back-link"><i class="fas fa-arrow-left"></i> بازگشت به پنل مدیریت</a>
</div>

<!-- مودال ویرایش سوال -->
<div id="editFaqModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); backdrop-filter:blur(5px); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:#1e293b; padding:30px; border-radius:48px; width:500px; color:white;">
        <span style="float:left; font-size:1.8rem; cursor:pointer;" onclick="closeEditFaq()">&times;</span>
        <h3>ویرایش سوال</h3>
        <form method="post">
            <input type="hidden" name="faq_id" id="edit_faq_id">
            <div class="form-group">
                <label>سوال</label>
                <input type="text" name="question" id="edit_question" required>
            </div>
            <div class="form-group">
                <label>پاسخ</label>
                <textarea name="answer" id="edit_answer" rows="4" required></textarea>
            </div>
            <button type="submit" name="edit_faq">ذخیره تغییرات</button>
        </form>
    </div>
</div>

<script>
    // Tab switching
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            contents.forEach(content => content.classList.remove('active'));
            document.getElementById(tabId + '-tab').classList.add('active');
            tabs.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        });
    });

    function openEditFaq(id, question, answer) {
        document.getElementById('edit_faq_id').value = id;
        document.getElementById('edit_question').value = question;
        document.getElementById('edit_answer').value = answer;
        document.getElementById('editFaqModal').style.display = 'flex';
    }
    function closeEditFaq() {
        document.getElementById('editFaqModal').style.display = 'none';
    }
</script>
</body>
</html>