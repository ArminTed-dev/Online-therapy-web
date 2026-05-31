<?php
require_once 'config.php';

// دریافت تایتل و H1 از دیتابیس
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_title'");
$stmt->execute();
$site_title = $stmt->fetchColumn() ?: 'دکتریار | مشاوره آنلاین روانشناسی';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'h1_text'");
$stmt->execute();
$h1_text = $stmt->fetchColumn() ?: 'بهترین جرعه آرامش را بنوش';

// ثبت بازدید روزانه
$today = date('Y-m-d');
$stmt = $pdo->prepare("INSERT INTO page_views (visit_date, view_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE view_count = view_count + 1");
$stmt->execute([$today]);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($site_title) ?></title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ========== RESET & BASE ========== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            color: #1a2a3a;
            scroll-behavior: smooth;
            line-height: 1.5;
            min-height: 100vh;
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 10px; }

        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .container { max-width: 1280px; margin: auto; padding: 0 24px; }

        /* ========== هدر شیشه‌ای ========== */
        .glass-header {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 18px 0; flex-wrap: wrap; }
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e1b4b, #4f46e5);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .nav-links { display: flex; gap: 32px; }
        .nav-links a { text-decoration: none; font-weight: 500; color: #1e293b; transition: 0.3s; }
        .nav-links a:hover { color: #4f46e5; }
        .btn-glow {
            text-decoration: none;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            padding: 12px 28px;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(79,70,229,0.3);
            border: none;
            display: inline-block;
        }
        .btn-glow:hover { transform: scale(1.05); box-shadow: 0 12px 28px rgba(79,70,229,0.5); }

        /* ========== اسلایدر ========== */
        .slider-section { margin: 40px 0; border-radius: 32px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); }
        .slider-container { position: relative; width: 100%; background: #000; }
        .slider { position: relative; width: 100%; height: 500px; overflow: hidden; }
        .slide { position: absolute; width: 100%; height: 100%; opacity: 0; transition: opacity 0.8s ease-in-out; background-size: cover; background-position: center; }
        .slide.active { opacity: 1; }
        .slide::before { content: ''; position: absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(135deg, rgba(0,0,0,0.3), rgba(0,0,0,0.1)); }
        .slide-content { position: absolute; bottom:0; left:0; right:0; padding:40px; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); color:white; text-align:center; transform: translateY(100%); transition: transform 0.5s ease; }
        .slide.active .slide-content { transform: translateY(0); }
        .slider-btn { position: absolute; top:50%; transform: translateY(-50%); background: rgba(255,255,255,0.3); backdrop-filter: blur(10px); width:50px; height:50px; border-radius:50%; cursor:pointer; font-size:1.5rem; color:white; display:flex; align-items:center; justify-content:center; z-index:10; }
        .slider-btn:hover { background: rgba(255,255,255,0.6); color:#4f46e5; }
        .btn-prev { left:20px; }
        .btn-next { right:20px; }
        .dots { position: absolute; bottom:20px; left:50%; transform: translateX(-50%); display:flex; gap:12px; z-index:10; }
        .dot { width:12px; height:12px; border-radius:50%; background: rgba(255,255,255,0.5); cursor:pointer; transition:0.3s; }
        .dot.active { background:white; width:30px; border-radius:10px; }
        .progress-bar { position: absolute; bottom:0; left:0; height:4px; background: linear-gradient(90deg,#4f46e5,#a855f7); width:0%; z-index:10; transition: width 0.1s linear; }

        /* ========== بخش اصلی ========== */
        .hero { padding: 60px 0 80px; text-align: center; }
        .hero h1 { font-size: 3.5rem; font-weight:800; background: linear-gradient(145deg,#0f172a,#3b2e8e); -webkit-background-clip:text; background-clip:text; color:transparent; margin-bottom:20px; animation:fadeUp 0.8s ease; }
        .hero p { font-size:1.25rem; color:#2d3e50; max-width:700px; margin:0 auto 30px; }
        .card-grid { display:flex; flex-wrap:wrap; gap:30px; justify-content:center; margin:60px 0; }
        .service-card { background: rgba(255,255,255,0.6); backdrop-filter:blur(8px); border-radius:48px; padding:32px 24px; width:280px; text-align:center; transition:0.4s; border:1px solid rgba(255,255,255,0.8); box-shadow:0 20px 35px -12px rgba(0,0,0,0.1); }
        .service-card:hover { transform:translateY(-12px); background:white; }
        .service-card i { font-size:3rem; background:linear-gradient(145deg,#4f46e5,#a855f7); -webkit-background-clip:text; background-clip:text; color:transparent; margin-bottom:20px; }
        .service-card h3 { font-size:1.6rem; margin-bottom:12px; }
        .experts { background: rgba(255,255,255,0.3); backdrop-filter:blur(4px); border-radius:64px; padding:50px 30px; margin:70px 0; }
        .expert-flex { display:flex; flex-wrap:wrap; justify-content:center; gap:40px; margin-top:40px; }
        .expert-item { text-align:center; width:200px; }
        .expert-img { width:120px; height:120px; background:linear-gradient(145deg,#818cf8,#c084fc); border-radius:60px; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:3rem; color:white; }
        .glass-form { background: rgba(255,255,255,0.25); backdrop-filter:blur(12px); border-radius:48px; padding:48px 36px; margin:50px 0; border:1px solid rgba(255,255,255,0.5); }
        .form-group { margin-bottom:24px; }
        input, textarea, select { width:100%; padding:16px 20px; background:rgba(255,255,255,0.85); border:1px solid #ddd; border-radius:32px; font-family:inherit; font-size:1rem; }
        input:focus, textarea:focus, select:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,0.2); }

        #myModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(8px); align-items:center; justify-content:center; z-index:1000; }
        .modal-content { background:white; max-width:400px; margin:20px; padding:32px; border-radius:56px; text-align:center; }
        @media (max-width:768px) {
            .hero h1 { font-size:2.2rem; }
            .slider { height:350px; }
            nav { flex-direction:column; gap:16px; }
            .nav-links { gap:20px; }
        }

        /* ========== منوی همبرگری ========== */
        .glass-header nav { margin:10px; display:flex; justify-content:space-between; align-items:center; }
        .nav-links { display:flex; align-items:center; gap:20px; }
        .hamburger { display:none; font-size:1.8rem; cursor:pointer; background:rgba(255,255,255,0.2); width:44px; height:44px; border-radius:30px; align-items:center; justify-content:center; }
        .mobile-sidebar { position:fixed; top:0; right:-280px; width:280px; height:100%; background:rgba(255,255,255,0.95); backdrop-filter:blur(20px); box-shadow:-5px 0 20px rgba(0,0,0,0.1); z-index:1000; transition:0.3s; padding:20px; overflow-y:auto; }
        .mobile-sidebar.open { right:0; }
        .sidebar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; padding-bottom:15px; border-bottom:1px solid #e2e8f0; }
        .logo-sidebar { font-size:1.4rem; font-weight:bold; color:#4f46e5; }
        .close-sidebar { font-size:2rem; cursor:pointer; color:#64748b; }
        .mobile-sidebar ul { list-style:none; padding:0; }
        .mobile-sidebar li { margin:20px 0; }
        .mobile-sidebar a { text-decoration:none; color:#1e293b; font-weight:500; display:flex; align-items:center; gap:12px; padding:8px 12px; border-radius:40px; transition:0.2s; }
        .mobile-sidebar a:hover { background:#4f46e5; color:white; }
        .btn-glow-mobile { background:linear-gradient(90deg,#4f46e5,#7c3aed); padding:8px 16px; border-radius:40px; color:white; display:inline-block; text-align:center; }
        .overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(2px); z-index:999; display:none; }
        .overlay.show { display:block; }
        @media (max-width:768px) { .desktop-menu { display:none !important; } .hamburger { display:flex; } }

        /* ========== افکت گراند (ذرات) ========== */
        #dragCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
            z-index: 0;
            pointer-events: auto; /* امکان کشیدن ذرات با ماوس */
        }
        /* محتوای اصلی بالای افکت قرار گیرد */
        body > :not(canvas) {
            position: relative;
            z-index: 2;
        }
        /* برای اطمینان از شفافیت المان‌های شیشه‌ای */
        .glass-header, .glass-form, .service-card, .experts {
            background-color: rgba(255,255,255,0.2);
            backdrop-filter: blur(12px);
        }
         .support-float-button {
                width: 50px;
                height: 50px;
                font-size: 20px;
                 bottom: 20px;
                right: 20px;
            }
               .support-float-button {
            position: fixed; /* ثابت در صفحه */
            bottom: 30px; /* فاصله از پایین */
            right: 30px; /* فاصله از راست */
            background-color:yellow; /* رنگ قرمز */
            color: white;
            border: none;
            border-radius: 50%; /* دایره‌ای کردن */
            width: 60px; /* عرض */
            height: 60px; /* ارتفاع */
            font-size: 24px; /* اندازه آیکون یا متن */
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* سایه */
            z-index: 1001; /* بالاتر از هدر */
            text-decoration: none; /* برای تگ a */
        }
        
    </style>
</head>
<body>
<!--    دکمه پشتیبانی-->
<a  href="support.php"> <img  class="support-float-button"  src="assests/img/p.picture.jpg"></a>
<!-- هدر و منو (همان کد شما) -->
<div class="glass-header">
    <div class="container">
        <nav>
            <div class="logo"><i class="fas fa-brain"></i> DrYar | دکتریار</div>
            <div class="nav-links desktop-menu">
                <a href="#">خانه</a>
                <a href="login.php">ثبت نام و ورود</a>
                <a href="#experts-section">متخصصین</a>
                <a href="#reservation">رزرو نوبت</a>
                <a href="#">تماس با ما</a>
                <a href="Disorders.php">🧠 اختلالات</a>
                <a href="client_chat.php" class="btn-glow">تراپی آنلاین</a>
            </div>
            <div class="hamburger" id="hamburgerBtn"><i class="fas fa-bars"></i></div>
        </nav>
    </div>
</div>

<div class="mobile-sidebar" id="mobileSidebar">
    <div class="sidebar-header"><span class="logo-sidebar">دکتریار</span><div class="close-sidebar" id="closeSidebarBtn">&times;</div></div>
    <ul>
        <li><a href="#"><i class="fas fa-home"></i> خانه</a></li>
        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> ثبت نام و ورود</a></li>
        <li><a href="#experts-section"><i class="fas fa-user-md"></i> متخصصین</a></li>
        <li><a href="#reservation"><i class="fas fa-calendar-alt"></i> رزرو نوبت</a></li>
        <li><a href="#"><i class="fas fa-phone-alt"></i> تماس با ما</a></li>
        <li><a href="Disorders.php"><i class="fas fa-brain"></i> اختلالات</a></li>
        <li><a href="chat-morajekonande.php" class="btn-glow-mobile">تراپی آنلاین</a></li>
    </ul>
</div>
<div class="overlay" id="overlay"></div>

<main>
    <div class="container">
        <section class="hero">
            <h1><?= htmlspecialchars($h1_text) ?></h1>
            <p>مشاوره آنلاین با برترین روانشناسان کشور — از هر کجا، در هر ساعت. حریم خصوصی کامل و جلسات امن ویدیویی.</p>
            <button class="btn-glow" id="heroBtn">همین حالا صحبت کن <i class="fas fa-arrow-left"></i></button>
        </section>

        <!-- اسلایدر -->
        <div class="slider-section">
            <div class="slider-container">
                <div class="slider" id="slider">
                    <div class="slide active" style="background-image: url('assests/img/pic.jpg');"><div class="slide-content"><h2>🌸 مشاوره تخصصی روانشناسی</h2><p>همراه با بهترین متخصصین کشور</p></div></div>
                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=1200&h=500&fit=crop');"><div class="slide-content"><h2>🧘‍♀️ آرامش ذهن با مدیتیشن</h2><p>تکنیک‌های مدرن کاهش استرس</p></div></div>
                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1521791055366-0d553872125f?w=1200&h=500&fit=crop');"><div class="slide-content"><h2>💬 جلسات آنلاین ویدیویی</h2><p>امن، خصوصی و با کیفیت 4K</p></div></div>
                    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1531482615713-2afd69097998?w=1200&h=500&fit=crop');"><div class="slide-content"><h2>❤️ حمایت ۲۴ ساعته</h2><p>هر زمان که نیاز داشته باشی، کنارتم</p></div></div>
                </div>
                <button class="slider-btn btn-prev" id="prevBtn">❮</button>
                <button class="slider-btn btn-next" id="nextBtn">❯</button>
                <div class="dots" id="dots"></div>
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>

        <section>
            <h2 style="font-size: 2rem; text-align: center;">خدمات فوق‌پیشرفته</h2>
            <div class="card-grid">
                <div class="service-card"><i class="fas fa-video"></i><h3>مشاوره ویدیویی</h3><p>کیفیت 4K با رمزگذاری کامل</p></div>
                <div class="service-card"><i class="fas fa-head-side-vr"></i><h3>واقعیت مجازی</h3><p>درمان فوبیا با VR</p></div>
                <div class="service-card"><i class="fas fa-chart-line"></i><h3>پیشرفت روزانه</h3><p>گزارش هوشمند mood tracker</p></div>
                <div class="service-card"><i class="fas fa-shield-alt"></i><h3>امنیت کامل</h3><p>گواهی SSL و محرمانگی کامل</p></div>
            </div>
        </section>

        <div class="experts" id="experts-section">
            <h2 style="text-align: center; font-size: 2rem;">بهترین متخصصین تراپیست</h2>
            <div class="expert-flex">
                <div class="expert-item"><div class="expert-img"><i class="fas fa-user-md"></i></div><h4>دکتر سارا کریمی</h4><p>روانشناس </p></div>
                <div class="expert-item"><div class="expert-img"><i class="fas fa-user-graduate"></i></div><h4>دکتر علی رضایی</h4><p>متخصص  </p></div>
                <div class="expert-item"><div class="expert-img"><i class="fas fa-child"></i></div><h4>دکتر مریم نادری</h4><p>روانشناس </p></div>
            </div>
        </div>

        <div class="glass-form" id="reservation">
            <h2 style="text-align: center;">نوبت دهی آنلاین</h2>
            <form id="appointmentForm" method="post" action="book_appointment.php">
                <div class="form-group"><input type="text" name="fullname" placeholder="نام و نام خانوادگی" required></div>
                <div class="form-group"><input type="tel" name="phone" placeholder="شماره تماس" required></div>
                <div class="form-group"><select name="therapist" required><option value="">انتخاب متخصص</option><option value="دکتر سارا کریمی">دکتر سارا کریمی</option><option value="دکتر علی رضایی">دکتر علی رضایی</option><option value="دکتر مریم نادری">دکتر مریم نادری</option></select></div>
                <div class="form-group"><input type="date" name="date" required></div>
                <div class="form-group"><input type="time" name="time" required></div>
                <button type="submit" class="btn-glow">ثبت نوبت</button>
            </form>
            <div id="formMessage"></div>
        </div>
    </div>
</main>



<div id="myModal">
    <div class="modal-content">
        <i class="fas fa-hand-peace" style="font-size: 3rem; color:#4f46e5;"></i>
        <h3 style="margin: 20px 0;">شروع جلسه رایگان تست</h3>
        <p>لطفاً شماره موبایل خود را وارد کنید تا مشاور با شما تماس بگیرد.</p>
        <input type="text" id="modalPhone" placeholder="09xxxxxxxxx" style="margin: 15px 0; padding: 12px; width: 100%; border-radius: 30px; border: 1px solid #ddd;">
        <button id="closeModalBtn" class="btn-glow" style="width: 100%;">دریافت کد تخفیف ۳۰٪</button>
        <button id="cancelModal" style="margin-top: 12px; background: none; border: none; color: gray; cursor: pointer;">بستن</button>
    </div>
</div>

<!-- ========== CANVAS برای افکت گراند ========== -->
<canvas id="dragCanvas"></canvas>

<script>
    // ========== اسلایدر ==========
    const slides = document.querySelectorAll('#slider .slide');
    const dotsContainer = document.getElementById('dots');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const progressBar = document.getElementById('progressBar');
    let currentIndex = 0, slideInterval, progressInterval;
    const intervalTime = 5000;

    function createDots() {
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if(index === currentIndex) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
    }
    function goToSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        document.querySelectorAll('.dot').forEach(dot => dot.classList.remove('active'));
        currentIndex = index;
        if(currentIndex >= slides.length) currentIndex = 0;
        if(currentIndex < 0) currentIndex = slides.length - 1;
        slides[currentIndex].classList.add('active');
        document.querySelectorAll('.dot')[currentIndex].classList.add('active');
        resetTimer();
    }
    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }
    function resetTimer() {
        if(slideInterval) clearInterval(slideInterval);
        if(progressInterval) clearInterval(progressInterval);
        progressBar.style.width = '0%';
        slideInterval = setInterval(() => { nextSlide(); }, intervalTime);
        let width = 0;
        const stepTime = intervalTime / 100;
        progressInterval = setInterval(() => {
            if(width >= 100) clearInterval(progressInterval);
            else { width++; progressBar.style.width = width + '%'; }
        }, stepTime);
    }
    prevBtn.addEventListener('click', () => { prevSlide(); resetTimer(); });
    nextBtn.addEventListener('click', () => { nextSlide(); resetTimer(); });
    const sliderContainer = document.querySelector('.slider-container');
    if(sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => { if(slideInterval) clearInterval(slideInterval); if(progressInterval) clearInterval(progressInterval); });
        sliderContainer.addEventListener('mouseleave', () => { resetTimer(); });
    }
    createDots();
    goToSlide(0);

    // ========== مودال ==========
    const modal = document.getElementById('myModal');
    const openBtns = document.querySelectorAll('#openModalBtn, #heroBtn');
    const cancelModal = document.getElementById('cancelModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    function openModal() { if(modal) modal.style.display = 'flex'; }
    function closeModal() { if(modal) modal.style.display = 'none'; }
    if(openBtns.length) openBtns.forEach(btn => btn.addEventListener('click', openModal));
    if(cancelModal) cancelModal.addEventListener('click', closeModal);
    if(closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            let phone = document.getElementById('modalPhone').value;
            if(phone.trim() === "") alert("لطفا شماره تماس را وارد کنید");
            else { alert("با تشکر! کد تخفیف برای شما ارسال شد."); closeModal(); }
        });
    }
    window.onclick = function(e) { if(e.target === modal) closeModal(); };

    // ========== منوی همبرگری ==========
    const hamburger = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('overlay');
    const closeBtn = document.getElementById('closeSidebarBtn');
    function openSidebar() { if(sidebar) sidebar.classList.add('open'); if(overlay) overlay.classList.add('show'); }
    function closeSidebar() { if(sidebar) sidebar.classList.remove('open'); if(overlay) overlay.classList.remove('show'); }
    if(hamburger) hamburger.addEventListener('click', openSidebar);
    if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if(overlay) overlay.addEventListener('click', closeSidebar);

    // ========== افکت گراند (ذرات با قابلیت کشیدن) ==========
    (function() {
        const canvas = document.getElementById('dragCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        const PARTICLE_COUNT = 120;
        let dragging = false;
        let dragX = 0, dragY = 0;
        const COLOR_PALETTE = [
            'hsla(210, 70%, 55%, 0.7)', 'hsla(190, 70%, 55%, 0.7)',
            'hsla(260, 60%, 65%, 0.7)', 'hsla(200, 80%, 70%, 0.7)',
            'hsla(280, 50%, 70%, 0.7)'
        ];
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.5;
                this.vy = (Math.random() - 0.5) * 0.5;
                this.size = Math.random() * 2.5 + 1.5;
                this.color = COLOR_PALETTE[Math.floor(Math.random() * COLOR_PALETTE.length)];
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (dragging && dragX && dragY) {
                    const dx = dragX - this.x;
                    const dy = dragY - this.y;
                    const dist = Math.hypot(dx, dy);
                    if (dist < 150) {
                        const angle = Math.atan2(dy, dx);
                        const force = (150 - dist) / 150 * 0.45;
                        this.vx += Math.cos(angle) * force;
                        this.vy += Math.sin(angle) * force;
                    }
                }
                const maxSpeed = 2.8;
                if (Math.abs(this.vx) > maxSpeed) this.vx = maxSpeed * Math.sign(this.vx);
                if (Math.abs(this.vy) > maxSpeed) this.vy = maxSpeed * Math.sign(this.vy);
                if (this.x < -50) this.x = width + 50;
                if (this.x > width + 50) this.x = -50;
                if (this.y < -50) this.y = height + 50;
                if (this.y > height + 50) this.y = -50;
            }
            draw() {
                ctx.save();
                ctx.shadowBlur = 8;
                ctx.shadowColor = 'rgba(100, 150, 230, 0.5)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size * 0.4, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255,255,255,0.85)';
                ctx.fill();
                ctx.restore();
            }
        }
        function initParticles() {
            particles = [];
            for (let i = 0; i < PARTICLE_COUNT; i++) particles.push(new Particle());
        }
        function drawConnections() {
            ctx.beginPath();
            ctx.lineWidth = 0.8;
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.hypot(dx, dy);
                    if (dist < 100) {
                        const alpha = (1 - dist / 100) * 0.25;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(100, 100, 230, ${alpha})`;
                        ctx.stroke();
                    }
                }
            }
        }
        function resize() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
            initParticles();
        }
        function animate() {
            if (!ctx) return;
            ctx.clearRect(0, 0, width, height);
            for (let p of particles) p.update();
            drawConnections();
            for (let p of particles) p.draw();
            requestAnimationFrame(animate);
        }
        canvas.addEventListener('mousedown', (e) => {
            dragging = true;
            const rect = canvas.getBoundingClientRect();
            dragX = e.clientX - rect.left;
            dragY = e.clientY - rect.top;
            e.preventDefault();
        });
        window.addEventListener('mousemove', (e) => {
            if (dragging) {
                const rect = canvas.getBoundingClientRect();
                dragX = e.clientX - rect.left;
                dragY = e.clientY - rect.top;
            }
        });
        window.addEventListener('mouseup', () => dragging = false);
        window.addEventListener('resize', resize);
        resize();
        animate();
    })();
</script>
</body>
<?php include "footer.php";?>
</html>
