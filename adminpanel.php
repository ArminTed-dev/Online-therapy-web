
<?php
session_start();
require_once 'config.php';

// بررسی دسترسی ادمین (type_user = 1)
if (!isset($_SESSION['type_user']) || $_SESSION['type_user'] != 1) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// ======================== مدیریت رزرو ========================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    if ($action == 'approve') {
        $pdo->prepare("UPDATE appointments SET status = 'approved' WHERE id = ?")->execute([$id]);
    } elseif ($action == 'cancel') {
        $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?")->execute([$id]);
    }
    header("Location: adminpanel.php?page=appointments");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_appointment'])) {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $therapist = trim($_POST['therapist']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    if ($fullname && $phone && $therapist && $date && $time) {
        $pdo->prepare("INSERT INTO appointments (fullname, phone, therapist_name, date, time) VALUES (?,?,?,?,?)")
            ->execute([$fullname, $phone, $therapist, $date, $time]);
    }
    header("Location: admin_panel.php?page=appointments");
    exit;
}

if (isset($_GET['delete_appointment'])) {
    $id = (int)$_GET['delete_appointment'];
    $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$id]);
    header("Location: admin_panel.php?page=appointments");
    exit;
    
    // دریافت آمار بازدیدهای ۷ روز اخیر
$stmt = $pdo->prepare("
    SELECT visit_date, view_count 
    FROM page_views 
    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ORDER BY visit_date ASC
");
$stmt->execute();
$views = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];
foreach ($views as $row) {
    $labels[] = date('d/m', strtotime($row['visit_date']));
    $data[] = $row['view_count'];
}
}

$active_page = $_GET['page'] ?? 'dashboard';

// دریافت کاربران (بدون استفاده از id)
$users = $pdo->query("SELECT number_user, fullname_user, type_user FROM tb_users ORDER BY number_user")->fetchAll();
$appointments = $pdo->query("SELECT * FROM appointments ORDER BY date DESC, time DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .admin-panel {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            border-radius: 48px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
            display: flex;
            min-height: 90vh;
        }
        .sidebar {
            width: 260px;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(8px);
            border-left: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        .logo {
            padding: 28px 20px;
            text-align: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .menu {
            padding: 20px 12px;
        }
        .menu-item {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            margin-bottom: 8px;
            border-radius: 32px;
            color: #e2e8f0;
            cursor: pointer;
            transition: 0.2s;


}
        .menu-item i { width: 24px; }
        .menu-item:hover, .menu-item.active {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            color: white;
        }
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .top-bar {
            padding: 20px 30px;
            background: rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        .logout-btn {
            background: #ef4444;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            color: white;
            font-weight: bold;
        }
        .page {
            padding: 30px;
            overflow-y: auto;
            flex: 1;
        }
        .card {
            background: rgba(255,255,255,0.08);
            border-radius: 32px;
            padding: 24px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #f1f5f9;
        }
        th, td {
            padding: 12px 8px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
        }
        .pending { background: #f59e0b; color: black; }
        .approved { background: #10b981; color: white; }
        .cancelled { background: #ef4444; color: white; }
        .btn-small {
            padding: 4px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.75rem;
            margin: 0 4px;
            display: inline-block;
        }
        .btn-approve { background: #10b981; color: white; }
        .btn-cancel { background: #ef4444; color: white; }
        .btn-delete { background: #dc2626; color: white; }
        form input, form select {
            padding: 10px 15px;
            border-radius: 30px;
            border: none;
            margin: 5px;
            width: 200px;
        }
        .btn-submit {
            background: #4f46e5;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
        }
        .chat-box {
            background: rgba(0,0,0,0.3);
            height: 350px;
            border-radius: 30px;
            padding: 15px;
            overflow-y: auto;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .sidebar span:not(.logo) { display: none; }
            .menu-item { justify-content: center; }
        }
        .text-decoration{
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="admin-panel">
    <div class="sidebar">
        <div class="logo">دکتریار</div>
        
        <div class="menu">
            
            <a href="dashbord.php" class="menu-item" data-page="dashboard"><i class="fas fa-tachometer-alt"></i> <span>آمار بازدید سایت</span></a>
            <a href="admin_user.php" class="menu-item"><i class="fas fa-users"></i> <span>مدیریت کاربران</span></a>
            <a href="admin_support.php" class="menu-item" data-page="chat"><i class="fas fa-comment-dots"></i> <span>چت ادمین</span></a>
            <a href="reservations_admin.php" class="menu-item"><i class="fas fa-calendar-check"></i> <span>رزرو نوبت</span></a>
            <a href="admin_settings.php" class="menu-item" data-page="settings"><i class="fas fa-cog"></i> <span>تنظیمات</span></a>
            <a href="admin_disorders.php "class="menu-item" ><i class="fas fa-cog"><span>اختلالات</span></i></a>
        </div>
            
    </div>
    <div class="content-area">
        <div class="top-bar">
            <h2 id="pageTitle">داشبورد مدیریت</h2>


<a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> خروج</a>
        </div>
        <div class="page">
            <!-- داشبورد -->
            <div id="dashboard-page" class="page-content" style="display: <?= $active_page=='dashboard' ? 'block' : 'none' ?>">
                
                <div class="card">
                    <h3>خوش آمدید مدیر گرامی</h3>
                    <p>تعداد کاربران: <?= count($users) ?> | تعداد رزروها: <?= count($appointments) ?></p>
                </div>
                
            </div>
            
            <!-- کاربران (بدون ستون id) -->
            <div id="users-page" class="page-content" style="display: <?= $active_page=='users' ? 'block' : 'none' ?>">
                <div class="card">
                    <h3>لیست کاربران</h3>
                    <table>
                        <thead>
                            <th>شماره تماس</th><th>نام کامل</th><th>نوع کاربر</th>
                        </thead>
                        <tbody>
                            <?php foreach($users as $u): 
                                $role = ($u['type_user']==1)?'ادمین':(($u['type_user']==2)?'مشاور':'مراجعه‌کننده');
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($u['number_user']) ?></td>
                                <td><?= htmlspecialchars($u['fullname_user']) ?></td>
                                <td><?= $role ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- چت ساده -->
            <div id="chat-page" class="page-content" style="display: <?= $active_page=='chat' ? 'block' : 'none' ?>">
                <div class="card">
                    <h3>چت ادمین</h3>
                    <div class="chat-box" id="chatMessages">پیام‌ها...</div>
                    <input type="text" id="chatInput" style="width:70%; padding:12px; border-radius:40px;"><button id="sendChatBtn" class="btn-submit">ارسال</button>
                </div>
            </div>
            <!-- رزرو نوبت -->
            <div id="appointments-page" class="page-content" style="display: <?= $active_page=='appointments' ? 'block' : 'none' ?>">
                <div class="card">
                    <h3>لیست رزروها</h3>
                    <table>
                        <thead><th>نام</th><th>شماره</th><th>مشاور</th><th>تاریخ</th><th>ساعت</th><th>وضعیت</th><th>عملیات</th></thead>
                        <tbody>
                            <?php foreach($appointments as $row): 
                                $statusClass = $row['status']=='pending'?'pending':($row['status']=='approved'?'approved':'cancelled');
                                $statusText = $row['status']=='pending'?'در انتظار':($row['status']=='approved'?'تأیید شده':'لغو شد');
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['therapist_name']) ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= $row['time'] ?></td>
                                <td><span class="status <?= $statusClass ?>"><?= $statusText ?></span></td>
                                <td>
                                    <?php if($row['status']=='pending'): ?>
                                        <a href="?action=approve&id=<?= $row['id'] ?>" class="btn-small btn-approve">تأیید</a>
                                        <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn-small btn-cancel">لغو</a>


<?php elseif($row['status']=='approved'): ?>
                                        <a href="?action=cancel&id=<?= $row['id'] ?>" class="btn-small btn-cancel">لغو</a>
                                    <?php endif; ?>
                                    <a href="?delete_appointment=<?= $row['id'] ?>" class="btn-small btn-delete" onclick="return confirm('حذف شود؟')">حذف</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card">
                    <h3>افزودن رزرو</h3>
                    <form method="post">
                        <input type="text" name="fullname" placeholder="نام کامل" required>
                        <input type="tel" name="phone" placeholder="شماره" required>
                        <input type="text" name="therapist" placeholder="مشاور" required>
                        <input type="date" name="date" required>
                        <input type="time" name="time" required>
                        <button type="submit" name="add_appointment" class="btn-submit">ثبت</button>
                    </form>
                </div>
            </div>
            <!-- تنظیمات -->
            <div id="settings-page" class="page-content" style="display: <?= $active_page=='settings' ? 'block' : 'none' ?>">
                <div class="card"><h3>تنظیمات</h3><p>به زودی...</p></div>
            </div>
        </div>
    </div>
</div>
<script>
    const menuItems = document.querySelectorAll('.menu-item');
    const pages = ['dashboard', 'users', 'chat', 'appointments', 'settings'];
    function showPage(page) {
        pages.forEach(p => {
            const el = document.getElementById(p + '-page');
            if (el) el.style.display = 'none';
        });
        document.getElementById(page + '-page').style.display = 'block';
        document.getElementById('pageTitle').innerText = 
            page == 'dashboard' ? 'داشبورد مدیریت' :
            page == 'users' ? 'مدیریت کاربران' :
            page == 'chat' ? 'چت ادمین' :
            page == 'appointments' ? 'مدیریت رزرو نوبت' : 'تنظیمات';
        menuItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-page') == page) item.classList.add('active');
        });
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.history.pushState({}, '', url);
    }
    menuItems.forEach(item => {
        item.addEventListener('click', () => showPage(item.getAttribute('data-page')));
    });
    const urlPage = new URLSearchParams(window.location.search).get('page');
    if (urlPage && pages.includes(urlPage)) showPage(urlPage);
    else showPage('dashboard');

    // چت ساده
    const sendBtn = document.getElementById('sendChatBtn');
    if (sendBtn) {
        sendBtn.addEventListener('click', () => {
            const input = document.getElementById('chatInput');
            const chatBox = document.getElementById('chatMessages');
            if (input.value.trim()) {
                const msgDiv = document.createElement('div');
                msgDiv.innerHTML = <strong>ادمین:</strong> ${input.value};
                chatBox.appendChild(msgDiv);
                input.value = '';
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
    }
</script>
</body>
</html>