
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>پنل مشاور | دکتریار</title>
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
            background: linear-gradient(135deg, #1e1b4b 0%, #2e1a6b 100%);
            min-height: 100vh;
            padding: 20px;
        }

        /* پنل اصلی مشاور */
        .therapist-panel {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255,255,255,0.2);
            display: flex;
            flex-direction: column;
            height: 90vh;
        }

        /* هدر مشاور */
        .therapist-header {
            background: rgba(79, 70, 229, 0.3);
            backdrop-filter: blur(8px);
            padding: 16px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            flex-wrap: wrap;
            gap: 16px;
        }

        .therapist-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .therapist-avatar-large {
            width: 56px;
            height: 56px;
            background: linear-gradient(145deg, #a855f7, #4f46e5);
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .therapist-details h2 {
            color: white;
            font-size: 1.4rem;
        }

        .therapist-details p {
            color: #cbd5e1;
            font-size: 0.8rem;
        }

        .stats {
            display: flex;
            gap: 24px;
        }

        .stat-card {
            background: rgba(255,255,255,0.15);
            border-radius: 32px;
            padding: 8px 20px;
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: bold;
        }

        /* دوقسمتی اصلی: لیست مراجعین + چت */
        .main-workspace {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* لیست مراجعین (سبک مشابه سایدبار) */
        .clients-list {
            width: 320px;
            background: rgba(0, 0, 0, 0.2);
            border-left: 1px solid rgba(255,255,255,0.15);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .clients-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
            font-weight: bold;
        }

        .client-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            margin: 6px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 28px;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid transparent;
        }

        .client-item.active {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.4);
        }

        .client-avatar {
            width: 48px;
            height: 48px;
            background: #818cf8;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            position: relative;
        }

        .online-dot {
            width: 10px;
            height: 10px;
            background: #22c55e;
            border-radius: 50%;
            position: absolute;
            bottom: 2px;
            right: 2px;
            border: 2px solid #1e1b4b;
        }

        .client-info {
            flex: 1;
        }

        .client-name {
            color: white;
            font-weight: 500;
        }

        .last-message {
            font-size: 0.7rem;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .message-time {
            font-size: 0.6rem;
            color: #64748b;
        }

        /* ناحیه چت */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(255,255,255,0.05);
        }

        .chat-header-client {
            padding: 16px 24px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .selected-client-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .quick-responses {
            display: flex;
            gap: 8px;
        }

        .quick-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            padding: 6px 14px;
            border-radius: 30px;
            color: white;
            font-size: 0.75rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .quick-btn:hover {
            background: #4f46e5;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* استایل پیام‌ها از دید مشاور */
        .message {
            max-width: 70%;
            padding: 10px 16px;
            border-radius: 24px;
            position: relative;
        }

        .message.client-to-therapist {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            align-self: flex-start;
            border-bottom-left-radius: 6px;
        }

        .message.therapist-reply {
            background: white;
            color: #1e293b;
            align-self: flex-end;
            border-bottom-right-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .message-time {
            font-size: 0.6rem;
            margin-top: 6px;
            opacity: 0.7;
            text-align: left;
        }

        .chat-input-area {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            display: flex;
            gap: 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .chat-input-area input {
            flex: 1;
            padding: 14px 20px;
            border-radius: 40px;
            border: none;
            background: rgba(255,255,255,0.9);
            font-family: inherit;
            outline: none;
        }

        .chat-input-area button {
            background: #4f46e5;
            border: none;
            padding: 0 28px;
            border-radius: 40px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .typing-bubble {
            font-size: 0.7rem;
            color: #94a3b8;
            margin: 4px 16px;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .clients-list { width: 80px; }
            .client-info, .last-message { display: none; }
            .stats { display: none; }
        }
    </style>
</head>
<body>

<div class="therapist-panel">
    <div class="therapist-header">
        <div class="therapist-info">
            <div class="therapist-avatar-large">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="therapist-details">
                <h2>دکتر سارا کریمی</h2>
                <p>روانشناس بالینی | کد نظام‌پزشکی: ۱۲۳۴۵</p>
            </div>
        </div>
        <div class="stats">
            <div class="stat-card"><div class="stat-number" id="activeClientsCount">0</div><div>مراجعه‌کننده فعال</div></div>
            <div class="stat-card"><div class="stat-number" id="unreadCount">0</div><div>پیام خوانده‌نشده</div></div>
        </div>
    </div>

    <div class="main-workspace">
        <!-- لیست مراجعه‌کنندگان -->
        <div class="clients-list" id="clientsListContainer">
            <div class="clients-header"><i class="fas fa-users"></i> مراجعین من</div>
            <div id="clientsList"></div>
        </div>

        <!-- منطقه چت -->
        <div class="chat-area">
            <div class="chat-header-client" id="chatHeaderClient">
                <div class="selected-client-info">
                    <i class="fas fa-user-circle fa-2x"></i>
                    <div>
                        <h4 id="selectedClientName">انتخاب مراجعه‌کننده</h4>
                        <span id="clientStatus" style="font-size:0.7rem;color:#22c55e;">آفلاین</span>
                    </div>
                </div>
                <div class="quick-responses">
                    <button class="quick-btn" data-reply=" امید وار هستم که بهترین استفاده رو داشته باشید">🔒 صحبت ما به پایان رسید  </button>
                    <button class="quick-btn" data-reply="در حال بررسی پیام شما هستم، لطفاً صبور باشید.">⏳ در حال بررسی</button>
                    <button class="quick-btn" data-reply="یک تمرین تنفس عمیق انجام بدید و به من بگید چه حسی دارید.">🧘 تمرین تنفس</button>
                    <button class="quick-btn" data-reply="براتون وقت جلسه ویدیویی تعیین کنم؟">🎥 وقت ویدیو</button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessagesPanel">
                <!-- پیام‌ها اینجا قرار می‌گیرد -->
                <div style="text-align:center;color:#94a3b8;padding:40px;">از سمت راست یک مراجعه‌کننده را انتخاب کنید</div>
            </div>

            <div class="chat-input-area">
                <input type="text" id="therapistMessageInput" placeholder="پاسخ خود را بنویسید..." autocomplete="off">
                <button id="therapistSendBtn"><i class="fas fa-paper-plane"></i> ارسال</button>
            </div>
            <div id="typingHint" class="typing-bubble" style="display:none;">مراجع در حال تایپ است...</div>
        </div>
    </div>
</div>

<script>
    // ---------- داده‌های نمونه (شبیه‌سازی دیتابیس) ----------
    // هر مراجعه‌کننده شامل: id, name, online, messages, lastSeen
    let clientsData = {
        "cli_1": {
            id: "cli_1",
            name: "مریم احمدی",
            online: true,
            lastMessage: "دکتر خیلی نگرانم...",
            lastMessageTime: new Date().toISOString(),
            unread: 2,
            messages: [
                { role: "client", text: "سلام دکتر، چند شب هست اصلاً نمی‌تونم بخوابم", time: new Date(Date.now() - 3600000).toISOString(), seen: true },
                { role: "therapist", text: "سلام مریم جان. از کی شروع شده؟", time: new Date(Date.now() - 3500000).toISOString(), seen: true },
                { role: "client", text: "حدود یک هفته پیش، استرس شدید دارم", time: new Date(Date.now() - 200000).toISOString(), seen: false },
                { role: "client", text: "دکتر خیلی نگرانم...", time: new Date(Date.now() - 100000).toISOString(), seen: false }
            ]
        },
        "cli_2": {
            id: "cli_2",
            name: "علیرضا حسینی",
            online: false,
            lastMessage: "ممنون از راهنماییتون",
            lastMessageTime: new Date(Date.now() - 86400000).toISOString(),
            unread: 0,
            messages: [
                { role: "client", text: "سلام دکتر، مشکل ارتباط با همسرم", time: new Date(Date.now() - 172800000).toISOString(), seen: true },
                { role: "therapist", text: "لطفاً بیشتر توضیح بدید", time: new Date(Date.now() - 170000000).toISOString(), seen: true },
                { role: "client", text: "ممنون از راهنماییتون", time: new Date(Date.now() - 86400000).toISOString(), seen: true }
            ]
        },
        "cli_3": {
            id: "cli_3",
            name: "زهرا کریمی",
            online: true,
            lastMessage: "الان حالم بهتر شده",
            lastMessageTime: new Date(Date.now() - 1200000).toISOString(),
            unread: 0,
            messages: [
                { role: "client", text: "دکتر من وسواس فکری دارم", time: new Date(Date.now() - 7200000).toISOString(), seen: true },
                { role: "therapist", text: "درمان شناختی رفتاری می‌تونه کمک کنه", time: new Date(Date.now() - 7000000).toISOString(), seen: true },
                { role: "client", text: "الان حالم بهتر شده", time: new Date(Date.now() - 1200000).toISOString(), seen: false }
            ]
        }
    };

    let currentClientId = null; // id مراجع انتخاب شده
    let typingTimeout = null;

    // ذخیره در localStorage برای ماندگاری
    function saveAllData() {
        localStorage.setItem("therapistClientsData", JSON.stringify(clientsData));
    }

    function loadData() {
        const saved = localStorage.getItem("therapistClientsData");
        if(saved) {
            clientsData = JSON.parse(saved);
        } else {
            saveAllData();
        }
    }

    // آپدیت آماری (تعداد مراجعین آنلاین و پیام‌های خوانده نشده)
    function updateStats() {
        let onlineCount = 0;
        let totalUnread = 0;
        for(let id in clientsData) {
            if(clientsData[id].online) onlineCount++;
            totalUnread += (clientsData[id].unread || 0);
        }
        document.getElementById("activeClientsCount").innerText = onlineCount;
        document.getElementById("unreadCount").innerText = totalUnread;
    }

    // رندر لیست مراجعین در سایدبار
    function renderClientsList() {
        const container = document.getElementById("clientsList");
        container.innerHTML = "";
        for(let id in clientsData) {
            const client = clientsData[id];
            const div = document.createElement("div");
            div.className = `client-item ${currentClientId === id ? "active" : ""}`;
            div.setAttribute("data-id", id);
            const lastMsgTime = new Date(client.lastMessageTime).toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'});
            div.innerHTML = `
                <div class="client-avatar">
                    <i class="fas fa-user"></i>
                    ${client.online ? '<div class="online-dot"></div>' : ''}
                </div>
                <div class="client-info">
                    <div class="client-name">${escapeHtml(client.name)}</div>
                    <div class="last-message">${escapeHtml(client.lastMessage.substring(0, 30))}</div>
                    <div class="message-time">${lastMsgTime}</div>
                </div>
                ${client.unread > 0 ? `<span style="background:#ef4444; border-radius:20px; padding:2px 8px; font-size:0.7rem; color:white;">${client.unread}</span>` : ''}
            `;
            div.addEventListener("click", () => selectClient(id));
            container.appendChild(div);
        }
    }

    // انتخاب مراجعه‌کننده و نمایش چت
    function selectClient(clientId) {
        currentClientId = clientId;
        const client = clientsData[clientId];
        if(!client) return;
        // ریست کردن unread برای این مراجع
        client.unread = 0;
        saveAllData();
        updateStats();
        renderClientsList(); // ریفرش لیست برای حذف نشانگر unread

        // آپدیت هدر چت
        document.getElementById("selectedClientName").innerText = client.name;
        const statusSpan = document.getElementById("clientStatus");
        statusSpan.innerText = client.online ? "🟢 آنلاین" : "⚫ آفلاین";
        statusSpan.style.color = client.online ? "#22c55e" : "#94a3b8";

        // نمایش پیام‌ها
        renderMessages(clientId);
    }

    // نمایش پیام‌های جاری
    function renderMessages(clientId) {
        const container = document.getElementById("chatMessagesPanel");
        const client = clientsData[clientId];
        if(!client) {
            container.innerHTML = "<div style='text-align:center;color:#94a3b8;padding:40px;'>مراجعه‌کننده انتخاب نشده</div>";
            return;
        }
        if(client.messages.length === 0) {
            container.innerHTML = "<div style='text-align:center;color:#94a3b8;padding:40px;'>هنوز پیامی وجود ندارد</div>";
            return;
        }
        container.innerHTML = "";
        client.messages.forEach((msg, idx) => {
            const msgDiv = document.createElement("div");
            msgDiv.className = `message ${msg.role === "client" ? "client-to-therapist" : "therapist-reply"}`;
            const timeStr = new Date(msg.time).toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'});
            msgDiv.innerHTML = `
                <div>${escapeHtml(msg.text)}</div>
                <div class="message-time">${timeStr}</div>
            `;
            container.appendChild(msgDiv);
        });
        container.scrollTop = container.scrollHeight;
    }

    // ارسال پیام از طرف مشاور
    function sendTherapistReply() {
        if(!currentClientId) {
            alert("لطفاً ابتدا یک مراجعه‌کننده را انتخاب کنید");
            return;
        }
        const input = document.getElementById("therapistMessageInput");
        const text = input.value.trim();
        if(text === "") return;
        
        const client = clientsData[currentClientId];
        const newMsg = {
            role: "therapist",
            text: text,
            time: new Date().toISOString(),
            seen: true
        };
        client.messages.push(newMsg);
        client.lastMessage = text;
        client.lastMessageTime = new Date().toISOString();
        // اگر مراجع آنلاین باشد، شبیه‌سازی تایید خواندن
        if(client.online) {
            // در حالت واقعی، پیام خوانده می‌شود
        }
        saveAllData();
        renderMessages(currentClientId);
        renderClientsList(); // بروزرسانی آخرین پیام در لیست
        input.value = "";
        
        // شبیه‌سازی پاسخ خودکار از طرف مراجع (برای جذابیت دمو)
        if(client.online && Math.random() > 0.6) {
            setTimeout(() => {
                const autoClientMsg = {
                    role: "client",
                    text: "ممنونم دکتر...",
                    time: new Date().toISOString(),
                    seen: true
                };
                client.messages.push(autoClientMsg);
                client.lastMessage = autoClientMsg.text;
                client.lastMessageTime = autoClientMsg.time;
                saveAllData();
                renderMessages(currentClientId);
                renderClientsList();
            }, 2000);
        }
    }

    // پاسخ سریع با دکمه‌های از پیش تعیین شده
    function quickReply(replyText) {
        if(!currentClientId) return;
        document.getElementById("therapistMessageInput").value = replyText;
        sendTherapistReply();
    }

    // شبیه‌سازی پیام جدید از طرف مراجع (برای تست آنلاین)
    function simulateIncomingMessage() {
        // فقط برای مراجع آنلاین و به صورت رندوم
        for(let id in clientsData) {
            const client = clientsData[id];
            if(client.online && Math.random() < 0.2 && id !== currentClientId) {
                const randomMessages = ["سلام دکتر وقتتون بخیر", "احساس میکنم امروز بهتر شدم", "یه سوال داشتم", "ممنون از همراهیتون"];
                const randomText = randomMessages[Math.floor(Math.random() * randomMessages.length)];
                const newMsg = {
                    role: "client",
                    text: randomText,
                    time: new Date().toISOString(),
                    seen: false
                };
                client.messages.push(newMsg);
                client.lastMessage = randomText;
                client.lastMessageTime = newMsg.time;
                client.unread = (client.unread || 0) + 1;
                saveAllData();
                if(currentClientId === id) {
                    renderMessages(id);
                }
                renderClientsList();
                updateStats();
                break;
            }
        }
    }

    // نمایش تایپینگ (برای دمو)
    function showTyping() {
        const hint = document.getElementById("typingHint");
        hint.style.display = "block";
        if(typingTimeout) clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            hint.style.display = "none";
        }, 3000);
    }

    // هر ۱۵ ثانیه یک پیام ورودی شبیه‌سازی شود
    setInterval(simulateIncomingMessage, 15000);
    // شبیه‌سازی تایپ کردن مراجع (اگر آنلاین باشد)
    setInterval(() => {
        if(currentClientId && clientsData[currentClientId]?.online) {
            showTyping();
        }
    }, 20000);

    // Event listeners
    document.getElementById("therapistSendBtn").addEventListener("click", sendTherapistReply);
    document.getElementById("therapistMessageInput").addEventListener("keypress", (e) => {
        if(e.key === "Enter") sendTherapistReply();
    });
    document.querySelectorAll(".quick-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const reply = btn.getAttribute("data-reply");
            if(reply) quickReply(reply);
        });
    });

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }

    // مقداردهی اولیه
    loadData();
    renderClientsList();
    updateStats();
    // اولین مراجع را به صورت خودکار انتخاب کن (در صورت وجود)
    const firstClientId = Object.keys(clientsData)[0];
    if(firstClientId) selectClient(firstClientId);
</script>
</body>
</html>