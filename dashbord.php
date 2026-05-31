<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 1) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT visit_date, view_count FROM page_views ORDER BY visit_date ASC");
$views = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];
foreach ($views as $row) {
    $labels[] = date('Y-m-d', strtotime($row['visit_date'])); // فرمت استاندارد برای ApexCharts
    $data[] = (int)$row['view_count'];
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>داشبورد بازدیدها | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Vazirmatn',sans-serif;background:linear-gradient(135deg,#0f172a,#1e1b4b);min-height:100vh;padding:30px;color:#f1f5f9}
        .container{max-width:1200px;margin:0 auto;background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border-radius:48px;padding:30px}
        h2{display:flex;align-items:center;gap:12px;margin-bottom:20px}
        .chart-box{background:rgba(0,0,0,0.2);border-radius:32px;padding:20px;margin:20px 0}
        table{width:100%;border-collapse:collapse;margin-top:20px;background:rgba(0,0,0,0.3);border-radius:24px;overflow:hidden}
        th,td{padding:12px;text-align:center;border-bottom:1px solid rgba(255,255,255,0.1)}
        th{background:#4f46e5}
        .back-link{display:inline-block;margin-top:20px;color:#c084fc;text-decoration:none}
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-chart-line"></i> آمار بازدیدها</h2>

    <div class="chart-box">
        <div id="chart"></div>
    </div>

    <h3>📋 جزئیات بازدیدها</h3>
    <table>
        <thead>
            <th>تاریخ</th><th>تعداد بازدید</th>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr><td colspan="2">هیچ داده‌ای یافت نشد</td></tr>
            <?php else: ?>
                <?php foreach ($views as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('Y/m/d', strtotime($row['visit_date']))) ?></td>
                        <td><?= (int)$row['view_count'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="adminpanel.php" class="back-link">← بازگشت به پنل مدیریت</a>
</div>

<script>
    // داده‌ها از PHP
    const labels = <?= json_encode($labels) ?>;
    const seriesData = <?= json_encode($data) ?>;

    if (labels.length > 0) {
        var options = {
            series: [{
                name: 'تعداد بازدید',
                data: seriesData
            }],
            chart: {
                type: 'line',
                height: 350,
                toolbar: { show: true },
                background: 'transparent',
                foreColor: '#fff'
            },
            stroke: { curve: 'smooth', width: 3, colors: ['#a855f7'] },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 0.5, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] }
            },
            markers: { size: 5, colors: ['#4f46e5'], strokeColors: '#fff', strokeWidth: 2 },
            xaxis: {
                categories: labels,
                title: { text: 'تاریخ', style: { color: '#cbd5e1' } },
                labels: { style: { colors: '#fff', fontSize: '12px' } }
            },
            yaxis: {
                title: { text: 'تعداد بازدید', style: { color: '#cbd5e1' } },
                labels: { style: { colors: '#fff' } },
                min: 0
            },
            grid: { borderColor: 'rgba(255,255,255,0.1)', row: { colors: ['transparent'], opacity: 0.5 } },
            tooltip: { theme: 'dark' }
        };
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    } else {
        document.getElementById('chart').innerHTML = '<div class="no-data">داده‌ای برای نمایش وجود ندارد.</div>';
    }
</script>
</body>
</html>