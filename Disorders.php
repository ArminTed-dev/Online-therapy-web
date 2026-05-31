<?php
require_once 'config.php';
$disorders = $pdo->query("SELECT * FROM disorders ORDER BY order_index ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختلالات روانشناسی | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Vazirmatn',sans-serif;background:linear-gradient(135deg,#e0eafc,#cfdef3);min-height:100vh;padding:20px}
        .container{max-width:1300px;margin:0 auto}
        .header{text-align:center;margin-bottom:40px}
        .header h1{background:linear-gradient(135deg,#1e1b4b,#4f46e5);-webkit-background-clip:text;background-clip:text;color:transparent}
        .search-box{max-width:600px;margin:20px auto}
        .search-box input{width:100%;padding:14px 20px;border-radius:60px;border:none;background:rgba(255,255,255,0.8);font-family:inherit;font-size:1rem;text-align:right}
        .card-grid{display:flex;flex-wrap:wrap;gap:30px;justify-content:center}
        .disorder-card{
            background:rgba(255,255,255,0.7);
            backdrop-filter:blur(8px);
            border-radius:32px;
            padding:24px;
            width:340px;
            transition:0.3s;
            border:1px solid rgba(255,255,255,0.8);
            box-shadow:0 4px 12px rgba(0,0,0,0.05);
            display:flex;
            flex-direction:column;
        }
        .disorder-card:hover{transform:translateY(-5px);background:white}
        .disorder-card h3{display:flex;align-items:center;gap:8px;margin-bottom:12px;color:#1e1b4b}
        .disorder-card .desc{
            color:#334155;
            margin-bottom:12px;
            line-height:1.6;
            overflow-wrap:break-word;
            word-wrap:break-word;
            word-break:break-word;
        }
        .symptoms{
            border-top:1px dashed rgba(0,0,0,0.1);
            padding-top:10px;
            font-size:0.85rem;
            color:#475569;
            overflow-wrap:break-word;
            word-wrap:break-word;
            word-break:break-word;
        }
        .no-result{text-align:center;padding:50px;background:rgba(255,255,255,0.5);border-radius:48px}
        footer{text-align:center;margin-top:50px;padding:20px;color:#475569}
        @media (max-width:700px){.disorder-card{width:100%}}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-brain"></i> اختلالات روانشناسی</h1>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="جستجو در اختلالات...">
        </div>
    </div>
    <div class="card-grid" id="disordersGrid">
        <?php foreach ($disorders as $d): ?>
            <div class="disorder-card"
                 data-name="<?= htmlspecialchars($d['name']) ?>"
                 data-description="<?= htmlspecialchars($d['description']) ?>"
                 data-symptoms="<?= htmlspecialchars($d['symptoms']) ?>">
                <h3><i class="fas <?= htmlspecialchars($d['icon'] ?? 'fa-brain') ?>"></i> <?= htmlspecialchars($d['name']) ?></h3>
                <div class="desc"><?= nl2br(htmlspecialchars($d['description'])) ?></div>
                <div class="symptoms"><strong>🧾 علائم اصلی:</strong><br><?= nl2br(htmlspecialchars($d['symptoms'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<footer>© 2025 دکتریار - همراه آگاهی و آرامش ذهن شما</footer>
<script>
    const searchInput = document.getElementById('searchInput');
    const cards = Array.from(document.querySelectorAll('.disorder-card'));

    function filterCards() {
        const term = searchInput.value.trim().toLowerCase();
        let anyVisible = false;
        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const desc = card.dataset.description.toLowerCase();
            const symp = card.dataset.symptoms.toLowerCase();
            if (name.includes(term) || desc.includes(term) || symp.includes(term)) {
                card.style.display = 'flex';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });
        let noDiv = document.getElementById('noResultMsg');
        if (!anyVisible) {
            if (!noDiv) {
                const grid = document.getElementById('disordersGrid');
                noDiv = document.createElement('div');
                noDiv.id = 'noResultMsg';
                noDiv.className = 'no-result';
                noDiv.innerHTML = '🔍 نتیجه‌ای یافت نشد. لطفاً عبارت دیگری جستجو کنید.';
                grid.appendChild(noDiv);
            }
        } else if (noDiv) {
            noDiv.remove();
        }
    }

    searchInput.addEventListener('input', filterCards);
</script>
</body>
</html>