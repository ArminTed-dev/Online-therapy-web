
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>چت پیشرفته تراپی | دکتریار</title>
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
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* کانتینر اصلی با دو پنل (لیست مشاوران و چت) */
        .chat-dashboard {
            max-width: 1300px;
            width: 100%;
            height: 85vh;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(16px);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            display: flex;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.4);
        }

        /* پنل سمت راست - لیست مشاوران */
        .therapist-list {
            width: 300px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            border-left: 1px solid rgba(255,255,255,0.3);
            display: flex;
            flex-direction: column;
        }

        .list-header {
            padding: 24px 20px;
            background: rgba(79, 70, 229, 0.2);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .list-header h3 {
            font-size: 1.3rem;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .therapist-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .therapist-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            background: rgba(255,255,255,0.5);
            margin-bottom: 12px;
            border-radius: 24px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .therapist-card.active {
            background: white;
            border-color: #4f46e5;
            box-shadow: 0 8px 20px rgba(79,70,229,0.2);
        }

        .therapist-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(145deg, #818cf8, #c084fc);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            position: relative;
        }

        .online-indicator {
            width: 12px;
            height: 12px;
            background: #22c55e;
            border-radius: 50%;
            position: absolute;
            bottom: 2px;
            right: 2px;
            border: 2px solid white;
        }

        .offline-indicator {
            background: #94a3b8;
        }

        .therapist-info h4 {
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .therapist-info p {
            font-size: 0.75rem;
            color: #475569;
        }

        /* پنل چت */
        .chat-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(255,255,255,0.2);
        }

        .chat-header {
            padding: 20px 24px;
            background: rgba(255,255,255,0.4);
            backdrop-filter: blur(4px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .selected-therapist-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-text {
            font-size: 0.7rem;
            color: #22c55e;
        }

        .typing-indicator {
            font-size: 0.8rem;
            color: #4f46e5;
            background: rgba(79,70,229,0.1);
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* پیام‌ها */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .date-separator {
            text-align: center;
            font-size: 0.7rem;
            color: #64748b;
            margin: 8px 0;
            position: relative;
        }
        .date-separator span {
            background: rgba(255,255,255,0.6);
            padding: 4px 12px;
            border-radius: 20px;
            backdrop-filter: blur(4px);
        }

        .message {
            display: flex;
            flex-direction: column;
            max-width: 75%;
            padding: 10px 14px;
            border-radius: 20px;
            position: relative;
        }

        .message.client {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .message.therapist {
            background: white;
            color: #1e293b;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .message-img {
            max-width: 200px;
            border-radius: 16px;
            margin-top: 5px;
            cursor: pointer;
        }

        .message-time {
            font-size: 0.6rem;
            margin-top: 6px;
            text-align: left;
            opacity: 0.7;
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .tick {
            font-size: 0.7rem;
        }

        /* ورودی پیام */
        .chat-input-area {
            padding: 16px 20px;
            background: rgba(255,255,255,0.4);
            backdrop-filter: blur(4px);
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .emoji-picker {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        .emoji-btn {
            background: rgba(255,255,255,0.7);
            border: none;
            border-radius: 30px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.2s;
        }
        .input-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .input-wrapper input {
            flex: 1;
            padding: 14px 18px;
            border: none;
            border-radius: 40px;
            font-family: inherit;
            font-size: 0.9rem;
            background: white;
            outline: none;
        }
        .file-label, .send-btn {
            background: #4f46e5;
            width: 48px;
            height: 48px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: 0.2s;
            border: none;
        }
        .send-btn:hover, .file-label:hover {
            background: #7c3aed;
            transform: scale(1.02);
        }
        #fileInput {
            display: none;
        }

        @media (max-width: 768px) {
            .therapist-list { width: 80px; }
            .therapist-list .therapist-info { display: none; }
            .list-header h3 span { display: none; }
            .therapist-card { justify-content: center; }
            .message { max-width: 90%; }
        }
    </style>
</head>
<body>

<div class="chat-dashboard">
    <!-- پنل لیست مشاوران -->
    <div class="therapist-list">
        <div class="list-header">
            <h3><i class="fas fa-user-md"></i> <span>متخصصین</span></h3>
        </div>
        <div class="therapist-items" id="therapistList">
            <!-- مشاور 1 -->
            <div class="therapist-card" data-id="1" data-name="دکتر سارا کریمی" data-specialty="روانشناس بالینی" data-online="true">
                <div class="therapist-avatar">
                    <i class="fas fa-user-md"></i>
                    <div class="online-indicator"></div>
                </div>
                <div class="therapist-info">
                    <h4>دکتر سارا کریمی</h4>
                    <p>روانشناس بالینی</p>
                </div>
            </div>
            <!-- مشاور 2 -->
            <div class="therapist-card" data-id="2" data-name="دکتر علی رضایی" data-specialty="متخصص زوج درمانی" data-online="true">
                <div class="therapist-avatar">
                    <i class="fas fa-user-graduate"></i>
                    <div class="online-indicator"></div>
                </div>
                <div class="therapist-info">
                    <h4>دکتر علی رضایی</h4>
                    <p>زوج درمانی</p>
                </div>
            </div>
            <!-- مشاور 3 آفلاین -->
            <div class="therapist-card" data-id="3" data-name="دکتر مریم نادری" data-specialty="روانشناس کودک" data-online="false">
                <div class="therapist-avatar">
                    <i class="fas fa-child"></i>
                    <div class="online-indicator offline-indicator" style="background:#94a3b8;"></div>
                </div>
                <div class="therapist-info">
                    <h4>دکتر مریم نادری</h4>
                    <p>روانشناس کودک</p>
                </div>
            </div>
        </div>
    </div>

    <!-- پنل چت -->
    <div class="chat-panel">
        <div class="chat-header">
            <div class="selected-therapist-info" id="chatHeaderInfo">
                <i class="fas fa-user-circle fa-2x"></i>
                <div>
                    <h4 id="selectedTherapistName">دکتر سارا کریمی</h4>
                    <p class="status-text" id="onlineStatus">🟢 آنلاین</p>
                </div>
            </div>
            <div id="typingStatus" class="typing-indicator" style="display: none;">در حال تایپ...</div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <!-- پیام‌ها داینامیک -->
        </div>

        <div class="chat-input-area">
            <div class="emoji-picker">
                <button class="emoji-btn" data-emoji="😊">😊</button>
                <button class="emoji-btn" data-emoji="❤️">❤️</button>
                <button class="emoji-btn" data-emoji="👍">👍</button>
                <button class="emoji-btn" data-emoji="😂">😂</button>
                <button class="emoji-btn" data-emoji="🙏">🙏</button>
                <button class="emoji-btn" data-emoji="🌹">🌹</button>
            </div>
            <div class="input-wrapper">
                <input type="text" id="messageInput" placeholder="پیام خود را بنویسید...">
                <label for="fileInput" class="file-label"><i class="fas fa-image"></i></label>
                <input type="file" id="fileInput" accept="image/*">
                <button class="send-btn" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    // ======================= داده‌های مشاوران =======================
    const therapists = {
        1: { name: "دکتر سارا کریمی", specialty: "روانشناس بالینی", online: true, avatar: "user-md" },
        2: { name: "دکتر علی رضایی", specialty: "زوج درمانی", online: true, avatar: "user-graduate" },
        3: { name: "دکتر مریم نادری", specialty: "روانشناس کودک", online: false, avatar: "child" }
    };

    let currentTherapistId = "1";
    let messages = {}; // ساختار: { therapistId: [messages] }
    
    // بارگذاری پیام‌های ذخیره شده از localStorage
    function loadMessages() {
        const saved = localStorage.getItem("chatMessagesAdvanced");
        if(saved) {
            messages = JSON.parse(saved);
        } else {
            // پیام‌های پیش‌فرض برای هر مشاور
            messages = {
                "1": [
                    { role: "therapist", text: "سلام! چطور می‌تونم کمکتون کنم؟", time: new Date().toISOString(), seen: true },
                    { role: "client", text: "سلام دکتر، احساس اضطراب شدید دارم", time: new Date(Date.now() - 60000).toISOString(), seen: true }
                ],
                "2": [
                    { role: "therapist", text: "در مورد مشکلات زوجین صحبت کنیم؟", time: new Date().toISOString(), seen: true }
                ],
                "3": [
                    { role: "therapist", text: "سلام، در حال حاضر آفلاین هستم، لطفاً بعداً پیام بگذارید", time: new Date().toISOString(), seen: true }
                ]
            };
        }
    }

    function saveMessages() {
        localStorage.setItem("chatMessagesAdvanced", JSON.stringify(messages));
    }

    // تابع برای نمایش تاریخ خوانا
    function formatMessageDate(date) {
        const now = new Date();
        const msgDate = new Date(date);
        if(msgDate.toDateString() === now.toDateString()) return "امروز";
        const yesterday = new Date(now); yesterday.setDate(now.getDate()-1);
        if(msgDate.toDateString() === yesterday.toDateString()) return "دیروز";
        return msgDate.toLocaleDateString('fa-IR');
    }

    // نمایش پیام‌های مشاور جاری
    function renderMessages() {
        const container = document.getElementById("chatMessages");
        if(!container) return;
        const msgs = messages[currentTherapistId] || [];
        let lastDate = null;
        container.innerHTML = "";
        msgs.forEach((msg, idx) => {
            const msgDate = new Date(msg.time);
            const dateKey = msgDate.toDateString();
            if(lastDate !== dateKey) {
                const sep = document.createElement("div");
                sep.className = "date-separator";
                sep.innerHTML = `<span>${formatMessageDate(msg.time)}</span>`;
                container.appendChild(sep);
                lastDate = dateKey;
            }
            const messageDiv = document.createElement("div");
            messageDiv.className = `message ${msg.role}`;
            let content = `<div class="text">${escapeHtml(msg.text)}</div>`;
            if(msg.image) {
                content += `<img src="${msg.image}" class="message-img" alt="تصویر">`;
            }
            const timeStr = new Date(msg.time).toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'});
            let tickHtml = "";
            if(msg.role === "client") {
                tickHtml = `<span class="tick">${msg.seen ? '✓✓' : '✓'}</span>`;
            }
            content += `<div class="message-time">${timeStr} ${tickHtml}</div>`;
            messageDiv.innerHTML = content;
            container.appendChild(messageDiv);
        });
        container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }

    // اضافه کردن پیام جدید (از سمت client)
    function addMessage(text, imageBase64 = null) {
        if(!text && !imageBase64) return;
        const newMsg = {
            role: "client",
            text: text || "📷 تصویر",
            time: new Date().toISOString(),
            seen: false,
            image: imageBase64
        };
        if(!messages[currentTherapistId]) messages[currentTherapistId] = [];
        messages[currentTherapistId].push(newMsg);
        saveMessages();
        renderMessages();
        
        // شبیه‌سازی پاسخ مشاور (اگر آنلاین باشد)
        const therapistOnline = therapists[currentTherapistId]?.online;
        if(therapistOnline) {
            // نشان دادن تایپینگ
            const typingDiv = document.getElementById("typingStatus");
            typingDiv.style.display = "block";
            setTimeout(() => {
                typingDiv.style.display = "none";
                // پاسخ خودکار بر اساس کلمات کلیدی ساده
                let replyText = "ممنون از پیام شما. در حال بررسی هستم.";
                if(text.includes("اضطراب")) replyText = "اضطراب قابل کنترل است. تمرین تنفس عمیق رو امتحان کنید.";
                else if(text.includes("خواب")) replyText = "بی‌خوابی رو میشه با اصلاح ساعت خواب درمان کرد.";
                else if(text.includes("ممنون")) replyText = "خواهش می‌کنم. هر سوالی دارید بپرسید.";
                const therapistMsg = {
                    role: "therapist",
                    text: replyText,
                    time: new Date().toISOString(),
                    seen: true
                };
                messages[currentTherapistId].push(therapistMsg);
                // علامت دیده شدن برای پیام قبلی client
                const lastClientMsg = messages[currentTherapistId].findLast(m => m.role === "client");
                if(lastClientMsg) lastClientMsg.seen = true;
                saveMessages();
                renderMessages();
            }, 1500);
        } else {
            // مشاور آفلاین: فقط پیام ذخیره می‌شود بدون پاسخ خودکار
            const offlineReply = {
                role: "therapist",
                text: "متخصص مورد نظر آنلاین نیست. پیام شما ثبت شد، به زودی پاسخ می‌دهیم.",
                time: new Date().toISOString(),
                seen: true
            };
            messages[currentTherapistId].push(offlineReply);
            saveMessages();
            renderMessages();
        }
    }

    // تغییر مشاور
    function switchTherapist(id) {
        currentTherapistId = id;
        const therapist = therapists[id];
        document.getElementById("selectedTherapistName").innerText = therapist.name;
        const statusSpan = document.getElementById("onlineStatus");
        if(therapist.online) {
            statusSpan.innerHTML = "🟢 آنلاین";
            statusSpan.style.color = "#22c55e";
        } else {
            statusSpan.innerHTML = "⚫ آفلاین";
            statusSpan.style.color = "#94a3b8";
        }
        // هایلایت کارت فعال
        document.querySelectorAll(".therapist-card").forEach(card => {
            if(card.getAttribute("data-id") === id) card.classList.add("active");
            else card.classList.remove("active");
        });
        renderMessages();
    }

    // ارسال پیام متنی یا ایموجی
    function sendMessage() {
        const input = document.getElementById("messageInput");
        const text = input.value.trim();
        if(text === "") return;
        addMessage(text);
        input.value = "";
    }

    // event listeners
    document.getElementById("sendBtn").addEventListener("click", sendMessage);
    document.getElementById("messageInput").addEventListener("keypress", (e) => {
        if(e.key === "Enter") sendMessage();
    });
    
    // انتخاب ایموجی
    document.querySelectorAll(".emoji-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const emoji = btn.getAttribute("data-emoji");
            const input = document.getElementById("messageInput");
            input.value += emoji;
            input.focus();
        });
    });
    
    // آپلود تصویر
    const fileInput = document.getElementById("fileInput");
    fileInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if(file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                addMessage("", ev.target.result);
                fileInput.value = "";
            };
            reader.readAsDataURL(file);
        } else alert("لطفاً یک فایل تصویر انتخاب کنید");
    });
    
    // انتخاب مشاور از لیست
    document.querySelectorAll(".therapist-card").forEach(card => {
        card.addEventListener("click", () => {
            const id = card.getAttribute("data-id");
            switchTherapist(id);
        });
    });
    
    // بارگذاری اولیه
    loadMessages();
    switchTherapist("1");
</script>
</body>
</html>