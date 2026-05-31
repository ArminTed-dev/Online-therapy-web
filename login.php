<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود | دکتریار</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(270deg, #0f172a, #1e1b4b, #4c1d95, #0f172a);
            background-size: 800% 800%;
            animation: gradientShift 12s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-box {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(16px);
            border-radius: 48px;
            padding: 40px 32px;
            width: 100%;
            max-width: 450px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 25px 45px rgba(0,0,0,0.2);
        }
        .login-box h1 { color: white; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; text-align: right; position: relative; }
        .input-group i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 40px;
            color: white;
            font-family: inherit;
            font-size: 1rem;
        }
        input::placeholder { color: #cbd5e1; }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #4f46e5, #a855f7);
            border: none;
            border-radius: 40px;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }
        .error { color: #f87171; margin-top: 10px; }
        .register-link { margin-top: 20px; color: #cbd5e1; }
        .register-link a { color: #c084fc; text-decoration: none; }
    </style>
</head>
<body>
<div class="login-box">
    <h1><i class="fas fa-brain"></i> ورود به دکتریار</h1>
    <?php if(isset($_SESSION['login_error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?></div>
    <?php endif; ?>
        <form method="post" action="login-action.php">
        <div class="input-group">
            <i class="fas fa-phone-alt"></i>
            <input type="text" name="number_user" placeholder="شماره موبایل یا نام کاربری" required>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="pass_user" placeholder="رمز عبور" required>
        </div>
        <button type="submit">ورود</button>
    </form>
    <div class="register-link">
        حساب ندارید؟ <a href="register.php">ثبت‌نام کنید</a>
    </div>
    <div class="register-link" style="margin-top:10px; font-size:0.7rem;">ادمین: admin / 123456</div>
</div>
</body>
</html>