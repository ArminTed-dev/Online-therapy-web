
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>ثبت‌نام | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* همان استایل‌های قبلی */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Vazirmatn', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: linear-gradient(270deg, #0f172a, #1e1b4b, #4c1d95, #0f172a);
            background-size: 800% 800%;
            animation: gradientShift 12s ease infinite;
            position: relative;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .blob {
            position: fixed;
            width: 400px;
            height: 400px;
            background: rgba(79, 70, 229, 0.3);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: floatBlob 20s infinite alternate;
        }
        .blob1 { top: -100px; left: -100px; }
        .blob2 { bottom: -100px; right: -100px; background: rgba(168, 85, 247, 0.3); animation-duration: 25s; }
        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 100px) scale(1.2); }
        }
        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 48px;
            padding: 32px 28px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.15);
        }
        .logo { text-align: center; margin-bottom: 32px; color: white; }
        .logo i { font-size: 3rem; background: linear-gradient(135deg, #fff, #c084fc); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .logo h1 { font-size: 1.8rem; margin-top: 8px; }
        .form-group { margin-bottom: 24px; position: relative; }
        .form-group i { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        input, select {
            width: 100%;
            padding: 16px 48px 16px 16px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 40px;
            font-family: inherit;
            font-size: 1rem;
            color: white;
            outline: none;
        }
        select option { background: #1e1b4b; }
        input::placeholder { color: #cbd5e1; }
        input:focus, select:focus { border-color: #a855f7; background: rgba(255,255,255,0.25); }
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, #4f46e5, #a855f7);
            border: none;
            border-radius: 40px;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 8px;
        }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 8px 20px rgba(79,70,229,0.5); }
        .error-msg { color: #f87171; font-size: 0.75rem; margin-top: 6px; padding-right: 16px; }
        .footer-note { text-align: center; margin-top: 24px; color: #cbd5e1; font-size: 0.75rem; }
        .footer-note a { color: #c084fc; text-decoration: none; }
    </style>
</head>
<body>
<div class="blob blob1"></div>
<div class="blob blob2"></div>

<div class="auth-container">
    <div class="logo">
        <i class="fas fa-brain"></i>
        <h1>دکتریار</h1>
        <p style="font-size:0.8rem; color:#cbd5e1;">همین حالا عضو شوید</p>
    </div>
    <form method="post" action="register-action.php">
    <div id="registerForm" class="auth-form">
        <div class="form-group">
            <i class="fas fa-user"></i>
            <input type="text" id="txtname" name="fullname_user" placeholder="نام و نام خانوادگی">
        </div>
        <div class="form-group">
            <i class="fas fa-phone-alt"></i>
            <input type="tel" id="txtnumber" name="number_user" placeholder="شماره موبایل">
        </div>
<!--        <div class="form-group">
            <i class="fas fa-briefcase"></i>
            <select id="regRole">
                <option value="client">مراجعه‌کننده</option>
                <option value="therapist">مشاور (روانشناس)</option>
            </select>
        </div>-->
        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="pass_user" placeholder="رمز عبور (حداقل ۴ رقم)">
        </div>
         <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="repassword" name="repassword" placeholder="رمز عبور ( تکرار )">
        </div>
        <div id="regError" class="error-msg"></div>
        <button class="btn-submit" id="doRegister">ثبت‌نام</button>
        <div class="footer-note">
            قبلاً عضو شده‌اید؟ <a href="login.php">وارد شوید</a>
        </div>
    </div>
        </form>
</div>

<script>
    function loadClients() {
        const stored = localStorage.getItem("admin_clients");
        return stored ? JSON.parse(stored) : [];
    }
    function loadTherapists() {
        const stored = localStorage.getItem("admin_therapists");
        return stored ? JSON.parse(stored) : [];
    }
    function saveClients(clients) { localStorage.setItem("admin_clients", JSON.stringify(clients)); }
    function saveTherapists(therapists) { localStorage.setItem("admin_therapists", JSON.stringify(therapists)); }

    function isPhoneExists(phone) {
        const clients = loadClients();
        const therapists = loadTherapists();
        return clients.some(c => c.phone === phone) || therapists.some(t => t.phone === phone);
    }

    function registerUser(name, phone, password, role) {
        if(!name || !phone || !password) return "لطفاً تمام فیلدها را پر کنید";
        if(phone.length < 11) return "شماره موبایل نامعتبر است";
        if(password.length < 4) return "رمز عبور حداقل ۴ کاراکتر";
        if(isPhoneExists(phone)) return "این شماره قبلاً ثبت شده است";

        const newId = (role === "client" ? "cli_" : "th_") + Date.now();
        const newUser = { id: newId, name, phone, password, role, createdAt: new Date().toISOString() };
        if(role === "client") {
            const clients = loadClients();
            clients.push(newUser);
            saveClients(clients);
        } else {
            const therapists = loadTherapists();
            therapists.push({ ...newUser, specialty: "عمومی" });
            saveTherapists(therapists);
        }
        return "success";
    }

    document.getElementById("doRegister").addEventListener("click", () => {
        const name = document.getElementById("regName").value.trim();
        const phone = document.getElementById("regPhone").value.trim();
        const role = document.getElementById("regRole").value;
        const password = document.getElementById("regPassword").value;
        const errorSpan = document.getElementById("regError");
        const res = registerUser(name, phone, password, role);
        if(res === "success") {
            alert("ثبت‌نام با موفقیت انجام شد. اکنون می‌توانید وارد شوید.");
            window.location.href = "register.php";
        } else {
            errorSpan.innerText = res;
        }
    });
</script>
</body>
</html>