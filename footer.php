<footer>
    <div class="footer-container">
        <div class="footer-column">
            <h3><i class="fas fa-brain"></i> دکتریار</h3>
            <p>پلتفرم تخصصی مشاوره آنلاین روانشناسی با برترین متخصصین کشور. حریم خصوصی، امنیت و پشتیبانی ۲۴ ساعته.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-telegram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div class="footer-column">
            <h3>دسترسی سریع</h3>
            <ul>
                <li><a href="index.html"><i class="fas fa-chevron-left"></i> صفحه اصلی</a></li>
                <li><a href="disorders.html"><i class="fas fa-chevron-left"></i> اختلالات روانشناسی</a></li>
                <li><a href="chat-advanced.html"><i class="fas fa-chevron-left"></i> مشاوره آنلاین</a></li>
                <li><a href="therapist-panel.html"><i class="fas fa-chevron-left"></i> پنل مشاوران</a></li>
                <li><a href="login.html"><i class="fas fa-chevron-left"></i> ورود / ثبت‌نام</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>تماس با ما</h3>
            <p><i class="fas fa-phone-alt"></i> تلفن: ۰۲۱-۱۲۳۴۵۶۷۸</p>
            <p><i class="fas fa-mobile-alt"></i> همراه: ۰۹۱۲۳۴۵۶۷۸۹</p>
            <p><i class="fas fa-envelope"></i> ایمیل: info@drsite.com</p>
            <p><i class="fas fa-map-marker-alt"></i> آدرس: تهران، خیابان ولیعصر، پلاک ۱۲۳، واحد ۵</p>
            <p><i class="fas fa-clock"></i> ساعت کاری: ۸ صبح تا ۱۰ شب (همه روزه)</p>
        </div>
        <div class="footer-column">
            <h3>مجوزها و نمادها</h3>
            <div class="badges">
                <span>⚕️ نظام روانشناسی</span>
                <span>🔒 SSL امن</span>
                <span>✅ اینماد</span>
            </div>
            <img src="https://trustseal.enamad.ir/logo.aspx?id=12345" alt="enamad" style="width:100px; margin-top:10px;">
            <p style="font-size:0.7rem; margin-top:15px;">&copy; ۱۴۰۴ کلیه حقوق برای دکتریار محفوظ است.</p>
        </div>
    </div>
</footer>
<style>/* ========== FOOTER پیشرفته ========== */
footer {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(16px);
    border-top: 1px solid rgba(255,255,255,0.3);
    margin-top: 80px;
    padding: 50px 20px 30px;
    border-radius: 48px 48px 0 0;
}
.footer-container {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 30px;
}
.footer-column {
    flex: 1;
    min-width: 200px;
}
.footer-column h3 {
    font-size: 1.3rem;
    margin-bottom: 20px;
    color: #1e1b4b;
    font-weight: 700;
}
.footer-column p, .footer-column li {
    margin-bottom: 12px;
    color: #334155;
    font-size: 0.9rem;
}
.footer-column ul {
    list-style: none;
}
.footer-column a {
    text-decoration: none;
    color: #334155;
    transition: 0.2s;
}
.footer-column a:hover {
    color: #4f46e5;
    padding-right: 5px;
}
.social-links {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}
.social-links a {
    background: rgba(255,255,255,0.3);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.social-links a:hover {
    background: #4f46e5;
    color: white;
}
.badges span {
    display: inline-block;
    background: rgba(0,0,0,0.05);
    padding: 6px 12px;
    border-radius: 40px;
    margin-left: 8px;
    margin-bottom: 8px;
    font-size: 0.7rem;
}
@media (max-width: 768px) {
    .footer-container {
        flex-direction: column;
        text-align: center;
    }
    .social-links { justify-content: center; }
}
</style>